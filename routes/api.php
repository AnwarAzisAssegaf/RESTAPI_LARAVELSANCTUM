<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;


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

Route::namespace('V1')->prefix('v1')->group(function () {
    Route::namespace('Auth')->group(function () {
        Route::post('/login', [App\Http\Controllers\V1\Auth\AuthController::class,'login']);
        Route::post('/registration', [App\Http\Controllers\V1\Auth\AuthController::class, 'registration']);
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/logout', [App\Http\Controllers\V1\Auth\AuthController::class,'logout']);
            Route::get('/logout-from-all-device', [App\Http\Controllers\V1\Auth\AuthController::class,'logoutFromAllDevice']);
        });
    });
   

     Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });
	
	Route::namespace('Income')->group(function () {
	Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::post('/create', [App\Http\Controllers\V1\Income\IncomeController::class,'create']);
    Route::get('/edit/{id}', [App\Http\Controllers\V1\Income\IncomeController::class,'edit']);
    Route::post('/edit/{id}', [App\Http\Controllers\V1\Income\IncomeController::class,'update']);
    Route::get('/delete/{id}', [App\Http\Controllers\V1\Income\IncomeController::class,'delete']);
	});
	});
	
	
	
});
