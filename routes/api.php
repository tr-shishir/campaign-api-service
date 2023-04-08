<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CampaignController;


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

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

Route::middleware('jwt.auth')->group(function () {
    Route::post('logout', [UserController::class, 'logout']);
    Route::prefix('campaigns')->group(function () {
        
        Route::get('/', [CampaignController::class, 'index']);
        Route::post('/', [CampaignController::class, 'store']);
        Route::get('/{campaign}', [CampaignController::class, 'show']);
        Route::put('/{campaign}', [CampaignController::class, 'update']);
        Route::delete('/{campaign}', [CampaignController::class, 'destroy']);
        Route::patch('/{campaign}/status', [CampaignController::class, 'updateStatus']);
        Route::post('/{campaign}/join', [CampaignController::class, 'join']);
        Route::post('/{campaign}/leave', [CampaignController::class, 'leave']);

        Route::get('/all/orders', [OrderController::class, 'index']);
        Route::post('/{campaign}/collection', [OrderController::class, 'getCollection']);
        Route::post('/{campaign}/orders', [OrderController::class, 'store']);
        Route::put('/{campaign}/orders/{order}', [OrderController::class, 'update']);
        Route::delete('/{campaign}/orders/{order}', [OrderController::class, 'destroy']);
    });
});