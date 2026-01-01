@extends('layouts.app')

@section('content')
@php
    $otpEnabled = $otpEnabled ?? false;
    $serviceAreas = $serviceAreas ?? collect();
    $defaultDialCode = $defaultDialCode ?? '+977';
    $showOtp = $otpEnabled && (old('otp') || $errors->has('otp') || session('otp_purpose') === 'auth_login' || session('dev_otp_login'));
    $loginMode = old('email') ? 'email' : (old('mobile') ? 'mobile' : 'mobile');
@endphp
<section class="py-12 lg:py-16">
    <div class="container max-w-5xl">
        <div class="grid overflow-hidden rounded-3xl border border-border bg-card shadow-soft lg:grid-cols-2">
            <div class="relative hidden bg-muted/40 p-10 lg:block">
                <div class="absolute inset-0 bg-gradient-to-br from-primary/20 via-transparent to-transparent"></div>
                <div class="relative z-10 flex h-full flex-col justify-between">
                    <div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
                            <svg class="h-6 w-6 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                            </svg>
                        </div>
                        <h1 class="mt-6 text-3xl font-bold text-foreground">Welcome back</h1>
                        <p class="mt-3 text-sm text-muted-foreground">Sign in to manage registered devices, transfers, and certificates.</p>
                    </div>
                    <img src="/images/auth-login.svg" alt="Login illustration" class="mt-10 w-full max-w-sm" />
                </div>
            </div>
            <div class="p-6 sm:p-10">
                <div class="space-y-2">
                    <h2 class="text-2xl font-semibold text-foreground">Sign in</h2>
                    <p class="text-sm text-muted-foreground">Access your dashboard and device activity.</p>
                </div>

                <x-auth-session-status class="mt-4" :status="session('status')" />

                @if ($errors->any())
                    <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        Please check your credentials and try again.
                    </div>
                @endif

                <form class="mt-6 space-y-4" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 rounded-xl border border-border bg-muted/30 p-2 text-xs font-semibold text-muted-foreground">
                            <button type="button" id="login-mode-email" class="flex-1 rounded-lg border px-3 py-2 text-center {{ $loginMode === 'email' ? 'border-primary bg-primary/10 text-primary shadow-sm' : 'border-transparent' }}">
                                Email
                            </button>
                            <button type="button" id="login-mode-mobile" class="flex-1 rounded-lg border px-3 py-2 text-center {{ $loginMode === 'mobile' ? 'border-primary bg-primary/10 text-primary shadow-sm' : 'border-transparent' }}">
                                Phone
                            </button>
                        </div>

                        <div id="email-panel" class="{{ $loginMode === 'email' ? '' : 'hidden' }}">
                            <label class="text-sm font-medium text-foreground" for="email">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" placeholder="name@example.com" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            <x-input-error :messages="$errors->get('login')" class="mt-2" />
                        </div>

                        <div id="mobile-panel" class="{{ $loginMode === 'mobile' ? '' : 'hidden' }}">
                            <label class="text-sm font-medium text-foreground" for="mobile">Mobile Number</label>
                            <div class="mt-2 grid gap-3 sm:grid-cols-[140px,1fr]">
                                <select id="country_code" name="country_code" class="h-12 rounded-lg border border-border bg-background px-3 text-sm">
                                    @forelse ($serviceAreas as $serviceArea)
                                        <option value="{{ $serviceArea->dial_code }}" {{ old('country_code', $defaultDialCode) === $serviceArea->dial_code ? 'selected' : '' }}>
                                            {{ $serviceArea->name }} ({{ $serviceArea->dial_code }})
                                        </option>
                                    @empty
                                        <option value="+977">Nepal (+977)</option>
                                    @endforelse
                                </select>
                                <input id="mobile" type="text" name="mobile" value="{{ old('mobile') }}" autocomplete="tel" placeholder="98XXXXXXXX" class="h-12 rounded-lg border border-border bg-background px-4 text-sm" />
                            </div>
                            <p class="mt-2 text-xs text-muted-foreground">Use your phone number with country code.</p>
                            <x-input-error :messages="$errors->get('login')" class="mt-2" />
                            <x-input-error :messages="$errors->get('mobile')" class="mt-2" />
                        </div>
                    </div>
                    <div id="password-panel" class="{{ $showOtp ? 'hidden' : '' }}">
                        <label class="text-sm font-medium text-foreground" for="password">Password</label>
                        <input id="password" type="password" name="password" autocomplete="current-password" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                        <p class="mt-2 text-xs text-muted-foreground">Use password or sign in with OTP.</p>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    @if ($otpEnabled)
                        <div>
                            <button type="button" id="otp-toggle" class="inline-flex items-center gap-2 rounded-lg border border-border px-3 py-2 text-xs font-semibold text-foreground">
                                Sign in with OTP
                            </button>
                        </div>
                        <div id="otp-panel" class="{{ $showOtp ? '' : 'hidden' }} rounded-xl border border-border bg-muted/30 p-4">
                            <label class="text-sm font-medium text-foreground" for="otp">OTP</label>
                            <div class="mt-2 flex flex-wrap gap-3">
                                <input id="otp" type="text" name="otp" value="{{ old('otp') }}" autocomplete="one-time-code" class="h-11 flex-1 rounded-lg border border-border bg-background px-3 text-sm" />
                                <button type="submit" formaction="{{ route('login.otp') }}" formmethod="POST" class="inline-flex h-11 items-center justify-center rounded-lg border border-border px-4 text-sm font-semibold text-foreground">
                                    Send OTP
                                </button>
                            </div>
                            @if (session('dev_otp_login'))
                                <p class="mt-2 text-xs text-amber-600">Dev OTP: {{ session('dev_otp_login') }}</p>
                            @endif
                            @if (session('otp_cooldown') && session('otp_purpose') === 'auth_login')
                                <p class="mt-2 text-xs text-rose-600">Resend available in {{ session('otp_cooldown') }}s</p>
                            @endif
                            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
                        </div>
                    @endif
                    <div class="flex items-center justify-between text-sm">
                        <label class="inline-flex items-center gap-2 text-muted-foreground">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-border" />
                            Remember me
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="font-semibold text-primary hover:underline">Forgot password?</a>
                        @endif
                    </div>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">Sign in</button>
                </form>

                <p class="mt-6 text-center text-sm text-muted-foreground">
                    Need an account?
                    <a href="{{ route('register') }}" class="font-semibold text-primary hover:underline">Create one</a>
                </p>
            </div>
        </div>
    </div>
