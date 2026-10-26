<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\productController;
use App\Http\Controllers\purchaseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StockReportController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.submit');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::middleware('role:Admin')->group(function () {
        Route::resource('users', UserController::class);
    });

    Route::middleware('role:Admin,Sales User')->group(function () {

        Route::get('/customers/find-matches', [
            CustomerController::class,
            'findMatches'
        ])->name('customers.findMatches');

        Route::get('/customers/quick-create', [
            CustomerController::class,
            'quickCreate'
        ])->name('customers.quickCreate');

        Route::resource('customers', CustomerController::class);

        Route::get('/payment-status', [
            InvoiceController::class,
            'paymentStatus'
        ])->name('invoices.payment-status');

        Route::resource('invoices', InvoiceController::class);

        Route::resource('payments', PaymentController::class)
            ->only(['index', 'create', 'store', 'show']);

        Route::get('/reports', [ReportController::class, 'index'])
            ->name('reports.index');
    });

    Route::middleware('role:Admin,Inventory User')->group(function () {

        Route::resource('categories', CategoryController::class);

        Route::resource('suppliers', SupplierController::class);

        Route::resource('products', ProductController::class);

        Route::get('/stock-report', [
            StockReportController::class,
            'index'
        ])->name('stock-report.index');

        Route::resource('purchases', PurchaseController::class);
    });
});
