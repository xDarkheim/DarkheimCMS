<?php

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
use App\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\OrganizationDataController;

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
Route::post('/login', [AuthController::class, 'login'])->middleware('brute-force');
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
    Route::get('/search', [NewsController::class, 'search']);
    Route::get('/archive', [NewsController::class, 'archive']);
    Route::get('/archive/{year}/{month}', [NewsController::class, 'archiveDate']);
    Route::get('/sitemap', [NewsController::class, 'sitemap']);
    Route::get('/all-categories', [NewsController::class, 'allCategories']);
    Route::get('/categories', [NewsController::class, 'allCategories']); // Keep both for compatibility
    Route::get('/category/{category}', [NewsController::class, 'byCategory']);
    Route::get('/category-stats', [NewsController::class, 'categoryStats']);
    Route::get('/tags', [NewsController::class, 'tags']);
    Route::get('/stats', [NewsController::class, 'stats']);
    Route::get('/{slug}', [NewsController::class, 'show']);
    Route::get('/{slug}/related', [NewsController::class, 'related']);
});

// Public Contact Form
Route::post('/contact', [ContactController::class, 'submit'])->middleware('file-security');

// Public Statistics
Route::get('/stats', [App\Http\Controllers\Api\StatsController::class, 'public']);

// Public Company Info
Route::get('/company-info', [App\Http\Controllers\Admin\CompanyInfoController::class, 'public']);

// New dedicated contact info endpoint
Route::get('/contact-info', [App\Http\Controllers\Api\CompanyInfoController::class, 'index']);

// Career routes
Route::get('/careers', [CareerController::class, 'index']);
Route::get('/careers/{career}', [CareerController::class, 'show']);

// Team routes
Route::get('/team', [TeamMemberController::class, 'index']);
Route::get('/team/{teamMember}', [TeamMemberController::class, 'show']);

// Admin Routes (protected by Sanctum)
Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);
    Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats']);
    Route::get('/dashboard/recent-activity', [AdminDashboardController::class, 'recentActivity']);

    // Legacy stats endpoint (keep for backward compatibility)
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
        Route::get('/', [AdminContactMessageController::class, 'index']);
        Route::get('/stats', [AdminContactMessageController::class, 'stats']);
        Route::get('/{contactMessage}', [AdminContactMessageController::class, 'show']);
        Route::post('/{contactMessage}/mark-as-read', [AdminContactMessageController::class, 'markAsRead']);
        Route::delete('/{contactMessage}', [AdminContactMessageController::class, 'destroy']);
        Route::get('/{contactMessage}/resume', [AdminContactMessageController::class, 'downloadResume']);
        Route::post('/bulk-mark-as-read', [AdminContactMessageController::class, 'bulkMarkAsRead']);
        Route::post('/bulk-delete', [AdminContactMessageController::class, 'bulkDelete']);
    });

    // Career management
    Route::post('/careers', [CareerController::class, 'store']);
    Route::put('/careers/{career}', [CareerController::class, 'update']);
    Route::delete('/careers/{career}', [CareerController::class, 'destroy']);

    // Team management
    Route::get('/team', [TeamMemberController::class, 'index']); // Добавляем GET для загрузки списка
    Route::post('/team', [TeamMemberController::class, 'store']);
    Route::put('/team/{teamMember}', [TeamMemberController::class, 'update']);
    Route::delete('/team/{teamMember}', [TeamMemberController::class, 'destroy']);
    Route::post('/team/{teamMember}/toggle-visible', [TeamMemberController::class, 'toggleVisible']);

    // Company Info Management
    Route::prefix('company-info')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\CompanyInfoController::class, 'index']);
        Route::post('/', [App\Http\Controllers\Admin\CompanyInfoController::class, 'store']);
        Route::get('/{companyInfo}', [App\Http\Controllers\Admin\CompanyInfoController::class, 'show']);
        Route::put('/{companyInfo}', [App\Http\Controllers\Admin\CompanyInfoController::class, 'update']);
        Route::delete('/{companyInfo}', [App\Http\Controllers\Admin\CompanyInfoController::class, 'destroy']);
        Route::post('/update-order', [App\Http\Controllers\Admin\CompanyInfoController::class, 'updateOrder']);
    });

    // Settings Management
    Route::prefix('settings')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SettingsController::class, 'index']);
        Route::get('/group/{group}', [App\Http\Controllers\Admin\SettingsController::class, 'getByGroup']);
        Route::put('/group/{group}', [App\Http\Controllers\Admin\SettingsController::class, 'updateGroup']);
        Route::put('/{key}', [App\Http\Controllers\Admin\SettingsController::class, 'update']);
        Route::delete('/{key}', [App\Http\Controllers\Admin\SettingsController::class, 'destroy']);
        Route::post('/reset-defaults', [App\Http\Controllers\Admin\SettingsController::class, 'resetToDefaults']);
    });

    // File Manager
    Route::prefix('files')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\FileManagerController::class, 'index']);
        Route::post('/upload', [App\Http\Controllers\Admin\FileManagerController::class, 'upload'])->middleware('file-security');
        Route::post('/directory', [App\Http\Controllers\Admin\FileManagerController::class, 'createDirectory']);
        Route::delete('/', [App\Http\Controllers\Admin\FileManagerController::class, 'delete']);
    });

    // Activity Logs
    Route::prefix('activity-logs')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ActivityLogController::class, 'index']);
        Route::get('/stats', [App\Http\Controllers\Admin\ActivityLogController::class, 'stats']);
        Route::get('/filter-options', [App\Http\Controllers\Admin\ActivityLogController::class, 'filterOptions']);
        Route::post('/export', [App\Http\Controllers\Admin\ActivityLogController::class, 'export']);
        Route::post('/cleanup', [App\Http\Controllers\Admin\ActivityLogController::class, 'cleanup']);
    });
});

// Public settings endpoint
Route::get('/settings/public', [App\Http\Controllers\Admin\SettingsController::class, 'getPublic']);

// Organization Data API routes (public access)
Route::prefix('organization')->group(function () {
    Route::get('/departments', [OrganizationDataController::class, 'departments']);
    Route::get('/positions', [OrganizationDataController::class, 'positions']);
    Route::get('/skills', [OrganizationDataController::class, 'skills']);
    Route::get('/employment-types', [OrganizationDataController::class, 'employmentTypes']);
    Route::get('/experience-levels', [OrganizationDataController::class, 'experienceLevels']);
    Route::get('/locations', [OrganizationDataController::class, 'locations']);
    Route::get('/statuses', [OrganizationDataController::class, 'statuses']);
    Route::get('/data-types', [OrganizationDataController::class, 'dataTypes']);
    Route::get('/', [OrganizationDataController::class, 'index']);
});

// Admin Organization Data Management
Route::middleware(['auth:sanctum'])->prefix('admin/organization')->group(function () {
    Route::get('/', [OrganizationDataController::class, 'index']);
    Route::post('/', [OrganizationDataController::class, 'store']);
    Route::put('/{organizationData}', [OrganizationDataController::class, 'update']);
    Route::delete('/{organizationData}', [OrganizationDataController::class, 'destroy']);
    Route::post('/update-order', [OrganizationDataController::class, 'updateOrder']);
});
