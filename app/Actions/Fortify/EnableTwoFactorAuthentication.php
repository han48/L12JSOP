<?php

namespace App\Actions\Fortify;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Laravel\Fortify\Events\TwoFactorAuthenticationEnabled;
use Laravel\Fortify\RecoveryCode;

/**
 * Xử lý bật xác thực hai yếu tố (2FA) cho user.
 *
 * Kế thừa từ action gốc của Laravel Fortify và override để tự động
 * xác nhận 2FA ngay lập tức (set `two_factor_confirmed_at`) thay vì
 * yêu cầu bước xác nhận riêng. Hỗ trợ cấu hình độ dài secret key
 * qua `fortify-options.two-factor-authentication.secret-length`.
 *
 * Khi bật 2FA, hệ thống sẽ:
 * - Sinh secret key mới (mã hóa trước khi lưu)
 * - Sinh 8 recovery codes (mã hóa trước khi lưu)
 * - Dispatch sự kiện TwoFactorAuthenticationEnabled
 *
 * @see \Laravel\Fortify\Actions\EnableTwoFactorAuthentication
 */
class EnableTwoFactorAuthentication extends \Laravel\Fortify\Actions\EnableTwoFactorAuthentication
{
    /**
     * Bật xác thực hai yếu tố cho user.
     *
     * Nếu user chưa có `two_factor_secret` hoặc `$force = true`,
     * sẽ sinh secret key mới và 8 recovery codes, lưu vào database
     * dưới dạng đã mã hóa, và dispatch sự kiện TwoFactorAuthenticationEnabled.
     *
     * Nếu user đã có `two_factor_secret` và `$force = false`, không làm gì.
     *
     * @param  mixed  $user   User cần bật 2FA
     * @param  bool   $force  Bắt buộc tạo lại secret dù đã có (mặc định: false)
     * @return void
     */
    public function __invoke($user, $force = false)
    {
        if (empty($user->two_factor_secret) || $force === true) {
            $secretLength = (int) config('fortify-options.two-factor-authentication.secret-length', 16);

            $user->forceFill([
                'two_factor_confirmed_at' => Carbon::now(),
                'two_factor_secret' => encrypt($this->provider->generateSecretKey($secretLength)),
                'two_factor_recovery_codes' => encrypt(json_encode(Collection::times(8, function () {
                    return RecoveryCode::generate();
                })->all())),
            ])->save();

            TwoFactorAuthenticationEnabled::dispatch($user);
        }
    }
}
