<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttributesController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\AttributeValuesController;
use App\Http\Controllers\HeaderAndFooterController;
use App\Http\Controllers\ShippingMethodsController;

Route::get('/test', function (Request $request) {
    return response()->json([
        'test' => 'Ok',
    ]);
});

Route::get('/clear-cache', [MaintenanceController::class, 'clearCache']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/admin-login', [UsersController::class, 'login']);

// for home page
Route::get('/header-footer', [HeaderAndFooterController::class, 'index']);
Route::get('/brands', [BrandsController::class, 'index']);
Route::get('/categories-home', [CategoriesController::class, 'indexHome']);

Route::get('/all-products', [ProductsController::class, 'allProducts']);
Route::get('/search-products', [ProductsController::class, 'searchProducts']);
Route::get('/new-products-trend', [ProductsController::class, 'allNewTrendingProducts']);
Route::get('/featured-products', [ProductsController::class, 'allFeaturedProducts']);
Route::get('/new-products/{name}', [ProductsController::class, 'newAllProductsByTrendOrFeatured']);
Route::get('/product/{slug}', [ProductsController::class, 'productBySlug']);
Route::get('/category/{slug}', [ProductsController::class, 'productByCategorySlug']);
Route::get('/categories-all', [CategoriesController::class, 'index']);

// ============ Users Login and Register =============
Route::post('/user-login', [UsersController::class, 'userLogin']);
Route::post('/user-registration', [UsersController::class, 'userCreate']);
Route::get('/user-by-username/{username}', [UsersController::class, 'userById']); //not used
Route::post('/user-update/{id}', [UsersController::class, 'userUpdate']);
Route::get('orders/{id}', [InvoicesController::class, 'showInvoiceSummary']);

// ============ order =============
Route::get('/shipping-method', [ShippingMethodsController::class, 'shippingMethodsAll']);
Route::post('/order', [InvoicesController::class, 'makeOrder']);
Route::get('/invoice/{id}', [InvoicesController::class, 'showInvoice']);

// =========== slider =============
Route::get('/sliders', [SliderController::class, 'userSlidersAll']);






Route::middleware('auth:sanctum')->group(function () {

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
