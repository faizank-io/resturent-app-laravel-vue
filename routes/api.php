<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MenuItemController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\StaffController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for Restaurant Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

Route::apiResource('categories', CategoryController::class)->only(['index', 'store']);

Route::apiResource('menu-items', MenuItemController::class);
Route::patch('/menu-items/{menuItem}/toggle-availability', [MenuItemController::class, 'toggleAvailability']);

Route::apiResource('orders', OrderController::class)->only(['index', 'store']);
Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);

Route::apiResource('tables', TableController::class)->only(['index', 'store']);
Route::patch('/tables/{table}/status', [TableController::class, 'updateStatus']);

Route::apiResource('staffs', StaffController::class);
Route::patch('/staffs/{staff}/status', [StaffController::class, 'updateStatus']);

