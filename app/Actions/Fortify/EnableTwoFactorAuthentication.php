<?php

namespace App\Actions\Fortify;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Laravel\Fortify\Events\TwoFactorAuthenticationEnabled;
use Laravel\Fortify\RecoveryCode;

class EnableTwoFactorAuthentication extends \Laravel\Fortify\Actions\EnableTwoFactorAuthentication
{
    /**
     * Enable two factor authentication for the user.
     *
     * @param  mixed  $user
     * @param  bool  $force
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
