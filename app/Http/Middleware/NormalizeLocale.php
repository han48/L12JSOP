<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

/**
 * Middleware chuẩn hóa giá trị locale từ header Accept-Language.
 *
 * Middleware này tiền xử lý header `Accept-Language` trước khi `LocalizationMiddleware`
 * áp dụng locale. Nó thực hiện các bước chuẩn hóa sau:
 * - Lấy locale đầu tiên trong danh sách (trước dấu phẩy), bỏ qua các locale dự phòng
 * - Thay thế dấu gạch ngang (`-`) bằng dấu gạch dưới (`_`) theo chuẩn PHP locale
 * - Kiểm tra tính hợp lệ bằng regex; nếu không hợp lệ, fallback về `config('app.locale')`
 * - Ghi đè lại header `Accept-Language` với giá trị đã chuẩn hóa
 *
 * Ví dụ: `en-US, fr;q=0.9` → `en_US`
 *
 * Vai trò trong request lifecycle:
 * - Phải chạy trước `LocalizationMiddleware` trong middleware stack
 * - Đảm bảo `LocalizationMiddleware` luôn nhận được giá trị locale hợp lệ
 *
 * @see \App\Http\Middleware\LocalizationMiddleware
 * @satisfies Requirement 15.2
 */
/**
 * Middleware chuẩn hóa giá trị locale từ header Accept-Language.
 *
 * Lấy locale đầu tiên, thay `-` bằng `_`, validate bằng regex, fallback về
 * `config('app.locale')` nếu không hợp lệ. Ví dụ: `en-US, fr;q=0.9` → `en_US`.
 *
 * @see \App\Http\Middleware\LocalizationMiddleware
 * @satisfies Requirement 15.2
 */
class NormalizeLocale
{
    /**
     * Chuẩn hóa giá trị locale trong header Accept-Language và chuyển tiếp request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $header = $request->header('Accept-Language');

        if ($header) {
            // Take only the first locale before comma
            $locale = explode(',', $header)[0];

            // Normalize: replace hyphens with underscores
            $locale = str_replace('-', '_', $locale);

            // Validate: fallback if invalid
            if (!preg_match('/^[a-zA-Z_]+$/', $locale)) {
                $locale = config('app.locale');
            }

            $request->headers->set('Accept-Language', $locale);
        }

        return $next($request);
    }
}
