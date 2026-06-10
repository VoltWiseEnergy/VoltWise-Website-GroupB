<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsageController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\PointsController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\Admin\MasterDeviceController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Forum\ForumPostController;
use App\Http\Controllers\SimulatorController;
use App\Http\Controllers\Admin\ForumModerationController;
use App\Http\Controllers\CompareController; // Make sure to import or double-check these namespaces
use App\Http\Controllers\TariffController;

// Main Page
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Auth Routes (Public)
|--------------------------------------------------------------------------
*/
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes (Authenticated Users)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // User Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/profile',            [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile',            [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar',    [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/password',  [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Budget Routes
    Route::post('/budget/update', [BudgetController::class, 'update'])->name('budget.update');
    Route::post('/budget/clear',  [BudgetController::class, 'clear'])->name('budget.clear');

    // Usage Tracking Routes
    Route::get('/usage/tracker',  [UsageController::class, 'tracker'])->name('usage.tracker');
    Route::post('/usage/default', [UsageController::class, 'setDefault'])->name('usage.setDefault');
    Route::post('/usage/override',[UsageController::class, 'override'])->name('usage.override');
    Route::get('/usage/history',  [UsageController::class, 'history'])->name('usage.history');
    Route::get('/usage/today',    [UsageController::class, 'today'])->name('usage.today');

    // Points / Gamification Routes
    Route::get('/points', [PointsController::class, 'index'])->name('points.index');
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');

    // Device Routes
    Route::get('/devices',               [DeviceController::class, 'index'])->name('devices.index');
    Route::get('/devices/create',        [DeviceController::class, 'create'])->name('devices.create');
    Route::post('/devices',              [DeviceController::class, 'store'])->name('devices.store');
    Route::get('/devices/{device}/edit', [DeviceController::class, 'edit'])->name('devices.edit');
    Route::put('/devices/{device}',      [DeviceController::class, 'update'])->name('devices.update');
    Route::delete('/devices/{device}',   [DeviceController::class, 'destroy'])->name('devices.destroy');
    Route::post('/devices/reminder',     [DeviceController::class, 'scheduleReminder'])->name('devices.reminders.schedule');
    Route::get('/devices/{device}/reminder/edit', [DeviceController::class, 'editReminder'])->name('devices.reminders.edit');
    Route::put('/devices/{device}/reminder', [DeviceController::class, 'updateReminder'])->name('devices.reminders.update');
    Route::delete('/devices/{device}/reminder', [DeviceController::class, 'destroyReminder'])->name('devices.reminders.destroy');

    // Recommendation Routes
    Route::get('/recommendations', [RecommendationController::class, 'index'])->name('recommendations.index');
    Route::post('/recommendations/toggle', [RecommendationController::class, 'toggle'])->name('recommendations.toggle');

    // Forum Routes
    Route::get('/forum', [ForumPostController::class, 'index'])->name('forum.index');
    Route::get('/forum/create', [ForumPostController::class, 'create'])->name('forum.create');
    Route::post('/forum', [ForumPostController::class, 'store'])->name('forum.store');
    Route::get('/forum/{id}', [ForumPostController::class, 'show'])->name('forum.show');
    Route::post('/forum/{id}/comment', [ForumPostController::class, 'storeComment'])->name('forum.comment.store');
    
    // Forum Post Management & Actions
    Route::post('/forum/{id}/report', [ForumPostController::class, 'report'])->name('forum.report'); // PBI #55
    Route::get('/forum/{id}/edit', [ForumPostController::class, 'edit'])->name('forum.edit');
    Route::put('/forum/{id}', [ForumPostController::class, 'update'])->name('forum.update');
    Route::delete('/forum/{id}', [ForumPostController::class, 'destroy'])->name('forum.destroy');
    
    // Forum Post Voting
    Route::post('/forum/{id}/upvote', [ForumPostController::class, 'upvote'])->name('forum.upvote');
    Route::post('/forum/{id}/downvote', [ForumPostController::class, 'downvote'])->name('forum.downvote');

    // Energy Cost Simulator Routes
    Route::get('/simulator',              [SimulatorController::class, 'index'])->name('simulator.index');   // PBI #56
    Route::post('/simulator',             [SimulatorController::class, 'store'])->name('simulator.store');   // PBI #56 + #57
    Route::get('/simulator/{scenario}',   [SimulatorController::class, 'show'])->name('simulator.show');    // PBI #58
    Route::delete('/simulator/{scenario}',[SimulatorController::class, 'destroy'])->name('simulator.destroy');

    // Device Comparison (PBI #33, #34, #35)
    Route::get('/compare', [CompareController::class, 'index'])->name('compare.index');
    Route::post('/compare/results', [CompareController::class, 'compare'])->name('compare.results');

    // Admin Tariff Management
    Route::prefix('admin/tariff')->group(function () {
        Route::get('/', [TariffController::class, 'index']);
        Route::post('/set', [TariffController::class, 'store']);
        Route::put('/update/{id}', [TariffController::class, 'update']);
        Route::get('/history', [TariffController::class, 'history']);
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Specific Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('master-devices', MasterDeviceController::class);
        
        // Forum Moderation (PBI #52-55)
        Route::get('/forum', [ForumModerationController::class, 'index'])->name('forum.index');
        Route::get('/forum/reports', [ForumModerationController::class, 'reports'])->name('forum.reports');
        Route::post('/forum/reports/{report}/review', [ForumModerationController::class, 'reviewReport'])->name('forum.reports.review');
        Route::delete('/forum/{post}', [ForumModerationController::class, 'destroy'])->name('forum.destroy');
        Route::post('/forum/{post}/verify', [ForumModerationController::class, 'toggleVerified'])->name('forum.verify');
    });
});