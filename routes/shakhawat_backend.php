<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\backend\shakhawat\BackendController;
use App\Http\Controllers\Web\backend\shakhawat\UserController;
use App\Http\Controllers\Web\backend\shakhawat\SettingController;
use App\Http\Controllers\Web\backend\shakhawat\DynamicPagesController;
use App\Http\Controllers\Web\backend\shakhawat\ProfileSettingController;
use App\Http\Controllers\Web\backend\shakhawat\SystemSettingController;
use App\Http\Controllers\Web\backend\shakhawat\AdminSettingController;
use App\Http\Controllers\Web\backend\shakhawat\RoleController;
use App\Http\Controllers\Web\backend\shakhawat\PermissionController;



Route::middleware(['auth:sanctum',config('jetstream.auth_session'),'verified'])->group(function () {
    Route::get('/dashboard', [BackendController::class, 'index'])->name('dashboard');
    //User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/data', [UserController::class, 'getData'])->name('users.data');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    //setting Management
    Route::get('/mail', [SettingController::class, 'mail'])->name('settings.mail');
    Route::post('/mail/store', [SettingController::class, 'mailstore'])->name('settings.mailstore');

    //dynamic pages Management
    Route::get('/dynamicpage', [DynamicPagesController::class, 'index'])->name('dynamicpage.index');
    Route::get('/dynamicpage/data', [DynamicPagesController::class, 'getData'])->name('dynamicpage.data');
    Route::get('/dynamicpage/create', [DynamicPagesController::class, 'create'])->name('dynamicpage.create');
    Route::post('/dynamicpage', [DynamicPagesController::class, 'store'])->name('dynamicpage.store');
    Route::get('/dynamicpage/{id}/edit', [DynamicPagesController::class, 'edit'])->name('dynamicpage.edit');
    Route::put('/dynamicpage/{id}', [DynamicPagesController::class, 'update'])->name('dynamicpage.update');
    Route::delete('/dynamicpage/{id}', [DynamicPagesController::class, 'destroy'])->name('dynamicpage.destroy');
    // Route::post('/dynamicpage/{id}/toggle-status', [DynamicPagesController::class, 'toggleStatus'])->name('dynamicpage.toggleStatus');

    //Profile Settings
    Route::get('/profile_upload/index', [ProfileSettingController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileSettingController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password/update', [ProfileSettingController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/profile/avatar', [ProfileSettingController::class, 'updatePhoto'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [ProfileSettingController::class, 'removeAvatar'])->name('profile.avatar.remove');

    //System Settings
    Route::get('/system-settings', [SystemSettingController::class, 'edit'])->name('system-settings.edit');
    Route::post('/system-settings', [SystemSettingController::class, 'update'])->name('system-settings.update');

    //Admin Settings
    Route::get('/admin/settings', [AdminSettingController::class, 'edit'])->name('admin.settings.edit');
    Route::post('/admin/settings', [AdminSettingController::class, 'update'])->name('admin.settings.update');

    //Role Management
    Route::get('role', [RoleController::class, 'index'])->name('role.index');
    Route::post('role/store', [RoleController::class, 'store'])->name('role.store');
    Route::get('role/{id}/edit', [RoleController::class, 'edit'])->name('role.edit');
    Route::post('role/update/{id}', [RoleController::class, 'update'])->name('role.update');
    Route::get('role/permission/{id}', [RoleController::class, 'permission'])->name('role.permission');
    Route::post('role/set-permission', [RoleController::class, 'setPermission'])->name('role.setPermission');
    Route::delete('role/delete/{id}', [RoleController::class, 'destroy'])->name('role.destroy');


});