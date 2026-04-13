<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

/**
 * Middleware thiết lập locale của ứng dụng từ header Accept-Language.
 *
 * Middleware này đọc giá trị từ HTTP header `Accept-Language` trong request
 * và gọi `App::setLocale()` để áp dụng ngôn ngữ tương ứng cho toàn bộ
 * request hiện tại. Nếu header không tồn tại, locale mặc định của ứng dụng
 * (cấu hình trong `config/app.php`) sẽ được giữ nguyên.
 *
 * Middleware này thường được đặt sau `NormalizeLocale` trong pipeline để
 * đảm bảo giá trị locale đã được chuẩn hóa trước khi áp dụng.
 *
 * Vai trò trong request lifecycle:
 * - Chạy sớm trong middleware stack, trước khi controller xử lý request
 * - Cho phép ứng dụng trả về nội dung đa ngôn ngữ dựa trên preference của client
 *
 * @see \App\Http\Middleware\NormalizeLocale
 * @satisfies Requirement 15.1
 */
/**
 * Middleware thiết lập locale từ header Accept-Language.
 *
 * Đọc header `Accept-Language` và gọi `App::setLocale()`. Thường chạy sau
 * NormalizeLocale trong middleware stack.
 *
 * @see \App\Http\Middleware\NormalizeLocale
 * @satisfies Requirement 15.1
 */
class LocalizationMiddleware
{
    /**
     * Xử lý request đến và thiết lập locale từ header Accept-Language.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->hasHeader('Accept-Language')) {
            App::setLocale($request->header('Accept-Language'));
        }
        return $next($request);
    }
}
