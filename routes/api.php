<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Api\BoardController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('get-card', [BoardController::class, 'getCard']);

Route::post('change-column-title', [BoardController::class, 'changeColumnTitle']);
Route::post('change-card-title', [BoardController::class, 'changeCardTitle']);

Route::put('save-card-description/{id}', [BoardController::class, 'saveCardDescription']);
Route::put('save-card-title/{id}', [BoardController::class, 'saveCardTitle']);
