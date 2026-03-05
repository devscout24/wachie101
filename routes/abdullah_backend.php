<?php

use App\Http\Controllers\AmenityController;
use App\Http\Controllers\OpenAiChatController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\Web\backend\Raihan\AdminBookingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\backend\abdullah\PropertyController;





// ================= Property Management =================
Route::prefix('admin/properties')->name('admin.property.')->group(function () {
    Route::get('/index', [PropertyController::class, 'index'])->name('index');
    Route::get('/create', [PropertyController::class, 'create'])->name('create');
    Route::post('/store', [PropertyController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [PropertyController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [PropertyController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [PropertyController::class, 'destroy'])->name('destroy');
    Route::get('/show/{id}', [PropertyController::class, 'show'])->name('show');
});
// =======================================================

Route::prefix('admin/bookings')->name('admin.booking.')->group(function () {
    Route::get('/index', [AdminBookingController::class, 'index'])->name('index');
    Route::get('/show/{id}', [AdminBookingController::class, 'show'])->name('show');
    Route::post('/update-status', [AdminBookingController::class, 'updateStatus'])->name('updateStatus');
});


// ================= Amenity Management =================
Route::prefix('admin/amenities')->name('admin.amenity.')->group(function () {
    Route::get('/', [AmenityController::class, 'index'])->name('index');
    Route::get('/create', [AmenityController::class, 'create'])->name('create');
    Route::post('/store', [AmenityController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [AmenityController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [AmenityController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [AmenityController::class, 'destroy'])->name('destroy');
});
// =======================================================


// ================= Team Management =================

Route::prefix('admin/teams')->name('admin.team.')->group(function () {
    Route::get('/index', [TeamController::class, 'index'])->name('index');
    Route::get('/create', [TeamController::class, 'create'])->name('create');
    Route::post('/store', [TeamController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [TeamController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [TeamController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [TeamController::class, 'destroy'])->name('destroy');
});


// =======================================================




Route::get('/openai-chat', [OpenAiChatController::class, 'index'])->name('admin.aichat');
Route::post('/openai-chat/send', [OpenAiChatController::class, 'send'])->name('openai.send');
