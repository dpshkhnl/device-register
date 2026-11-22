@extends('layouts.app')

@section('title', 'Sign in')

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
    $showOtpPanel        = $authRequiresOtp && ($devOtpEmail || $devOtpPhone || session('otp_purpose') === 'auth_login' || $errors->has('otp') || old('otp'));
@endphp

<div class="container grid min-h-[calc(100vh-4rem)] gap-10 py-8 lg:grid-cols-2 lg:py-10">
    @include('partials.auth-aside', [
        'title' => 'Welcome back',
        'text' => 'Sign in to manage your registered devices, transfers and lost reports.',
        'points' => ['Instant IMEI verification', 'Secure ownership transfers', 'Lost & stolen protection'],
    ])

    <div class="flex items-center justify-center">
        <div class="w-full max-w-md py-6">
            <h1 class="font-display text-5xl text-foreground">Sign in</h1>
            <p class="mt-2 text-sm text-muted-foreground">
                @if ($authFlow === 'existing') Enter your password to continue. @else Use the email or phone number on your account. @endif
            </p>

            @if ($errors->any())
                <div class="callout callout-danger mt-6" role="alert">
                    <x-site.icon name="alert" class="mt-px h-5 w-5 shrink-0" />
                    <p>{{ $errors->first() ?: 'Please check your details and try again.' }}</p>
                </div>
            @endif

            {{-- Step 1: identify --}}
            @if (! $authFlow)
                <form class="mt-8 space-y-5" method="POST" action="{{ route('login.identify') }}">
                    @csrf
                    <div>
                        <label class="field-label" for="service_area_id">Country / service area</label>
                        <select id="service_area_id" name="service_area_id" class="field-input">
                            @forelse ($serviceAreas as $serviceArea)
                                <option value="{{ $serviceArea->id }}"
                                        data-allow-email="{{ $serviceArea->allow_email_login ? '1' : '0' }}"
                                        data-require-email-otp="{{ $serviceArea->require_email_otp ? '1' : '0' }}"
                                        data-allow-phone="{{ $serviceArea->allow_phone_login ? '1' : '0' }}"
                                        data-require-phone-otp="{{ $serviceArea->require_phone_otp ? '1' : '0' }}"
                                        @selected((string) old('service_area_id', $authServiceAreaId) === (string) $serviceArea->id)>
                                    {{ $serviceArea->name }} ({{ $serviceArea->dial_code }})
                                </option>
                            @empty
                                <option value="" disabled selected>No service areas configured</option>
                            @endforelse
                        </select>
                        <x-site.error :messages="$errors->get('service_area_id')" />
                    </div>

                    <div>
                        <label class="field-label" for="login">Email or phone</label>
                        <input id="login" type="text" name="login" value="{{ old('login') }}" autocomplete="username" autofocus
                               placeholder="name@example.com or 98XXXXXXXX" class="field-input" />
                        <p id="login-hint" class="field-help"></p>
                        <x-site.error :messages="$errors->get('login')" />
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-full">
                        Continue <x-site.icon name="arrow-right" class="h-4 w-4" stroke="2.2" />
                    </button>
                </form>
            @endif

            {{-- Step 2: authenticate --}}
            @if ($authFlow === 'existing')
                <form class="mt-8 space-y-5" method="POST" action="{{ route('login') }}">
                    @csrf
                    <input type="hidden" name="login" value="{{ $authLogin }}">
                    <input type="hidden" name="service_area_id" value="{{ $authServiceAreaId }}">

                    <div class="flex items-center justify-between gap-3 rounded-xl border border-border bg-card px-4 py-3">
                        <span class="flex min-w-0 items-center gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-accent text-accent-foreground"><x-site.icon name="user" class="h-4 w-4" /></span>
                            <span class="truncate text-sm font-semibold text-foreground">{{ $authLogin }}</span>
                        </span>
                        <a href="{{ route('login', ['reset' => 1]) }}" class="text-xs font-semibold text-primary hover:underline">Not you?</a>
                    </div>

                    @if ($authRequiresOtp)
                        <div class="seg" role="tablist">
                            <button type="button" id="login-password-toggle" class="seg-btn" data-active="{{ $showOtpPanel ? 'false' : 'true' }}">Password</button>
                            <button type="button" id="login-otp-toggle" class="seg-btn" data-active="{{ $showOtpPanel ? 'true' : 'false' }}">One-time code</button>
                        </div>
                    @endif

                    <div id="login-password-panel" class="{{ $showOtpPanel ? 'hidden' : '' }}">
                        <div class="mb-1.5 flex items-center justify-between">
                            <label class="field-label mb-0" for="password">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-primary hover:underline">Forgot?</a>
                            @endif
                        </div>
                        <input id="password" type="password" name="password" autocomplete="current-password" autofocus class="field-input" />
                        <x-site.error :messages="$errors->get('password')" />
                    </div>

                    @if ($authRequiresOtp)
                        <div id="login-otp-panel" class="{{ $showOtpPanel ? '' : 'hidden' }}">
                            <label class="field-label" for="otp">One-time code</label>
                            <div class="flex gap-2">
                                <input id="otp" type="text" name="otp" value="{{ old('otp') }}" autocomplete="one-time-code" inputmode="numeric" placeholder="••••••" class="field-input otp-input flex-1" />
                                <button type="button" id="login-otp-send" formaction="{{ route('login.otp') }}" class="btn btn-secondary h-12">Send code</button>
                            </div>
                            <p id="login-otp-status" class="callout callout-success mt-3 hidden"></p>
                            <p id="login-dev-otp-email" class="field-help text-amber-600 {{ $devOtpEmail ? '' : 'hidden' }}">Dev OTP (email): {{ $devOtpEmail }}</p>
                            <p id="login-dev-otp-phone" class="field-help text-amber-600 {{ $devOtpPhone ? '' : 'hidden' }}">Dev OTP (phone): {{ $devOtpPhone }}</p>
                            <x-site.error :messages="$errors->get('otp')" />
                        </div>
                    @endif

                    <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-muted-foreground">
                        <input type="checkbox" name="remember" class="h-4 w-4 rounded border-input text-primary focus:ring-ring" />
                        Keep me signed in
                    </label>

                    <button type="submit" class="btn btn-primary btn-lg w-full">
                        Sign in <x-site.icon name="arrow-right" class="h-4 w-4" stroke="2.2" />
                    </button>
                </form>
            @endif

            @if (Route::has('register'))
                <p class="mt-10 border-t border-border pt-6 text-center text-sm text-muted-foreground">
                    New here?
                    <a href="{{ route('register') }}" class="font-semibold text-primary hover:underline">Create a free account</a>
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
                const selected = serviceSelect.options[serviceSelect.selectedIndex];
                if (!selected) return;
                const allowEmail = selected.dataset.allowEmail === '1';
                const allowPhone = selected.dataset.allowPhone === '1';
                if (allowEmail && allowPhone) loginHint.textContent = 'You can sign in with email or phone.';
                else if (allowEmail)          loginHint.textContent = 'This area allows email sign-in only.';
                else if (allowPhone)          loginHint.textContent = 'This area allows phone sign-in only.';
                else                          loginHint.textContent = 'Sign-in is disabled for this area.';
            };
            serviceSelect.addEventListener('change', updateHint);
            updateHint();
        }

        if (passwordToggle && otpToggle && otpPanel && passwordPanel) {
            const setMode = (mode) => {
                const isPassword = mode === 'password';
                otpPanel.classList.toggle('hidden', isPassword);
                passwordPanel.classList.toggle('hidden', !isPassword);
                passwordToggle.dataset.active = String(isPassword);
                otpToggle.dataset.active = String(!isPassword);
            };
            passwordToggle.addEventListener('click', () => setMode('password'));
            otpToggle.addEventListener('click', () => setMode('otp'));
            setMode(@json($showOtpPanel ? 'otp' : 'password'));
        }

        if (otpSendButton) {
            const form = otpSendButton.closest('form');
            const idleLabel = 'Send code';
            let cooldownActive = false;
            const setOtpStatus = (message, isError = false) => {
                if (!otpStatus) return;
                otpStatus.textContent = message || '';
                otpStatus.classList.toggle('hidden', !message);
                otpStatus.classList.toggle('callout-success', !isError);
                otpStatus.classList.toggle('callout-danger', isError);
            };
            const startCooldown = (seconds) => {
                if (!seconds || seconds <= 0) return;
                let remaining = seconds;
                cooldownActive = true;
                otpSendButton.disabled = true;
                const tick = () => {
                    if (remaining <= 0) { otpSendButton.disabled = false; otpSendButton.textContent = 'Resend code'; cooldownActive = false; return; }
                    otpSendButton.textContent = `Resend in ${remaining}s`;
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
                devOtpEmail?.classList.add('hidden');
                devOtpPhone?.classList.add('hidden');
                const formData = new FormData(form);
                formData.delete('password'); formData.delete('remember'); formData.delete('otp');
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
                        setOtpStatus(data.message || (response.status === 419 ? 'Session expired. Refresh and try again.' : 'Unable to send the code. Please try again.'), true);
                        return;
                    }
                    const emailOtp = data.dev_otp_login_email || data.dev_otp_email;
                    const phoneOtp = data.dev_otp_login_phone || data.dev_otp_phone;
                    if (emailOtp && devOtpEmail) { devOtpEmail.textContent = `Dev OTP (email): ${emailOtp}`; devOtpEmail.classList.remove('hidden'); }
                    if (phoneOtp && devOtpPhone) { devOtpPhone.textContent = `Dev OTP (phone): ${phoneOtp}`; devOtpPhone.classList.remove('hidden'); }
                    if (data.dev_otp && data.channel) { const t = data.channel === 'email' ? devOtpEmail : devOtpPhone; if (t) { t.textContent = `Dev OTP (${data.channel}): ${data.dev_otp}`; t.classList.remove('hidden'); } }
                    setOtpStatus(data.status || data.message || 'Code sent. Check your email or phone.');
                    document.getElementById('otp')?.focus();
                    if (data.cooldown) { startCooldown(data.cooldown); return; }
                    if (data.resend_seconds) startCooldown(data.resend_seconds);
                } catch (error) {
                    setOtpStatus('Unable to send the code. Please try again.', true);
                } finally {
                    if (!cooldownActive) { otpSendButton.disabled = false; otpSendButton.textContent = idleLabel; }
                }
            });
        }
    });
</script>
@endsection
