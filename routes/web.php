<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortfolioController;

// API маршруты для портфолио
Route::prefix('api')->group(function () {
    // Публичные API маршруты для портфолио
    Route::get('/portfolios', [PortfolioController::class, 'index']);
    Route::get('/portfolios/featured', [PortfolioController::class, 'featured']);
    Route::get('/portfolios/categories', [PortfolioController::class, 'categories']);
    Route::get('/portfolios/{portfolio}', [PortfolioController::class, 'show']);

    // Админские маршруты (требуют аутентификации)
    Route::middleware(['auth'])->group(function () {
        Route::post('/portfolios', [PortfolioController::class, 'store']);
        Route::put('/portfolios/{portfolio}', [PortfolioController::class, 'update']);
        Route::delete('/portfolios/{portfolio}', [PortfolioController::class, 'destroy']);
    });
});

// SPA route - все маршруты обрабатываются Vue Router
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
