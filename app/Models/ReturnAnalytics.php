<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReturnAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_date',
        'report_type',
        'data',
        'generated_by',
    ];

    protected $casts = [
        'report_date' => 'datetime',
        'data' => 'array',
    ];

    /**
     * Report type constants
     */
    const TYPE_DAILY = 'daily';
    const TYPE_WEEKLY = 'weekly';
    const TYPE_MONTHLY = 'monthly';
    const TYPE_QUARTERLY = 'quarterly';
    const TYPE_ANNUAL = 'annual';
    const TYPE_CUSTOM = 'custom';

    /**
     * Generate return rate by product category
     */
    public static function getReturnRateByCategory(string $startDate = null, string $endDate = null): array
    {
        $startDate = $startDate ?? now()->subMonths(3)->format('Y-m-d');
        $endDate = $endDate ?? now()->format('Y-m-d');

        return DB::table('products')
            ->join('return_requests', 'products.id', '=', 'return_requests.product_id')
            ->whereBetween('return_requests.created_at', [$startDate, $endDate])
            ->select('products.category', DB::raw('COUNT(return_requests.id) as return_count'))
            ->groupBy('products.category')
            ->get()
            ->toArray();
    }

    /**
     * Generate return reasons analysis
     */
    public static function getReturnReasonAnalysis(string $startDate = null, string $endDate = null): array
    {
        $startDate = $startDate ?? now()->subMonths(3)->format('Y-m-d');
        $endDate = $endDate ?? now()->format('Y-m-d');

        return DB::table('return_requests')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('return_category', DB::raw('COUNT(id) as count'))
            ->groupBy('return_category')
            ->get()
            ->toArray();
    }

    /**
     * Generate financial impact report
     */
    public static function getFinancialImpactReport(string $startDate = null, string $endDate = null): array
    {
        $startDate = $startDate ?? now()->subMonths(3)->format('Y-m-d');
        $endDate = $endDate ?? now()->format('Y-m-d');

        $refundTotal = DB::table('return_refunds')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', ReturnRefund::STATUS_COMPLETED)
            ->sum('amount');

        $restockingFees = DB::table('return_refunds')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', ReturnRefund::STATUS_COMPLETED)
            ->sum('restocking_fee_applied');

        $returnCount = DB::table('return_requests')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $averageRefundAmount = $returnCount > 0 ? $refundTotal / $returnCount : 0;

        return [
            'total_refund_amount' => $refundTotal,
            'total_restocking_fees' => $restockingFees,
            'net_financial_impact' => $refundTotal - $restockingFees,
            'return_count' => $returnCount,
            'average_refund_amount' => $averageRefundAmount,
        ];
    }

    /**
     * Detect potential fraud patterns
     */
    public static function detectPotentialFraud(): array
    {
        $thresholdPeriod = now()->subMonths(3);
        $returnThreshold = 5; // Number of returns that triggers a flag

        $suspiciousUsers = DB::table('users')
            ->join('return_requests', 'users.id', '=', 'return_requests.user_id')
            ->where('return_requests.created_at', '>=', $thresholdPeriod)
            ->groupBy('users.id', 'users.name', 'users.email')
            ->having(DB::raw('COUNT(return_requests.id)'), '>=', $returnThreshold)
            ->select('users.id', 'users.name', 'users.email', DB::raw('COUNT(return_requests.id) as return_count'))
            ->get();

        return $suspiciousUsers->toArray();
    }

    /**
     * Save a generated report
     */
    public static function saveReport(string $reportType, array $data, int $generatedBy): self
    {
        return self::create([
            'report_date' => now(),
            'report_type' => $reportType,
            'data' => $data,
            'generated_by' => $generatedBy,
        ]);
    }
}
