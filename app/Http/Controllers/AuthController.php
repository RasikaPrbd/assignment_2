<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $key = strtolower($credentials['email']) . '|' . $request->ip();
        $lockKey = 'login-lock:' . $key;
        $attemptKey = 'login-attempts:' . $key;

        // Block login while the lock is active.
        if (RateLimiter::tooManyAttempts($lockKey, 1)) {
            $seconds = RateLimiter::availableIn($lockKey);

            return back()
                ->withErrors([
                    'email' => "Too many failed attempts. Try again in {$seconds} seconds.",
                ])
                ->with('lock_seconds', $seconds)
                ->onlyInput('email');
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            if (! Auth::user()->is_active) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Your account is inactive.',
                ])->onlyInput('email');
            }

            RateLimiter::clear($attemptKey);
            RateLimiter::clear($lockKey);

            return redirect()->intended('/dashboard');
        }

        // Count failed attempts over a 15-minute window.
        RateLimiter::hit($attemptKey, 900);

        $attempts = RateLimiter::attempts($attemptKey);
        $remaining = max(0, 5 - $attempts);

        if ($attempts >= 5) {
            // Create a separate lock that lasts 30 seconds.
            RateLimiter::hit($lockKey, 30);

            return back()
                ->withErrors([
                    'email' => 'Too many failed attempts. Please wait 30 seconds.',
                ])
                ->with('lock_seconds', 30)
                ->onlyInput('email');
        }

        return back()->withErrors([
            'email' => "The provided credentials do not match our records. {$remaining} attempts remaining.",
        ])->onlyInput('email');
    }


    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
