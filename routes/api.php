<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\ProductController;
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
    Route::get('user-profile', [AuthController::class, 'userProfile']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::patch('change-profile', [AuthController::class, 'changeProfile']);
});

//Home page API
Route::get('categories', [CategoryController::class, 'index']); //get Category List
Route::get('products/feature', [ProductController::class, 'getFeatureProduct']); //get product feature
Route::get('products/sale', [ProductController::class, 'getSaleProduct']); //get product on sale
Route::get('products/categories/{id}', [ProductController::class, 'getProductByCategory']); //get product by category id
Route::get('banners', [SlideController::class, 'show']); //show banners list

//Product page API
Route::get('products', [ProductController::class, 'index']); //get Product list
Route::get('products/{id}', [ProductController::class, 'show']); // get detail product

//Admin API
Route::post('admin-login', [AdminController::class, 'login']);
Route::post('admin-register', [AdminController::class, 'register']);
Route::group([
    'prefix' => 'admin',
    'middleware' => ['assign.guard:admins','jwt.auth']
], function(){
    Route::post('create-banner', [SlideController::class, 'store']); //add banner
    Route::apiResource('products', ProductController::class)->except(['create', 'edit']); //CRUD Products
});









