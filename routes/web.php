<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

if (config('fortify.home_page.enable', false)) {
    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'hasPostIndex' => Route::has('posts.index'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'appVersion' => \App\Application::version(),
            'appName' => \App\Application::name(),
        ]);
    })->name('welcome');
}

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    if (config('fortify.user.enable', false)) {
        Route::get('/dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');
    }
});

Route::middleware([
    'web',
])->group(function ($route) {
    $route->resources([
        // TODO for DEV: enable web route
        'posts' => \App\Http\Controllers\Api\PostController::class,
        // '{{ table }}' => \App\Http\Controllers\Api\{{ class }}Controller::class,
    ]);
});
