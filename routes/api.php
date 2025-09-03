<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\Admin\PortfolioController as AdminPortfolioController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\AuthController;

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

// Authentication Routes
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);
Route::middleware('auth:sanctum')->get('/validate-token', [AuthController::class, 'validateToken']);

// Public Portfolio Routes
Route::prefix('portfolios')->group(function () {
    Route::get('/', [PortfolioController::class, 'index']);
    Route::get('/featured', [PortfolioController::class, 'featured']);
    Route::get('/categories', [PortfolioController::class, 'categories']);
    Route::get('/stats', [PortfolioController::class, 'stats']);
    Route::get('/{id}', [PortfolioController::class, 'show']);
});

// Admin Routes (protected by Sanctum)
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    // Dashboard stats
    Route::get('/stats', [AdminDashboardController::class, 'stats']);

    // User Management
    Route::apiResource('users', AdminUserController::class);

    // News Management
    Route::apiResource('news', AdminNewsController::class);
    Route::get('/news-categories', [AdminNewsController::class, 'categories']);

    // Admin Portfolio Management
    Route::apiResource('portfolios', AdminPortfolioController::class);

    // Portfolio categories for admin
    Route::get('/portfolios-categories', [AdminPortfolioController::class, 'categories']);
});
