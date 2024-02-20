<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccurateController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::view('about', 'about')->name('about');

    Route::post('/insertTransaction', [\App\Http\Controllers\HomeController::class, 'insertTransaction']);

    // Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    // Route::get('/users/{user}', [\App\Http\Controllers\UserController::class, 'show'])->name('user.show');
    // Route::get('/users/{user}/edit', [\App\Http\Controllers\UserController::class, 'edit'])->name('user.edit');
    // Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('user.destroy');
    // Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    // Route::get('/users/create', [\App\Http\Controllers\UserController::class, 'create'])->name('users.create');
    // Route::get('/users/search', [\App\Http\Controllers\UserController::class, 'search'])->name('users.search');

    Route::resource('/users', \App\Http\Controllers\UserController::class);

    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Route::get('product', [\App\Http\Controllers\ProductController::class, 'show'])->name('product.show');
    Route::resource('/product', \App\Http\Controllers\ProductController::class);
    Route::get('/product/search', [\App\Http\Controllers\UserController::class, 'search'])->name('product.search');
    Route::get('/search', [\App\Http\Controllers\UserController::class, 'search']);
    Route::resource('/vehicle', \App\Http\Controllers\VehicleController::class);

    Route::get('/mapping', [App\Http\Controllers\ProductController::class, 'indexMapping'])->name('product.mapping');
    Route::get('/searchAuto', [\App\Http\Controllers\ProductController::class, 'searchAuto']);
    Route::get('/searchMotor', [\App\Http\Controllers\ProductController::class, 'searchMotor']);
    Route::get('/search-vehicles', [\App\Http\Controllers\VehicleController::class, 'search'])->name('vehicles.search');
    Route::post('/mappingStore', [\App\Http\Controllers\MappingController::class, 'store']);
    Route::post('/updateMapping', [\App\Http\Controllers\MappingController::class, 'updateMapping']);
    Route::post('/updateMappingAll', [\App\Http\Controllers\MappingController::class, 'updateMappingAll']);

    Route::post('/userStore', [\App\Http\Controllers\UserController::class, 'store'])->name('user.store');
    Route::put('/userUpdate', [\App\Http\Controllers\UserController::class, 'update'])->name('user.update');


    // Route::get('/my-new-function', [App\Http\Controllers\ProductController::class, 'myNewFunction'])->name('product.my-new-function');

    // Route::get("/home", [\App\Http\Controllers\KontenerController::class,'index']);
    // Route::get("/search", [\App\Http\Controllers\KontenerController::class,'search']);

    // Route::get('/product', [\App\Http\Controllers\ProductController::class, 'show'])->name('product.index');
    // Route::get('/product/{id}', [\App\Http\Controllers\ProductController::class, 'show'])->name('product.show');

    Route::get('/refresh-token', [AccurateController::class, 'refreshToken']);
    Route::get('/session-token', [AccurateController::class, 'getSession']);
    Route::get('/getlistitem', [AccurateController::class, 'getListItem']);
    Route::get('/postTransaction', [AccurateController::class, 'postTransaction']);
    Route::get('/updatePriceAndStock', [AccurateController::class, 'updatePriceAndStock']);
});