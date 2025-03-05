<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\AccurateController;
use App\Http\Controllers\Api\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// routes/api.php
// Route::post('/refresh-token', [LoginController::class, 'refreshToken']);

Route::post('/login', [LoginController::class, 'apiLogin']);
// Route::post('/logout', [LoginController::class, 'apiLogout'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->post('/logout', [LoginController::class, 'apiLogout']);

Route::middleware('auth:sanctum')->post('/cart', [CartController::class, 'index']);
Route::middleware('auth:sanctum')->put('/cart/deleteCart', [CartController::class, 'deleteCartByID']);
Route::middleware('auth:sanctum')->get('/profile/getUserByEmail', [ProfileController::class, 'getUserByEmail']);

Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'index']);
Route::middleware('auth:sanctum')->post('/user/getUser', [UserController::class, 'getUserById']);
Route::middleware('auth:sanctum')->post('/user/createUser', [UserController::class, 'createUser']);
Route::middleware('auth:sanctum')->put('/user/updateUser', [UserController::class, 'updateUser']);
Route::middleware('auth:sanctum')->put('/user/updatePass', [UserController::class, 'updatePass']);
Route::middleware('auth:sanctum')->put('/user/deleteUser', [UserController::class, 'deleteUser']);

Route::middleware('auth:sanctum')->get('/product', [UserController::class, 'index']);
Route::middleware('auth:sanctum')->post('/product/getProduct', [UserController::class, 'getProductById']);
Route::middleware('auth:sanctum')->post('/product/createProduct', [ProductController::class, 'createProduct']);
Route::middleware('auth:sanctum')->put('/product/updateProduct', [ProductController::class, 'updateProduct']);
Route::middleware('auth:sanctum')->put('/product/deleteProduct', [ProductController::class, 'deleteProduct']);

Route::middleware('auth:sanctum')->get('/vehicle', [VehicleController::class, 'index']);
Route::middleware('auth:sanctum')->post('/vehicle/getVehicle', [VehicleController::class, 'getVehicleById']);
Route::middleware('auth:sanctum')->post('/vehicle/createVehicle', [VehicleController::class, 'createVehicle']);
Route::middleware('auth:sanctum')->post('/vehicle/updateVehicle', [VehicleController::class, 'updateVehicle']);
Route::middleware('auth:sanctum')->put('/vehicle/deleteVehicle', [VehicleController::class, 'deleteVehicle']);

Route::middleware('auth:sanctum')->post('/cart/postTransaction', [AccurateController::class, 'postTransaction']);
