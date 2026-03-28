<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OtherProductController;
use App\Http\Controllers\OtherProductSalesController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\DailyBalanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillingController;

Route::get('/debug-session', function () {
    return response()->json([
        'session_driver'  => config('session.driver'),
        'cache_store'     => config('cache.default'),
        'app_env'         => config('app.env'),
        'session_path'    => storage_path('framework/sessions'),
        'session_writable'=> is_writable(storage_path('framework/sessions')),
        'csrf_token'      => csrf_token(),
    ]);
});

Route::get('/debug-routes', function () {
    $routes = collect(\Route::getRoutes())->filter(function($r) {
        return str_contains($r->uri(), 'login');
    })->map(function($r) {
        return [
            'method' => implode('|', $r->methods()),
            'uri'    => $r->uri(),
            'name'   => $r->getName(),
        ];
    })->values();
    return response()->json($routes);
});

Route::get('/clear', function () {

    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('optimize');

    return "Optimization commands executed successfully ✅";
});

// AUTH ROUTES (public)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// PROTECTED ROUTES
Route::middleware(['checklogin'])->group(function () {
    
    
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ADMIN ONLY
    Route::middleware(['checklogin:admin'])->group(function () {


        Route::get('/products/export-csv', [ProductController::class, 'exportCsv'])->name('products.exportCsv');

        // Products Resource Routes (index, create, store, edit, update, destroy)
        Route::resource('products', ProductController::class);

        // Other Products Resource
        Route::resource('other-products', OtherProductController::class);

        // Other Product Sales (index only - read only report)
        Route::get('/other-product-sales', [OtherProductSalesController::class, 'index'])->name('other-product-sales.index');


        // Staff Resource (index, store, edit, update, destroy — no create page, form is on index)
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
        Route::get('/staff/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit');
        Route::put('/staff/{id}', [StaffController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->name('staff.destroy');

    });

    // Daily Balance (admin + staff)
    // Daily Balance Flow
    Route::get('/daily-balance', [DailyBalanceController::class, 'index'])->name('daily-balance.index');
    Route::post('/daily-balance/add-cash', [DailyBalanceController::class, 'addCash'])->name('daily-balance.add-cash');
    Route::post('/daily-balance/remove-cash', [DailyBalanceController::class, 'removeCash'])->name('daily-balance.remove-cash');


    // Billing (staff + admin)
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::get('/billing/search', [BillingController::class, 'searchProduct'])->name('billing.search');
    Route::get('/billing/fetch', [BillingController::class, 'fetchProduct'])->name('billing.fetch');
    Route::post('/billing/save', [BillingController::class, 'saveInvoice'])->name('billing.save');
    Route::get('/billing/invoice/{id}', [BillingController::class, 'showInvoice'])->name('billing.invoice');
    Route::get('/billing/invoices', [BillingController::class, 'invoiceList'])
     ->name('billing.invoice-list');
    Route::post('/billing/mark-paid/{id}', [BillingController::class, 'markPaid'])->name('billing.markPaid');

});
