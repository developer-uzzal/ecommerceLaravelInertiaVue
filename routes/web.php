<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthMiddleware;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\HeaderAndFooterController;
use App\Http\Controllers\ShippingMethodsController;




Route::get("/link",function(){

    Artisan::call('optimize:clear');
    Artisan::call('storage:link');
    dd('Done.');
});

Route::get('/admin/login',[UsersController::class, 'loginPage'])->name('login');
Route::post('/login', [UsersController::class, 'login']);

Route::middleware([AuthMiddleware::class])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Home');
    })->name('dashboard');
    Route::get('/logout', [UsersController::class, 'logout']);
    Route::get('/user', [UsersController::class, 'index']);
    Route::post('/user/{id}', [UsersController::class, 'update']);
    Route::delete('/user/{id}', [UsersController::class, 'destroy']);

    // ============ Categories =============
    Route::get('/categories', [CategoriesController::class, 'allCategories']);
    Route::post('/category', [CategoriesController::class, 'store']);
    Route::get('/category/{id}', [CategoriesController::class, 'show']);
    Route::post('/category/{id}', [CategoriesController::class, 'update']);
    Route::delete('/category/{id}', [CategoriesController::class, 'destroy']);
    // ============ Brands =============
    Route::get('/brandAll', [BrandsController::class, 'allBrands']);
    Route::post('/brand', [BrandsController::class, 'store']);
    Route::get('/brand/{id}', [BrandsController::class, 'show']);
    Route::post('/brand/{id}', [BrandsController::class, 'update']);
    Route::delete('/brand/{id}', [BrandsController::class, 'destroy']);



    // ============ Colors =============
    Route::get('admin/colors', [ColorController::class, 'index']);
    Route::post('admin/color', [ColorController::class, 'store']);
    Route::get('admin/color/{id}', [ColorController::class, 'show']);
    Route::post('admin/color/{id}', [ColorController::class, 'update']);
    Route::delete('admin/color/{id}', [ColorController::class, 'destroy']);
    
    // =========== sizes =============
    Route::get('admin/sizes', [SizeController::class, 'index']);
    Route::post('admin/size', [SizeController::class, 'store']);
    Route::get('admin/size/{id}', [SizeController::class, 'show']);
    Route::post('admin/size/{id}', [SizeController::class, 'update']);
    Route::delete('admin/size/{id}', [SizeController::class, 'destroy']);

    // ============ Products =============
    Route::get('/products', [ProductsController::class, 'index']);
    Route::post('admin/product', [ProductsController::class, 'store']);
    

    Route::post('admin/product/{id}', [ProductsController::class, 'update']);
    Route::delete('/product/{id}', [ProductsController::class, 'destroy']);
    Route::get('admin/product/{id}', [ProductsController::class, 'show']);
    Route::post('/product-related-image-delete/{id}', [ProductsController::class, 'delateRelatedImage']);
    

    Route::post('/product-variant', [ProductsController::class, 'storeVariant']);

    // ============ Shipping Method =============

    Route::get('/shipping-methods', [ShippingMethodsController::class, 'index']);
    Route::post('/shipping-method', [ShippingMethodsController::class, 'store']);
    Route::get('/shipping-method/{id}', [ShippingMethodsController::class, 'show']);
    Route::post('/shipping-method/{id}', [ShippingMethodsController::class, 'update']);
    Route::delete('/shipping-method/{id}', [ShippingMethodsController::class, 'destroy']);


    // ============ Header and Footer =============

    Route::post('/header-footer', [HeaderAndFooterController::class, 'store']);

    // ============ Order =============
    Route::get('/admin/orders', [InvoicesController::class, 'index']);
    Route::post('/admin/order-status-update/{id}', [InvoicesController::class, 'updateStatus']);

    // ============ slider =============
    Route::get('/admin/sliders', [SliderController::class, 'index']);
    Route::post('admin/slider', [SliderController::class, 'store']);
    Route::get('admin/slider/{id}', [SliderController::class, 'show']);
    Route::post('admin/slider/{id}', [SliderController::class, 'update']);
    Route::delete('admin/slider/{id}', [SliderController::class, 'destroy']);

    // ============ dashboard =============
    Route::get('/dashboard-summary', [DashboardController::class, 'index']);
    
});
