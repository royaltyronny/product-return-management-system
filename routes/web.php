<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\ReturnAnalyticsController;
use App\Http\Controllers\ReturnPolicyController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\ContactController;

// Disable default login routes if using custom ones
Auth::routes(['login' => false]);

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// About Page
Route::get('/about', [PageController::class, 'about'])->name('about');

// Services Page
Route::get('/services', [ServicesController::class, 'services'])->name('services');

// Contact Page
Route::get('/contact', [ContactController::class, 'contact'])->name('contact');

// Diagnostic Routes
Route::get('/test-returns', function() {
    return 'Returns test route is working!';
});

// Auth Diagnostic Route
Route::get('/auth-check', function() {
    if (auth()->check()) {
        return response()->json([
            'authenticated' => true,
            'user' => auth()->user(),
            'session_id' => session()->getId(),
        ]);
    } else {
        return response()->json([
            'authenticated' => false,
            'session_id' => session()->getId(),
        ]);
    }
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Admin Return Management Routes
    Route::prefix('returns')->name('returns.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ReturnController::class, 'index'])->name('index');
        Route::get('/{returnRequest}', [\App\Http\Controllers\Admin\ReturnController::class, 'show'])->name('show');
        Route::post('/{returnRequest}/process', [\App\Http\Controllers\Admin\ReturnController::class, 'process'])->name('process');
        Route::post('/{returnRequest}/update-status', [\App\Http\Controllers\Admin\ReturnController::class, 'updateStatus'])->name('update-status');
        Route::post('/{returnRequest}/process-refund', [\App\Http\Controllers\Admin\ReturnController::class, 'processRefund'])->name('process-refund');
        Route::get('/reports/analytics', [\App\Http\Controllers\Admin\ReturnController::class, 'reports'])->name('reports');
        Route::get('/export', [\App\Http\Controllers\Admin\ReturnController::class, 'export'])->name('export');
    });
});

// Public Return Route for Testing
Route::get('/public-returns', [App\Http\Controllers\ReturnController::class, 'index']);

// Custom Login Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Password Confirmation Routes
Route::get('password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
Route::post('password/confirm', [ConfirmPasswordController::class, 'confirm']);

// Protected Routes (require auth)
Route::middleware('auth')->group(function () {
    // Home
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

   

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/view', [ProfileController::class, 'show'])->name('profile.view');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/orders', [ProfileController::class, 'orders'])->name('profile.orders');

    // Products
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // About Page - Moved outside auth middleware

    // Return Management System Routes
    Route::prefix('returns')->name('returns.')->group(function () {
        // Customer Routes
        Route::get('/', [ReturnController::class, 'index'])->name('index');
        Route::get('/create', [ReturnController::class, 'create'])->name('create');
        Route::post('/', [ReturnController::class, 'store'])->name('store');
        Route::get('/{returnRequest}', [ReturnController::class, 'show'])->name('show');
        Route::post('/{returnRequest}/cancel', [ReturnController::class, 'cancel'])->name('cancel');
        Route::post('/{returnRequest}/survey', [ReturnController::class, 'submitSurvey'])->name('submit-survey');
        
        // Support Agent Routes
        Route::middleware(['can:process-returns'])->group(function () {
            Route::post('/{returnRequest}/process', [ReturnController::class, 'process'])->name('process');
        });
        
        // Warehouse Staff Routes
        Route::middleware(['can:manage-warehouse'])->group(function () {
            Route::post('/{returnRequest}/status', [ReturnController::class, 'updateStatus'])->name('update-status');
            Route::post('/{returnRequest}/complete', [ReturnController::class, 'complete'])->name('complete');
        });
        
        // Finance Team Routes
        Route::middleware(['can:process-refunds'])->group(function () {
            Route::post('/{returnRequest}/refund', [ReturnController::class, 'processRefund'])->name('process-refund');
        });
    });
    
    // Return Analytics Routes (Admin Only)
    Route::prefix('analytics')->name('analytics.')->middleware(['can:view-analytics'])->group(function () {
        Route::get('/returns', [ReturnAnalyticsController::class, 'index'])->name('index');
        Route::get('/returns/categories', [ReturnAnalyticsController::class, 'categories'])->name('categories');
        Route::get('/returns/reasons', [ReturnAnalyticsController::class, 'reasons'])->name('reasons');
        Route::get('/returns/financial', [ReturnAnalyticsController::class, 'financial'])->name('financial');
        Route::get('/returns/fraud', [ReturnAnalyticsController::class, 'fraud'])->name('fraud');
        Route::get('/returns/customer-satisfaction', [ReturnAnalyticsController::class, 'satisfaction'])->name('satisfaction');
        Route::get('/returns/export', [ReturnAnalyticsController::class, 'export'])->name('export');
    });
    
    // Return Policy Management Routes (Admin Only)
    Route::prefix('policies')->name('policies.')->middleware(['can:manage-policies'])->group(function () {
        Route::get('/', [ReturnPolicyController::class, 'index'])->name('index');
        Route::get('/create', [ReturnPolicyController::class, 'create'])->name('create');
        Route::post('/', [ReturnPolicyController::class, 'store'])->name('store');
        Route::get('/{returnPolicy}', [ReturnPolicyController::class, 'show'])->name('show');
        Route::get('/{returnPolicy}/edit', [ReturnPolicyController::class, 'edit'])->name('edit');
        Route::put('/{returnPolicy}', [ReturnPolicyController::class, 'update'])->name('update');
        Route::delete('/{returnPolicy}', [ReturnPolicyController::class, 'destroy'])->name('destroy');
    });
    
    // Warehouse Management Routes
    Route::prefix('warehouses')->name('warehouses.')->middleware(['can:manage-warehouses'])->group(function () {
        Route::get('/', [WarehouseController::class, 'index'])->name('index');
        Route::get('/create', [WarehouseController::class, 'create'])->name('create');
        Route::post('/', [WarehouseController::class, 'store'])->name('store');
        Route::get('/{warehouse}', [WarehouseController::class, 'show'])->name('show');
        Route::get('/{warehouse}/edit', [WarehouseController::class, 'edit'])->name('edit');
        Route::put('/{warehouse}', [WarehouseController::class, 'update'])->name('update');
        Route::delete('/{warehouse}', [WarehouseController::class, 'destroy'])->name('destroy');
        
        // Inventory Management
        Route::get('/{warehouse}/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('/{warehouse}/inventory', [InventoryController::class, 'update'])->name('inventory.update');
    });

    // Services and Contact Pages - Moved outside auth middleware
    
    
});
