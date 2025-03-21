<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    use HasFactory;

    // Movement types
    const TYPE_ADD = 'add';
    const TYPE_REMOVE = 'remove';
    const TYPE_TRANSFER = 'transfer';
    const TYPE_ADJUSTMENT = 'adjustment';
    const TYPE_RETURN = 'return';
    
    // Inventory types
    const INVENTORY_REGULAR = 'regular';
    const INVENTORY_DAMAGED = 'damaged';
    const INVENTORY_REFURBISHED = 'refurbished';
    const INVENTORY_PENDING = 'pending';

    protected $fillable = [
        'warehouse_id',
        'product_id',
        'user_id',
        'movement_type',
        'quantity',
        'direction',
        'inventory_type',
        'reference_type',
        'reference_id',
        'location_code',
        'notes',
        'details',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'details' => 'json',
    ];

    /**
     * Get the warehouse associated with this movement
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the product associated with this movement
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who performed this movement
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the return request associated with this movement (if applicable)
     */
    public function returnRequest()
    {
        if ($this->reference_type === 'return_request') {
            return $this->belongsTo(ReturnRequest::class, 'reference_id');
        }
        
        return null;
    }

    /**
     * Get formatted description of the movement
     */
    public function getDescriptionAttribute(): string
    {
        $description = '';
        
        switch ($this->movement_type) {
            case self::TYPE_ADD:
                $description = "Added {$this->quantity} {$this->inventory_type} items";
                break;
                
            case self::TYPE_REMOVE:
                $description = "Removed {$this->quantity} {$this->inventory_type} items";
                break;
                
            case self::TYPE_TRANSFER:
                $details = json_decode($this->details);
                $destination = $details->destination_warehouse_name ?? 'another warehouse';
                $description = "Transferred {$this->quantity} {$this->inventory_type} items to {$destination}";
                break;
                
            case self::TYPE_ADJUSTMENT:
                $details = json_decode($this->details);
                $previous = $details->previous_quantity ?? 0;
                $new = $details->new_quantity ?? 0;
                $description = "Adjusted {$this->inventory_type} inventory from {$previous} to {$new}";
                break;
                
            case self::TYPE_RETURN:
                $details = json_decode($this->details);
                $rma = $details->rma_number ?? 'unknown';
                $description = "Processed return #{$rma} - added {$this->quantity} {$this->inventory_type} items";
                break;
                
            default:
                $description = "Inventory movement of {$this->quantity} {$this->inventory_type} items";
        }
        
        if ($this->location_code) {
            $description .= " (Location: {$this->location_code})";
        }
        
        return $description;
    }
    
    /**
     * Get recent movements for a product
     */
    public static function getRecentMovementsForProduct(int $productId, int $limit = 10)
    {
        return self::with(['warehouse', 'user'])
            ->where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get recent movements for a warehouse
     */
    public static function getRecentMovementsForWarehouse(int $warehouseId, int $limit = 10)
    {
        return self::with(['product', 'user'])
            ->where('warehouse_id', $warehouseId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
