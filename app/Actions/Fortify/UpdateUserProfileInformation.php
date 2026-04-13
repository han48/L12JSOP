<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

/**
 * Xử lý cập nhật thông tin hồ sơ người dùng.
 *
 * Triển khai contract UpdatesUserProfileInformation của Laravel Fortify.
 * Cho phép user cập nhật tên, email, và ảnh đại diện. Nếu email thay đổi
 * và user implement MustVerifyEmail, email_verified_at sẽ bị reset và
 * một email xác minh mới sẽ được gửi đi.
 *
 * @see \Laravel\Fortify\Contracts\UpdatesUserProfileInformation
 */
class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate và cập nhật thông tin hồ sơ của user.
     *
     * Các trường được validate:
     * - name: bắt buộc, tối đa 255 ký tự
     * - email: bắt buộc, định dạng email hợp lệ, unique (bỏ qua user hiện tại), tối đa 255 ký tự
     * - photo: tùy chọn, chỉ chấp nhận jpg/jpeg/png, tối đa 1024KB
     *
     * Nếu có ảnh mới, ảnh cũ sẽ được thay thế qua `updateProfilePhoto()`.
     * Nếu email thay đổi và user cần xác minh email, sẽ gọi `updateVerifiedUser()`.
     *
     * @param  \App\Models\User  $user   User cần cập nhật
     * @param  array<string, mixed>  $input  Dữ liệu mới gồm name, email, và tùy chọn photo
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException  Nếu dữ liệu không hợp lệ (bag: updateProfileInformation)
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if (
            $input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail
        ) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
            ])->save();
        }
    }

    /**
     * Cập nhật thông tin hồ sơ cho user đã xác minh email khi email thay đổi.
     *
     * Reset `email_verified_at` về null và gửi lại email xác minh đến địa chỉ mới.
     * Được gọi khi user thay đổi email và class User implement MustVerifyEmail.
     *
     * @param  \App\Models\User  $user   User cần cập nhật
     * @param  array<string, string>  $input  Dữ liệu mới gồm name và email
     * @return void
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
