<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\Pages\LoginController;
use App\Http\Controllers\Auth\Pages\RegisterController;
use App\Http\Controllers\Auth\Pages\ForgotPasswordController;
use App\Http\Controllers\Auth\Pages\ResetPasswordController;

use App\Http\Controllers\Auth\Actions\LoginAction;
use App\Http\Controllers\Auth\Actions\RegisterAction;
use App\Http\Controllers\Auth\Actions\ForgotPasswordAction;
use App\Http\Controllers\Auth\Actions\LogoutAction;
use App\Http\Controllers\Auth\Actions\ResetPasswordAction;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [
        'localize',
        'localizationRedirect',
        'localeSessionRedirect',
        'localeViewPath',
    ],
], function () {
    Route::middleware('guest')->group(function () {

        Route::get('/login', LoginController::class)->name('login');
        Route::get('/register', RegisterController::class)->name('register');

        Route::get('/forgot-password', ForgotPasswordController::class)->name('password.request');
        Route::get('/reset-password/{token}', ResetPasswordController::class)->name('password.reset');

        // Actions (POST)
        Route::post('/login', LoginAction::class)->name('login.store');
        Route::post('/register', RegisterAction::class)->name('register.store');

        Route::post('/forgot-password', ForgotPasswordAction::class)->name('password.email');
        Route::post('/reset-password', ResetPasswordAction::class)->name('password.update');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', LogoutAction::class)->name('logout');
    });
});
