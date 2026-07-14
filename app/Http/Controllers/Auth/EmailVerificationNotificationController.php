<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
{
    if ($request->user()->hasVerifiedEmail()) {
        return redirect()->intended(route('dashboard', absolute: false));
    }

    try {
        $request->user()->sendEmailVerificationNotification();
    } catch (\Throwable $exception) {
        report($exception);

        return back()->with(
            'verification_error',
            'Doğrulama maili kısa süre önce gönderildi. Lütfen birkaç saniye bekleyip tekrar deneyin.'
        );
    }

    return back()->with('status', 'verification-link-sent');
}
}
