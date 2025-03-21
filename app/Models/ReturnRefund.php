<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRefund extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_request_id',
        'amount',
        'refund_method',
        'status',
        'transaction_id',
        'payment_gateway',
        'refund_date',
        'store_credit_code',
        'restocking_fee_applied',
        'notes',
        'processed_by',
    ];

    protected $casts = [
        'amount' => 'float',
        'refund_date' => 'datetime',
        'restocking_fee_applied' => 'float',
    ];

    /**
     * Refund status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_PROCESSED = 'processed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Refund method constants
     */
    const METHOD_ORIGINAL = 'original_payment';
    const METHOD_STORE_CREDIT = 'store_credit';
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_REPLACEMENT = 'replacement';

    /**
     * Get the return request that owns the refund
     */
    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    /**
     * Get the user who processed the refund
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Process the refund
     */
    public function processRefund(): bool
    {
        // This would integrate with a payment gateway in a real implementation
        // For now, we'll just simulate a successful refund
        
        $this->status = self::STATUS_PROCESSING;
        $this->save();
        
        // Simulate payment gateway processing
        $success = true; // In real implementation, this would be the result from the payment gateway
        
        if ($success) {
            $this->status = self::STATUS_COMPLETED;
            $this->refund_date = now();
            $this->transaction_id = 'REF-' . strtoupper(substr(uniqid(), -8));
            $this->save();
            
            // Update the related return request status
            $this->returnRequest->update(['status' => ReturnRequest::STATUS_REFUNDED]);
            
            return true;
        } else {
            $this->status = self::STATUS_FAILED;
            $this->notes = 'Failed to process refund through payment gateway';
            $this->save();
            
            return false;
        }
    }

    /**
     * Generate a store credit code
     */
    public static function generateStoreCreditCode(): string
    {
        return 'SC-' . strtoupper(substr(md5(uniqid()), 0, 10));
    }
    
    /**
     * Get all available refund methods
     *
     * @return array
     */
    public static function getRefundMethods(): array
    {
        return [
            self::METHOD_ORIGINAL,
            self::METHOD_STORE_CREDIT,
            self::METHOD_BANK_TRANSFER,
            self::METHOD_REPLACEMENT
        ];
    }
}