</section>
@if ($otpEnabled)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggle = document.getElementById('otp-toggle');
            const panel = document.getElementById('otp-panel');
            const passwordPanel = document.getElementById('password-panel');
            const emailPanel = document.getElementById('email-panel');
            const mobilePanel = document.getElementById('mobile-panel');
            const emailButton = document.getElementById('login-mode-email');
            const mobileButton = document.getElementById('login-mode-mobile');
            const emailInput = document.getElementById('email');
            const mobileInput = document.getElementById('mobile');
            if (!toggle || !panel || !passwordPanel) {
                return;
            }

            const updateLabel = () => {
                toggle.textContent = panel.classList.contains('hidden')
                    ? 'Sign in with OTP'
                    : 'Hide OTP';
            };

            toggle.addEventListener('click', () => {
                panel.classList.toggle('hidden');
                passwordPanel.classList.toggle('hidden');
                updateLabel();
            });

            updateLabel();

            const setMode = (mode) => {
                if (!emailPanel || !mobilePanel || !emailButton || !mobileButton) {
                    return;
                }
                const isEmail = mode === 'email';
                emailPanel.classList.toggle('hidden', !isEmail);
                mobilePanel.classList.toggle('hidden', isEmail);
                emailButton.classList.toggle('border-primary', isEmail);
                emailButton.classList.toggle('bg-primary/10', isEmail);
                emailButton.classList.toggle('text-primary', isEmail);
                emailButton.classList.toggle('shadow-sm', isEmail);
                emailButton.classList.toggle('border-transparent', !isEmail);
                mobileButton.classList.toggle('border-primary', !isEmail);
                mobileButton.classList.toggle('bg-primary/10', !isEmail);
                mobileButton.classList.toggle('text-primary', !isEmail);
                mobileButton.classList.toggle('shadow-sm', !isEmail);
                mobileButton.classList.toggle('border-transparent', isEmail);

                if (isEmail && mobileInput) {
                    mobileInput.value = '';
                }
                if (!isEmail && emailInput) {
                    emailInput.value = '';
                }
            };

            emailButton?.addEventListener('click', () => setMode('email'));
            mobileButton?.addEventListener('click', () => setMode('mobile'));
        });
    </script>
@endif
@endsection
