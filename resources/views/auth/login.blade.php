@extends('layouts.app')

@section('content')
@php
    $otpEnabled          = $otpEnabled ?? false;
    $serviceAreas        = $serviceAreas ?? collect();
    $defaultDialCode     = $defaultDialCode ?? '+977';
    $authFlow            = session('auth_flow');
    $authLogin           = session('auth_login');
    $authChannel         = session('auth_channel');
    $authServiceAreaId   = session('auth_service_area_id') ?? $defaultServiceAreaId;
    $authRequiresOtp     = (bool) session('auth_requires_otp');
    $devOtpEmail         = session('dev_otp_login_email');
    $devOtpPhone         = session('dev_otp_login_phone');
    $showOtpPanel        = $authRequiresOtp && ($devOtpEmail || $devOtpPhone || $errors->has('otp') || old('otp'));
@endphp

<div class="min-h-[calc(100vh-4rem)] flex">

    {{-- ── Left panel (branding) ── --}}
    <div class="relative hidden w-1/2 overflow-hidden lg:flex lg:flex-col lg:justify-between"
         style="background: linear-gradient(160deg, #0a1420 0%, #132132 100%);">

        {{-- Ledger-grid texture --}}
        <div class="absolute inset-0 bg-hero-dots opacity-40 pointer-events-none"></div>

        <div class="relative z-10 flex flex-col justify-between h-full p-12">

            {{-- Logo --}}
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full border-2"
                     style="border-color: #2dd4c8; color: #2dd4c8;">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                @php $appName = $settings?->app_name ?? 'DRMS'; @endphp
                <span class="font-display text-xl font-semibold text-white">{{ $appName }}</span>
            </div>

            {{-- Center content --}}
            <div>
                <div class="mb-6 inline-flex items-center rounded-md border px-3 py-1 text-xs font-medium tracking-wide"
                     style="border-color: rgba(45,212,200,0.3); color: #7fe4da; background: rgba(45,212,200,0.08);">
                    Secure sign-in
                </div>
                <h2 class="font-display text-4xl font-semibold leading-tight text-white">
                    Welcome back
                </h2>
                <p class="mt-4 text-base text-slate-400 max-w-xs leading-relaxed">
                    Sign in to manage registered devices, ownership transfers, and certificates.
                </p>

                {{-- Feature list --}}
                <ul class="mt-8 space-y-3">
                    @foreach ([
                        'Real-time IMEI verification',
                        'Secure ownership transfers',
                        'Official status certificates',
                    ] as $feat)
                        <li class="flex items-center gap-3 text-sm text-slate-300">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border"
                                  style="border-color: rgba(45,212,200,0.4); color: #2dd4c8;">
                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                            {{ $feat }}
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Bottom tagline --}}
            <p class="text-xs text-slate-500">
                Official Device Registry &amp; Management System
            </p>
        </div>
    </div>

    {{-- ── Right panel (form) ── --}}
    <div class="flex flex-1 items-center justify-center bg-background px-6 py-12 lg:px-12">
        <div class="w-full max-w-md">

            {{-- Step indicator --}}
            @php $loginStep = $authFlow === 'existing' ? 2 : 1; @endphp
            <div class="mb-8">
                <p class="text-xs font-medium text-muted-foreground">
                    Step {{ $loginStep }} of 2
                </p>
                <div class="mt-2 flex items-center gap-1.5">
                    <div class="h-1 w-8 rounded-full {{ $loginStep >= 1 ? 'bg-primary' : 'bg-border' }}"></div>
                    <div class="h-1 w-4 rounded-full {{ $loginStep >= 2 ? 'bg-primary/40' : 'bg-border' }}"></div>
                    <div class="h-1 w-8 rounded-full {{ $loginStep >= 2 ? 'bg-primary' : 'bg-border' }}"></div>
                </div>
            </div>

            {{-- Heading --}}
            <div class="mb-8">
                <h1 class="font-display text-3xl font-semibold tracking-tight text-foreground">Sign in</h1>
                <p class="mt-2 text-sm text-muted-foreground">
                    Access your dashboard and device activity.
                </p>
            </div>

            {{-- Session Status --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            {{-- Errors --}}
            @if ($errors->any())
                <div class="mb-6 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3">
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-rose-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <p class="text-sm text-rose-700">Please check your credentials and try again.</p>
                </div>
            @endif

            {{-- ── Step 1: Identify ── --}}
            @if (! $authFlow)
                <form class="space-y-5" method="POST" action="{{ route('login.identify') }}">
                    @csrf

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-foreground" for="service_area_id">
                            Service Area
                        </label>
                        <select id="service_area_id" name="service_area_id"
                                class="h-12 w-full rounded-xl border border-border bg-background px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring transition-shadow">
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
                        <x-input-error :messages="$errors->get('service_area_id')" class="mt-1.5" />
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-foreground" for="login">
                            Email or Phone
                        </label>
                        <input
                            id="login" type="text" name="login"
                            value="{{ old('login') }}"
                            autocomplete="username"
                            placeholder="name@example.com or 98XXXXXXXX"
                            class="h-12 w-full rounded-xl border border-border bg-background px-4 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring transition-shadow"
                        />
                        <p id="login-hint" class="mt-1.5 text-xs text-muted-foreground"></p>
                        <x-input-error :messages="$errors->get('login')" class="mt-1.5" />
                    </div>

                    <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl py-3 text-sm font-semibold text-white shadow-soft transition-all hover:shadow-md hover:-translate-y-px"
                            style="background: #0e7c86;">
                        Continue
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/>
                        </svg>
                    </button>
                </form>
            @endif

            {{-- ── Step 2: Authenticate ── --}}
            @if ($authFlow === 'existing')
                <form class="space-y-5" method="POST" action="{{ route('login') }}">
                    @csrf
                    <input type="hidden" name="login" value="{{ $authLogin }}">
                    <input type="hidden" name="service_area_id" value="{{ $authServiceAreaId }}">

                    {{-- Signed-in-as chip --}}
                    <div class="flex items-center justify-between rounded-xl border border-border bg-muted/40 px-4 py-3 text-sm">
                        <span class="text-muted-foreground">
                            Signing in as <span class="font-semibold text-foreground">{{ $authLogin }}</span>
                        </span>
                        <a href="{{ route('login', ['reset' => 1]) }}"
                           class="text-xs font-semibold text-primary hover:underline">
                            Change
                        </a>
                    </div>

                    {{-- Auth method tabs --}}
                    <div class="rounded-2xl border border-border bg-muted/20 p-4">
                        @if ($authRequiresOtp)
                            <div class="mb-4 flex gap-2">
                                <button type="button" id="login-password-toggle"
                                        class="flex-1 rounded-lg border py-2 text-center text-xs font-semibold transition-all {{ $showOtpPanel ? 'border-transparent text-muted-foreground' : 'text-primary shadow-sm' }}"
                                        style="{{ ! $showOtpPanel ? 'border-color: hsl(var(--primary)/0.3); background: hsl(var(--primary)/0.08);' : '' }}">
                                    Password
                                </button>
                                <button type="button" id="login-otp-toggle"
                                        class="flex-1 rounded-lg border py-2 text-center text-xs font-semibold transition-all {{ $showOtpPanel ? 'text-primary shadow-sm' : 'border-transparent text-muted-foreground' }}"
                                        style="{{ $showOtpPanel ? 'border-color: hsl(var(--primary)/0.3); background: hsl(var(--primary)/0.08);' : '' }}">
                                    OTP
                                </button>
                            </div>
                        @endif

                        {{-- Password panel --}}
                        <div id="login-password-panel" class="{{ $showOtpPanel ? 'hidden' : '' }}">
                            <label class="mb-1.5 block text-sm font-semibold text-foreground" for="password">
                                Password
                            </label>
                            <input id="password" type="password" name="password"
                                   autocomplete="current-password"
                                   class="h-12 w-full rounded-xl border border-border bg-background px-4 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring transition-shadow"/>
                            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                        </div>
                    </div>

                    {{-- OTP panel --}}
                    @if ($authRequiresOtp)
                        <div id="login-otp-panel"
                             class="{{ $showOtpPanel ? '' : 'hidden' }} rounded-2xl border border-border bg-muted/20 p-4">
                            <label class="mb-1.5 block text-sm font-semibold text-foreground" for="otp">One-Time Password</label>
                            <div class="flex flex-wrap gap-3">
                                <input id="otp" type="text" name="otp" value="{{ old('otp') }}"
                                       autocomplete="one-time-code"
                                       class="h-12 flex-1 rounded-xl border border-border bg-background px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring"/>
                                <button type="button" id="login-otp-send"
                                        formaction="{{ route('login.otp') }}"
                                        class="inline-flex h-12 items-center gap-1.5 rounded-xl border border-border bg-background px-4 text-sm font-semibold text-foreground hover:bg-muted transition-colors">
                                    Send OTP
                                </button>
                            </div>
                            <p class="mt-2 text-xs text-muted-foreground">Use password or OTP to sign in.</p>
                            <p id="login-otp-status" class="mt-2 hidden rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-700"></p>
                            <p id="login-dev-otp-email" class="mt-2 text-xs text-amber-600 {{ $devOtpEmail ? '' : 'hidden' }}">Dev OTP (email): {{ $devOtpEmail }}</p>
                            <p id="login-dev-otp-phone" class="mt-2 text-xs text-amber-600 {{ $devOtpPhone ? '' : 'hidden' }}">Dev OTP (phone): {{ $devOtpPhone }}</p>
                            <x-input-error :messages="$errors->get('otp')" class="mt-1.5" />
                        </div>
                    @endif

                    {{-- Remember / Forgot --}}
                    <div class="flex items-center justify-between text-sm">
                        <label class="inline-flex items-center gap-2 text-muted-foreground cursor-pointer">
                            <input type="checkbox" name="remember"
                                   class="h-4 w-4 rounded border-border text-primary focus:ring-ring"/>
                            Remember me
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="font-semibold text-primary hover:underline">Forgot password?</a>
                        @endif
                    </div>

                    <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl py-3 text-sm font-semibold text-white shadow-soft transition-all hover:shadow-md hover:-translate-y-px"
                            style="background: #0e7c86;">
                        Sign in
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/>
                        </svg>
                    </button>
                </form>
            @endif

            {{-- Sign-up link --}}
            @if (Route::has('register'))
                <p class="mt-8 text-center text-sm text-muted-foreground">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-semibold text-primary hover:underline">
                        Create one free
                    </a>
                </p>
            @endif

        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const serviceSelect   = document.getElementById('service_area_id');
        const loginHint       = document.getElementById('login-hint');
        const passwordToggle  = document.getElementById('login-password-toggle');
        const otpToggle       = document.getElementById('login-otp-toggle');
        const otpPanel        = document.getElementById('login-otp-panel');
        const passwordPanel   = document.getElementById('login-password-panel');
        const otpSendButton   = document.getElementById('login-otp-send');
        const otpStatus       = document.getElementById('login-otp-status');
        const devOtpEmail     = document.getElementById('login-dev-otp-email');
        const devOtpPhone     = document.getElementById('login-dev-otp-phone');
        const csrfToken       = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (serviceSelect && loginHint) {
            const updateHint = () => {
                const selected   = serviceSelect.options[serviceSelect.selectedIndex];
                if (!selected) return;
                const allowEmail = selected.dataset.allowEmail === '1';
                const allowPhone = selected.dataset.allowPhone === '1';
                if (allowEmail && allowPhone)      loginHint.textContent = 'You can sign in using email or phone.';
                else if (allowEmail)               loginHint.textContent = 'This service area allows email login only.';
                else if (allowPhone)               loginHint.textContent = 'This service area allows phone login only.';
                else                               loginHint.textContent = 'Login is disabled for this service area.';
            };
            serviceSelect.addEventListener('change', updateHint);
            updateHint();
        }

        if (passwordToggle && otpToggle && otpPanel && passwordPanel) {
            const setMode = (mode) => {
                const isPassword = mode === 'password';
                otpPanel.classList.toggle('hidden', isPassword);
                passwordPanel.classList.toggle('hidden', !isPassword);

                const activeStyle  = 'border-color: hsl(var(--primary)/0.3); background: hsl(var(--primary)/0.08);';
                const inactiveStyle = '';

                passwordToggle.style.cssText = isPassword  ? activeStyle : inactiveStyle;
                otpToggle.style.cssText      = !isPassword ? activeStyle : inactiveStyle;

                passwordToggle.classList.toggle('text-primary', isPassword);
                passwordToggle.classList.toggle('text-muted-foreground', !isPassword);
                passwordToggle.classList.toggle('border-transparent', !isPassword);
                otpToggle.classList.toggle('text-primary', !isPassword);
                otpToggle.classList.toggle('text-muted-foreground', isPassword);
                otpToggle.classList.toggle('border-transparent', isPassword);
            };
            passwordToggle.addEventListener('click', () => setMode('password'));
            otpToggle.addEventListener('click',      () => setMode('otp'));
            setMode(@json($showOtpPanel ? 'otp' : 'password'));
        }

        if (otpSendButton) {
            const form = otpSendButton.closest('form');
            let cooldownActive = false;
            const setOtpStatus = (message, isError = false) => {
                if (!otpStatus) return;
                if (!message) { otpStatus.classList.add('hidden'); otpStatus.textContent = ''; return; }
                otpStatus.textContent = message;
                otpStatus.classList.remove('hidden');
                otpStatus.classList.toggle('border-emerald-200', !isError);
                otpStatus.classList.toggle('bg-emerald-50',      !isError);
                otpStatus.classList.toggle('text-emerald-700',   !isError);
                otpStatus.classList.toggle('border-rose-200',    isError);
                otpStatus.classList.toggle('bg-rose-50',         isError);
                otpStatus.classList.toggle('text-rose-700',      isError);
            };
            const startCooldown = (seconds) => {
                if (!seconds || seconds <= 0) return;
                let remaining = seconds;
                cooldownActive = true;
                otpSendButton.disabled = true;
                const tick = () => {
                    if (remaining <= 0) { otpSendButton.disabled = false; otpSendButton.textContent = 'Send OTP'; cooldownActive = false; return; }
                    otpSendButton.textContent = `Send OTP (${remaining}s)`;
                    remaining -= 1;
                    setTimeout(tick, 1000);
                };
                tick();
            };
            otpSendButton.addEventListener('click', async () => {
                if (!form) return;
                const action = otpSendButton.getAttribute('formaction');
                if (!action) return;
                setOtpStatus('');
                if (devOtpEmail) devOtpEmail.classList.add('hidden');
                if (devOtpPhone) devOtpPhone.classList.add('hidden');
                const formData = new FormData(form);
                formData.delete('password'); formData.delete('remember'); formData.delete('otp');
                const originalLabel = otpSendButton.textContent;
                otpSendButton.disabled = true; otpSendButton.textContent = 'Sending…';
                try {
                    const response = await fetch(action, {
                        method: 'POST', credentials: 'same-origin',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}) },
                        body: formData,
                    });
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        if (response.status === 429) {
                            const retryAfter = parseInt(response.headers.get('Retry-After') || '', 10);
                            const cooldown = data.cooldown || (Number.isFinite(retryAfter) ? retryAfter : null);
                            setOtpStatus(cooldown ? `Please wait ${cooldown}s before resending.` : 'Too many requests. Please wait and try again.', true);
                            if (cooldown) startCooldown(cooldown);
                            return;
                        }
                        setOtpStatus(data.message || (response.status === 419 ? 'Session expired. Refresh and try again.' : 'Unable to send OTP. Please try again.'), true);
                        return;
                    }
                    const emailOtp = data.dev_otp_login_email || data.dev_otp_email;
                    const phoneOtp = data.dev_otp_login_phone || data.dev_otp_phone;
                    if (emailOtp && devOtpEmail) { devOtpEmail.textContent = `Dev OTP (email): ${emailOtp}`; devOtpEmail.classList.remove('hidden'); }
                    if (phoneOtp && devOtpPhone) { devOtpPhone.textContent = `Dev OTP (phone): ${phoneOtp}`; devOtpPhone.classList.remove('hidden'); }
                    if (data.dev_otp && data.channel) { const t = data.channel === 'email' ? devOtpEmail : devOtpPhone; if (t) { t.textContent = `Dev OTP (${data.channel}): ${data.dev_otp}`; t.classList.remove('hidden'); } }
                    setOtpStatus(data.status || data.message || 'OTP sent. Please check your email or phone.', false);
                    if (data.cooldown) { startCooldown(data.cooldown); return; }
                    if (data.resend_seconds) startCooldown(data.resend_seconds);
                } catch (error) {
                    setOtpStatus('Unable to send OTP. Please try again.', true);
                } finally {
                    if (!cooldownActive) { otpSendButton.disabled = false; otpSendButton.textContent = originalLabel; }
                }
            });
        }
    });
</script>
@endsection
