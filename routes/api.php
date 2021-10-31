<?php

use App\Http\Controllers\API\AddressController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\FavoriteController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\SlideController;
use App\Models\Order;
use App\Models\Product;
use App\Transformers\OrderTransformer;
use App\Transformers\ProductTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//User API
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::group([
    'prefix' => 'user',
    'middleware' => ['assign.guard:users', 'jwt.auth'],
], function () {
    Route::get('user-profile', [AuthController::class, 'userProfile']);
    Route::patch('change-profile', [AuthController::class, 'changeProfile']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::apiResource('favorites', FavoriteController::class)->only(['index', 'store', 'destroy']);
    Route::apiResource('orders', OrderController::class)->only(['index', 'store']);
    Route::get('orders/{order}', function (Order $order){
        return responder()->success($order, new OrderTransformer)->respond();
    });
    Route::apiResource('addresses', AddressController::class)->only('index', 'store');
    Route::patch('change-address', [AddressController::class, 'update']);
    Route::post('logout', [AuthController::class, 'logout']);
});
//Home page API
Route::get('categories', [CategoryController::class, 'index']);
Route::get('banners', [SlideController::class, 'index']);
Route::get('products', [ProductController::class, 'index']); //get Product list(feature/sale/byCategory/search)
Route::get('products/{product}', function (Product $product){
    return responder()->success($product, new ProductTransformer)->with(['category','images'])->respond();
});

//Admin API
Route::post('admin-login', [AdminController::class, 'login']);
Route::group([
    'prefix' => 'admin',
    'middleware' => ['assign.guard:admins','jwt.auth']
], function(){
    Route::post('admin-register', [AdminController::class, 'register']);
    Route::post('create-banner', [SlideController::class, 'store']);
    Route::apiResource('products', ProductController::class)->except(['create', 'edit']); //CRUD Products
    Route::post('logout', [AdminController::class, 'logout']);
});


Route::post('is-favorite', [FavoriteController::class, 'isFavorite']);









