<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'status',
        'payment_method',
        'payment_id',
        'shipping_address',
        'billing_address',
        'shipping_method',
        'tracking_number',
        'notes',
        'order_date',
    ];

    protected $casts = [
        'total_amount' => 'float',
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'order_date' => 'datetime',
    ];

    /**
     * Order status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_PARTIALLY_REFUNDED = 'partially_refunded';

    /**
     * Get the user that owns the order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items for this order
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the return requests for this order
     */
    public function returnRequests(): HasMany
    {
        return $this->hasMany(ReturnRequest::class);
    }

    /**
     * Check if the order is eligible for return
     */
    public function isEligibleForReturn(): bool
    {
        // Check if order is delivered and within return period
        $returnPeriodDays = config('returns.default_return_period_days', 30);
        $orderDeliveredDate = $this->updated_at; // Assuming updated_at is when order was delivered
        
        return $this->status === self::STATUS_DELIVERED && 
               now()->diffInDays($orderDeliveredDate) <= $returnPeriodDays;
    }
}
