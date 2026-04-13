<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ContactApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::get('/contact-subjects', [ContactApiController::class, 'subjects']);
    Route::post('/contacts', [ContactApiController::class, 'store']);
});
