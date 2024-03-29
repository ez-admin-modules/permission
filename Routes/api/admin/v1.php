<?php

use EzAdmin\Modules\Permission\Http\Controllers\Api\Admin\V1\AdminerController;
use EzAdmin\Modules\Permission\Http\Controllers\Api\Admin\V1\AuthController;
use EzAdmin\Modules\Permission\Http\Controllers\Api\Admin\V1\MenuController;
use EzAdmin\Modules\Permission\Http\Controllers\Api\Admin\V1\RoleController;

// 鉴权
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/logout', [AuthController::class, 'logout']);
Route::get('auth', [AuthController::class, 'show']);
Route::put('auth', [AuthController::class, 'update']);

// 用户管理
Route::get('adminers', [AdminerController::class, 'index']);
Route::post('adminers', [AdminerController::class, 'store']);
Route::get('adminers/{adminer}', [AdminerController::class, 'show']);
Route::put('adminers/{adminer}', [AdminerController::class, 'update']);
Route::delete('adminers/{adminer}', [AdminerController::class, 'destroy']);
Route::patch('adminers/changeStatus', [AdminerController::class, 'changeStatus']);

// 角色管理
Route::get('roles', [RoleController::class, 'index']);
Route::post('roles', [RoleController::class, 'store']);
Route::get('roles/{role}', [RoleController::class, 'show']);
Route::put('roles/{role}', [RoleController::class, 'update']);
Route::delete('roles/{role}', [RoleController::class, 'destroy']);
Route::patch('roles/changeStatus', [RoleController::class, 'changeStatus']);

// 菜单管理
Route::get('menus', [MenuController::class, 'index']);
Route::post('menus', [MenuController::class, 'store']);
Route::get('menus/{menu}', [MenuController::class, 'show']);
Route::put('menus/{menu}', [MenuController::class, 'update']);
Route::delete('menus/{menu}', [MenuController::class, 'destroy']);
Route::patch('menus/changeVisible', [MenuController::class, 'changeVisible']);

