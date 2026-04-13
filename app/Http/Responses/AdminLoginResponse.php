<?php

namespace App\Http\Responses;

use Laravel\Fortify\Fortify;

/**
 * Response tùy chỉnh sau khi đăng nhập thành công vào Admin Panel.
 *
 * Class này ghi đè `LoginResponse` mặc định của Laravel Fortify để xử lý
 * redirect sau khi đăng nhập qua đường dẫn `/admin/`. Thay vì redirect về
 * trang mặc định của Fortify, nó chuyển hướng người dùng đến trang chính
 * của Orchid Admin Panel (`platform.main`).
 *
 * Được sử dụng bởi `AuthenticatedSessionController::store()` khi phát hiện
 * request URI bắt đầu bằng `/admin/`.
 *
 * @see \App\Http\Controllers\AuthenticatedSessionController
 * @see \Laravel\Fortify\Http\Responses\LoginResponse
 */
/**
 * Response tùy chỉnh sau khi đăng nhập thành công vào Admin Panel.
 *
 * Ghi đè LoginResponse của Fortify để redirect về `platform.main` (Orchid)
 * thay vì trang mặc định của Fortify.
 *
 * @see \App\Http\Controllers\AuthenticatedSessionController
 */
class AdminLoginResponse extends \Laravel\Fortify\Http\Responses\LoginResponse
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : redirect()->intended(Fortify::redirects('platform.main'));
    }
}
