<?php

namespace App\Actions\Fortify;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

/**
 * Xử lý đăng ký người dùng mới.
 *
 * Triển khai contract CreatesNewUsers của Laravel Fortify.
 * Validate dữ liệu đầu vào, tạo User mới trong database, và tự động
 * tạo một personal Team cho user đó trong cùng một database transaction.
 *
 * @see \Laravel\Fortify\Contracts\CreatesNewUsers
 * @see \App\Actions\Fortify\PasswordValidationRules
 */
class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate dữ liệu và tạo user mới cùng personal team.
     *
     * Thực hiện trong một database transaction để đảm bảo tính toàn vẹn:
     * nếu tạo team thất bại, user cũng sẽ không được tạo.
     *
     * Các trường bắt buộc: name, email (unique), password (confirmed).
     * Nếu ứng dụng bật tính năng Terms & Privacy Policy (Jetstream),
     * trường `terms` cũng bắt buộc phải được chấp nhận.
     *
     * @param  array<string, string>  $input  Dữ liệu đăng ký gồm name, email, password, password_confirmation
     * @return \App\Models\User  User vừa được tạo
     *
     * @throws \Illuminate\Validation\ValidationException  Nếu dữ liệu không hợp lệ
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return DB::transaction(function () use ($input) {
            return tap(User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]), function (User $user) {
                $this->createTeam($user);
            });
        });
    }

    /**
     * Tạo personal team cho user mới đăng ký.
     *
     * Team được đặt tên theo format "{FirstName}'s Team" và được đánh dấu
     * là `personal_team = true`. Team này được gắn trực tiếp vào user
     * thông qua quan hệ `ownedTeams`.
     *
     * @param  \App\Models\User  $user  User vừa được tạo
     * @return void
     */
    protected function createTeam(User $user): void
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0] . "'s Team",
            'personal_team' => true,
        ]));
    }
}
