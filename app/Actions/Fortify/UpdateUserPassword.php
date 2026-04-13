<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

/**
 * Xử lý đổi mật khẩu khi user đã đăng nhập.
 *
 * Triển khai contract UpdatesUserPasswords của Laravel Fortify.
 * Yêu cầu user cung cấp mật khẩu hiện tại để xác minh danh tính
 * trước khi cho phép đặt mật khẩu mới.
 *
 * @see \Laravel\Fortify\Contracts\UpdatesUserPasswords
 * @see \App\Actions\Fortify\PasswordValidationRules
 */
class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate và cập nhật mật khẩu của user.
     *
     * Validate các trường:
     * - current_password: bắt buộc, phải khớp với mật khẩu hiện tại của user (guard: web)
     * - password: bắt buộc, phải tuân theo password rules và có trường xác nhận
     *
     * Mật khẩu mới được hash bằng bcrypt trước khi lưu vào database.
     *
     * @param  \App\Models\User  $user   User cần đổi mật khẩu
     * @param  array<string, string>  $input  Dữ liệu gồm current_password, password, password_confirmation
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException  Nếu mật khẩu hiện tại sai hoặc mật khẩu mới không hợp lệ (bag: updatePassword)
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => $this->passwordRules(),
        ], [
            'current_password.current_password' => __('The provided password does not match your current password.'),
        ])->validateWithBag('updatePassword');

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
