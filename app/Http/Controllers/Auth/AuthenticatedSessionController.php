<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        if (session()->has('two_factor_login_user_id')) {
            return Inertia::render('Auth/TwoFactorChallenge');
        }

        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if ($request->session()->has('two_factor_login_user_id')) {
            return redirect()->route('two-factor.challenge');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function showTwoFactorChallenge(): Response|RedirectResponse
    {
        if (! session()->has('two_factor_login_user_id')) {
            return redirect()->route('login');
        }

        return Inertia::render('Auth/TwoFactorChallenge');
    }

    public function verifyTwoFactorChallenge(Request $request, TwoFactorService $twoFactor): RedirectResponse
    {
        $user = User::find(session('two_factor_login_user_id'));

        if (! $user || $user->two_factor_confirmed_at === null) {
            $request->session()->forget(['two_factor_login_user_id', 'two_factor_login_remember']);

            return redirect()->route('login');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:64'],
        ]);
        $code = trim($validated['code']);
        $valid = $twoFactor->verifyCode($user->two_factor_secret, $code);

        if (! $valid) {
            $recoveryCodes = $user->two_factor_recovery_codes ?? [];
            foreach ($recoveryCodes as $index => $recoveryCodeHash) {
                if (Hash::check($code, $recoveryCodeHash)) {
                    unset($recoveryCodes[$index]);
                    $user->two_factor_recovery_codes = array_values($recoveryCodes);
                    $user->save();
                    $valid = true;
                    break;
                }
            }
        }

        if (! $valid) {
            throw ValidationException::withMessages([
                'code' => 'The authentication code is invalid.',
            ]);
        }

        $remember = (bool) $request->session()->pull('two_factor_login_remember', false);
        $request->session()->forget('two_factor_login_user_id');
        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
