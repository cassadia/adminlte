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

Route::middleware(['auth' , 'check.menu.access', 'check.public.path'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::view('show.user', 'showUser')->name('showUser');

    // Route::get('/user-menu', 'MenuController@index')->name('user-menu.index');
    // Route::get('/user-menu', [\App\Http\Controllers\UserMenuController::class, 'index'])->name('user-menu.index');

    Route::post('/insertTransaction', [\App\Http\Controllers\HomeController::class, 'insertTransaction'])->name('home.transaction');
    Route::get('/getStockPerLokasi', [\App\Http\Controllers\HomeController::class, 'getStockPerLokasi'])->name('home.getstockperlokasi');

    // Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    // Route::get('/users/{user}', [\App\Http\Controllers\UserController::class, 'show'])->name('user.show');
    // Route::get('/users/{user}/edit', [\App\Http\Controllers\UserController::class, 'edit'])->name('user.edit');
    // Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('user.destroy');
    // Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::get('/users/create', [\App\Http\Controllers\UserController::class, 'create'])->name('users.create');
    // Route::get('/users/search', [\App\Http\Controllers\UserController::class, 'search'])->name('users.search');

    Route::resource('/users', \App\Http\Controllers\UserController::class);

    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Route::get('product', [\App\Http\Controllers\ProductController::class, 'show'])->name('product.show');
    // Route::resource('/product', \App\Http\Controllers\ProductController::class);
    Route::resource('/product', \App\Http\Controllers\ProductController::class)->names([
        'index' => 'product.index',
        'show' => 'product.show',
        'edit' => 'product.edit',
        'create' => 'product.create',
        'destroy' => 'product.destroy'
    ]);
    Route::get('/product/search', [\App\Http\Controllers\UserController::class, 'search'])->name('product.search');
    Route::get('/search', [\App\Http\Controllers\UserController::class, 'search']);

    Route::resource('/vehicle', \App\Http\Controllers\VehicleController::class);

    Route::get('/mapping', [App\Http\Controllers\ProductController::class, 'indexMapping'])->name('product.mapping');
    Route::get('/searchAuto', [\App\Http\Controllers\ProductController::class, 'searchAuto'])->name('product.searchAuto');
    Route::get('/searchMotor', [\App\Http\Controllers\ProductController::class, 'searchMotor'])->name('product.searchMotor');
    Route::get('/productExport', [\App\Http\Controllers\ProductController::class, 'productExport'])->name('product.export');
    Route::get('/search-vehicles', [\App\Http\Controllers\VehicleController::class, 'search'])->name('vehicles.search');
    Route::get('/vehicleExport', [\App\Http\Controllers\VehicleController::class, 'vehicleExport'])->name('vehicle.export');
    Route::post('/mappingStore', [\App\Http\Controllers\MappingController::class, 'store']);
    Route::post('/updateMapping', [\App\Http\Controllers\MappingController::class, 'updateMapping'])->name('mapping.updateMapp');
    Route::post('/updateMappingAll', [\App\Http\Controllers\MappingController::class, 'updateMappingAll'])->name('mapping.updateMappAll');
    Route::get('/mappingExport', [\App\Http\Controllers\MappingController::class, 'mappingExport'])->name('mapping.export');

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

    // Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart');
    Route::resource('/cart', \App\Http\Controllers\CartController::class);
    // Route::resource('/about', \App\Http\Controllers\AboutController::class);
    // Route::get('about', [App\Http\Controllers\AboutController::class, 'index'])->name('about.index');
    // Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    // Route::get('/about', [App\Http\Controllers\AboutController::class, 'index'])->name('about');
    Route::get('/users/detail/{id}', [App\Http\Controllers\UserController::class, 'getUserId'])->name('show.user');
});

// Tambahkan juga route untuk akses /public
Route::prefix('public')->group(function () {
    Route::middleware(['auth', 'check.menu.access', 'check.public.path'])->group(function () {
        Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('public.home');
        Route::post('/insertTransaction', [\App\Http\Controllers\HomeController::class, 'insertTransaction'])->name('home.transaction');
        Route::get('/getStockPerLokasi', [\App\Http\Controllers\HomeController::class, 'getStockPerLokasi'])->name('home.getstockperlokasi');

        Route::resource('/product', \App\Http\Controllers\ProductController::class)->names([
            'index' => 'public.product.index',
            'show' => 'public.product.show',
            'edit' => 'public.product.edit',
            'create' => 'public.product.create',
            'destroy' => 'public.product.destroy'
        ]);

        Route::resource('/vehicle', \App\Http\Controllers\VehicleController::class)->names([
            'index' => 'public.vehicle.index',
            'show' => 'public.vehicle.show',
            'edit' => 'public.vehicle.edit'
        ]);

        Route::get('/searchAuto', [\App\Http\Controllers\ProductController::class, 'searchAuto'])->name('public.product.searchAuto');
        Route::get('/searchMotor', [\App\Http\Controllers\ProductController::class, 'searchMotor'])->name('public.product.searchMotor');

        Route::get('/mapping', [App\Http\Controllers\ProductController::class, 'indexMapping'])->name('public.product.mapping');
        Route::post('/updateMapping', [\App\Http\Controllers\MappingController::class, 'updateMapping'])->name('public.mapping.updateMapp');
        Route::post('/updateMappingAll', [\App\Http\Controllers\MappingController::class, 'updateMappingAll'])->name('public.mapping.updateMappAll');
        Route::get('/mappingExport', [\App\Http\Controllers\MappingController::class, 'mappingExport'])->name('public.mapping.export');
        Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('public.users.index');
        Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('public.cart.index');
        // Route::resource('/users', \App\Http\Controllers\UserController::class);

        // Route::resource('/cart', \App\Http\Controllers\CartController::class);
        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('public.profile.show')->middleware('allow.fallback.access');
        Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('public.profile.update');
        Route::get('/users/detail/{id}', [App\Http\Controllers\UserController::class, 'getUserId'])->name('show.user');

        Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('public.logout');
    });
});
