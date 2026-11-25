<?php

// =====================================================
//  Required Classes / Imports
// =====================================================

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\LogActivity;
use App\Http\Controllers\FileController;
use App\Http\Controllers\AppNotificationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\RBAC\ModuleController;
use App\Http\Controllers\Backend\RBAC\RoleController;
use App\Http\Controllers\Backend\RBAC\RoleModuleController;
use App\Http\Controllers\Backend\RBAC\RolePermissionController;
use App\Http\Controllers\Backend\Address\DivisionController;
use App\Http\Controllers\Backend\Address\DistrictController;
use App\Http\Controllers\Backend\Address\UpazilaController;
use App\Http\Controllers\Backend\Example\ExampleController;

// =====================================================
//  Localization & Route Configuration
// =====================================================
Route::get('/locale.json', [SupportController::class, 'getLocalization']);
Route::get('/update.json', [SupportController::class, 'addLocalization']);
Route::get('/routes.json', [SupportController::class, 'getRoutes']);
Route::get('/load.json' ,[SupportController::class, 'loadJson']);

// =====================================================
//  Guest Routes (Authentication)
// =====================================================
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('login', [AuthController::class, 'doLogin'])->name('login.submit');
});

// =====================================================
//  Authenticated Application Routes
// =====================================================
Route::middleware([AuthMiddleware::class, LogActivity::class])->group(function () {
    Route::get('/admin/{any?}', [DashboardController::class, 'singleApp'])->where('any', '.*')->name('home');
    Route::get('/auth/{any?}', [DashboardController::class, 'employeeApp'])->where('any', '.*')->name('employee_home');
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');

    // =================================================
    // All routes in this group start with /api
    // =================================================
    Route::prefix('api')->group(function () {

        // General Data, Dashboard & File Management
        Route::post('file_upload', [FileController::class, 'fileUpload']);
        Route::post('general', [SupportController::class, 'getGeneralData']);
        Route::post('configurations', [SupportController::class, 'appConfigurations']);
        Route::get('dashboard', [SupportController::class, 'appDashboard']);
        Route::get('get_dashboard', [DashboardController::class, 'dashboardData']);
        Route::get('activities', [SupportController::class, 'userActivities']);

        // User Profile, User Management & Notifications
        Route::post('profile', [AuthController::class, 'update']);
        Route::resource('profile', AuthController::class);
        Route::resource('users', UserController::class);
        Route::resource('app_notification', AppNotificationController::class);
        Route::resource('settings', SettingController::class);

        // ---------------------------------------------
        // RBAC: Role-Based Access Control
        // ---------------------------------------------
        Route::resource('modules', ModuleController::class);
        Route::resource('/roles', RoleController::class);
        Route::resource('/module_permissions', RoleModuleController::class);
        Route::resource('/role_permissions', RolePermissionController::class);

        // ---------------------------------------------
        // Address Management
        // ---------------------------------------------
        Route::resource('/division', DivisionController::class);
        Route::resource('/district', DistrictController::class);
        Route::resource('/upazila', UpazilaController::class);


        Route::resource('/example', ExampleController::class);

    });
});
