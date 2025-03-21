<?php

namespace App\Http\Controllers;

use App\Models\ReturnPolicy;
use App\Models\ReturnAuditLog;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnPolicyController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'can:manage-policies']);
    }

    /**
     * Display a listing of return policies.
     */
    public function index()
    {
        $policies = ReturnPolicy::with('products', 'categories')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('policies.index', compact('policies'));
    }

    /**
     * Show the form for creating a new return policy.
     */
    public function create()
    {
        $products = Product::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        
        return view('policies.create', compact('products', 'categories'));
    }

    /**
     * Store a newly created return policy in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'return_period_days' => 'required|integer|min:0',
            'restocking_fee_percentage' => 'required|numeric|min:0|max:100',
            'requires_receipt' => 'boolean',
            'requires_original_packaging' => 'boolean',
            'allows_partial_returns' => 'boolean',
            'applies_to_all_products' => 'boolean',
            'product_ids' => 'required_if:applies_to_all_products,0|array',
            'category_ids' => 'required_if:applies_to_all_products,0|array',
            'active' => 'boolean',
            'effective_date' => 'required|date',
            'expiration_date' => 'nullable|date|after:effective_date',
        ]);
        
        // Start a transaction
        DB::beginTransaction();
        
        try {
            // Create the return policy
            $policy = ReturnPolicy::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'return_period_days' => $validated['return_period_days'],
                'restocking_fee_percentage' => $validated['restocking_fee_percentage'],
                'requires_receipt' => $request->has('requires_receipt'),
                'requires_original_packaging' => $request->has('requires_original_packaging'),
                'allows_partial_returns' => $request->has('allows_partial_returns'),
                'applies_to_all_products' => $request->has('applies_to_all_products'),
                'active' => $request->has('active'),
                'effective_date' => $validated['effective_date'],
                'expiration_date' => $validated['expiration_date'] ?? null,
                'created_by' => Auth::id(),
            ]);
            
            // If the policy doesn't apply to all products, attach the selected products and categories
            if (!$request->has('applies_to_all_products')) {
                if ($request->has('product_ids')) {
                    $policy->products()->attach($request->input('product_ids'));
                }
                
                if ($request->has('category_ids')) {
                    $policy->categories()->attach($request->input('category_ids'));
                }
            }
            
            // Log the policy creation
            ReturnAuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'policy_created',
                'entity_type' => 'return_policy',
                'entity_id' => $policy->id,
                'details' => json_encode([
                    'policy_name' => $policy->name,
                    'policy_id' => $policy->id,
                    'effective_date' => $policy->effective_date,
                    'applies_to_all' => $policy->applies_to_all_products,
                ]),
                'ip_address' => $request->ip(),
            ]);
            
            DB::commit();
            
            return redirect()->route('policies.index')
                ->with('success', 'Return policy created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()->with('error', 'Error creating return policy: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified return policy.
     */
    public function show(ReturnPolicy $returnPolicy)
    {
        $returnPolicy->load('products', 'categories', 'createdBy');
        
        // Get policy usage statistics
        $usageStats = [
            'total_returns' => DB::table('return_requests')
                ->where('policy_id', $returnPolicy->id)
                ->count(),
            'approved_returns' => DB::table('return_requests')
                ->where('policy_id', $returnPolicy->id)
                ->where('status', 'approved')
                ->count(),
            'rejected_returns' => DB::table('return_requests')
                ->where('policy_id', $returnPolicy->id)
                ->where('status', 'rejected')
                ->count(),
        ];
        
        // Get recent returns using this policy
        $recentReturns = DB::table('return_requests')
            ->join('users', 'return_requests.user_id', '=', 'users.id')
            ->join('products', 'return_requests.product_id', '=', 'products.id')
            ->where('return_requests.policy_id', $returnPolicy->id)
            ->select(
                'return_requests.id',
                'return_requests.rma_number',
                'users.name as customer_name',
                'products.name as product_name',
                'return_requests.status',
                'return_requests.created_at'
            )
            ->orderBy('return_requests.created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('policies.show', compact('returnPolicy', 'usageStats', 'recentReturns'));
    }

    /**
     * Show the form for editing the specified return policy.
     */
    public function edit(ReturnPolicy $returnPolicy)
    {
        $returnPolicy->load('products', 'categories');
        
        $products = Product::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        
        $selectedProducts = $returnPolicy->products->pluck('id')->toArray();
        $selectedCategories = $returnPolicy->categories->pluck('id')->toArray();
        
        return view('policies.edit', compact(
            'returnPolicy',
            'products',
            'categories',
            'selectedProducts',
            'selectedCategories'
        ));
    }

    /**
     * Update the specified return policy in storage.
     */
    public function update(Request $request, ReturnPolicy $returnPolicy)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'return_period_days' => 'required|integer|min:0',
            'restocking_fee_percentage' => 'required|numeric|min:0|max:100',
            'requires_receipt' => 'boolean',
            'requires_original_packaging' => 'boolean',
            'allows_partial_returns' => 'boolean',
            'applies_to_all_products' => 'boolean',
            'product_ids' => 'required_if:applies_to_all_products,0|array',
            'category_ids' => 'required_if:applies_to_all_products,0|array',
            'active' => 'boolean',
            'effective_date' => 'required|date',
            'expiration_date' => 'nullable|date|after:effective_date',
        ]);
        
        // Start a transaction
        DB::beginTransaction();
        
        try {
            // Store the old policy data for audit log
            $oldData = [
                'name' => $returnPolicy->name,
                'return_period_days' => $returnPolicy->return_period_days,
                'restocking_fee_percentage' => $returnPolicy->restocking_fee_percentage,
                'active' => $returnPolicy->active,
                'applies_to_all_products' => $returnPolicy->applies_to_all_products,
            ];
            
            // Update the return policy
            $returnPolicy->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'return_period_days' => $validated['return_period_days'],
                'restocking_fee_percentage' => $validated['restocking_fee_percentage'],
                'requires_receipt' => $request->has('requires_receipt'),
                'requires_original_packaging' => $request->has('requires_original_packaging'),
                'allows_partial_returns' => $request->has('allows_partial_returns'),
                'applies_to_all_products' => $request->has('applies_to_all_products'),
                'active' => $request->has('active'),
                'effective_date' => $validated['effective_date'],
                'expiration_date' => $validated['expiration_date'] ?? null,
                'updated_by' => Auth::id(),
            ]);
            
            // Update product and category associations
            if ($request->has('applies_to_all_products')) {
                // If applies to all products, detach all specific associations
                $returnPolicy->products()->detach();
                $returnPolicy->categories()->detach();
            } else {
                // Otherwise, sync the selected products and categories
                if ($request->has('product_ids')) {
                    $returnPolicy->products()->sync($request->input('product_ids'));
                } else {
                    $returnPolicy->products()->detach();
                }
                
                if ($request->has('category_ids')) {
                    $returnPolicy->categories()->sync($request->input('category_ids'));
                } else {
                    $returnPolicy->categories()->detach();
                }
            }
            
            // Log the policy update
            ReturnAuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'policy_updated',
                'entity_type' => 'return_policy',
                'entity_id' => $returnPolicy->id,
                'details' => json_encode([
                    'policy_name' => $returnPolicy->name,
                    'policy_id' => $returnPolicy->id,
                    'changes' => [
                        'old' => $oldData,
                        'new' => [
                            'name' => $returnPolicy->name,
                            'return_period_days' => $returnPolicy->return_period_days,
                            'restocking_fee_percentage' => $returnPolicy->restocking_fee_percentage,
                            'active' => $returnPolicy->active,
                            'applies_to_all_products' => $returnPolicy->applies_to_all_products,
                        ],
                    ],
                ]),
                'ip_address' => $request->ip(),
            ]);
            
            DB::commit();
            
            return redirect()->route('policies.index')
                ->with('success', 'Return policy updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()->with('error', 'Error updating return policy: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified return policy from storage.
     */
    public function destroy(Request $request, ReturnPolicy $returnPolicy)
    {
        // Check if the policy is in use by any return requests
        $inUse = DB::table('return_requests')
            ->where('policy_id', $returnPolicy->id)
            ->exists();
        
        if ($inUse) {
            return back()->with('error', 'This return policy cannot be deleted because it is in use by existing return requests.');
        }
        
        // Start a transaction
        DB::beginTransaction();
        
        try {
            // Detach all products and categories
            $returnPolicy->products()->detach();
            $returnPolicy->categories()->detach();
            
            // Log the policy deletion
            ReturnAuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'policy_deleted',
                'entity_type' => 'return_policy',
                'entity_id' => $returnPolicy->id,
                'details' => json_encode([
                    'policy_name' => $returnPolicy->name,
                    'policy_id' => $returnPolicy->id,
                    'deleted_at' => now()->format('Y-m-d H:i:s'),
                ]),
                'ip_address' => $request->ip(),
            ]);
            
            // Delete the policy
            $returnPolicy->delete();
            
            DB::commit();
            
            return redirect()->route('policies.index')
                ->with('success', 'Return policy deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Error deleting return policy: ' . $e->getMessage());
        }
    }
}
