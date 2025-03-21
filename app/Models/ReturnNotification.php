<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'return_request_id',
        'type',
        'subject',
        'message',
        'channel',
        'status',
        'sent_at',
        'read_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    /**
     * Notification type constants
     */
    const TYPE_RETURN_CREATED = 'return_created';
    const TYPE_RETURN_APPROVED = 'return_approved';
    const TYPE_RETURN_REJECTED = 'return_rejected';
    const TYPE_SHIPPING_LABEL = 'shipping_label';
    const TYPE_RETURN_RECEIVED = 'return_received';
    const TYPE_RETURN_INSPECTED = 'return_inspected';
    const TYPE_REFUND_PROCESSED = 'refund_processed';
    const TYPE_RETURN_COMPLETED = 'return_completed';
    const TYPE_SURVEY = 'satisfaction_survey';

    /**
     * Notification channel constants
     */
    const CHANNEL_EMAIL = 'email';
    const CHANNEL_SMS = 'sms';
    const CHANNEL_PUSH = 'push';
    const CHANNEL_IN_APP = 'in_app';

    /**
     * Notification status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_FAILED = 'failed';
    const STATUS_READ = 'read';

    /**
     * Get the user that owns the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the return request associated with the notification
     */
    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        $this->read_at = now();
        $this->status = self::STATUS_READ;
        $this->save();
    }

    /**
     * Create a return status notification
     */
    public static function createStatusNotification(ReturnRequest $returnRequest, string $type): self
    {
        $templates = [
            self::TYPE_RETURN_CREATED => [
                'subject' => 'Return Request Received - RMA #' . $returnRequest->rma_number,
                'message' => 'Your return request has been received and is pending review. We will notify you once it has been processed.',
            ],
            self::TYPE_RETURN_APPROVED => [
                'subject' => 'Return Request Approved - RMA #' . $returnRequest->rma_number,
                'message' => 'Your return request has been approved. Please follow the instructions to ship your item back to us.',
            ],
            self::TYPE_RETURN_REJECTED => [
                'subject' => 'Return Request Not Approved - RMA #' . $returnRequest->rma_number,
                'message' => 'We regret to inform you that your return request could not be approved. Please contact customer support for more information.',
            ],
            self::TYPE_SHIPPING_LABEL => [
                'subject' => 'Return Shipping Label - RMA #' . $returnRequest->rma_number,
                'message' => 'Your return shipping label is ready. Please print it and attach it to your package.',
            ],
            self::TYPE_RETURN_RECEIVED => [
                'subject' => 'Return Received - RMA #' . $returnRequest->rma_number,
                'message' => 'We have received your returned item. It will now undergo inspection.',
            ],
            self::TYPE_RETURN_INSPECTED => [
                'subject' => 'Return Inspection Complete - RMA #' . $returnRequest->rma_number,
                'message' => 'We have completed the inspection of your returned item. Your refund will be processed shortly.',
            ],
            self::TYPE_REFUND_PROCESSED => [
                'subject' => 'Refund Processed - RMA #' . $returnRequest->rma_number,
                'message' => 'Your refund has been processed. Please allow 3-5 business days for the funds to appear in your account.',
            ],
            self::TYPE_RETURN_COMPLETED => [
                'subject' => 'Return Completed - RMA #' . $returnRequest->rma_number,
                'message' => 'Your return process has been completed. Thank you for your patience.',
            ],
            self::TYPE_SURVEY => [
                'subject' => 'How Was Your Return Experience? - RMA #' . $returnRequest->rma_number,
                'message' => 'We value your feedback. Please take a moment to complete a short survey about your return experience.',
            ],
        ];

        $template = $templates[$type] ?? [
            'subject' => 'Return Update - RMA #' . $returnRequest->rma_number,
            'message' => 'There has been an update to your return request.',
        ];

        return self::create([
            'user_id' => $returnRequest->user_id,
            'return_request_id' => $returnRequest->id,
            'type' => $type,
            'subject' => $template['subject'],
            'message' => $template['message'],
            'channel' => self::CHANNEL_EMAIL, // Default to email
            'status' => self::STATUS_PENDING,
        ]);
    }

    /**
     * Send the notification
     */
    public function send(): bool
    {
        // This would integrate with email/SMS/push notification services in a real implementation
        // For now, we'll just simulate sending
        
        $this->status = self::STATUS_SENT;
        $this->sent_at = now();
        $this->save();
        
        // In a real implementation, we would check if the send was successful
        return true;
    }
}
