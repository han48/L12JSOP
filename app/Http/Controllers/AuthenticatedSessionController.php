<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Http\Responses\AdminLoginResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Http\Requests\LoginRequest;

/**
 * Controller xử lý đăng nhập và đăng xuất phiên làm việc.
 *
 * Kế thừa từ `AuthenticatedSessionController` của Laravel Fortify và ghi đè
 * phương thức `store()` để phân biệt luồng đăng nhập giữa Admin Panel và
 * ứng dụng web thông thường.
 *
 * Khi request URI bắt đầu bằng `/admin/`, controller sử dụng `AdminLoginResponse`
 * để redirect về trang chính của Orchid (`platform.main`). Với các request khác,
 * sử dụng `LoginResponse` mặc định của Fortify.
 *
 * @see \App\Http\Responses\AdminLoginResponse
 * @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController
 */
/**
 * Controller xử lý đăng nhập phiên làm việc.
 *
 * Ghi đè `store()` của Fortify để phân biệt luồng đăng nhập: nếu URI bắt đầu
 * bằng `/admin/`, dùng AdminLoginResponse (redirect về Orchid); ngược lại dùng
 * LoginResponse mặc định của Fortify.
 *
 * @see \App\Http\Responses\AdminLoginResponse
 */
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
