<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Always return the same generic response to avoid leaking which
        // email addresses are registered (account enumeration).
        if ($status !== Password::RESET_LINK_SENT) {
            Log::debug('Password reset link not sent: '.$status);
        }

        return back()->with('status', 'If an account with that email address exists, a password reset link has been sent.');
    }
}