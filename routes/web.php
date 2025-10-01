<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Admin routes - completely isolated
Route::prefix('admin')->group(function () {
    // Admin login and dashboard routes (serve admin.blade.php template)
    Route::get('/login', function () {
        return view('admin');
    });

    Route::get('/{any?}', function () {
        return view('admin');
    })->where('any', '.*');
});

// Main site SPA route - all other routes handled by main Vue Router
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
