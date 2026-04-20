<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\CreditController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TransferController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Admin\AdminController;
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
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('roles-list', [RoleController::class, 'roleList'])->name('roles.list');
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
    Route::patch('expense/{expense}/approve', [ExpenseController::class, 'approve'])->name('expense.approve');
    Route::patch('expense/{expense}/reject', [ExpenseController::class, 'reject'])->name('expense.reject');
    Route::resource('expense', ExpenseController::class);
    // Credits
    Route::get('credit-list', [CreditController::class, 'list'])->name('credit.list');
    Route::resource('credit', CreditController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/download', [ReportController::class, 'download'])->name('reports.download');
    Route::get('reports/timeline-list', [ReportController::class, 'timelineList'])->name('reports.timeline-list');
   
    Route::get('category-list', [CategoryController::class, 'list'])->name('category.list');
    Route::resource('category', CategoryController::class);

    Route::get('item-list', [ItemController::class, 'list'])->name('item.list');
    Route::resource('item', ItemController::class);

    Route::get('transfer-list', [TransferController::class, 'list'])->name('transfer.list');
    Route::resource('transfer', TransferController::class);
    Route::get('activity-logs',       [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');

    // Admin profile (admin area)
    Route::get('admin/profile', [AdminController::class, 'edit'])->name('admin.profile.edit');
    Route::get('admin/profile/password', [AdminController::class, 'password'])->name('admin.profile.password');
    Route::patch('admin/profile', [AdminController::class, 'update'])->name('admin.profile.update');
    Route::post('admin/profile/password', [AdminController::class, 'updatePassword'])->name('admin.profile.updatePassword');
});

require __DIR__.'/auth.php';
