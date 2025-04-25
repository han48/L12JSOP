<?php

use App\Http\Controllers\UserNotificationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::prefix('notifications')
        ->name('notifications.')
        ->group(function ($route) {
            $route->get('/', [UserNotificationController::class, 'index'])->name('index');
            $route->get('/unread', [UserNotificationController::class, 'unreadNotification'])->name('unread');
            $route->delete('/removeAll', [UserNotificationController::class, 'removeAll'])->name('remove.all');
            $route->post('/markAllAsRead', [UserNotificationController::class, 'markAllAsRead'])->name('mark.read.all');
            $route->post('/maskNotification', [UserNotificationController::class, 'maskNotification'])->name('mark.read');
        });
});
