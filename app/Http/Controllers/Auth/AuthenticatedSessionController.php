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
    public function create(): View
    {
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

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

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
