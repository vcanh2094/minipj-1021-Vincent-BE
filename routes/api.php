<?php

use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\ProductController;
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
//user API
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('user-profile', [AuthController::class, 'userProfile']);
    Route::post('change-pass', [AuthController::class, 'changePassword']);

});
//Home page API
Route::get('categories', [CategoryController::class, 'index']); //get Category List
Route::get('products/feature', [ProductController::class, 'getFeatureProduct']); //get product feature
Route::get('products/sale', [ProductController::class, 'getSaleProduct']); //get product on sale
Route::get('products/cate/{id}', [ProductController::class, 'getProductByCategory']); //get product by category id

//Product page API
Route::get('products', [ProductController::class, 'index']); //get Product list
Route::get('products/{id}', [ProductController::class, 'show']); // get one product by id

//CRUD Products
Route::post('create', [ProductController::class, 'store']); //create new product
Route::patch('update/{id}', [ProductController::class, 'update']);//update product
Route::delete('delete/{product}', [ProductController::class, 'destroy']); //delete product

//Image upload

Route::post('upload', [ImageController::class, 'store']);
Route::get('/{image}', [ImageController::class, 'show']);






