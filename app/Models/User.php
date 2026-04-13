<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Platform\Models\User as Authenticatable;

/**
 * Model đại diện cho người dùng trong hệ thống.
 *
 * Kế thừa từ Orchid Platform User (Authenticatable), cung cấp đầy đủ tính năng
 * xác thực, quản lý team, hồ sơ cá nhân, và API token.
 *
 * Bảng CSDL: `users`
 *
 * ## Traits
 * - `HasApiTokens` (Laravel Sanctum): Quản lý Personal Access Token cho REST API.
 * - `HasFactory`: Hỗ trợ tạo dữ liệu giả lập qua Factory.
 * - `HasProfilePhoto` (Jetstream): Lưu trữ và truy xuất ảnh đại diện (`profile_photo_path`, `profile_photo_url`).
 * - `HasTeams` (Jetstream): Quản lý team — tạo team cá nhân, tham gia/rời team, phân quyền trong team.
 * - `Notifiable`: Gửi và nhận thông báo Laravel (email, database, v.v.).
 * - `TwoFactorAuthenticatable` (Fortify): Hỗ trợ xác thực hai yếu tố (2FA) qua Google Authenticator.
 * - `AsSource` (Orchid, kế thừa): Tích hợp model với Orchid Screen/Layout.
 * - `HasValidationData` (kế thừa): Cung cấp helper validation dùng trong Orchid screens.
 *
 * ## Fillable fields
 * @property string      $name                       Tên hiển thị của người dùng.
 * @property string      $email                      Địa chỉ email (unique).
 * @property string      $password                   Mật khẩu đã được hash (bcrypt).
 *
 * ## Các trường khác (quản lý bởi hệ thống)
 * @property int         $id                         Khóa chính.
 * @property string|null $remember_token             Token ghi nhớ đăng nhập.
 * @property int|null    $current_team_id            ID team hiện tại đang hoạt động.
 * @property string|null $profile_photo_path         Đường dẫn lưu ảnh đại diện.
 * @property string      $profile_photo_url          URL ảnh đại diện (appended accessor).
 * @property int         $status                     Trạng thái tài khoản (0=private, 1=public, 2=internal).
 * @property string|null $two_factor_secret          Secret key mã hóa cho 2FA.
 * @property string|null $two_factor_recovery_codes  Mã khôi phục 2FA (JSON, ẩn khi serialize).
 * @property string|null $two_factor_confirmed_at    Thời điểm xác nhận bật 2FA.
 * @property array|null  $permissions                Danh sách quyền riêng lẻ (JSON, ẩn khi serialize).
 * @property string|null $email_verified_at          Thời điểm xác minh email.
 * @property \Carbon\Carbon $created_at              Thời điểm tạo.
 * @property \Carbon\Carbon $updated_at              Thời điểm cập nhật.
 *
 * ## Relationships
 * - `teams()` (HasTeams): Danh sách team mà user là thành viên.
 * - `ownedTeams()` (HasTeams): Danh sách team do user sở hữu.
 * - `currentTeam()` (HasTeams): Team hiện tại đang hoạt động.
 * - `tokens()` (HasApiTokens): Danh sách Sanctum Personal Access Token.
 * - `notifications()` (Notifiable): Danh sách thông báo nhận được.
 *
 * ## Hành vi đặc biệt
 * - Mật khẩu tự động hash khi gán (`'password' => 'hashed'`).
 * - `permissions` được cast sang `array` (JSON).
 * - `email_verified_at` được cast sang `datetime`.
 * - Các trường nhạy cảm (`password`, `remember_token`, `two_factor_*`, `permissions`) bị ẩn khi serialize.
 * - Hỗ trợ Orchid filter theo `id`, `name`, `email`, `created_at`, `updated_at`.
 * - Gửi email đặt lại mật khẩu qua `App\Notifications\ResetPassword`.
 * - Gửi email xác minh tài khoản qua `App\Notifications\VerifyEmail`.
 *
 * @satisfies Requirements 3.5, 4.1
 */
class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'permissions',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'password'             => 'hashed',
        'permissions'          => 'array',
        'email_verified_at'    => 'datetime',
    ];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected $allowedFilters = [
        'id'         => Where::class,
        'name'       => Like::class,
        'email'      => Like::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'name',
        'email',
        'updated_at',
        'created_at',
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification(#[\SensitiveParameter] $token)
    {
        $this->notify(new \App\Notifications\ResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmail());
    }

}
