<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\Otp;
use App\Models\ServiceArea;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $login = $this->input('login');
        $serviceAreaId = $this->input('service_area_id');
        if (! $serviceAreaId) {
            $serviceAreaId = $this->session()->get('auth_service_area_id');
        }

        $email = trim((string) $this->input('email', ''));
        $mobile = trim((string) $this->input('mobile', ''));
        $countryCode = trim((string) $this->input('country_code', ''));
        if (! $login) {
            $login = $this->session()->get('auth_login');
        }

        if ($email) {
            $login = $email;
        } elseif ($mobile) {
            $login = str_starts_with($mobile, '+') ? $mobile : $countryCode.$mobile;
        }

        $this->merge([
            'login' => $login,
            'service_area_id' => $serviceAreaId,
        ]);
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'email' => ['nullable', 'string'],
            'country_code' => ['nullable', 'string', 'max:10'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'service_area_id' => ['nullable', 'integer', 'exists:service_areas,id'],
            'password' => ['nullable', 'string'],
            'otp' => ['nullable', 'digits:6'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = $this->string('login')->toString();
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);

        $serviceAreaId = $this->input('service_area_id');
        $serviceArea = $serviceAreaId
            ? ServiceArea::where('id', $serviceAreaId)->where('is_active', true)->first()
            : null;
        if (! $serviceArea) {
            throw ValidationException::withMessages([
                'login' => 'Please select a valid service area.',
            ]);
        }

        if ($isEmail && ! $serviceArea->allow_email_login) {
            throw ValidationException::withMessages([
                'login' => 'Email login is disabled for this service area.',
            ]);
        }
        if (! $isEmail && ! $serviceArea->allow_phone_login) {
            throw ValidationException::withMessages([
                'login' => 'Phone login is disabled for this service area.',
            ]);
        }

        $user = null;
        $loginCandidates = [$login];
        if ($isEmail) {
            $user = User::where('email', $login)->first();
        } else {
            $rawMobile = preg_replace('/\s+/', '', $login);
            if (! preg_match('/^\+?\d{6,15}$/', $rawMobile)) {
                throw ValidationException::withMessages([
                    'login' => 'Enter email or select a service area and provide mobile number.',
                ]);
            }
            $normalized = str_starts_with($rawMobile, '+') ? $rawMobile : $serviceArea->dial_code.$rawMobile;
            $loginCandidates = array_unique([$rawMobile, $normalized]);
            $user = User::whereIn('mobile', $loginCandidates)->first();
        }
        if (! $user) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        $otpValue = $this->input('otp');
        $passwordValue = $this->input('password');
        $otpAllowed = $isEmail ? (bool) $serviceArea->require_email_otp : (bool) $serviceArea->require_phone_otp;

        if ($otpValue) {
            if (! $otpAllowed) {
                throw ValidationException::withMessages([
                    'otp' => 'OTP login is disabled.',
                ]);
            }

            if (! $otpValue) {
                throw ValidationException::withMessages([
                    'otp' => 'OTP is required.',
                ]);
            }

            $otpPurpose = $isEmail ? 'auth_login_email' : 'auth_login_phone';
            $otpRecipient = $isEmail ? $user->email : $user->mobile;
            $otp = Otp::where('mobile', $otpRecipient)
                ->where('purpose', $otpPurpose)
                ->first();

            if (! $otp || $otp->expires_at?->isPast()) {
                throw ValidationException::withMessages([
                    'otp' => 'OTP expired or not found. Please resend OTP.',
                ]);
            }

            if (! Hash::check($otpValue, $otp->otp_hash)) {
                $otp->increment('attempts');
                throw ValidationException::withMessages([
                    'otp' => 'Invalid OTP. Please try again.',
                ]);
            }

            $otp->delete();
            Auth::login($user, $this->boolean('remember'));
        } else {
            if (! $passwordValue) {
                throw ValidationException::withMessages([
                    'password' => 'Password or OTP is required.',
                ]);
            }

            $credentials = [
                ($isEmail ? 'email' : 'mobile') => $isEmail ? $user->email : $user->mobile,
                'password' => $passwordValue,
            ];
            if (! Auth::attempt($credentials, $this->boolean('remember'))) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'login' => trans('auth.failed'),
                ]);
            }
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('login')).'|'.$this->ip());
    }
}
