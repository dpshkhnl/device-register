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
            'mobile' => ['required', 'string', 'max:20'],
        ]);

        $serviceArea = ServiceArea::where('id', $data['service_area_id'])
            ->where('is_active', true)
            ->firstOrFail();
        $countryCode = preg_replace('/\s+/', '', $serviceArea->dial_code);
        $mobileInput = preg_replace('/\s+/', '', $data['mobile']);
        $normalizedMobile = str_starts_with($mobileInput, '+')
            ? $mobileInput
            : $countryCode.$mobileInput;

        if (! preg_match('/^\\+\\d{7,15}$/', $normalizedMobile)) {
            return back()->withErrors(['mobile' => 'Select a service area and enter a valid mobile number.'])->withInput();
        }

        if (User::where('mobile', $normalizedMobile)->exists()) {
            return back()->withErrors(['mobile' => 'Mobile number is already registered.'])->withInput();
        }

        return $this->sendOtp($normalizedMobile, 'auth_register', 'dev_otp_register', $request, app(NotificationService::class));
    }

    public function sendLoginOtp(Request $request)
    {
        $settings = SystemSetting::first();
        if (! $settings?->auth_force_otp) {
            return back()->withErrors(['otp' => 'OTP login is disabled.'])->withInput();
        }

        $data = $request->validate([
            'login' => ['nullable', 'string'],
            'email' => ['nullable', 'string'],
            'country_code' => ['nullable', 'string', 'max:10'],
            'mobile' => ['nullable', 'string', 'max:20'],
        ]);

        $login = $data['login'] ?? '';
        $email = trim($data['email'] ?? '');
        $mobile = trim($data['mobile'] ?? '');
        $countryCode = trim($data['country_code'] ?? '');
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
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
        $user = User::where($field, $login)->first();

        if (! $user) {
            return back()->withErrors(['login' => 'Account not found.'])->withInput();
        }

        return $this->sendOtp($user->mobile, 'auth_login', 'dev_otp_login', $request, app(NotificationService::class), $user->email);
    }

    private function sendOtp(string $mobile, string $purpose, string $sessionKey, Request $request, NotificationService $notifier, ?string $email = null)
    {
        $settings = SystemSetting::first();
        $resendSeconds = (int) ($settings?->otp_resend_seconds ?? 60);
        if ($resendSeconds < 10) {
            $resendSeconds = 10;
        } elseif ($resendSeconds > 600) {
            $resendSeconds = 600;
        }

        $existingOtp = Otp::where('mobile', $mobile)
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
                    return response()->json([
                        'cooldown' => $remaining,
                    ], 429);
                }

                return back()
                    ->withInput()
                    ->with('otp_cooldown', $remaining)
                    ->with('otp_purpose', $purpose);
            }
        }

        $otpCode = (string) random_int(100000, 999999);

        Otp::updateOrCreate(
            [
                'mobile' => $mobile,
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
        $notifier->sendSms($mobile, $otpMessage);
        if ($email) {
            $notifier->sendEmail($email, 'Your OTP code', $otpMessage);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'OTP sent successfully.',
                'cooldown' => $resendSeconds,
                'dev_otp' => $otpCode,
            ]);
        }

        return back()
            ->withInput()
            ->with('status', 'OTP sent successfully.')
            ->with($sessionKey, $otpCode)
            ->with('otp_resend_seconds', $resendSeconds)
            ->with('otp_purpose', $purpose);
    }
}
