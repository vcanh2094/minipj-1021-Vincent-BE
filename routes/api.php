<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\FavoriteController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\API\SlideController;
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
    Route::get('user-profile', [AuthController::class, 'user_profile']);
    Route::patch('change-profile', [AuthController::class, 'change_profile']);
    Route::apiResource('favorites', FavoriteController::class)->only(['index', 'store', 'destroy']);
    Route::apiResource('orders', OrderController::class)->only(['index', 'show', 'store']);
    Route::post('logout', [AuthController::class, 'logout']);
});
//Home page API
Route::get('categories', [CategoryController::class, 'index']);
Route::get('banners', [SlideController::class, 'show']);
Route::get('products', [ProductController::class, 'index']); //get Product list(feature/sale/byCategory/search)

//Admin API
Route::post('admin-login', [AdminController::class, 'login']);
Route::post('admin-register', [AdminController::class, 'register']);
Route::group([
    'prefix' => 'admin',
    'middleware' => ['assign.guard:admins','jwt.auth']
], function(){
    Route::post('create-banner', [SlideController::class, 'store']);
    Route::apiResource('products', ProductController::class)->except(['create', 'edit']); //CRUD Products
});









