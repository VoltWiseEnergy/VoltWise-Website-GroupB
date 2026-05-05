<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TariffController;

// Main Page
Route::get('/', function () {
    return view('welcome');
});

// Auth Routes

// Login
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Register
Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // ADMIN TARIFF MANAGEMENT
    Route::prefix('admin/tariff')->group(function () {
        Route::get('/', [TariffController::class, 'index']);
        Route::post('/set', [TariffController::class, 'store']);
        Route::put('/update/{id}', [TariffController::class, 'update']);
        Route::get('/history', [TariffController::class, 'history']);
    });

});