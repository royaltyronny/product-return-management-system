<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnShipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_request_id',
        'shipping_carrier',
        'tracking_number',
        'shipping_label_url',
        'status',
        'estimated_delivery_date',
        'actual_delivery_date',
        'pickup_date',
        'pickup_address',
        'destination_warehouse_id',
        'shipping_cost',
        'weight',
        'dimensions',
        'notes',
    ];

    protected $casts = [
        'pickup_address' => 'array',
        'estimated_delivery_date' => 'datetime',
        'actual_delivery_date' => 'datetime',
        'pickup_date' => 'datetime',
        'shipping_cost' => 'float',
        'weight' => 'float',
        'dimensions' => 'array',
    ];

    /**
     * Shipment status constants
     */
    const STATUS_LABEL_CREATED = 'label_created';
    const STATUS_READY_FOR_PICKUP = 'ready_for_pickup';
    const STATUS_PICKED_UP = 'picked_up';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_EXCEPTION = 'exception';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the return request that owns the shipment
     */
    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    /**
     * Get the destination warehouse
     */
    public function destinationWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
    }

    /**
     * Generate a shipping label for the return
     */
    public function generateShippingLabel(): string
    {
        // This would integrate with a shipping API in a real implementation
        // For now, we'll just generate a mock URL
        $labelUrl = 'labels/return_' . $this->return_request_id . '_' . time() . '.pdf';
        $this->shipping_label_url = $labelUrl;
        $this->save();
        
        return $labelUrl;
    }

    /**
     * Update shipment status based on tracking info
     */
    public function updateTrackingStatus(string $status, ?string $notes = null): void
    {
        $this->status = $status;
        
        if ($status === self::STATUS_PICKED_UP) {
            $this->pickup_date = now();
        } elseif ($status === self::STATUS_DELIVERED) {
            $this->actual_delivery_date = now();
        }
        
        if ($notes) {
            $this->notes = $notes;
        }
        
        $this->save();
        
        // Update the related return request status
        if ($status === self::STATUS_PICKED_UP) {
            $this->returnRequest->update(['status' => ReturnRequest::STATUS_IN_TRANSIT]);
        } elseif ($status === self::STATUS_DELIVERED) {
            $this->returnRequest->update(['status' => ReturnRequest::STATUS_RECEIVED]);
        }
    }
}
