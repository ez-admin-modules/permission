<?php

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

Route::prefix('admin')
    ->middleware('permission:permission')
    ->name('admin.')
    ->namespace('Admin')
    ->group(function () {
        Route::prefix('v1')
            ->name('v1.')
            ->namespace('V1')
            ->group(module_path('Permission', '/Routes/api/admin/v1.php'));
    });
