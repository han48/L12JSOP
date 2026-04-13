<?php

namespace App\Http\Controllers\Api;

use Illuminate\Auth\Events\Registered;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Fortify;

/**
 * AuthController xử lý xác thực người dùng qua REST API.
 *
 * Cung cấp các endpoint đăng ký, đăng nhập (có hỗ trợ 2FA), và đăng xuất.
 * Tất cả các route này chỉ được kích hoạt khi cấu hình `fortify.user.enable` là `true`.
 *
 * Route prefix: /api (xem routes/api.php)
 * Authentication: Laravel Sanctum (Bearer token)
 *
 * ## Routes được phục vụ
 * - `POST /api/register`  → `register()` — Đăng ký tài khoản mới (public)
 * - `POST /api/login`     → `login()`    — Đăng nhập, lấy token (public)
 * - `POST /api/logout`    → `logout()`   — Đăng xuất, thu hồi token (auth:sanctum)
 *
 * ## Route bổ sung (inline closure trong routes/api.php)
 * - `GET /api/user` — Trả về thông tin user đang xác thực (auth:sanctum).
 *   Route này được định nghĩa trực tiếp trong `routes/api.php` như một closure,
 *   không thông qua controller.
 *
 * @see \routes\api.php — routes được bọc trong `if (config('fortify.user.enable', false))`
 * @see \Laravel\Fortify\Contracts\CreatesNewUsers — action tạo user mới
 * @see \Laravel\Fortify\TwoFactorAuthenticationProvider — xác minh mã 2FA
 */
class AuthController extends Controller
{
    /**
     * Đăng ký tài khoản người dùng mới.
     *
     * Tạo user thông qua Fortify `CreatesNewUsers` action (xem `app/Actions/Fortify/CreateNewUser.php`),
     * phát sự kiện `Registered`, và trả về user cùng Sanctum token.
     *
     * Nếu cấu hình `fortify.lowercase_usernames` được bật, username sẽ được chuyển về chữ thường
     * trước khi tạo.
     *
     * Route: POST /api/register
     * Auth: Không yêu cầu (public endpoint)
     *
     * @param  \Illuminate\Http\Request  $request  Dữ liệu đăng ký (name, email, password, password_confirmation)
     * @param  \Laravel\Fortify\Contracts\CreatesNewUsers  $creator  Action tạo user mới (inject qua DI)
     * @return \Illuminate\Http\JsonResponse  JSON chứa `user` object và `token` string (HTTP 200)
     *
     * @throws \Illuminate\Validation\ValidationException  Nếu email đã tồn tại hoặc password không đủ mạnh (HTTP 422)
     *
     * @see \App\Actions\Fortify\CreateNewUser::create()
     *
     * Validates: Requirements 1.1, 1.3, 1.4, 1.5
     */
    public function register(Request $request, CreatesNewUsers $creator)
    {
        if (config('fortify.lowercase_usernames') && $request->has(Fortify::username())) {
            $request->merge([
                Fortify::username() => Str::lower($request->{Fortify::username()}),
            ]);
        }

        event(new Registered($user = $creator->create($request->all())));

        $token = $user->createToken('API Token')->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Đăng nhập và lấy Sanctum token.
     *
     * Xác thực email và password. Nếu user đã bật 2FA (`two_factor_secret` không rỗng),
     * luồng xác thực diễn ra theo hai bước:
     *
     * **Bước 1 — Gửi credentials (không có `code`):**
     * - Server xác thực email/password.
     * - Nếu 2FA được bật và không có `code` trong request, trả về HTTP 200 với
     *   `{ "requires_2fa": true }` để client biết cần nhập mã OTP.
     *
     * **Bước 2 — Gửi lại với `code`:**
     * - Client gửi lại request với `email`, `password`, và `code` (mã TOTP 6 chữ số).
     * - Server xác minh mã qua `TwoFactorAuthenticationProvider` (Google2FA).
     * - Nếu hợp lệ → trả về `user` và `token`; nếu không → HTTP 401.
     *
     * Route: POST /api/login
     * Auth: Không yêu cầu (public endpoint)
     *
     * @param  \Illuminate\Http\Request  $request  Dữ liệu đăng nhập:
     *   - `email` (string, required): Email của user
     *   - `password` (string, required): Mật khẩu
     *   - `code` (string, optional): Mã TOTP 6 chữ số khi 2FA được bật
     * @return \Illuminate\Http\JsonResponse
     *   - HTTP 200 + `{ user, token }` khi đăng nhập thành công
     *   - HTTP 200 + `{ requires_2fa: true }` khi cần nhập mã 2FA
     *   - HTTP 401 + `{ message }` khi credentials sai hoặc mã 2FA không hợp lệ
     *
     * @throws \Illuminate\Validation\ValidationException  Nếu thiếu email hoặc password (HTTP 422)
     *
     * @see \Laravel\Fortify\TwoFactorAuthenticationProvider::verify()
     * @see \PragmaRX\Google2FA\Google2FA
     *
     * Validates: Requirements 2.1, 2.2, 2.3, 2.4, 2.5
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => __('auth.failed')], 401);
        }

        // Check if 2FA is enabled
        if ($user->two_factor_secret) {
            if (!$request->has('code') || empty($request->code)) {
                return response()->json([
                    'message' => __('auth.two_factor_required'),
                    'requires_2fa' => true
                ], 200);
            }

            $provider = new TwoFactorAuthenticationProvider(new Google2FA());
            if (!$provider->verify(decrypt($user->two_factor_secret), $request->code)) {
                return response()->json(['message' => __('auth.two_factor_invalid')], 401);
            }
        }

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Đăng xuất và thu hồi Sanctum token hiện tại.
     *
     * Xóa access token đang được dùng trong request này. Các token khác của user
     * (nếu có) không bị ảnh hưởng.
     *
     * Route: POST /api/logout
     * Auth: Yêu cầu Bearer token hợp lệ (`auth:sanctum` middleware)
     *
     * @param  \Illuminate\Http\Request  $request  Request đã được xác thực qua Sanctum
     * @return \Illuminate\Http\JsonResponse  HTTP 200 + `{ "message": "Logged out" }`
     *
     * Validates: Requirements 2.6
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
