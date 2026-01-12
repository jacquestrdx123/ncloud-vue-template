<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Vue Login Flow routes for web guard
Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Web\LoginController::class, 'showLoginForm'])->name('web.login');
    Route::post('/login', [\App\Http\Controllers\Web\LoginController::class, 'login'])->name('web.login.post');
});

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [\App\Http\Controllers\Web\LoginController::class, 'logout'])->name('web.logout');

    // vue.users routes
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [App\Http\Controllers\Inertia\User\UserController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Inertia\User\UserController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Inertia\User\UserController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Inertia\User\UserController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\Inertia\User\UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Inertia\User\UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Inertia\User\UserController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-action', [App\Http\Controllers\Inertia\User\UserController::class, 'bulkAction'])->name('bulk-action');
        Route::post('/export', [App\Http\Controllers\Inertia\User\UserController::class, 'export'])->name('export');
    });
});
