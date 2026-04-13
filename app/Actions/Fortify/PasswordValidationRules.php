<?php

namespace App\Actions\Fortify;

use Illuminate\Validation\Rules\Password;

/**
 * Trait cung cấp các validation rules cho mật khẩu.
 *
 * Được sử dụng bởi các action liên quan đến mật khẩu:
 * CreateNewUser, UpdateUserPassword, ResetUserPassword.
 * Áp dụng Laravel's Password rule mặc định (cấu hình trong AppServiceProvider)
 * kết hợp với yêu cầu xác nhận mật khẩu (confirmed).
 *
 * @see \Illuminate\Validation\Rules\Password
 */
trait PasswordValidationRules
{
    /**
     * Trả về mảng validation rules áp dụng cho trường mật khẩu.
     *
     * Rules bao gồm:
     * - required: bắt buộc nhập
     * - string: phải là chuỗi ký tự
     * - Password::default(): áp dụng password policy mặc định của ứng dụng
     * - confirmed: phải có trường `password_confirmation` khớp
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function passwordRules(): array
    {
        return ['required', 'string', Password::default(), 'confirmed'];
    }
}
