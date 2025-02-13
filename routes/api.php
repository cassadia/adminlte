<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;

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

Route::post('/login', [LoginController::class, 'apiLogin']);
// Route::post('/logout', [LoginController::class, 'apiLogout'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->post('/logout', [LoginController::class, 'apiLogout']);

Route::middleware('auth:sanctum')->get('/cart', [CartController::class, 'index']);
Route::middleware('auth:sanctum')->put('/cart/deleteCart', [CartController::class, 'deleteCartByID']);
Route::middleware('auth:sanctum')->get('/profile/getUserByEmail', [ProfileController::class, 'getUserByEmail']);

Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'index']);
Route::middleware('auth:sanctum')->post('/user/getUser', [UserController::class, 'getUserById']);
Route::middleware('auth:sanctum')->post('/user/createUser', [UserController::class, 'createUser']);
Route::middleware('auth:sanctum')->put('/user/updateUser', [UserController::class, 'updateUser']);
Route::middleware('auth:sanctum')->put('/user/updatePass', [UserController::class, 'updatePass']);

Route::middleware('auth:sanctum')->get('/product', [UserController::class, 'index']);
Route::middleware('auth:sanctum')->post('/product/getProduct', [UserController::class, 'getProductById']);
Route::middleware('auth:sanctum')->post('/product/createProduct', [UserController::class, 'createProduct']);
Route::middleware('auth:sanctum')->put('/product/updateProduct', [UserController::class, 'updateProduct']);
Route::middleware('auth:sanctum')->put('/product/deleteProduct', [UserController::class, 'deleteProduct']);

Route::middleware('auth:sanctum')->get('/vehicle', [UserController::class, 'index']);
Route::middleware('auth:sanctum')->post('/vehicle/getVehicle', [UserController::class, 'getVehicleById']);
Route::middleware('auth:sanctum')->post('/vehicle/createVehicle', [UserController::class, 'createVehicle']);
Route::middleware('auth:sanctum')->put('/vehicle/updateVehicle', [UserController::class, 'updateVehicle']);
Route::middleware('auth:sanctum')->put('/vehicle/deleteVehicle', [UserController::class, 'deleteVehicle']);
