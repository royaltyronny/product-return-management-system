<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PageController; // Added import for PageController

// Disable default login routes if using custom ones
Auth::routes(['login' => false]);

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

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

    Route::middleware('auth')->group(function () {
        Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');
        Route::get('/returns/create/{shoe}', [ReturnController::class, 'create'])->name('returns.create');
        Route::post('/returns/store/{shoe}', [ReturnController::class, 'store'])->name('returns.store');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/view', [ProfileController::class, 'show'])->name('profile.view');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Products
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // About Page
    Route::get('/about', [PageController::class, 'about'])->name('about');
    Route::get('/shoes/{shoe}/returns/create', [ReturnController::class, 'create'])->name('returns.create');
    Route::post('/shoes/{shoe}/returns', [ReturnController::class, 'store'])->name('returns.store');
    
});
