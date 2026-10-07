<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TwoFactorController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'two_factor_enabled' => $request->user()->two_factor_confirmed_at !== null,
        ]);
    }

    public function enable(Request $request, TwoFactorService $twoFactor): JsonResponse
    {
        $user = $request->user();
        abort_if($user->two_factor_confirmed_at !== null, 409, 'Two-factor authentication is already enabled.');

        $user->two_factor_secret = $twoFactor->generateSecret();
        $user->two_factor_recovery_codes = null;
        $user->save();

        return response()->json(['secret' => $user->two_factor_secret]);
    }

    public function confirm(Request $request, TwoFactorService $twoFactor): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'regex:/^\d{6}$/'],
        ]);

        $user = $request->user();
        if (! $user->two_factor_secret || $user->two_factor_confirmed_at !== null) {
            throw ValidationException::withMessages([
                'code' => 'Start two-factor setup before confirming a code.',
            ]);
        }

        if (! $twoFactor->verifyCode($user->two_factor_secret, $validated['code'])) {
            throw ValidationException::withMessages([
                'code' => 'The authentication code is invalid.',
            ]);
        }

        $recoveryCodes = $twoFactor->generateRecoveryCodes();
        $user->two_factor_recovery_codes = array_map(
            fn (string $code) => Hash::make($code),
            $recoveryCodes,
        );
        $user->two_factor_confirmed_at = now();
        $user->save();

        return response()->json(['recovery_codes' => $recoveryCodes]);
    }

    public function disable(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        return response()->json(['two_factor_enabled' => false]);
    }
}
