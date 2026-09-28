<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomOrderController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('company-profile');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
});

Route::middleware(['auth', 'role'])->group(function () {

    Route::get('/profile', [AuthController::class, 'profile'])->name('admin.profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('admin.profile.update');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    Route::prefix('dashboard')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status.update');
        Route::patch('/orders/{order}/shipping', [OrderController::class, 'updateShipping'])->name('orders.shipping.update');

        Route::get('/custom-orders', [CustomOrderController::class, 'index'])->name('custom-orders.index');
        Route::get('/custom-orders/{customOrder}', [CustomOrderController::class, 'show'])->name('custom-orders.show');
        Route::patch('/custom-orders/{customOrder}/quote', [CustomOrderController::class, 'quote'])->name('custom-orders.quote');
        Route::patch('/custom-orders/{customOrder}/reject', [CustomOrderController::class, 'reject'])->name('custom-orders.reject');
        Route::patch('/custom-orders/{customOrder}/status', [CustomOrderController::class, 'updateStatus'])->name('custom-orders.status');

        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::post('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/categories/{category}/subcategories', [CategoryController::class, 'subcategories'])->name('subcategories.index');
        Route::post('/categories/{category}/subcategories', [SubcategoryController::class, 'store'])->name('subcategories.store');
        Route::put('/categories/{category}/subcategories/{subcategory}', [SubcategoryController::class, 'update'])->name('subcategories.update');
        Route::delete('/categories/{category}/subcategories/{subcategory}', [SubcategoryController::class, 'destroy'])->name('subcategories.destroy');

        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        Route::get('/products/{product}/variants', [ProductController::class, 'variants'])->name('variants.index');
        Route::post('/products/{product}/variants', [ProductVariantController::class, 'store'])->name('variants.store');
        Route::put('/products/{product}/variants/{variant}', [ProductVariantController::class, 'update'])->name('variants.update');
        Route::delete('/products/{product}/variants/{variant}', [ProductVariantController::class, 'destroy'])->name('variants.destroy');

        Route::get('/products/bundles', [ProductController::class, 'bundles'])->name('bundles.index');
        Route::post('/products/bundles', [ProductController::class, 'storeBundle'])->name('bundles.store');
        Route::put('/products/bundles/{product}', [ProductController::class, 'updateBundle'])->name('bundles.update');
        Route::delete('/products/bundles/{product}', [ProductController::class, 'destroyBundle'])->name('bundles.destroy');

        Route::get('/customer', [CustomerController::class, 'index'])->name('customers.index');
        Route::post('/customer', [CustomerController::class, 'store'])->name('customers.store');
        Route::put('/customer/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('/customer/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

        Route::get('/promo', [PromoController::class, 'index'])->name('promo.index');
        Route::post('/date', [PromoController::class, 'storeDatePromo'])->name('date.store');
        Route::put('/date/{datePromo}', [PromoController::class, 'updateDatePromo'])->name('date.update');
        Route::delete('/date/{datePromo}', [PromoController::class, 'destroyDatePromo'])->name('date.destroy');
        Route::patch('/date/{datePromo}/toggle', [PromoController::class, 'toggleDatePromo'])->name('date.toggle');
        Route::post('/voucher', [PromoController::class, 'storeVoucher'])->name('voucher.store');
        Route::put('/voucher/{voucher}', [PromoController::class, 'updateVoucher'])->name('voucher.update');
        Route::delete('/voucher/{voucher}', [PromoController::class, 'destroyVoucher'])->name('voucher.destroy');
        Route::patch('/voucher/{voucher}/toggle', [PromoController::class, 'toggleVoucher'])->name('voucher.toggle');
    });
});
