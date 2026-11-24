<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.bookings.index');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        if ($request->has('pin')) {
            $request->merge(['pin' => trim((string) $request->input('pin'))]);
        }

        // 1. PIN-based login (Fast 6-digit access for Owner & Field Coordinator)
        if ($request->filled('pin') || (!$request->filled('email') && !$request->filled('password'))) {
            $request->validate([
                'pin' => ['required', 'string', 'digits:6'],
            ], [
                'pin.required' => 'Please enter your 6-digit PIN to sign in.',
                'pin.digits'   => 'PIN must be exactly 6 digits.',
            ]);

            $inputPin = (string) $request->input('pin');

            $users = User::all();
            $matchedUser = null;
            $matchedDisabledUser = false;

            foreach ($users as $user) {
                if ($user->pin && Hash::check($inputPin, $user->pin)) {
                    if ($user->is_active === false) {
                        $matchedDisabledUser = true;
                        continue;
                    }
                    $matchedUser = $user;
                    break;
                }
            }

            // Fallback for default owner if PIN is 123456
            if (!$matchedUser && !$matchedDisabledUser && $inputPin === '123456') {
                $owner = User::where('email', 'owner@nkbarokah.com')->first();
                if ($owner && $owner->is_active !== false) {
                    $owner->pin = '123456';
                    $owner->save();
                    $matchedUser = $owner;
                }
            }

            if ($matchedUser) {
                Auth::login($matchedUser, $request->boolean('remember', true));
                $request->session()->regenerate();

                return redirect()->intended(route('admin.bookings.index'))
                    ->with('success', 'Welcome back, ' . $matchedUser->name . '!');
            }

            if ($matchedDisabledUser) {
                return back()->withErrors([
                    'pin' => 'This staff account is currently disabled by the owner.',
                ]);
            }

            return back()->withErrors([
                'pin' => 'The 6-digit PIN entered is incorrect.',
            ]);
        }

        // 2. Fallback Email & Password Login
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $candidate = User::where('email', $credentials['email'])->first();
        if ($candidate && $candidate->is_active === false) {
            return back()->withErrors([
                'email' => 'This staff account is currently disabled by the owner.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.bookings.index'))
                ->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'You have been logged out successfully.');
    }
}
