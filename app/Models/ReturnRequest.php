<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'rma_number',
        'return_reason',
        'return_category',
        'description',
        'evidence_images',
        'status',
        'refund_method',
        'refund_amount',
        'restocking_fee',
        'pickup_location',
        'tracking_number',
        'quality_check_result',
        'warehouse_notes',
        'admin_notes',
    ];

    protected $casts = [
        'evidence_images' => 'array',
        'refund_amount' => 'float',
        'restocking_fee' => 'float',
    ];

    /**
     * Return status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_RECEIVED = 'received';
    const STATUS_INSPECTED = 'inspected';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_REFUND_PROCESSED = 'refund_processed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Return category constants
     */
    const CATEGORY_DEFECTIVE = 'defective';
    const CATEGORY_WRONG_ITEM = 'wrong_item';
    const CATEGORY_NOT_AS_DESCRIBED = 'not_as_described';
    const CATEGORY_CHANGE_OF_MIND = 'change_of_mind';
    const CATEGORY_DAMAGED_IN_TRANSIT = 'damaged_in_transit';
    const CATEGORY_OTHER = 'other';

    /**
     * Refund method constants
     */
    const REFUND_METHOD_ORIGINAL = 'original_payment';
    const REFUND_METHOD_STORE_CREDIT = 'store_credit';
    const REFUND_METHOD_REPLACEMENT = 'replacement';

    /**
     * Get the user that owns the return request
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product being returned
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the order associated with this return
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
    /**
     * Get the warehouse processing this return
     */
    public function processingWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'processing_warehouse_id');
    }
    
    /**
     * Get the shipment details for this return
     */
    public function returnShipment()
    {
        return $this->hasOne(ReturnShipment::class);
    }
    
    /**
     * Get the refund details for this return
     */
    public function returnRefund()
    {
        return $this->hasOne(ReturnRefund::class);
    }

    /**
     * Generate a unique RMA number
     */
    public static function generateRMANumber(): string
    {
        return 'RMA-' . strtoupper(substr(uniqid(), -6)) . '-' . date('Ymd');
    }
    
    /**
     * Get all available return categories
     *
     * @return array
     */
    public static function getReturnCategories(): array
    {
        return [
            self::CATEGORY_DEFECTIVE,
            self::CATEGORY_WRONG_ITEM,
            self::CATEGORY_NOT_AS_DESCRIBED,
            self::CATEGORY_CHANGE_OF_MIND,
            self::CATEGORY_DAMAGED_IN_TRANSIT,
            self::CATEGORY_OTHER
        ];
    }
}
