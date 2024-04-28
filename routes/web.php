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

    Route::get('/get-archived-columns', [BoardController::class, 'getArchivedColumns']);
    Route::get('/get-columns', [BoardController::class, 'getColumns']);
    Route::delete('/archiveColumn', [BoardController::class, 'archiveColumn']);
    Route::put('/unarchived-column', [BoardController::class, 'unarchivedColumn']);

    Route::get('/get-archived-cards', [BoardController::class, 'getArchivedCards']);
    Route::put('/transfer-card', [BoardController::class, 'transferCard']);
    Route::delete('/archiveCard', [BoardController::class, 'archiveCard']);
    Route::put('/unarchived-card', [BoardController::class, 'unarchivedCard']);

    Route::put('/update-card-sort', [BoardController::class, 'updateSort']);
    Route::put('/update-column-sort', [BoardController::class, 'updateColumnSort']);
});
