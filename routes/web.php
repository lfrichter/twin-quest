<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

// use Inertia\Inertia;

Route::get('/', [ProductController::class, 'index'])->name('products.index');
Route::get('/registrations/create', [RegistrationController::class, 'create'])->name('registrations.create');
Route::post('/registrations', [RegistrationController::class, 'store'])->name('registrations.store');
// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified',
// ])->group(function () {
//     Route::get('/dashboard', function () {
//         return Inertia::render('Dashboard');
//     })->name('dashboard');
// });
