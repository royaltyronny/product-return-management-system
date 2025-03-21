<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_request_id',
        'user_id',
        'action',
        'description',
        'previous_status',
        'new_status',
        'ip_address',
        'user_agent',
    ];

    /**
     * Action type constants
     */
    const ACTION_CREATED = 'created';
    const ACTION_UPDATED = 'updated';
    const ACTION_STATUS_UPDATED = 'status_updated';
    const ACTION_APPROVED = 'approved';
    const ACTION_REJECTED = 'rejected';
    const ACTION_SHIPPED = 'shipped';
    const ACTION_RECEIVED = 'received';
    const ACTION_INSPECTED = 'inspected';
    const ACTION_REFUNDED = 'refunded';
    const ACTION_REFUND_PROCESSED = 'refund_processed';
    const ACTION_COMPLETED = 'completed';
    const ACTION_CANCELLED = 'cancelled';
    const ACTION_DELETED = 'deleted';

    /**
     * Get the return request associated with the log entry
     */
    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log a return request action
     */
    public static function logAction(
        ReturnRequest $returnRequest,
        string $action,
        ?string $description = null,
        ?string $previousStatus = null,
        ?int $userId = null
    ): self {
        return self::create([
            'return_request_id' => $returnRequest->id,
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'description' => $description,
            'previous_status' => $previousStatus,
            'new_status' => $returnRequest->status,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get audit trail for a specific return request
     */
    public static function getAuditTrail(int $returnRequestId): array
    {
        return self::where('return_request_id', $returnRequestId)
            ->with('user:id,name,email,role')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get recent actions by a specific user
     */
    public static function getUserActions(int $userId, int $limit = 50): array
    {
        return self::where('user_id', $userId)
            ->with('returnRequest:id,rma_number,status')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
