<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Http\Responses\AdminLoginResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Http\Requests\LoginRequest;

class AuthenticatedSessionController extends \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController
{

    /**
     * Attempt to authenticate a new session.
     *
     * @param  \Laravel\Fortify\Http\Requests\LoginRequest  $request
     * @return mixed
     */
    public function store(LoginRequest $request)
    {
        return $this->loginPipeline($request)->then(function ($request) {
            if (Str::startsWith($request->getRequestUri(), '/admin/')) {
                return app(AdminLoginResponse::class);
            }
            return app(LoginResponse::class);
        });
    }
}
