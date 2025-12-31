<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\Otp;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $login = $this->input('login');
        if ($login) {
            return;
        }

        $email = trim((string) $this->input('email', ''));
        $mobile = trim((string) $this->input('mobile', ''));
        $countryCode = trim((string) $this->input('country_code', ''));

        if ($email) {
            $login = $email;
        } elseif ($mobile) {
            $login = str_starts_with($mobile, '+') ? $mobile : $countryCode.$mobile;
        }

        $this->merge([
            'login' => $login,
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
        if (! $isEmail && ! preg_match('/^\+\d{7,15}$/', $login)) {
            throw ValidationException::withMessages([
                'login' => 'Enter email or select a service area and provide mobile number.',
            ]);
        }

        $field = $isEmail ? 'email' : 'mobile';
        $credentials = [
            $field => $login,
            'password' => $this->input('password'),
        ];

        $user = User::where($field, $login)->first();
        if (! $user) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        $settings = SystemSetting::first();
        $otpValue = $this->input('otp');
        $passwordValue = $this->input('password');
        $otpEnabled = (bool) ($settings?->auth_force_otp);

        if ($otpValue) {
            if (! $otpEnabled) {
                throw ValidationException::withMessages([
                    'otp' => 'OTP login is disabled.',
                ]);
            }

            if (! $otpValue) {
                throw ValidationException::withMessages([
                    'otp' => 'OTP is required.',
                ]);
            }

            $otp = Otp::where('mobile', $user->mobile)
                ->where('purpose', 'auth_login')
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
