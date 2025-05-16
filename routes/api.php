<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\RegistrationController; // Add this line
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/products', [ProductController::class, 'index'])->name('api.products.index');

// Route for email validation (STORY-002 / T2.8)
Route::post('/validate-email', [RegistrationController::class, 'validateEmail'])->name('api.validate.email');
