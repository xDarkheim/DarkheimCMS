<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\AdminNewsController;
use App\Http\Controllers\Admin\PortfolioController as AdminPortfolioController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Api\CareerController;
use App\Http\Controllers\Api\TeamMemberController;

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

// Public News Routes
Route::prefix('news')->group(function () {
    Route::get('/', [NewsController::class, 'index']);
    Route::get('/featured', [NewsController::class, 'featured']);
    Route::get('/latest', [NewsController::class, 'latest']);
    Route::get('/categories', [NewsController::class, 'categories']);
    Route::get('/all-categories', [NewsController::class, 'allCategories']);
    Route::get('/{slug}', [NewsController::class, 'show']);
});

// Public Contact Form
Route::post('/contact', [ContactController::class, 'submit']);

// Public Statistics
Route::get('/stats', [App\Http\Controllers\Api\StatsController::class, 'public']);

// Career routes
Route::get('/careers', [CareerController::class, 'index']);
Route::get('/careers/{career}', [CareerController::class, 'show']);

// Team routes
Route::get('/team', [TeamMemberController::class, 'index']);
Route::get('/team/{teamMember}', [TeamMemberController::class, 'show']);

// Admin Routes (protected by Sanctum)
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    // Dashboard stats
    Route::get('/stats', [App\Http\Controllers\Api\StatsController::class, 'admin']);

    // User Management
    Route::apiResource('users', AdminUserController::class);

    // News management
    Route::prefix('news')->group(function () {
        Route::get('/', [AdminNewsController::class, 'index']);
        Route::post('/', [AdminNewsController::class, 'store']);
        Route::get('/categories', [AdminNewsController::class, 'categories']);
        Route::get('/{news}', [AdminNewsController::class, 'show']);
        Route::put('/{news}', [AdminNewsController::class, 'update']);
        Route::delete('/{news}', [AdminNewsController::class, 'destroy']);
        Route::post('/{news}/toggle-published', [AdminNewsController::class, 'togglePublished']);
        Route::post('/{news}/toggle-featured', [AdminNewsController::class, 'toggleFeatured']);
        Route::post('/bulk-action', [AdminNewsController::class, 'bulkAction']);
    });

    // Admin Portfolio Management
    Route::apiResource('portfolios', AdminPortfolioController::class);

    // Portfolio categories management
    Route::prefix('portfolio-categories')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\PortfolioCategoryController::class, 'index']);
        Route::post('/', [App\Http\Controllers\Admin\PortfolioCategoryController::class, 'store']);
        Route::get('/active', [App\Http\Controllers\Admin\PortfolioCategoryController::class, 'active']);
        Route::get('/{portfolioCategory}', [App\Http\Controllers\Admin\PortfolioCategoryController::class, 'show']);
        Route::put('/{portfolioCategory}', [App\Http\Controllers\Admin\PortfolioCategoryController::class, 'update']);
        Route::delete('/{portfolioCategory}', [App\Http\Controllers\Admin\PortfolioCategoryController::class, 'destroy']);
        Route::post('/update-order', [App\Http\Controllers\Admin\PortfolioCategoryController::class, 'updateOrder']);
    });

    // Portfolio categories for admin (legacy endpoint)
    Route::get('/portfolios-categories', [AdminPortfolioController::class, 'categories']);

    // Contact Messages Management
    Route::prefix('contact-messages')->group(function () {
        Route::get('/', [ContactController::class, 'index']);
        Route::get('/stats', [ContactController::class, 'stats']);
        Route::get('/{contactMessage}', [ContactController::class, 'show']);
        Route::patch('/{contactMessage}/mark-read', [ContactController::class, 'markAsRead']);
        Route::delete('/{contactMessage}', [ContactController::class, 'destroy']);
        Route::get('/{contactMessage}/resume', [ContactController::class, 'downloadResume']);
    });

    // Career management
    Route::post('/careers', [CareerController::class, 'store']);
    Route::put('/careers/{career}', [CareerController::class, 'update']);
    Route::delete('/careers/{career}', [CareerController::class, 'destroy']);

    // Team management
    Route::post('/team', [TeamMemberController::class, 'store']);
    Route::put('/team/{teamMember}', [TeamMemberController::class, 'update']);
    Route::delete('/team/{teamMember}', [TeamMemberController::class, 'destroy']);
});
