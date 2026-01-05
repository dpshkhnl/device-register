<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\ServiceArea;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OtpController extends Controller
{
    public function sendRegisterOtp(Request $request)
    {
        $data = $request->validate([
            'service_area_id' => ['required', 'integer', 'exists:service_areas,id'],
            'email' => ['nullable', 'email', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:20'],
        ]);

        $email = trim((string) ($data['email'] ?? ''));
        $mobile = trim((string) ($data['mobile'] ?? ''));
        if ($email === '' && $mobile === '') {
            return back()->withErrors(['email' => 'Email or mobile is required.'])->withInput();
        }

        $serviceArea = ServiceArea::where('id', $data['service_area_id'])
            ->where('is_active', true)
            ->firstOrFail();
        if ($email !== '' && ! $serviceArea->allow_email_login) {
            return back()->withErrors(['email' => 'Email login is disabled for this service area.'])->withInput();
        }
        $countryCode = preg_replace('/\s+/', '', $serviceArea->dial_code);
        $normalizedMobile = null;
        if ($mobile !== '') {
            if (! $serviceArea->allow_phone_login) {
                return back()->withErrors(['mobile' => 'Phone login is disabled for this service area.'])->withInput();
            }
            $mobileInput = preg_replace('/\s+/', '', $mobile);
            $normalizedMobile = str_starts_with($mobileInput, '+')
                ? $mobileInput
                : $countryCode.$mobileInput;

            if (! preg_match('/^\\+\\d{7,15}$/', $normalizedMobile)) {
                return back()->withErrors(['mobile' => 'Select a service area and enter a valid mobile number.'])->withInput();
            }

        }

        $existingPhoneUser = null;
        if ($normalizedMobile) {
            $existingPhoneUser = User::where('mobile', $normalizedMobile)->first();
        }

        $existingEmailUser = null;
        if ($email !== '') {
            $existingEmailUser = User::where('email', $email)->first();
        }

        if ($existingEmailUser || $existingPhoneUser) {
            $user = $existingEmailUser ?? $existingPhoneUser;
            $isEmailLogin = $existingEmailUser !== null;

            $request->session()->put([
                'auth_flow' => 'existing',
                'auth_login' => $isEmailLogin ? $user->email : $user->mobile,
                'auth_channel' => $isEmailLogin ? 'email' : 'phone',
                'auth_service_area_id' => $serviceArea->id,
                'auth_requires_otp' => $isEmailLogin ? (bool) $serviceArea->require_email_otp : (bool) $serviceArea->require_phone_otp,
            ]);

            return redirect()->route('login')->with('status', 'Account found. Please sign in.');
        }

        $notifier = app(NotificationService::class);
        $emailOtp = null;
        if ($email !== '') {
            $emailOtp = $this->sendOtp($email, 'auth_register_email', $request, $notifier);
        }
        if (isset($emailOtp['cooldown'])) {
            return $request->expectsJson()
                ? response()->json(['cooldown' => $emailOtp['cooldown']], 429)
                : back()->withInput()->with('otp_cooldown', $emailOtp['cooldown'])->with('otp_purpose', 'auth_register');
        }
        $mobileOtp = null;
        if ($normalizedMobile) {
            $mobileOtp = $this->sendOtp($normalizedMobile, 'auth_register_phone', $request, $notifier);
            if (isset($mobileOtp['cooldown'])) {
                return $request->expectsJson()
                    ? response()->json(['cooldown' => $mobileOtp['cooldown']], 429)
                    : back()->withInput()->with('otp_cooldown', $mobileOtp['cooldown'])->with('otp_purpose', 'auth_register');
            }
        }

        $cooldownSeconds = max((int) ($emailOtp['cooldown'] ?? 0), (int) ($mobileOtp['cooldown'] ?? 0));

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'OTP sent successfully.',
                'dev_otp_email' => $emailOtp['otp'] ?? null,
                'dev_otp_phone' => $mobileOtp['otp'] ?? null,
                'cooldown' => $cooldownSeconds,
            ]);
        }

        return back()
            ->withInput()
            ->with('status', 'OTP sent successfully.')
            ->with('dev_otp_register_email', $emailOtp['otp'] ?? null)
            ->with('dev_otp_register_phone', $mobileOtp['otp'] ?? null)
            ->with('otp_purpose', 'auth_register')
            ->with('otp_sent_email', $email !== '')
            ->with('otp_sent_phone', (bool) $normalizedMobile)
            ->with('register_email', $email !== '' ? $email : null)
            ->with('register_mobile', $normalizedMobile)
            ->with('register_service_area_id', $serviceArea->id);
    }

    public function verifyRegisterOtp(Request $request)
    {
        $data = $request->validate([
            'service_area_id' => ['required', 'integer', 'exists:service_areas,id'],
            'email' => ['nullable', 'email', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'email_otp' => ['nullable', 'digits:6'],
            'phone_otp' => ['nullable', 'digits:6'],
        ]);

        $email = trim((string) ($data['email'] ?? ''));
        $mobile = trim((string) ($data['mobile'] ?? ''));
        if ($email === '' && $mobile === '') {
            return back()->withErrors(['email' => 'Email or mobile is required.'])->withInput();
        }
        if ($email !== '' && ! $request->filled('email_otp')) {
            return back()->withErrors(['email_otp' => 'Email OTP is required.'])->withInput();
        }
        if ($mobile !== '' && ! $request->filled('phone_otp')) {
            return back()->withErrors(['phone_otp' => 'Phone OTP is required.'])->withInput();
        }

        $serviceArea = ServiceArea::where('id', $data['service_area_id'])
            ->where('is_active', true)
            ->firstOrFail();

        if ($email !== '') {
            $emailOtp = Otp::where('mobile', $email)
                ->where('purpose', 'auth_register_email')
                ->first();
            if (! $emailOtp || $emailOtp->expires_at?->isPast()) {
                return back()->withErrors(['email_otp' => 'Email OTP expired or not found.'])->withInput();
            }
            if (! Hash::check($data['email_otp'], $emailOtp->otp_hash)) {
                $emailOtp->increment('attempts');
                return back()->withErrors(['email_otp' => 'Invalid email OTP.'])->withInput();
            }
            $emailOtp->delete();
        }

        $normalizedMobile = null;
        if ($mobile !== '') {
            if (! $serviceArea->allow_phone_login) {
                return back()->withErrors(['mobile' => 'Phone login is disabled for this service area.'])->withInput();
            }
            $countryCode = preg_replace('/\s+/', '', $serviceArea->dial_code);
            $mobileInput = preg_replace('/\s+/', '', $mobile);
            $normalizedMobile = str_starts_with($mobileInput, '+')
                ? $mobileInput
                : $countryCode.$mobileInput;

            $phoneOtp = Otp::where('mobile', $normalizedMobile)
                ->where('purpose', 'auth_register_phone')
                ->first();
            if (! $phoneOtp || $phoneOtp->expires_at?->isPast()) {
                return back()->withErrors(['phone_otp' => 'Phone OTP expired or not found.'])->withInput();
            }
            if (! Hash::check($data['phone_otp'], $phoneOtp->otp_hash)) {
                $phoneOtp->increment('attempts');
                return back()->withErrors(['phone_otp' => 'Invalid phone OTP.'])->withInput();
            }
            $phoneOtp->delete();
        }

        $request->session()->put([
            'register_verified' => true,
            'register_email' => $email !== '' ? $email : null,
            'register_mobile' => $normalizedMobile,
            'register_service_area_id' => $serviceArea->id,
        ]);

        return redirect()->route('register');
    }

    public function sendLoginOtp(Request $request)
    {
        $data = $request->validate([
            'login' => ['nullable', 'string'],
            'email' => ['nullable', 'string'],
            'country_code' => ['nullable', 'string', 'max:10'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'service_area_id' => ['nullable', 'integer', 'exists:service_areas,id'],
        ]);

        $login = $data['login'] ?? '';
        $email = trim($data['email'] ?? '');
        $mobile = trim($data['mobile'] ?? '');
        $countryCode = trim($data['country_code'] ?? '');
        if (! $login) {
            $login = (string) $request->session()->get('auth_login', '');
        }
        $serviceAreaId = $data['service_area_id']
            ?? $request->session()->get('auth_service_area_id');
        $serviceArea = $serviceAreaId
            ? ServiceArea::where('id', $serviceAreaId)->where('is_active', true)->first()
            : null;
        if (! $login) {
            if ($email) {
                $login = $email;
            } elseif ($mobile) {
                $login = str_starts_with($mobile, '+') ? $mobile : $countryCode.$mobile;
            }
        }

        if (! $login) {
            return back()->withErrors(['login' => 'Email or mobile is required.'])->withInput();
        }
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);
        if ($isEmail) {
            $user = User::where('email', $login)->first();
        } else {
            $rawMobile = preg_replace('/\s+/', '', $login);
            $normalized = $rawMobile;
            if ($serviceArea && ! str_starts_with($rawMobile, '+')) {
                $normalized = $serviceArea->dial_code.$rawMobile;
            }
            $loginCandidates = array_unique([$rawMobile, $normalized]);
            $user = User::whereIn('mobile', $loginCandidates)->first();
        }

        if (! $user) {
            return back()->withErrors(['login' => 'Account not found.'])->withInput();
        }

        $notifier = app(NotificationService::class);
        if ($isEmail) {
            $otp = $this->sendOtp($login, 'auth_login_email', $request, $notifier);
            if (isset($otp['cooldown'])) {
                return $request->expectsJson()
                    ? response()->json(['cooldown' => $otp['cooldown']], 429)
                    : back()->withInput()->with('otp_cooldown', $otp['cooldown'])->with('otp_purpose', 'auth_login');
            }
            if ($user->mobile) {
                $notifier->sendSms($user->mobile, "Your login OTP is {$otp['otp']}. It expires in 10 minutes.");
            }
            return back()->withInput()->with('dev_otp_login_email', $otp['otp']);
        }

        $otp = $this->sendOtp($login, 'auth_login_phone', $request, $notifier);
        if (isset($otp['cooldown'])) {
            return $request->expectsJson()
                ? response()->json(['cooldown' => $otp['cooldown']], 429)
                : back()->withInput()->with('otp_cooldown', $otp['cooldown'])->with('otp_purpose', 'auth_login');
        }
        if ($user->email) {
            $notifier->sendEmail($user->email, 'Your OTP code', "Your login OTP is {$otp['otp']}. It expires in 10 minutes.");
        }
        return back()->withInput()->with('dev_otp_login_phone', $otp['otp']);
    }

    private function sendOtp(string $recipient, string $purpose, Request $request, NotificationService $notifier): array
    {
        $settings = SystemSetting::first();
        $resendSeconds = (int) ($settings?->otp_resend_seconds ?? 60);
        if ($resendSeconds < 10) {
            $resendSeconds = 10;
        } elseif ($resendSeconds > 600) {
            $resendSeconds = 600;
        }

        $existingOtp = Otp::where('mobile', $recipient)
            ->where('purpose', $purpose)
            ->first();

        if ($resendSeconds > 0 && $existingOtp?->last_sent_at) {
            $elapsed = (int) now()->diffInSeconds($existingOtp->last_sent_at);
            if ($elapsed < 0) {
                $elapsed = 0;
            }
            if ($elapsed < $resendSeconds) {
                $remaining = (int) max(0, $resendSeconds - $elapsed);
                if ($request->expectsJson()) {
                    return ['cooldown' => $remaining];
                }

                return ['cooldown' => $remaining];
            }
        }

        $otpCode = (string) random_int(100000, 999999);

        Otp::updateOrCreate(
            [
                'mobile' => $recipient,
                'purpose' => $purpose,
            ],
            [
                'otp_hash' => Hash::make($otpCode),
                'expires_at' => now()->addMinutes(10),
                'attempts' => 0,
                'last_sent_at' => now(),
            ]
        );

        $purposeLabel = str_replace('_', ' ', $purpose);
        $otpMessage = "Your {$purposeLabel} OTP is {$otpCode}. It expires in 10 minutes.";
        if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $notifier->sendEmail($recipient, 'Your OTP code', $otpMessage);
        } else {
            $notifier->sendSms($recipient, $otpMessage);
        }

        return ['otp' => $otpCode, 'cooldown' => $resendSeconds];
    }
}
