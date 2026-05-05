<?php
 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsageController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\Admin\MasterDeviceController;
use App\Http\Controllers\Admin\AdminDashboardController;

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
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
 
    // Budget Routes
    Route::post('/budget/update', [BudgetController::class, 'update'])->name('budget.update');
    Route::post('/budget/clear',  [BudgetController::class, 'clear'])->name('budget.clear');
 
    // Usage Tracking Routes
    Route::get('/usage/tracker',  [UsageController::class, 'tracker'])->name('usage.tracker');
    Route::post('/usage/default', [UsageController::class, 'setDefault'])->name('usage.setDefault');
    Route::post('/usage/override',[UsageController::class, 'override'])->name('usage.override');
    Route::get('/usage/history',  [UsageController::class, 'history'])->name('usage.history');
    Route::get('/usage/today',    [UsageController::class, 'today'])->name('usage.today');

    // Device Routes
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    Route::get('/devices/create', [DeviceController::class, 'create'])->name('devices.create');
    Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
    Route::get('/devices/{device}/edit', [DeviceController::class, 'edit'])->name('devices.edit');
    Route::put('/devices/{device}', [DeviceController::class, 'update'])->name('devices.update');
    Route::delete('/devices/{device}', [DeviceController::class, 'destroy'])->name('devices.destroy');
 
    /*
    |--------------------------------------------------------------------------
    | Admin Specific Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('master-devices', MasterDeviceController::class);
    });
 
});