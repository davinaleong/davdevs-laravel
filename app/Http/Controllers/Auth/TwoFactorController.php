<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class TwoFactorController extends Controller
{
    public function setup(Request $request)
    {
        $user = $request->user();

        if ($user->totp_enabled) {
            return redirect()->route('panel.dashboard');
        }

        $google2fa = app('pragmarx.google2fa');
        $secret    = session('totp_setup_secret') ?? $google2fa->generateSecretKey();
        session(['totp_setup_secret' => $secret]);

        $qrUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return view('auth.2fa-setup', compact('qrUrl', 'secret'));
    }

    public function storeSetup(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $user   = $request->user();
        $secret = session('totp_setup_secret');
        $google2fa = app('pragmarx.google2fa');

        if (!$google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid code. Please try again.']);
        }

        $recoveryCodes = collect(range(1, 8))->map(fn () => strtoupper(bin2hex(random_bytes(5)))).all();

        $user->update([
            'totp_secret'       => encrypt($secret),
            'totp_enabled'      => true,
            'totp_confirmed_at' => now(),
            'recovery_codes'    => array_map('bcrypt', $recoveryCodes),
        ]);

        session()->forget('totp_setup_secret');
        session(['2fa_verified' => true]);
        session(['totp_recovery_codes_plain' => $recoveryCodes]);

        return redirect()->route('2fa.save-codes');
    }

    public function saveCodes(Request $request)
    {
        $codes = session('totp_recovery_codes_plain', []);
        return view('auth.2fa-save-codes', compact('codes'));
    }

    public function challenge()
    {
        if (session('2fa_verified')) {
            return redirect()->route('panel.dashboard');
        }
        return view('auth.2fa-challenge');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $user   = $request->user();
        $secret = decrypt($user->totp_secret);
        $google2fa = app('pragmarx.google2fa');

        if (!$google2fa->verifyKey($secret, $request->code)) {
            ActivityLog::create(['channel' => 'auth', 'level' => 'warning', 'message' => "2FA verify failed: {$user->email}"]);

            return back()->withErrors(['code' => 'Invalid code. Please try again.']);
        }

        ActivityLog::create(['channel' => 'auth', 'level' => 'info', 'message' => "2FA verify success: {$user->email}"]);

        session(['2fa_verified' => true]);

        return redirect()->intended(route('panel.dashboard'));
    }

    public function recovery()
    {
        return view('auth.2fa-recovery');
    }

    public function verifyRecovery(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $user  = $request->user();
        $codes = $user->recovery_codes ?? [];
        $input = strtoupper(trim($request->code));

        foreach ($codes as $i => $hashed) {
            if (Hash::check($input, $hashed)) {
                unset($codes[$i]);
                $user->update(['recovery_codes' => array_values($codes)]);
                session(['2fa_verified' => true]);
                return redirect()->route('panel.dashboard');
            }
        }

        return back()->withErrors(['code' => 'Invalid recovery code.']);
    }

    public function disable(Request $request)
    {
        $request->validate(['password' => 'required|current_password']);

        $request->user()->update([
            'totp_secret'       => null,
            'totp_enabled'      => false,
            'totp_confirmed_at' => null,
            'recovery_codes'    => null,
        ]);

        session()->forget('2fa_verified');

        return redirect()->route('panel.dashboard')->with('success', '2FA has been disabled.');
    }
}
