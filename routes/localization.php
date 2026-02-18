<?php

use App\Http\Controllers\Dashboard\Localization\KeyController;
use App\Http\Controllers\Dashboard\Localization\LanguageController;
use App\Http\Controllers\Dashboard\Localization\TranslationController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')
    ->prefix('dashboard/localization')
    ->name('dashboard.localization.')
    ->group(function () {

        Route::resource('languages', LanguageController::class)
            ->except(['show']);

        Route::resource('keys', KeyController::class)
            ->parameters(['keys' => 'translationKey'])
            ->except(['show']);

        Route::resource('translations', TranslationController::class)
            ->only(['index', 'edit', 'update'])
            ->parameters(['translations' => 'translationKey']);
    });
