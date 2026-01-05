@extends('layouts.app')

@section('content')
@php
    $otpEnabled = $otpEnabled ?? false;
    $serviceAreas = $serviceAreas ?? collect();
    $defaultDialCode = $defaultDialCode ?? '+977';
    $authFlow = session('auth_flow');
    $authLogin = session('auth_login');
    $authChannel = session('auth_channel');
    $authServiceAreaId = session('auth_service_area_id') ?? $defaultServiceAreaId;
    $authRequiresOtp = (bool) session('auth_requires_otp');
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
                @php
                    $loginStep = $authFlow === 'existing' ? 2 : 1;
                @endphp
                <div class="mb-6">
                    <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Step {{ $loginStep }} of 2</p>
                    <div class="mt-2 flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full {{ $loginStep >= 1 ? 'bg-primary' : 'bg-border' }}"></span>
                        <span class="h-1 w-16 rounded-full {{ $loginStep >= 2 ? 'bg-primary' : 'bg-border' }}"></span>
                        <span class="h-2 w-2 rounded-full {{ $loginStep >= 2 ? 'bg-primary' : 'bg-border' }}"></span>
                    </div>
                </div>
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

                @if (! $authFlow)
                    <form class="mt-6 space-y-4" method="POST" action="{{ route('login.identify') }}">
                        @csrf
                        <div>
                            <label class="text-sm font-medium text-foreground" for="service_area_id">Service Area</label>
                            <select id="service_area_id" name="service_area_id" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-3 text-sm">
                                @forelse ($serviceAreas as $serviceArea)
                                    <option
                                        value="{{ $serviceArea->id }}"
                                        data-allow-email="{{ $serviceArea->allow_email_login ? '1' : '0' }}"
                                        data-require-email-otp="{{ $serviceArea->require_email_otp ? '1' : '0' }}"
                                        data-allow-phone="{{ $serviceArea->allow_phone_login ? '1' : '0' }}"
                                        data-require-phone-otp="{{ $serviceArea->require_phone_otp ? '1' : '0' }}"
                                        {{ (string) old('service_area_id', $authServiceAreaId) === (string) $serviceArea->id ? 'selected' : '' }}
                                    >
                                        {{ $serviceArea->name }} ({{ $serviceArea->dial_code }})
                                    </option>
                                @empty
                                    <option value="" disabled selected>Add service areas in admin</option>
                                @endforelse
                            </select>
                            <x-input-error :messages="$errors->get('service_area_id')" class="mt-2" />
                        </div>
                        <div>
                            <label class="text-sm font-medium text-foreground" for="login">Email or Phone</label>
                            <input id="login" type="text" name="login" value="{{ old('login') }}" autocomplete="username" placeholder="name@example.com or 98XXXXXXXX" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                            <p id="login-hint" class="mt-2 text-xs text-muted-foreground"></p>
                            <x-input-error :messages="$errors->get('login')" class="mt-2" />
                        </div>
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">Continue</button>
                    </form>
                @endif

                @if ($authFlow === 'existing')
                    <form class="mt-6 space-y-4" method="POST" action="{{ route('login') }}">
                        @csrf
                        <input type="hidden" name="login" value="{{ $authLogin }}">
                        <input type="hidden" name="service_area_id" value="{{ $authServiceAreaId }}">
                        <div class="rounded-xl border border-border bg-muted/30 px-4 py-3 text-sm text-muted-foreground">
                            Signed in as <span class="font-semibold text-foreground">{{ $authLogin }}</span>
                            <a href="{{ route('login', ['reset' => 1]) }}" class="ml-2 text-primary hover:underline">Change</a>
                        </div>
                        <div class="rounded-xl border border-border bg-muted/30 p-4">
                            <div class="mb-3 flex items-center gap-2 text-xs font-semibold text-muted-foreground">
                                <button type="button" id="login-password-toggle" class="flex-1 rounded-lg border px-3 py-2 text-center border-primary bg-primary/10 text-primary shadow-sm">
                                    Password
                                </button>
                                @if ($authRequiresOtp)
                                    <button type="button" id="login-otp-toggle" class="flex-1 rounded-lg border px-3 py-2 text-center border-transparent">
                                        OTP
                                    </button>
                                @endif
                            </div>
                            <div id="login-password-panel">
                                <label class="text-sm font-medium text-foreground" for="password">Password</label>
                                <input id="password" type="password" name="password" autocomplete="current-password" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                        </div>
                        @if ($authRequiresOtp)
                            <div id="login-otp-panel" class="hidden rounded-xl border border-border bg-muted/30 p-4">
                                <label class="text-sm font-medium text-foreground" for="otp">OTP</label>
                                <div class="mt-2 flex flex-wrap gap-3">
                                    <input id="otp" type="text" name="otp" value="{{ old('otp') }}" autocomplete="one-time-code" class="h-11 flex-1 rounded-lg border border-border bg-background px-3 text-sm" />
                                    <button type="submit" formaction="{{ route('login.otp') }}" formmethod="POST" class="inline-flex h-11 items-center justify-center rounded-lg border border-border px-4 text-sm font-semibold text-foreground">
                                        Send OTP
                                    </button>
                                </div>
                                <p class="mt-2 text-xs text-muted-foreground">Use password or OTP to sign in.</p>
                                @if (session('dev_otp_login_email'))
                                    <p class="mt-2 text-xs text-amber-600">Dev OTP (email): {{ session('dev_otp_login_email') }}</p>
                                @endif
                                @if (session('dev_otp_login_phone'))
                                    <p class="mt-2 text-xs text-amber-600">Dev OTP (phone): {{ session('dev_otp_login_phone') }}</p>
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
                @endif

            </div>
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const serviceSelect = document.getElementById('service_area_id');
        const loginHint = document.getElementById('login-hint');
        const passwordToggle = document.getElementById('login-password-toggle');
        const otpToggle = document.getElementById('login-otp-toggle');
        const otpPanel = document.getElementById('login-otp-panel');
        const passwordPanel = document.getElementById('login-password-panel');
        const passwordInput = document.getElementById('password');
        const otpInput = document.getElementById('otp');
        if (serviceSelect && loginHint) {
            const updateHint = () => {
                const selected = serviceSelect.options[serviceSelect.selectedIndex];
                if (!selected) {
                    return;
                }
                const allowEmail = selected.dataset.allowEmail === '1';
                const allowPhone = selected.dataset.allowPhone === '1';
                if (allowEmail && allowPhone) {
                    loginHint.textContent = 'You can sign in using email or phone.';
                } else if (allowEmail) {
                    loginHint.textContent = 'This service area allows email login only.';
                } else if (allowPhone) {
                    loginHint.textContent = 'This service area allows phone login only.';
                } else {
                    loginHint.textContent = 'Login is disabled for this service area.';
                }
            };

            serviceSelect.addEventListener('change', updateHint);
            updateHint();
        }

        if (passwordToggle && otpToggle && otpPanel && passwordPanel) {
            const setMode = (mode) => {
                const isPassword = mode === 'password';
                otpPanel.classList.toggle('hidden', isPassword);
                passwordPanel.classList.toggle('hidden', !isPassword);
                passwordToggle.classList.toggle('border-primary', isPassword);
                passwordToggle.classList.toggle('bg-primary/10', isPassword);
                passwordToggle.classList.toggle('text-primary', isPassword);
                passwordToggle.classList.toggle('shadow-sm', isPassword);
                passwordToggle.classList.toggle('border-transparent', !isPassword);
                otpToggle.classList.toggle('border-primary', !isPassword);
                otpToggle.classList.toggle('bg-primary/10', !isPassword);
                otpToggle.classList.toggle('text-primary', !isPassword);
                otpToggle.classList.toggle('shadow-sm', !isPassword);
                otpToggle.classList.toggle('border-transparent', isPassword);
            };

            passwordToggle.addEventListener('click', () => setMode('password'));
            otpToggle.addEventListener('click', () => setMode('otp'));
        }
    });
</script>
@endsection
