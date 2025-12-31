<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\Package;
use App\Models\ServiceArea;
use App\Models\SystemSetting;
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
        $otpEnabled = (bool) (SystemSetting::first()?->auth_force_otp);

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
            'mobile' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'otp' => ['nullable', 'digits:6'],
            'terms' => ['accepted'],
        ]);

        $serviceArea = ServiceArea::where('id', $request->service_area_id)
            ->where('is_active', true)
            ->firstOrFail();
        $countryCode = preg_replace('/\s+/', '', $serviceArea->dial_code);
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

        $settings = SystemSetting::first();
        if ($settings?->auth_force_otp) {
            $otpValue = $request->input('otp');
            if (! $otpValue) {
                return back()->withErrors(['otp' => 'OTP is required.'])->withInput();
            }

            $otp = Otp::where('mobile', $normalizedMobile)
                ->where('purpose', 'auth_register')
                ->first();

            if (! $otp || $otp->expires_at?->isPast()) {
                return back()->withErrors(['otp' => 'OTP expired or not found. Please resend OTP.'])->withInput();
            }

            if (! Hash::check($otpValue, $otp->otp_hash)) {
                $otp->increment('attempts');
                return back()->withErrors(['otp' => 'Invalid OTP. Please try again.'])->withInput();
            }

            $otp->delete();
        }

        $user = User::create([
            'name' => $request->name,
            'country' => $serviceArea->name,
            'country_code' => $countryCode,
            'mobile' => $normalizedMobile,
            'email' => $request->email,
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

        return redirect(route('dashboard', absolute: false));
    }
}
