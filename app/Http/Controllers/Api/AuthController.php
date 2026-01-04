<?php

namespace App\Http\Controllers\Api;

use Illuminate\Auth\Events\Registered;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Fortify;

class AuthController extends Controller
{
    public function register(Request $request, CreatesNewUsers $creator)
    {
        if (config('fortify.lowercase_usernames') && $request->has(Fortify::username())) {
            $request->merge([
                Fortify::username() => Str::lower($request->{Fortify::username()}),
            ]);
        }

        event(new Registered($user = $creator->create($request->all())));

        $token = $user->createToken('API Token')->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => __('auth.failed')], 401);
        }

        // Check if 2FA is enabled
        if ($user->two_factor_secret) {
            if (!$request->has('code') || empty($request->code)) {
                return response()->json([
                    'message' => __('auth.two_factor_required'),
                    'requires_2fa' => true
                ], 200);
            }

            $provider = new TwoFactorAuthenticationProvider(new Google2FA());
            if (!$provider->verify(decrypt($user->two_factor_secret), $request->code)) {
                return response()->json(['message' => __('auth.two_factor_invalid')], 401);
            }
        }

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
