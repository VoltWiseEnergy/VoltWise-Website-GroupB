<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
        $hasDevices = false;
        return view('dashboard', compact('hasDevices'));
    })->name('dashboard');
});

