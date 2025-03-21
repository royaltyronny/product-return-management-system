<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ReturnPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'return_period_days',
        'restocking_fee_percentage',
        'requires_receipt',
        'requires_original_packaging',
        'allows_partial_returns',
        'applies_to_all_products',
        'active',
        'effective_date',
        'expiration_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'return_period_days' => 'integer',
        'restocking_fee_percentage' => 'decimal:2',
        'requires_receipt' => 'boolean',
        'requires_original_packaging' => 'boolean',
        'allows_partial_returns' => 'boolean',
        'applies_to_all_products' => 'boolean',
        'active' => 'boolean',
        'effective_date' => 'date',
        'expiration_date' => 'date',
    ];

    /**
     * Get the user who created this policy
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this policy
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the products this policy applies to
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_return_policy');
    }

    /**
     * Get the categories this policy applies to
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_return_policy');
    }

    /**
     * Get return requests that used this policy
     */
    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class, 'policy_id');
    }

    /**
     * Check if a product is eligible for return based on this policy
     */
    public function isProductEligible(Product $product, \DateTime $purchaseDate): bool
    {
        // Check if policy is active
        if (!$this->active) {
            return false;
        }
        
        // Check if policy is within effective date range
        $today = now()->startOfDay();
        if ($today->lt($this->effective_date)) {
            return false;
        }
        
        if ($this->expiration_date && $today->gt($this->expiration_date)) {
            return false;
        }

        // Check if policy applies to this product
        if (!$this->applies_to_all_products) {
            $productIds = $this->products->pluck('id')->toArray();
            $categoryIds = $this->categories->pluck('id')->toArray();
            
            // If product is not directly associated and its category is not associated
            if (!in_array($product->id, $productIds) && !in_array($product->category_id, $categoryIds)) {
                return false;
            }
        }

        // Check if within return period
        $daysSincePurchase = now()->diffInDays($purchaseDate);
        if ($daysSincePurchase > $this->return_period_days) {
            return false;
        }

        return true;
    }

    /**
     * Calculate restocking fee for a return
     */
    public function calculateRestockingFee(float $productPrice): float
    {
        if ($this->restocking_fee_percentage <= 0) {
            return 0;
        }

        return round($productPrice * ($this->restocking_fee_percentage / 100), 2);
    }
    
    /**
     * Get active policies that could apply to a product
     */
    public static function getActivePoliciesForProduct(Product $product)
    {
        return self::where('active', true)
            ->where(function($query) use ($product) {
                $query->where('applies_to_all_products', true)
                    ->orWhereHas('products', function($q) use ($product) {
                        $q->where('products.id', $product->id);
                    })
                    ->orWhereHas('categories', function($q) use ($product) {
                        $q->where('categories.id', $product->category_id);
                    });
            })
            ->where('effective_date', '<=', now())
            ->where(function($query) {
                $query->whereNull('expiration_date')
                    ->orWhere('expiration_date', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
