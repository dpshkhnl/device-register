@extends('layouts.app')

@section('content')
@php
    $serviceAreas          = $serviceAreas ?? collect();
    $defaultServiceAreaId  = $defaultServiceAreaId ?? null;
    $otpEnabled            = $otpEnabled ?? false;
    $showOtp               = $otpEnabled && (old('email_otp') || $errors->has('email_otp') || $errors->has('phone_otp') || session('otp_purpose') === 'auth_register' || session('dev_otp_register_email') || session('dev_otp_register_phone'));
    $registerVerified      = session('register_verified');
    $registerEmail         = session('register_email');
    $registerMobile        = session('register_mobile');
    $registerServiceAreaId = session('register_service_area_id');
    $otpSent               = session('otp_sent_email') || session('otp_sent_phone');
    $registerStep          = $registerVerified ? 3 : ($otpSent ? 2 : 1);
@endphp

<div class="min-h-[calc(100vh-4rem)] flex">

    {{-- ── Left panel (branding) ── --}}
    <div class="relative hidden w-1/2 overflow-hidden lg:flex lg:flex-col lg:justify-between"
         style="background: linear-gradient(160deg, #0a1420 0%, #132132 100%);">

        <div class="absolute inset-0 bg-hero-dots opacity-40 pointer-events-none"></div>

        <div class="relative z-10 flex flex-col justify-between h-full p-12">

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

            <div>
                <div class="mb-6 inline-flex items-center rounded-md border px-3 py-1 text-xs font-medium tracking-wide"
                     style="border-color: rgba(200,151,62,0.35); color: #e0b165; background: rgba(200,151,62,0.08);">
                    Create your account
                </div>
                <h2 class="font-display text-4xl font-semibold leading-tight text-white">
                    Join the registry
                </h2>
                <p class="mt-4 text-base text-slate-400 max-w-xs leading-relaxed">
                    Register your devices, transfer ownership, and download official certificates — all in one place.
                </p>

                {{-- Step progress --}}
                <div class="mt-10 space-y-4">
                    @foreach ([
                        ['label' => 'Verify Contact', 'desc' => 'Enter your email or phone'],
                        ['label' => 'Confirm OTP',    'desc' => 'Enter the verification code'],
                        ['label' => 'Set Password',   'desc' => 'Create your account'],
                    ] as $i => $s)
                        @php $stepDone = ($i + 1) < $registerStep; $stepActive = ($i + 1) === $registerStep; @endphp
                        <div class="flex items-center gap-3">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold"
                                 style="{{ $stepDone ? 'background:#0e7c86;color:#fff;' : ($stepActive ? 'background:rgba(200,151,62,0.18);color:#e0b165;border:1px solid rgba(200,151,62,0.4);' : 'background:rgba(255,255,255,0.05);color:#64748b;border:1px solid rgba(255,255,255,0.08);') }}">
                                @if ($stepDone)
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-semibold {{ $stepActive ? 'text-white' : ($stepDone ? 'text-slate-400' : 'text-slate-600') }}">
                                    {{ $s['label'] }}
                                </p>
                                <p class="text-xs {{ $stepActive ? 'text-slate-400' : 'text-slate-600' }}">{{ $s['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <p class="text-xs text-slate-500">
                Official Device Registry &amp; Management System
            </p>
        </div>
    </div>

    {{-- ── Right panel (form) ── --}}
    <div class="flex flex-1 items-start justify-center bg-background px-6 py-12 lg:px-12 overflow-y-auto">
        <div class="w-full max-w-md">

            {{-- Step badge (mobile) --}}
            <div class="mb-8 lg:hidden">
                <p class="text-xs font-semibold uppercase tracking-widest text-muted-foreground">Step {{ $registerStep }} of 3</p>
                <div class="mt-2 flex items-center gap-1.5">
                    @for ($i = 1; $i <= 3; $i++)
                        <div class="h-1.5 rounded-full transition-all {{ $i <= $registerStep ? '' : 'bg-border' }}"
                             style="{{ $i <= $registerStep ? 'width:2rem;background:#0e7c86;' : 'width:0.5rem;' }}"></div>
                    @endfor
                </div>
            </div>

            {{-- Heading --}}
            <div class="mb-8">
                <h1 class="font-display text-3xl font-semibold tracking-tight text-foreground">Create account</h1>
                <p class="mt-2 text-sm text-muted-foreground">
                    @if ($registerStep === 1) Enter your contact details to get started.
                    @elseif ($registerStep === 2) Enter the OTP sent to your contact.
                    @else Set your password to complete registration.
                    @endif
                </p>
            </div>

            {{-- Global errors --}}
            @if ($errors->any() && !($errors->has('email_otp') && $errors->count() === 1))
                <div class="mb-5 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3">
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-rose-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <p class="text-sm text-rose-700">Please check the form for errors.</p>
                </div>
            @endif
            <div id="otp-send-error"   class="hidden mb-5 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"></div>
            <div id="otp-send-success" class="hidden mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"></div>

            {{-- ── Steps 1 & 2: OTP flow ── --}}
            @if (! $registerVerified)

                <form id="register-otp-form" class="space-y-5" method="POST" action="{{ route('register.otp') }}">
                    @csrf

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-foreground" for="service_area_id">
                            Service Area
                        </label>
                        <select id="service_area_id" name="service_area_id" required
                                class="h-12 w-full rounded-xl border border-border bg-background px-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring transition-shadow">
                            @forelse ($serviceAreas as $serviceArea)
                                <option
                                    value="{{ $serviceArea->id }}"
                                    data-allow-email="{{ $serviceArea->allow_email_login ? '1' : '0' }}"
                                    data-allow-phone="{{ $serviceArea->allow_phone_login ? '1' : '0' }}"
                                    {{ (string) old('service_area_id', $registerServiceAreaId ?? $defaultServiceAreaId) === (string) $serviceArea->id ? 'selected' : '' }}
                                >
                                    {{ $serviceArea->name }} ({{ $serviceArea->dial_code }})
                                </option>
                            @empty
                                <option value="" disabled selected>Add service areas in admin</option>
                            @endforelse
                        </select>
                        <x-input-error :messages="$errors->get('service_area_id')" class="mt-1.5" />
                    </div>

                    <div class="rounded-2xl border border-border bg-muted/20 p-4">
                        <div class="mb-4 flex gap-2">
                            <button type="button" id="register-email-toggle"
                                    class="flex-1 rounded-lg border py-2 text-center text-xs font-semibold transition-all text-primary shadow-sm"
                                    style="border-color: hsl(var(--primary)/0.3); background: hsl(var(--primary)/0.08);">
                                Email
                            </button>
                            <button type="button" id="register-phone-toggle"
                                    class="flex-1 rounded-lg border border-transparent py-2 text-center text-xs font-semibold text-muted-foreground transition-all">
                                Phone
                            </button>
                        </div>

                        <div id="register-email-panel">
                            <label class="mb-1.5 block text-sm font-semibold text-foreground" for="email">Email address</label>
                            <input id="email" type="email" name="email"
                                   value="{{ old('email', $registerEmail) }}"
                                   required autocomplete="username"
                                   placeholder="name@example.com"
                                   class="h-12 w-full rounded-xl border border-border bg-background px-4 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring transition-shadow"/>
                            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                        </div>

                        <div id="register-phone-panel" class="hidden">
                            <label class="mb-1.5 block text-sm font-semibold text-foreground" for="mobile">Mobile Number</label>
                            <input id="mobile" type="text" name="mobile"
                                   value="{{ old('mobile', $registerMobile) }}"
                                   autocomplete="tel" inputmode="numeric" pattern="[0-9]{6,15}"
                                   placeholder="98XXXXXXXX"
                                   class="h-12 w-full rounded-xl border border-border bg-background px-4 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring transition-shadow"/>
                            <x-input-error :messages="$errors->get('mobile')" class="mt-1.5" />
                        </div>
                    </div>

                    <button type="button" id="register-otp-send"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl py-3 text-sm font-semibold text-white shadow-soft transition-all hover:shadow-md hover:-translate-y-px"
                            style="background: #0e7c86;">
                        Send Verification OTP
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/>
                        </svg>
                    </button>
                </form>

                {{-- OTP Verify --}}
                <div id="register-otp-verify" class="{{ $otpSent ? '' : 'hidden' }} mt-6">
                    <form class="space-y-5" method="POST" action="{{ route('register.verify-otp') }}">
                        @csrf
                        <input type="hidden" id="register-verify-service-area" name="service_area_id"
                               value="{{ old('service_area_id', $registerServiceAreaId ?? $defaultServiceAreaId) }}">
                        <input type="hidden" id="register-verify-email"  name="email"  value="{{ old('email', $registerEmail) }}">
                        <input type="hidden" id="register-verify-mobile" name="mobile" value="{{ old('mobile', $registerMobile) }}">

                        <div class="rounded-2xl border border-border bg-muted/20 p-4">
                            <p class="mb-3 text-sm font-semibold text-foreground">Enter OTP</p>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div id="register-email-otp-field" class="{{ $registerEmail ? '' : 'hidden' }}">
                                    <label class="mb-1.5 block text-xs font-semibold text-muted-foreground" for="email_otp">Email OTP</label>
                                    <input id="email_otp" type="text" name="email_otp" value="{{ old('email_otp') }}"
                                           autocomplete="one-time-code"
                                           class="h-12 w-full rounded-xl border border-border bg-background px-4 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring transition-shadow"/>
                                    <x-input-error :messages="$errors->get('email_otp')" class="mt-1.5" />
                                </div>
                                <div id="register-phone-otp-field" class="{{ $registerMobile ? '' : 'hidden' }}">
                                    <label class="mb-1.5 block text-xs font-semibold text-muted-foreground" for="phone_otp">Phone OTP</label>
                                    <input id="phone_otp" type="text" name="phone_otp" value="{{ old('phone_otp') }}"
                                           autocomplete="one-time-code"
                                           class="h-12 w-full rounded-xl border border-border bg-background px-4 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring transition-shadow"/>
                                    <x-input-error :messages="$errors->get('phone_otp')" class="mt-1.5" />
                                </div>
                            </div>
                            <p id="register-dev-otp-email" class="mt-2 text-xs text-amber-600 {{ session('dev_otp_register_email') ? '' : 'hidden' }}">Dev OTP (email): {{ session('dev_otp_register_email') }}</p>
                            <p id="register-dev-otp-phone" class="mt-2 text-xs text-amber-600 {{ session('dev_otp_register_phone') ? '' : 'hidden' }}">Dev OTP (phone): {{ session('dev_otp_register_phone') }}</p>
                        </div>

                        <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl py-3 text-sm font-semibold text-white shadow-soft transition-all hover:shadow-md hover:-translate-y-px"
                                style="background: #0e7c86;">
                            Verify OTP
                        </button>
                    </form>
                </div>

            {{-- ── Step 3: Set Password ── --}}
            @else
                <form class="space-y-5" method="POST" action="{{ route('register') }}">
                    @csrf
                    <input type="hidden" name="service_area_id" value="{{ $registerServiceAreaId }}">
                    <input type="hidden" name="email"           value="{{ $registerEmail }}">
                    <input type="hidden" name="mobile"          value="{{ $registerMobile }}">

                    {{-- Verified contact chip --}}
                    <div class="flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm">
                        <svg class="h-4 w-4 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-emerald-700">
                            Verified: <span class="font-semibold">{{ $registerEmail ?: $registerMobile }}</span>
                        </span>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-foreground" for="name">Full Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                               required autofocus autocomplete="name"
                               placeholder="Your full name"
                               class="h-12 w-full rounded-xl border border-border bg-background px-4 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring transition-shadow"/>
                        <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-foreground" for="password">Password</label>
                        <input id="password" type="password" name="password"
                               required autocomplete="new-password"
                               placeholder="Create a strong password"
                               class="h-12 w-full rounded-xl border border-border bg-background px-4 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring transition-shadow"/>
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-foreground" for="password_confirmation">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               required autocomplete="new-password"
                               placeholder="Repeat your password"
                               class="h-12 w-full rounded-xl border border-border bg-background px-4 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring transition-shadow"/>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
                    </div>

                    <div>
                        <label class="inline-flex items-start gap-3 cursor-pointer text-sm text-muted-foreground">
                            <input type="checkbox" name="terms" value="1"
                                   class="mt-0.5 h-4 w-4 rounded border-border text-primary focus:ring-ring"
                                   required {{ old('terms') ? 'checked' : '' }}>
                            <span>I agree to the terms and conditions.</span>
                        </label>
                        <x-input-error :messages="$errors->get('terms')" class="mt-1.5" />
                    </div>

                    <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl py-3 text-sm font-semibold text-white shadow-soft transition-all hover:shadow-md hover:-translate-y-px"
                            style="background: #0e7c86;">
                        Create Account
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/>
                        </svg>
                    </button>
                </form>
            @endif

            <p class="mt-8 text-center text-sm text-muted-foreground">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">Sign in</a>
            </p>

        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const serviceAreaSelect  = document.getElementById('service_area_id');
        const emailToggle        = document.getElementById('register-email-toggle');
        const phoneToggle        = document.getElementById('register-phone-toggle');
        const emailPanel         = document.getElementById('register-email-panel');
        const phonePanel         = document.getElementById('register-phone-panel');
        const emailInput         = document.getElementById('email');
        const phoneInput         = document.getElementById('mobile');
        const otpForm            = document.getElementById('register-otp-form');
        const otpSendButton      = document.getElementById('register-otp-send');
        const otpError           = document.getElementById('otp-send-error');
        const otpSuccess         = document.getElementById('otp-send-success');
        const otpVerify          = document.getElementById('register-otp-verify');
        const verifyEmailField   = document.getElementById('register-email-otp-field');
        const verifyPhoneField   = document.getElementById('register-phone-otp-field');
        const verifyServiceArea  = document.getElementById('register-verify-service-area');
        const verifyEmail        = document.getElementById('register-verify-email');
        const verifyMobile       = document.getElementById('register-verify-mobile');
        const devOtpEmail        = document.getElementById('register-dev-otp-email');
        const devOtpPhone        = document.getElementById('register-dev-otp-phone');
        const csrfToken          = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const activeTab  = 'border-color: hsl(var(--primary)/0.3); background: hsl(var(--primary)/0.08);';
        const inactiveTab = '';

        const setActiveMode = (mode) => {
            if (!emailToggle || !phoneToggle || !emailPanel || !phonePanel) return;
            const isEmail = mode === 'email';
            emailPanel.classList.toggle('hidden', !isEmail);
            phonePanel.classList.toggle('hidden', isEmail);
            emailToggle.style.cssText = isEmail   ? activeTab : inactiveTab;
            phoneToggle.style.cssText = !isEmail  ? activeTab : inactiveTab;
            emailToggle.classList.toggle('text-primary',          isEmail);
            emailToggle.classList.toggle('text-muted-foreground', !isEmail);
            emailToggle.classList.toggle('border-transparent',    !isEmail);
            phoneToggle.classList.toggle('text-primary',          !isEmail);
            phoneToggle.classList.toggle('text-muted-foreground', isEmail);
            phoneToggle.classList.toggle('border-transparent',    isEmail);
            if (emailInput) emailInput.required = isEmail;
            if (phoneInput) phoneInput.required = !isEmail;
        };

        const updateServiceAreaFields = () => {
            if (!serviceAreaSelect) return;
            const selected  = serviceAreaSelect.options[serviceAreaSelect.selectedIndex];
            const allowEmail = selected?.dataset?.allowEmail === '1';
            const allowPhone = selected?.dataset?.allowPhone === '1';
            if (emailToggle) emailToggle.classList.toggle('hidden', !allowEmail);
            if (phoneToggle) phoneToggle.classList.toggle('hidden', !allowPhone);
            if (!allowEmail && allowPhone) setActiveMode('phone');
            else setActiveMode('email');
            if (!allowPhone && phoneInput) phoneInput.value = '';
            if (!allowEmail && emailInput) emailInput.value = '';
        };

        emailToggle?.addEventListener('click',  () => setActiveMode('email'));
        phoneToggle?.addEventListener('click',  () => setActiveMode('phone'));
        serviceAreaSelect?.addEventListener('change', updateServiceAreaFields);
        updateServiceAreaFields();

        if (otpSendButton && otpForm) {
            let cooldownActive = false;
            const setStatus = (element, message) => {
                if (!element) return;
                if (!message) { element.classList.add('hidden'); element.textContent = ''; return; }
                element.textContent = message;
                element.classList.remove('hidden');
            };
            const startCooldown = (seconds) => {
                if (!seconds || seconds <= 0) return;
                let remaining = seconds;
                cooldownActive = true;
                otpSendButton.disabled = true;
                const tick = () => {
                    if (remaining <= 0) { otpSendButton.disabled = false; otpSendButton.textContent = 'Send Verification OTP'; cooldownActive = false; return; }
                    otpSendButton.textContent = `Send OTP (${remaining}s)`;
                    remaining -= 1;
                    setTimeout(tick, 1000);
                };
                tick();
            };

            otpSendButton.addEventListener('click', async () => {
                const action = otpForm.getAttribute('action');
                if (!action) return;
                setStatus(otpError, ''); setStatus(otpSuccess, '');
                if (devOtpEmail) devOtpEmail.classList.add('hidden');
                if (devOtpPhone) devOtpPhone.classList.add('hidden');
                const formData = new FormData(otpForm);
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
                            setStatus(otpError, cooldown ? `Please wait ${cooldown}s before resending.` : 'Too many requests. Please wait and try again.');
                            if (cooldown) startCooldown(cooldown);
                            return;
                        }
                        setStatus(otpError, data.message || (response.status === 419 ? 'Session expired. Refresh and try again.' : 'Unable to send OTP. Please try again.'));
                        return;
                    }
                    if (data.redirect) { window.location.assign(data.redirect); return; }
                    const emailOtp = data.dev_otp_register_email || data.dev_otp_email;
                    const phoneOtp = data.dev_otp_register_phone || data.dev_otp_phone;
                    if (emailOtp && devOtpEmail) { devOtpEmail.textContent = `Dev OTP (email): ${emailOtp}`; devOtpEmail.classList.remove('hidden'); }
                    if (phoneOtp && devOtpPhone) { devOtpPhone.textContent = `Dev OTP (phone): ${phoneOtp}`; devOtpPhone.classList.remove('hidden'); }
                    setStatus(otpSuccess, data.status || data.message || 'OTP sent successfully.');
                    if (verifyServiceArea) verifyServiceArea.value = data.register_service_area_id || formData.get('service_area_id') || '';
                    if (verifyEmail)  verifyEmail.value  = data.register_email  || formData.get('email')  || '';
                    if (verifyMobile) verifyMobile.value = data.register_mobile || formData.get('mobile') || '';
                    if (verifyEmailField)  verifyEmailField.classList.toggle('hidden',  !verifyEmail?.value);
                    if (verifyPhoneField)  verifyPhoneField.classList.toggle('hidden',  !verifyMobile?.value);
                    if (otpVerify) otpVerify.classList.remove('hidden');
                    if (data.cooldown) { startCooldown(data.cooldown); return; }
                } catch (error) {
                    setStatus(otpError, 'Unable to send OTP. Please try again.');
                } finally {
                    if (!cooldownActive) { otpSendButton.disabled = false; otpSendButton.textContent = originalLabel; }
                }
            });
        }
    });
</script>
@endsection
