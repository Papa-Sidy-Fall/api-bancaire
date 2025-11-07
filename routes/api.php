<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')->group(function () {
    Route::post('/login',[AuthController::class,'login']);
    Route::middleware('auth:api')->group(function () {
        Route::get('/comptes',[CompteController::class,'index']);
        Route::get('/comptes/{id}',[CompteController::class,'show']);
        Route::post('/comptes',[CompteController::class,'store']);
        
    });

});
