<?php

use App\Http\Controllers\UserNotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'web',
    'verified',
])->group(function ($route) {
    $route->prefix('notifications')
        ->name('notifications.')
        ->group(function ($route) {
            $route->get('/', [UserNotificationController::class, 'index'])->name('index');
            $route->get('/unread', [UserNotificationController::class, 'unreadNotification'])->name('unread');
            $route->delete('/removeAll', [UserNotificationController::class, 'removeAll'])->name('remove.all');
            $route->post('/markAllAsRead', [UserNotificationController::class, 'markAllAsRead'])->name('mark.read.all');
            $route->post('/maskNotification', [UserNotificationController::class, 'maskNotification'])->name('mark.read');
        });

    $route->resources([
        'posts' => \App\Http\Controllers\Api\PostController::class,
        'products' => \App\Http\Controllers\Api\ProductController::class,
        'transactions' => \App\Http\Controllers\Api\TransactionController::class,
        // '{{ table }}' => \App\Http\Controllers\Api\{{ class }}Controller::class,
    ]);
});
