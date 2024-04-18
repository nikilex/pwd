<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Api\BoardController;
use Illuminate\Support\Facades\Route;

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

Route::group([
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
], function () { // custom admin routes
    Route::get('/', [DashboardController::class, 'index'])->name('main');

    Route::get('/get-boards', [BoardController::class, 'getBoards']);
    Route::get('/get-columns', [BoardController::class, 'getColumns']);

    Route::put('/transfer-card', [BoardController::class, 'transferCard']);

    Route::put('/update-card-sort', [BoardController::class, 'updateSort']);
    Route::put('/update-column-sort', [BoardController::class, 'updateColumnSort']);
});
