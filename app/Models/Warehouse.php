<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'contact_name',
        'contact_email',
        'contact_phone',
        'is_active',
        'can_process_returns',
        'can_process_refurbishment',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'can_process_returns' => 'boolean',
        'can_process_refurbishment' => 'boolean',
    ];

    /**
     * Get the user who created this warehouse
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this warehouse
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the inventory items in this warehouse
     */
    public function inventory()
    {
        return $this->hasMany(WarehouseInventory::class);
    }

    /**
     * Get the inventory movements for this warehouse
     */
    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Get the return requests processed at this warehouse
     */
    public function returnRequests(): HasMany
    {
        return $this->hasMany(ReturnRequest::class, 'warehouse_id');
    }
    
    /**
     * Get pending return requests for this warehouse
     */
    public function pendingReturns(): HasMany
    {
        return $this->returnRequests()->whereIn('status', [
            ReturnRequest::STATUS_SHIPPED,
            ReturnRequest::STATUS_RECEIVED
        ]);
    }
    
    /**
     * Get processed return requests for this warehouse
     */
    public function processedReturns(): HasMany
    {
        return $this->returnRequests()->whereIn('status', [
            ReturnRequest::STATUS_INSPECTED,
            ReturnRequest::STATUS_APPROVED_FOR_REFUND,
            ReturnRequest::STATUS_REJECTED_AFTER_INSPECTION,
            ReturnRequest::STATUS_REFUNDED,
            ReturnRequest::STATUS_COMPLETED
        ]);
    }
}
