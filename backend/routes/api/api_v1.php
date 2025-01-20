<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PublicController;

// Public routes
Route::controller(PublicController::class)
->prefix('public')
->name('public.')
->group(function () {
    Route::get('/announcements', 'announcements')->name('announcements ');
});

Route::controller(AuthController::class)
->prefix('auth')
->name('auth.')
->group(function () {
    Route::post('/register', 'register')->name('register');
    Route::post('/login', 'login')->name('login');
});

// Private routes
Route::middleware('auth:sanctum')->group(function () {
    
    Route::controller(AuthController::class)
        ->prefix('auth')
        ->name('auth.')
        ->group(function () {
            Route::get('/profile', 'profile')->name('profile');
            Route::put('/details', 'updateDetails')->name('update-details');
            Route::put('/password', 'updatePassword')->name('update-password');
            Route::get('/logout', 'logout')->name('logout');
        });

    Route::controller(AnnouncementController::class)
        ->prefix('announcements')
        ->name('announcements.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/{announcementId}', 'show')->name('show');
            Route::put('/{announcementId}', 'update')->name('update');
            Route::put('/dates/{announcementId}', 'updateDates')->name('update-dates');
            Route::delete('/{announcementId}', 'destroy')->name('destroy');
        });

    Route::controller(UserController::class)
        ->prefix('users')
        ->name('users.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/{userId}', 'show')->name('show');
            Route::put('/details/{userId}', 'updateDetails')->name('update-details');
            Route::put('/password/{userId}', 'updatePassword')->name('update-password');
            Route::delete('/{userId}', 'destroy')->name('destroy');
        });
});