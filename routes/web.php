<?php

use Illuminate\Support\Facades\Route;
use LaravelBuilder\VisualBuilder\Http\Controllers\BuilderController;
use LaravelBuilder\VisualBuilder\Http\Controllers\ApiBuilderController;
use LaravelBuilder\VisualBuilder\Http\Controllers\AuthBuilderController;
use LaravelBuilder\VisualBuilder\Http\Controllers\ComponentController;

Route::middleware(['web', 'auth'])->prefix('builder')->name('builder.')->group(function () {
    // Dashboard
    Route::get('/', [BuilderController::class, 'dashboard'])->name('dashboard');

    // Pages
    Route::prefix('pages')->name('pages.')->group(function () {
        Route::get('/', [BuilderController::class, 'index'])->name('index');
        Route::get('/create', [BuilderController::class, 'create'])->name('create');
        Route::post('/', [BuilderController::class, 'store'])->name('store');
        Route::get('/{page}', [BuilderController::class, 'show'])->name('show');
        Route::get('/{page}/edit', [BuilderController::class, 'edit'])->name('edit');
        Route::put('/{page}', [BuilderController::class, 'update'])->name('update');
        Route::delete('/{page}', [BuilderController::class, 'destroy'])->name('destroy');
    });

    // API Builder
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/', [ApiBuilderController::class, 'index'])->name('index');
        Route::post('/generate', [ApiBuilderController::class, 'generate'])->name('generate');
        Route::get('/documentation', [ApiBuilderController::class, 'documentation'])->name('documentation');
        Route::post('/export/postman', [ApiBuilderController::class, 'exportPostman'])->name('export.postman');
        Route::post('/export/openapi', [ApiBuilderController::class, 'exportOpenApi'])->name('export.openapi');
    });

    // Auth Builder
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::get('/', [AuthBuilderController::class, 'index'])->name('index');
        Route::post('/generate', [AuthBuilderController::class, 'generate'])->name('generate');
        Route::post('/roles', [AuthBuilderController::class, 'generateRoles'])->name('roles');
        Route::post('/permissions', [AuthBuilderController::class, 'generatePermissions'])->name('permissions');
        Route::post('/social', [AuthBuilderController::class, 'generateSocialAuth'])->name('social');
        Route::post('/2fa', [AuthBuilderController::class, 'generateTwoFactorAuth'])->name('2fa');
    });

    // Components
    Route::prefix('components')->name('components.')->group(function () {
        Route::get('/', [ComponentController::class, 'index'])->name('index');
        Route::post('/', [ComponentController::class, 'store'])->name('store');
        Route::get('/{component}', [ComponentController::class, 'show'])->name('show');
        Route::put('/{component}', [ComponentController::class, 'update'])->name('update');
        Route::delete('/{component}', [ComponentController::class, 'destroy'])->name('destroy');
        Route::post('/export', [ComponentController::class, 'export'])->name('export');
        Route::post('/import', [ComponentController::class, 'import'])->name('import');
    });

    // AI Features
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::post('/suggest', [BuilderController::class, 'aiSuggest'])->name('suggest');
        Route::post('/generate/crud', [BuilderController::class, 'generateCrud'])->name('generate.crud');
        Route::post('/generate/validation', [BuilderController::class, 'generateValidation'])->name('generate.validation');
        Route::post('/generate/seeder', [BuilderController::class, 'generateSeeder'])->name('generate.seeder');
        Route::post('/optimize', [BuilderController::class, 'optimizeCode'])->name('optimize');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [BuilderController::class, 'settings'])->name('index');
        Route::put('/', [BuilderController::class, 'updateSettings'])->name('update');
        Route::post('/theme', [BuilderController::class, 'updateTheme'])->name('theme');
        Route::post('/export', [BuilderController::class, 'exportSettings'])->name('export');
        Route::post('/import', [BuilderController::class, 'importSettings'])->name('import');
    });
}); 