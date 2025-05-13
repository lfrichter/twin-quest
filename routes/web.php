<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// use Inertia\Inertia;

Route::get('/', [ProductController::class, 'index'])->name('products.index');

// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified',
// ])->group(function () {
//     Route::get('/dashboard', function () {
//         return Inertia::render('Dashboard');
//     })->name('dashboard');
// });
