<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ContactApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Simple session-based auth check for the Next.js landing page.
// Must use 'web' middleware so Laravel starts the session and can read
// the laravel-session cookie — the 'api' group is stateless by default.
Route::middleware('web')->get('/auth/check', function () {
    return response()->json(['authenticated' => Auth::check()]);
});

Route::prefix('v1')->group(function () {
    Route::get('/contact-subjects', [ContactApiController::class, 'subjects']);
    Route::post('/contacts', [ContactApiController::class, 'store']);
});
