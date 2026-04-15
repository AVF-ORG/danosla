<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ContactApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Simple session-based auth check for the Next.js landing page
// Mirrors Laravel's @auth Blade directive — no Sanctum required
Route::get('/auth/check', function () {
    return response()->json(['authenticated' => Auth::check()]);
});

Route::prefix('v1')->group(function () {
    Route::get('/contact-subjects', [ContactApiController::class, 'subjects']);
    Route::post('/contacts', [ContactApiController::class, 'store']);
});
