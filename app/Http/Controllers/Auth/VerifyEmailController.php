<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\MustVerifyEmail; // ← 追加
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();                    // ← 毎回呼ばず一度変数へ

        if (! $user instanceof MustVerifyEmail) {    // ← null/型ガード
            abort(401); // または 403/redirect 等、方針に合わせてOK
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));              // ← 型が一致
        }

        return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
    }
}
