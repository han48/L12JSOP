<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

/**
 * Xử lý đặt lại mật khẩu qua luồng "Quên mật khẩu".
 *
 * Triển khai contract ResetsUserPasswords của Laravel Fortify.
 * Được gọi sau khi user đã xác minh token reset password từ email.
 * Không yêu cầu mật khẩu cũ — chỉ cần mật khẩu mới hợp lệ.
 *
 * @see \Laravel\Fortify\Contracts\ResetsUserPasswords
 * @see \App\Actions\Fortify\PasswordValidationRules
 * @see \App\Notifications\ResetPassword
 */
class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate và đặt lại mật khẩu đã quên của user.
     *
     * Validate trường password theo password rules (bao gồm xác nhận).
     * Mật khẩu mới được hash bằng bcrypt trước khi lưu vào database.
     *
     * @param  \App\Models\User  $user   User cần reset mật khẩu
     * @param  array<string, string>  $input  Dữ liệu gồm password và password_confirmation
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException  Nếu mật khẩu mới không hợp lệ
     */
    public function reset(User $user, array $input): void
    {
        Validator::make($input, [
            'password' => $this->passwordRules(),
        ])->validate();

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
