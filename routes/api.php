<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LocaleController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\TranslationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Locales
    Route::apiResource('locales', LocaleController::class);

    // Tags
    Route::apiResource('tags', TagController::class);

    // Translations
    Route::apiResource('translations', TranslationController::class)->except(['show']);
    Route::get('/translations/search', [TranslationController::class, 'search']);
    Route::get('/translations/export', [TranslationController::class, 'export']);
    Route::get('/translations/{translation}', [TranslationController::class, 'show'])->name('translations.show');
});
