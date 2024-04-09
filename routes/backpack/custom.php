<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('password', 'PasswordCrudController');
    Route::crud('project', 'ProjectCrudController');
    Route::crud('user', 'UserCrudController');
    Route::get('b/{id}', 'BoardController@index')->name('page.board.id.index');
    Route::get('b', 'BoardController@index')->name('page.board.index');
    Route::crud('board', 'BoardCrudController');
    Route::crud('column', 'ColumnCrudController');
    Route::crud('card', 'CardCrudController');
}); // this should be the absolute last line of this file