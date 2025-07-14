<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\HouseController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\RateController;
use App\Http\Middleware\LocalMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
// CITY Routes




Route::middleware(LocalMiddleware::class)->prefix('auth')->group(function () {
    Route::post('signup', [AuthController::class, "signup"]);
    Route::post('login', [AuthController::class, "login"]);
    Route::post('logout', [AuthController::class, 'logout']);
});

/************************************ */
Route::middleware(['auth:sanctum'])->prefix('houses')->controller(HouseController::class)->group(function () {


    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('/{house}', 'show');
    Route::post('/{house}', 'update');
    Route::delete('/{house}', 'delete');
});
Route::middleware(['auth:sanctum'])->prefix('office')->controller(OfficeController::class)->group(function () {


    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('/{office}', 'show');
    Route::post('/{office}', 'update');
    Route::delete('/{office}', 'delete');
});





Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('signup',  'signup');
    Route::post('login',   'login');
    Route::post('logout',  'logout');
});

Route::controller(CityController::class)->prefix('cities')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::post('/{city}', 'update');
    Route::delete('/{city}', 'destroy');
});
Route::controller(DistrictController::class)->prefix('districts')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::post('/{district}', 'update');
    Route::delete('/{district}', 'destroy');
});



Route::middleware(LocalMiddleware::class)->prefix('rates')->group(function () {
    Route::post('store', [RateController::class, "store"]);
    Route::post('update/{rate}', [RateController::class, "update"]);
});
