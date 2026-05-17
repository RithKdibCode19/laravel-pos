<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group.
|
*/

// Public Routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});

// Protected Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Language Switcher Route
    Route::get('language/{locale}', [LanguageController::class, 'switchLang'])->name('language.switch');
    
    // Logout Route
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Sales Routes
    Route::resource('sales', SaleController::class);
    Route::get('sales/{sale}/print', [SaleController::class, 'invoice'])->name('sales.print');
    Route::get('sales/{sale}/invoice', [SaleController::class, 'invoice'])->name('sales.invoice');

    // Product Routes
    Route::resource('products', ProductController::class);
    Route::get('products/{product}/stock', [ProductController::class, 'stock'])->name('products.stock');
    Route::post('products/{product}/stock', [ProductController::class, 'updateStock'])->name('products.stock.update');

    // Customer Routes
    Route::resource('customers', CustomerController::class);
    Route::get('customers/{customer}/history', [CustomerController::class, 'history'])->name('customers.history');

    // Report Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/sales/export', [ReportController::class, 'salesExport'])->name('sales.export');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/inventory/export', [ReportController::class, 'inventoryExport'])->name('inventory.export');
        Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
        Route::get('/customers/export', [ReportController::class, 'customersExport'])->name('customers.export');
    });

    // Settings Routes
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::get('/profile', [SettingController::class, 'profile'])->name('profile');
        Route::post('/profile', [SettingController::class, 'updateProfile'])->name('profile.update');
        Route::get('/password', [SettingController::class, 'password'])->name('password');
        Route::post('/password', [SettingController::class, 'updatePassword'])->name('password.update');
    });

    // Category Routes
    Route::resource('categories', CategoryController::class)->except(['index', 'create', 'edit', 'show']);

    // User Management Routes
    Route::resource('users', UserController::class);
});
