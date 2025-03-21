<?php

namespace App\Http\Controllers;

use App\Models\ReturnAnalytics;
use App\Models\ReturnRequest;
use App\Models\ReturnSurvey;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ReturnAnalyticsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'can:view-analytics']);
    }

    /**
     * Display the main analytics dashboard
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        // Get overall return statistics
        $totalReturns = ReturnRequest::whereBetween('created_at', [$startDate, $endDate])->count();
        $approvedReturns = ReturnRequest::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', ReturnRequest::STATUS_REJECTED)
            ->where('status', '!=', ReturnRequest::STATUS_CANCELLED)
            ->count();
        $rejectedReturns = ReturnRequest::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', ReturnRequest::STATUS_REJECTED)
            ->count();
        $cancelledReturns = ReturnRequest::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', ReturnRequest::STATUS_CANCELLED)
            ->count();
        
        // Calculate approval rate
        $approvalRate = $totalReturns > 0 ? ($approvedReturns / $totalReturns) * 100 : 0;
        
        // Get monthly return trends
        $monthlyTrends = DB::table('return_requests')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('COUNT(*) as count'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Get top return categories
        $topCategories = ReturnAnalytics::getReturnRateByCategory($startDate, $endDate);
        
        // Get return reasons breakdown
        $returnReasons = ReturnAnalytics::getReturnReasonAnalysis($startDate, $endDate);
        
        // Get financial impact
        $financialImpact = ReturnAnalytics::getFinancialImpactReport($startDate, $endDate);
        
        // Get customer satisfaction data
        $satisfactionData = ReturnSurvey::getAverageRatings($startDate, $endDate);
        
        return view('analytics.index', compact(
            'startDate',
            'endDate',
            'totalReturns',
            'approvedReturns',
            'rejectedReturns',
            'cancelledReturns',
            'approvalRate',
            'monthlyTrends',
            'topCategories',
            'returnReasons',
            'financialImpact',
            'satisfactionData'
        ));
    }
    
    /**
     * Display category-based return analytics
     */
    public function categories(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        // Get return rates by category
        $categoryData = ReturnAnalytics::getReturnRateByCategory($startDate, $endDate);
        
        // Get total sales by category for comparison
        $categorySales = DB::table('products')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('products.category', DB::raw('COUNT(order_items.id) as sales_count'))
            ->groupBy('products.category')
            ->get()
            ->keyBy('category');
        
        // Calculate return rates
        foreach ($categoryData as &$category) {
            $salesCount = $categorySales[$category->category]->sales_count ?? 0;
            $category->return_rate = $salesCount > 0 ? ($category->return_count / $salesCount) * 100 : 0;
        }
        
        // Get top returned products by category
        $topReturnedProducts = DB::table('products')
            ->join('return_requests', 'products.id', '=', 'return_requests.product_id')
            ->whereBetween('return_requests.created_at', [$startDate, $endDate])
            ->select('products.category', 'products.name', 'products.sku', DB::raw('COUNT(return_requests.id) as return_count'))
            ->groupBy('products.category', 'products.name', 'products.sku')
            ->orderBy('return_count', 'desc')
            ->limit(20)
            ->get()
            ->groupBy('category');
        
        return view('analytics.categories', compact(
            'startDate',
            'endDate',
            'categoryData',
            'topReturnedProducts'
        ));
    }
    
    /**
     * Display return reason analytics
     */
    public function reasons(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        // Get return reasons analysis
        $reasonsData = ReturnAnalytics::getReturnReasonAnalysis($startDate, $endDate);
        
        // Get detailed reasons by category
        $reasonsByCategory = DB::table('return_requests')
            ->join('products', 'return_requests.product_id', '=', 'products.id')
            ->whereBetween('return_requests.created_at', [$startDate, $endDate])
            ->select('products.category', 'return_requests.return_category', DB::raw('COUNT(return_requests.id) as count'))
            ->groupBy('products.category', 'return_requests.return_category')
            ->orderBy('count', 'desc')
            ->get()
            ->groupBy('category');
        
        // Get common return descriptions (text analysis)
        $commonPhrases = DB::table('return_requests')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('return_reason')
            ->select('return_reason')
            ->limit(1000)
            ->get()
            ->pluck('return_reason')
            ->implode(' ');
        
        // In a real implementation, we would use NLP to extract common phrases
        // For now, we'll just provide the raw data for display
        
        return view('analytics.reasons', compact(
            'startDate',
            'endDate',
            'reasonsData',
            'reasonsByCategory',
            'commonPhrases'
        ));
    }
    
    /**
     * Display financial impact analytics
     */
    public function financial(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        // Get financial impact report
        $financialData = ReturnAnalytics::getFinancialImpactReport($startDate, $endDate);
        
        // Get monthly financial trends
        $monthlyFinancialTrends = DB::table('return_refunds')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', ReturnRefund::STATUS_COMPLETED)
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(amount) as refund_amount'),
                DB::raw('SUM(restocking_fee_applied) as restocking_fees'),
                DB::raw('COUNT(*) as refund_count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Get refund method breakdown
        $refundMethods = DB::table('return_refunds')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', ReturnRefund::STATUS_COMPLETED)
            ->select('refund_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total_amount'))
            ->groupBy('refund_method')
            ->get();
        
        return view('analytics.financial', compact(
            'startDate',
            'endDate',
            'financialData',
            'monthlyFinancialTrends',
            'refundMethods'
        ));
    }
    
    /**
     * Display potential fraud detection analytics
     */
    public function fraud(Request $request)
    {
        // Get potential fraud patterns
        $suspiciousUsers = ReturnAnalytics::detectPotentialFraud();
        
        // Get suspicious return patterns
        $suspiciousPatterns = DB::table('users')
            ->join('return_requests', 'users.id', '=', 'return_requests.user_id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(return_requests.id) as return_count'),
                DB::raw('COUNT(DISTINCT return_requests.product_id) as unique_products'),
                DB::raw('MIN(return_requests.created_at) as first_return'),
                DB::raw('MAX(return_requests.created_at) as last_return')
            )
            ->where('return_requests.created_at', '>=', now()->subMonths(6))
            ->groupBy('users.id', 'users.name', 'users.email')
            ->having(DB::raw('COUNT(return_requests.id)'), '>=', 3)
            ->having(DB::raw('DATEDIFF(MAX(return_requests.created_at), MIN(return_requests.created_at))'), '<=', 30)
            ->orderBy('return_count', 'desc')
            ->get();
        
        // Get products with unusually high return rates
        $highReturnProducts = DB::table('products')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('return_requests', function ($join) {
                $join->on('products.id', '=', 'return_requests.product_id')
                    ->on('order_items.order_id', '=', 'return_requests.order_id');
            })
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'products.category',
                DB::raw('COUNT(DISTINCT order_items.id) as total_sold'),
                DB::raw('COUNT(DISTINCT return_requests.id) as total_returned'),
                DB::raw('(COUNT(DISTINCT return_requests.id) / COUNT(DISTINCT order_items.id)) * 100 as return_rate')
            )
            ->where('order_items.created_at', '>=', now()->subMonths(3))
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.category')
            ->having('total_sold', '>=', 10)
            ->having('return_rate', '>=', 30)
            ->orderBy('return_rate', 'desc')
            ->get();
        
        return view('analytics.fraud', compact(
            'suspiciousUsers',
            'suspiciousPatterns',
            'highReturnProducts'
        ));
    }
    
    /**
     * Display customer satisfaction analytics
     */
    public function satisfaction(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        // Get customer satisfaction data
        $satisfactionData = ReturnSurvey::getAverageRatings($startDate, $endDate);
        
        // Get monthly satisfaction trends
        $monthlySatisfactionTrends = DB::table('return_surveys')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->whereNotNull('completed_at')
            ->select(
                DB::raw('DATE_FORMAT(completed_at, "%Y-%m") as month'),
                DB::raw('AVG(overall_satisfaction) as avg_satisfaction'),
                DB::raw('AVG(process_rating) as avg_process'),
                DB::raw('AVG(support_rating) as avg_support'),
                DB::raw('AVG(timeliness_rating) as avg_timeliness'),
                DB::raw('SUM(CASE WHEN would_recommend = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100 as recommendation_rate'),
                DB::raw('COUNT(*) as survey_count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Get comments and suggestions
        $recentComments = DB::table('return_surveys')
            ->join('users', 'return_surveys.user_id', '=', 'users.id')
            ->whereBetween('return_surveys.completed_at', [$startDate, $endDate])
            ->whereNotNull('return_surveys.completed_at')
            ->whereNotNull('return_surveys.comments')
            ->select(
                'return_surveys.id',
                'users.name',
                'return_surveys.overall_satisfaction',
                'return_surveys.comments',
                'return_surveys.completed_at'
            )
            ->orderBy('return_surveys.completed_at', 'desc')
            ->limit(20)
            ->get();
        
        $recentSuggestions = DB::table('return_surveys')
            ->join('users', 'return_surveys.user_id', '=', 'users.id')
            ->whereBetween('return_surveys.completed_at', [$startDate, $endDate])
            ->whereNotNull('return_surveys.completed_at')
            ->whereNotNull('return_surveys.suggestions')
            ->select(
                'return_surveys.id',
                'users.name',
                'return_surveys.overall_satisfaction',
                'return_surveys.suggestions',
                'return_surveys.completed_at'
            )
            ->orderBy('return_surveys.completed_at', 'desc')
            ->limit(20)
            ->get();
        
        return view('analytics.satisfaction', compact(
            'startDate',
            'endDate',
            'satisfactionData',
            'monthlySatisfactionTrends',
            'recentComments',
            'recentSuggestions'
        ));
    }
    
    /**
     * Export analytics data
     */
    public function export(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $reportType = $request->input('report_type', 'returns');
        
        // Generate CSV data based on report type
        switch ($reportType) {
            case 'returns':
                $data = $this->generateReturnsExport($startDate, $endDate);
                $filename = 'returns_report_' . now()->format('Y-m-d') . '.csv';
                break;
                
            case 'categories':
                $data = $this->generateCategoriesExport($startDate, $endDate);
                $filename = 'return_categories_report_' . now()->format('Y-m-d') . '.csv';
                break;
                
            case 'financial':
                $data = $this->generateFinancialExport($startDate, $endDate);
                $filename = 'return_financial_report_' . now()->format('Y-m-d') . '.csv';
                break;
                
            case 'satisfaction':
                $data = $this->generateSatisfactionExport($startDate, $endDate);
                $filename = 'customer_satisfaction_report_' . now()->format('Y-m-d') . '.csv';
                break;
                
            default:
                return back()->with('error', 'Invalid report type specified.');
        }
        
        // Create a report record
        ReturnAnalytics::saveReport(
            ReturnAnalytics::TYPE_CUSTOM,
            [
                'report_type' => $reportType,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'generated_at' => now()->format('Y-m-d H:i:s'),
                'generated_by' => Auth::id(),
            ],
            Auth::id()
        );
        
        // Generate and return CSV file
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        return Response::make($data, 200, $headers);
    }
    
    /**
     * Generate returns export data
     */
    private function generateReturnsExport(string $startDate, string $endDate): string
    {
        $returns = DB::table('return_requests')
            ->join('users', 'return_requests.user_id', '=', 'users.id')
            ->join('products', 'return_requests.product_id', '=', 'products.id')
            ->join('orders', 'return_requests.order_id', '=', 'orders.id')
            ->leftJoin('return_refunds', 'return_requests.id', '=', 'return_refunds.return_request_id')
            ->whereBetween('return_requests.created_at', [$startDate, $endDate])
            ->select(
                'return_requests.id',
                'return_requests.rma_number',
                'users.name as customer_name',
                'users.email as customer_email',
                'products.name as product_name',
                'products.sku',
                'orders.order_number',
                'return_requests.return_category',
                'return_requests.status',
                'return_requests.created_at',
                'return_refunds.amount as refund_amount',
                'return_refunds.status as refund_status'
            )
            ->get();
        
        $csv = "ID,RMA Number,Customer Name,Customer Email,Product,SKU,Order Number,Return Category,Status,Created Date,Refund Amount,Refund Status\n";
        
        foreach ($returns as $return) {
            $csv .= implode(',', [
                $return->id,
                $return->rma_number,
                '"' . str_replace('"', '""', $return->customer_name) . '"',
                $return->customer_email,
                '"' . str_replace('"', '""', $return->product_name) . '"',
                $return->sku,
                $return->order_number,
                $return->return_category,
                $return->status,
                $return->created_at,
                $return->refund_amount ?? 'N/A',
                $return->refund_status ?? 'N/A'
            ]) . "\n";
        }
        
        return $csv;
    }
    
    /**
     * Generate categories export data
     */
    private function generateCategoriesExport(string $startDate, string $endDate): string
    {
        $categories = DB::table('products')
            ->join('return_requests', 'products.id', '=', 'return_requests.product_id')
            ->whereBetween('return_requests.created_at', [$startDate, $endDate])
            ->select('products.category', DB::raw('COUNT(return_requests.id) as return_count'))
            ->groupBy('products.category')
            ->get();
        
        $csv = "Category,Return Count\n";
        
        foreach ($categories as $category) {
            $csv .= implode(',', [
                '"' . str_replace('"', '""', $category->category) . '"',
                $category->return_count
            ]) . "\n";
        }
        
        return $csv;
    }
    
    /**
     * Generate financial export data
     */
    private function generateFinancialExport(string $startDate, string $endDate): string
    {
        $refunds = DB::table('return_refunds')
            ->join('return_requests', 'return_refunds.return_request_id', '=', 'return_requests.id')
            ->join('users', 'return_requests.user_id', '=', 'users.id')
            ->whereBetween('return_refunds.created_at', [$startDate, $endDate])
            ->where('return_refunds.status', ReturnRefund::STATUS_COMPLETED)
            ->select(
                'return_refunds.id',
                'return_requests.rma_number',
                'users.name as customer_name',
                'return_refunds.amount',
                'return_refunds.restocking_fee_applied',
                'return_refunds.refund_method',
                'return_refunds.transaction_id',
                'return_refunds.refund_date'
            )
            ->get();
        
        $csv = "ID,RMA Number,Customer,Refund Amount,Restocking Fee,Net Amount,Refund Method,Transaction ID,Refund Date\n";
        
        foreach ($refunds as $refund) {
            $netAmount = $refund->amount - $refund->restocking_fee_applied;
            
            $csv .= implode(',', [
                $refund->id,
                $refund->rma_number,
                '"' . str_replace('"', '""', $refund->customer_name) . '"',
                $refund->amount,
                $refund->restocking_fee_applied,
                $netAmount,
                $refund->refund_method,
                $refund->transaction_id,
                $refund->refund_date
            ]) . "\n";
        }
        
        return $csv;
    }
    
    /**
     * Generate satisfaction export data
     */
    private function generateSatisfactionExport(string $startDate, string $endDate): string
    {
        $surveys = DB::table('return_surveys')
            ->join('return_requests', 'return_surveys.return_request_id', '=', 'return_requests.id')
            ->join('users', 'return_surveys.user_id', '=', 'users.id')
            ->whereBetween('return_surveys.completed_at', [$startDate, $endDate])
            ->whereNotNull('return_surveys.completed_at')
            ->select(
                'return_surveys.id',
                'return_requests.rma_number',
                'users.name as customer_name',
                'return_surveys.overall_satisfaction',
                'return_surveys.process_rating',
                'return_surveys.support_rating',
                'return_surveys.timeliness_rating',
                'return_surveys.would_recommend',
                'return_surveys.completed_at'
            )
            ->get();
        
        $csv = "ID,RMA Number,Customer,Overall Satisfaction,Process Rating,Support Rating,Timeliness Rating,Would Recommend,Completed Date\n";
        
        foreach ($surveys as $survey) {
            $wouldRecommend = $survey->would_recommend ? 'Yes' : 'No';
            
            $csv .= implode(',', [
                $survey->id,
                $survey->rma_number,
                '"' . str_replace('"', '""', $survey->customer_name) . '"',
                $survey->overall_satisfaction,
                $survey->process_rating,
                $survey->support_rating,
                $survey->timeliness_rating,
                $wouldRecommend,
                $survey->completed_at
            ]) . "\n";
        }
        
        return $csv;
    }
}
