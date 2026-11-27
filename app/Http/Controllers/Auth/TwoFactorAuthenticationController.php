<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\Totp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TwoFactorAuthenticationController extends Controller
{
    public function create(): View|RedirectResponse
    {
        return view('auth.two-factor');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = $request->user();
        $secret = $user->mfa_secret ?: Totp::generateSecret();

        if (! Totp::verify($secret, (string) $request->input('code'))) {
            return back()->withErrors(['code' => 'The code does not match the generated secret.']);
        }

        // mfa_* are no longer mass-assignable; set them explicitly.
        $user->forceFill([
            'mfa_enabled' => true,
            'mfa_secret' => $secret,
        ])->save();

        return back()->with('status', 'two-factor-enabled');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $request->user()->forceFill([
            'mfa_enabled' => false,
            'mfa_secret' => null,
        ])->save();

        return back()->with('status', 'two-factor-disabled');
    }
}