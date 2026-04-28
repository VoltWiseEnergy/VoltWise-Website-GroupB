<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsageController;

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
    
    
 // Usage Tracking 
 Route::post('/usage/default', [UsageController::class, 'setDefault'])->name('usage.setDefault');
 Route::post('/usage/override', [UsageController::class, 'override'])->name('usage.override');
 Route::get('/usage/history', [UsageController::class, 'history'])->name('usage.history');
 Route::get('/usage/today', [UsageController::class, 'today'])->name('usage.today');    
});

