<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'mfa_code' => ['nullable', 'string'],
        ]);

        $mfaCode = $credentials['mfa_code'] ?? null;
        unset($credentials['mfa_code']);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if (Auth::user()->status !== 'active') {
            Auth::guard('web')->logout();

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Enforce the second factor when enabled.
        if (Auth::user()->mfa_enabled && Auth::user()->mfa_secret) {
            $code = (string) ($mfaCode ?? '');

            if (! \App\Support\Totp::verify(Auth::user()->mfa_secret, $code)) {
                Auth::guard('web')->logout();

                throw ValidationException::withMessages([
                    'mfa_code' => 'The two-factor code is invalid or missing.',
                ]);
            }
        }

        $request->session()->regenerate();

        $user = Auth::user();
        $user->update(['last_login_at' => now()]);

        return redirect()->intended($this->redirectPath($user));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    protected function redirectPath($user): string
    {
        if (in_array($user->role, ['patient', 'caregiver', 'family_member', 'booking_contact'], true)) {
            return route('home');
        }
        if ($user->isDriver()) {
            return route('driver.dashboard');
        }
        if (in_array($user->role, ['partner_admin', 'partner_operations_manager', 'partner_dispatcher', 'partner_finance_officer', 'partner_read_only_auditor'], true)) {
            return route('partner.dashboard');
        }
        return route('operations.dashboard');
    }
}