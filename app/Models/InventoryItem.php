<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity',
        'quantity_reserved',
        'quantity_pending_return',
        'quantity_returned',
        'location_code',
        'status',
        'last_inventory_date',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'quantity_reserved' => 'integer',
        'quantity_pending_return' => 'integer',
        'quantity_returned' => 'integer',
        'last_inventory_date' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_IN_STOCK = 'in_stock';
    const STATUS_LOW_STOCK = 'low_stock';
    const STATUS_OUT_OF_STOCK = 'out_of_stock';
    const STATUS_DISCONTINUED = 'discontinued';

    /**
     * Get the product that owns the inventory item
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the warehouse that owns the inventory item
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get available quantity (total minus reserved)
     */
    public function getAvailableQuantity(): int
    {
        return $this->quantity - $this->quantity_reserved;
    }

    /**
     * Update inventory after a return
     */
    public function processReturn(int $quantity, string $condition): void
    {
        // Increase returned quantity
        $this->quantity_returned += $quantity;
        
        // If product is in good condition, add back to available inventory
        if ($condition === 'resellable') {
            $this->quantity += $quantity;
        }
        
        // Decrease pending return quantity
        $this->quantity_pending_return = max(0, $this->quantity_pending_return - $quantity);
        
        // Update status based on new quantity
        $this->updateStatus();
        
        $this->save();
    }

    /**
     * Update inventory status based on quantity
     */
    private function updateStatus(): void
    {
        $lowStockThreshold = 5; // This could be configurable per product
        
        if ($this->quantity <= 0) {
            $this->status = self::STATUS_OUT_OF_STOCK;
        } elseif ($this->quantity <= $lowStockThreshold) {
            $this->status = self::STATUS_LOW_STOCK;
        } else {
            $this->status = self::STATUS_IN_STOCK;
        }
    }
}
