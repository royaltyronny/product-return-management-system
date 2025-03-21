<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'status',
        'returned_quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'float',
        'total_price' => 'float',
        'returned_quantity' => 'integer',
    ];

    /**
     * Get the order that owns the order item
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product for this order item
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if the item is fully returned
     */
    public function isFullyReturned(): bool
    {
        return $this->returned_quantity >= $this->quantity;
    }

    /**
     * Check if the item is partially returned
     */
    public function isPartiallyReturned(): bool
    {
        return $this->returned_quantity > 0 && $this->returned_quantity < $this->quantity;
    }

    /**
     * Get the remaining quantity that can be returned
     */
    public function getReturnableQuantity(): int
    {
        return $this->quantity - $this->returned_quantity;
    }
}
