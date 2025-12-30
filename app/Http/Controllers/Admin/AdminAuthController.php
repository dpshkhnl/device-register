<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        $settings = SystemSetting::first();

        return view('admin.auth.login', compact('settings'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }

        $request->session()->regenerate();

        $user = $request->user();
        if ($user->role !== User::ROLE_ADMIN) {
            Auth::logout();
            return back()->withErrors(['email' => 'You do not have admin access.']);
        }

        return redirect()->intended(route('admin.dashboard'));
    }
}
