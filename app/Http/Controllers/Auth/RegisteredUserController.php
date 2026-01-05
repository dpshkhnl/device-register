<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\ServiceArea;
use App\Models\User;
use App\Models\UserPackage;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $serviceAreas = ServiceArea::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        $defaultServiceAreaId = ServiceArea::where('iso2', 'NP')->value('id');
        $otpEnabled = true;

        return view('auth.register', compact('serviceAreas', 'defaultServiceAreaId', 'otpEnabled'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'service_area_id' => ['required', 'integer', 'exists:service_areas,id'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['accepted'],
        ]);

        if (! $request->filled('email') && ! $request->filled('mobile')) {
            return back()->withErrors(['email' => 'Email or mobile is required.'])->withInput();
        }

        $serviceArea = ServiceArea::where('id', $request->service_area_id)
            ->where('is_active', true)
            ->firstOrFail();
        $countryCode = preg_replace('/\s+/', '', $serviceArea->dial_code);
        $normalizedMobile = null;
        if ($request->filled('mobile')) {
            $mobileInput = preg_replace('/\s+/', '', $request->mobile);
            $normalizedMobile = str_starts_with($mobileInput, '+')
                ? $mobileInput
                : $countryCode.$mobileInput;

            if (! preg_match('/^\\+\\d{7,15}$/', $normalizedMobile)) {
                return back()->withErrors(['mobile' => 'Select a service area and enter a valid mobile number.'])->withInput();
            }

            if (User::where('mobile', $normalizedMobile)->exists()) {
                return back()->withErrors(['mobile' => 'Mobile number is already registered.'])->withInput();
            }
        }

        $verified = $request->session()->get('register_verified', false);
        $verifiedEmail = $request->session()->get('register_email');
        $verifiedMobile = $request->session()->get('register_mobile');
        $verifiedServiceArea = $request->session()->get('register_service_area_id');
        if (! $verified || $verifiedServiceArea != $request->service_area_id) {
            return back()->withErrors(['email' => 'Please verify OTP before registering.'])->withInput();
        }
        if ($request->filled('email') && $verifiedEmail !== $request->email) {
            return back()->withErrors(['email' => 'Please verify OTP before registering.'])->withInput();
        }
        if ($normalizedMobile && $verifiedMobile !== $normalizedMobile) {
            return back()->withErrors(['mobile' => 'Please verify phone OTP before registering.'])->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'country' => $serviceArea->name,
            'country_code' => $countryCode,
            'mobile' => $normalizedMobile,
            'email' => $request->email ?: null,
            'role' => User::ROLE_USER,
            'password' => Hash::make($request->password),
        ]);

        $trialPackage = Package::where('is_trial', true)
            ->where('is_active', true)
            ->first();

        if ($trialPackage) {
            $endsAt = $trialPackage->duration_days
                ? now()->addDays($trialPackage->duration_days)
                : null;

            UserPackage::create([
                'user_id' => $user->id,
                'package_id' => $trialPackage->id,
                'device_limit' => $trialPackage->device_limit,
                'imei_limit' => $trialPackage->imei_limit,
                'starts_at' => now(),
                'ends_at' => $endsAt,
                'status' => 'active',
            ]);
        }

        event(new Registered($user));

        Auth::login($user);
        $request->session()->forget([
            'auth_flow',
            'auth_login',
            'auth_channel',
            'auth_service_area_id',
            'auth_requires_otp',
            'register_verified',
            'register_email',
            'register_mobile',
            'register_service_area_id',
            'otp_sent_email',
            'otp_sent_phone',
        ]);

        return redirect(route('dashboard', absolute: false));
    }
}
