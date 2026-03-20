<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ExpenseController;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/clear-caches', function () {
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:cache');
    Artisan::call('config:cache');
    Artisan::call('optimize:clear');
    return 'Caches cleared and optimized!';
});

Route::group(['middleware' => ['auth']], function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('roles-list', [RoleController::class, 'index'])->name('roles.list');
    Route::resource('roles', RoleController::class);
    Route::get('permissions-list', [PermissionController::class, 'permissionsList'])->name('permissions.list');
    Route::resource('permissions', PermissionController::class);
    Route::get('userList', [UserController::class, 'userList'])->name('users.list');
    Route::resource('users', UserController::class);
    // Projects
    Route::get('projects-list', [ProjectController::class, 'list'])->name('projects.list');
    Route::resource('projects', ProjectController::class);
    // Expenses
    Route::get('expense-list', [ExpenseController::class, 'list'])->name('expense.list');
    Route::resource('expense', ExpenseController::class);
});

require __DIR__.'/auth.php';
