<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController; // Missing import

// Disable default login routes if you're using custom routes
Auth::routes(['login' => false]);

// Home Route (Public)
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Home Route after Authentication (Protected)
Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');

// Custom Login Routes (Optional, if not using Auth::routes)
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login'); // Show login form
Route::post('login', [LoginController::class, 'login']); // Handle login submission
Route::post('logout', [LoginController::class, 'logout'])->name('logout'); // Handle logout

// Returns Management Routes (Protected)
Route::middleware('auth')->group(function () {
    Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');
    Route::post('/returns', [ReturnController::class, 'create'])->name('returns.create');
    Route::get('/returns/{id}', [ReturnController::class, 'show']);
    Route::delete('/returns/{id}', [ReturnController::class, 'destroy']);
    Route::post('/returns/{shoe}', [ReturnController::class, 'create'])->name('returns.create');
    Route::get('/returns/report', [ReturnController::class, 'report'])->name('returns.report');


});

// Reports Management Routes (Protected)
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index')->middleware('auth');

// Password Confirmation Routes
Route::get('password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
Route::post('password/confirm', [ConfirmPasswordController::class, 'confirm']);

// Profile Routes (Protected)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/view', [ProfileController::class, 'show'])->name('profile.view');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Product Routes (Protected)
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    Route::get('/returns/reports', [ReturnController::class, 'reports'])->name('returns.reports');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/about', [PageController::class, 'about'])->name('about')->middleware('auth');

});
