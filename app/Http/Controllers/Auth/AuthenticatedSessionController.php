<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ServiceArea;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View|\Illuminate\Http\RedirectResponse
    {
        if (request()->boolean('reset')) {
            request()->session()->forget([
                'auth_flow',
                'auth_login',
                'auth_channel',
                'auth_service_area_id',
                'auth_requires_otp',
            ]);
        }
        if (session('auth_flow') && session('auth_flow') !== 'existing') {
            return redirect()->route('register');
        }
        $settings = SystemSetting::first();
        $serviceAreas = ServiceArea::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        $defaultServiceArea = ServiceArea::where('iso2', 'NP')->first();
        $defaultServiceAreaId = $defaultServiceArea?->id;
        $defaultDialCode = $defaultServiceArea?->dial_code;

        return view('auth.login', [
            'otpEnabled' => (bool) ($settings?->auth_force_otp),
            'serviceAreas' => $serviceAreas,
            'defaultServiceAreaId' => $defaultServiceAreaId,
            'defaultDialCode' => $defaultDialCode,
        ]);
    }

    public function identify(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'service_area_id' => ['required', 'integer', 'exists:service_areas,id'],
            'login' => ['required', 'string'],
        ]);

        $serviceArea = ServiceArea::where('id', $data['service_area_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $login = trim($data['login']);
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);
        if (! $isEmail) {
            $rawMobile = preg_replace('/\s+/', '', $login);
            if (! preg_match('/^\\+?\\d{6,15}$/', $rawMobile)) {
                return back()->withErrors(['login' => 'Enter a valid phone number.'])->withInput();
            }
            $normalizedMobile = str_starts_with($rawMobile, '+') ? $rawMobile : $serviceArea->dial_code.$rawMobile;
            $loginCandidates = array_unique([$rawMobile, $normalizedMobile]);
        }

        if ($isEmail && ! $serviceArea->allow_email_login) {
            return back()->withErrors(['login' => 'Email login is disabled for this service area.'])->withInput();
        }
        if (! $isEmail && ! $serviceArea->allow_phone_login) {
            return back()->withErrors(['login' => 'Phone login is disabled for this service area.'])->withInput();
        }

        if ($isEmail) {
            $user = \App\Models\User::where('email', $login)->first();
        } else {
            $user = \App\Models\User::whereIn('mobile', $loginCandidates)->first();
        }

        if (! $user) {
            $request->session()->put([
                'register_verified' => false,
                'register_email' => $isEmail ? $login : null,
                'register_mobile' => $isEmail ? null : $normalizedMobile,
                'register_service_area_id' => $serviceArea->id,
            ]);

            return redirect()->route('register');
        }

        $request->session()->put([
            'auth_flow' => 'existing',
            'auth_login' => $isEmail ? $user->email : $user->mobile,
            'auth_channel' => $isEmail ? 'email' : 'phone',
            'auth_service_area_id' => $serviceArea->id,
            'auth_requires_otp' => $isEmail ? (bool) $serviceArea->require_email_otp : (bool) $serviceArea->require_phone_otp,
        ]);

        return redirect()->route('login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        $request->session()->forget([
            'auth_flow',
            'auth_login',
            'auth_channel',
            'auth_service_area_id',
            'auth_requires_otp',
        ]);

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
