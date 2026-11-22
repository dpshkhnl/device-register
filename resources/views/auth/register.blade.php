@extends('layouts.app')

@section('title', 'Create account')

@section('content')
@php
    $serviceAreas          = $serviceAreas ?? collect();
    $defaultServiceAreaId  = $defaultServiceAreaId ?? null;
    $otpEnabled            = $otpEnabled ?? false;
    $registerVerified      = session('register_verified');
    $registerEmail         = session('register_email');
    $registerMobile        = session('register_mobile');
    $registerServiceAreaId = session('register_service_area_id');
    $otpSent               = session('otp_sent_email') || session('otp_sent_phone');
    $registerStep          = $registerVerified ? 3 : ($otpSent ? 2 : 1);
    $showGlobalError       = $errors->any() && ! ($errors->has('email_otp') && $errors->count() === 1);
@endphp

<div class="container grid min-h-[calc(100vh-4rem)] gap-10 py-8 lg:grid-cols-2 lg:py-10">
    @include('partials.auth-aside', [
        'title' => 'Join the registry',
        'text' => 'Register your devices, transfer ownership and report losses — all in one place. Free to join.',
        'steps' => [
            ['label' => 'Your contact', 'desc' => 'Email or phone number'],
            ['label' => 'Verify', 'desc' => 'Enter the code we send you'],
            ['label' => 'Finish', 'desc' => 'Name and password'],
        ],
        'currentStep' => $registerStep,
    ])

    <div class="flex items-start justify-center lg:items-center">
        <div class="w-full max-w-md py-6">
            {{-- Mobile progress --}}
            <div class="mb-6 flex items-center gap-2 lg:hidden">
                @for ($i = 1; $i <= 3; $i++)
                    <span class="h-1.5 flex-1 rounded-full {{ $i <= $registerStep ? 'bg-primary' : 'bg-border' }}"></span>
                @endfor
                <span class="ml-2 text-xs font-semibold text-muted-foreground">Step {{ $registerStep }} of 3</span>
            </div>

            <h1 class="font-display text-5xl text-foreground">Create account</h1>
            <p class="mt-2 text-sm text-muted-foreground">
                @if ($registerStep === 1) We’ll send a code to confirm it’s really you.
                @elseif ($registerStep === 2) Enter the code we just sent you.
                @else Almost there — set your name and password.
                @endif
            </p>

            @if ($showGlobalError)
                <div class="callout callout-danger mt-6" role="alert">
                    <x-site.icon name="alert" class="mt-px h-5 w-5 shrink-0" />
                    <p>Please check the highlighted fields.</p>
                </div>
            @endif
            <div id="otp-send-error" class="callout callout-danger mt-6 hidden"></div>
            <div id="otp-send-success" class="callout callout-success mt-6 hidden"></div>

            @if (! $registerVerified)
                {{-- Steps 1 & 2 --}}
                <form id="register-otp-form" class="mt-8 space-y-5" method="POST" action="{{ route('register.otp') }}">
                    @csrf
                    <div>
                        <label class="field-label" for="service_area_id">Country / service area</label>
                        <select id="service_area_id" name="service_area_id" required class="field-input">
                            @forelse ($serviceAreas as $serviceArea)
                                <option value="{{ $serviceArea->id }}"
                                        data-allow-email="{{ $serviceArea->allow_email_login ? '1' : '0' }}"
                                        data-allow-phone="{{ $serviceArea->allow_phone_login ? '1' : '0' }}"
                                        @selected((string) old('service_area_id', $registerServiceAreaId ?? $defaultServiceAreaId) === (string) $serviceArea->id)>
                                    {{ $serviceArea->name }} ({{ $serviceArea->dial_code }})
                                </option>
                            @empty
                                <option value="" disabled selected>No service areas configured</option>
                            @endforelse
                        </select>
                        <x-site.error :messages="$errors->get('service_area_id')" />
                    </div>

                    <div class="space-y-3">
                        <div class="seg" role="tablist" aria-label="Sign up with">
                            <button type="button" id="register-email-toggle" class="seg-btn" data-active="true">Email</button>
                            <button type="button" id="register-phone-toggle" class="seg-btn" data-active="false">Phone</button>
                        </div>
                        <div id="register-email-panel">
                            <label class="field-label" for="email">Email address</label>
                            <input id="email" type="email" name="email" value="{{ old('email', $registerEmail) }}" required autocomplete="username" placeholder="name@example.com" class="field-input" />
                            <x-site.error :messages="$errors->get('email')" />
                        </div>
                        <div id="register-phone-panel" class="hidden">
                            <label class="field-label" for="mobile">Mobile number</label>
                            <input id="mobile" type="text" name="mobile" value="{{ old('mobile', $registerMobile) }}" autocomplete="tel" inputmode="numeric" pattern="[0-9]{6,15}" placeholder="98XXXXXXXX" class="field-input" />
                            <x-site.error :messages="$errors->get('mobile')" />
                        </div>
                    </div>

                    <button type="button" id="register-otp-send" class="btn btn-primary btn-lg w-full">Send verification code</button>
                </form>

                <div id="register-otp-verify" class="{{ $otpSent ? '' : 'hidden' }} mt-8 border-t border-border pt-8">
                    <form class="space-y-5" method="POST" action="{{ route('register.verify-otp') }}">
                        @csrf
                        <input type="hidden" id="register-verify-service-area" name="service_area_id" value="{{ old('service_area_id', $registerServiceAreaId ?? $defaultServiceAreaId) }}">
                        <input type="hidden" id="register-verify-email" name="email" value="{{ old('email', $registerEmail) }}">
                        <input type="hidden" id="register-verify-mobile" name="mobile" value="{{ old('mobile', $registerMobile) }}">

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div id="register-email-otp-field" class="{{ $registerEmail ? '' : 'hidden' }} sm:col-span-2">
                                <label class="field-label" for="email_otp">Code from your email</label>
                                <input id="email_otp" type="text" name="email_otp" value="{{ old('email_otp') }}" autocomplete="one-time-code" inputmode="numeric" placeholder="••••••" class="field-input otp-input" />
                                <x-site.error :messages="$errors->get('email_otp')" />
                            </div>
                            <div id="register-phone-otp-field" class="{{ $registerMobile ? '' : 'hidden' }} sm:col-span-2">
                                <label class="field-label" for="phone_otp">Code from your SMS</label>
                                <input id="phone_otp" type="text" name="phone_otp" value="{{ old('phone_otp') }}" autocomplete="one-time-code" inputmode="numeric" placeholder="••••••" class="field-input otp-input" />
                                <x-site.error :messages="$errors->get('phone_otp')" />
                            </div>
                        </div>
                        <p id="register-dev-otp-email" class="field-help text-amber-600 {{ session('dev_otp_register_email') ? '' : 'hidden' }}">Dev OTP (email): {{ session('dev_otp_register_email') }}</p>
                        <p id="register-dev-otp-phone" class="field-help text-amber-600 {{ session('dev_otp_register_phone') ? '' : 'hidden' }}">Dev OTP (phone): {{ session('dev_otp_register_phone') }}</p>
                        @if (app()->environment('local'))
                            <p class="field-help text-amber-600">Testing: default OTP <span class="font-mono font-semibold">123456</span> works for email and phone.</p>
                        @endif

                        <button type="submit" class="btn btn-primary btn-lg w-full">Verify code <x-site.icon name="arrow-right" class="h-4 w-4" stroke="2.2" /></button>
                    </form>
                </div>
            @else
                {{-- Step 3 --}}
                <form class="mt-8 space-y-5" method="POST" action="{{ route('register') }}" enctype="multipart/form-data"
                      x-data="{ accountType: @js(old('account_type', 'customer')) }">
                    @csrf
                    <input type="hidden" name="service_area_id" value="{{ $registerServiceAreaId }}">
                    <input type="hidden" name="email" value="{{ $registerEmail }}">
                    <input type="hidden" name="mobile" value="{{ $registerMobile }}">

                    <div class="callout callout-success">
                        <x-site.icon name="check-circle" class="mt-px h-5 w-5 shrink-0" />
                        <p>Verified <span class="font-semibold">{{ $registerEmail ?: $registerMobile }}</span></p>
                    </div>

                    <div>
                        <label class="field-label" for="name">Full name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="As on your ID" class="field-input" />
                        <x-site.error :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <span class="field-label">I’m signing up as</span>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="choice p-3">
                                <input type="radio" name="account_type" value="customer" x-model="accountType" class="sr-only">
                                <x-site.icon name="user" class="h-5 w-5 text-primary" />
                                <span class="text-sm font-semibold text-foreground">Individual</span>
                            </label>
                            <label class="choice p-3">
                                <input type="radio" name="account_type" value="shop" x-model="accountType" class="sr-only">
                                <x-site.icon name="store" class="h-5 w-5 text-primary" />
                                <span class="text-sm font-semibold text-foreground">Shop / reseller</span>
                            </label>
                        </div>
                        <x-site.error :messages="$errors->get('account_type')" />
                    </div>

                    <div x-show="accountType === 'shop'" @style(['display: none' => old('account_type') !== 'shop']) class="surface-muted space-y-4 p-4">
                        <p class="text-xs text-muted-foreground">Shop accounts are reviewed by our team. You can use your account as an individual until it’s approved.</p>
                        <div>
                            <label class="field-label" for="shop_name">Shop name</label>
                            <input id="shop_name" type="text" name="shop_name" value="{{ old('shop_name') }}" maxlength="160" :required="accountType === 'shop'" :disabled="accountType !== 'shop'" class="field-input" />
                            <x-site.error :messages="$errors->get('shop_name')" />
                        </div>
                        <div>
                            <label class="field-label" for="shop_address">Shop address</label>
                            <input id="shop_address" type="text" name="shop_address" value="{{ old('shop_address') }}" maxlength="255" :required="accountType === 'shop'" :disabled="accountType !== 'shop'" class="field-input" />
                            <x-site.error :messages="$errors->get('shop_address')" />
                        </div>
                        <div>
                            <label class="field-label" for="business_registration">Business registration <span class="font-normal text-muted-foreground">(PDF or image)</span></label>
                            <input id="business_registration" type="file" name="business_registration" accept=".pdf,.jpg,.jpeg,.png,.webp" :required="accountType === 'shop'" :disabled="accountType !== 'shop'" class="field-input" />
                            <x-site.error :messages="$errors->get('business_registration')" />
                        </div>
                        <div>
                            <label class="field-label" for="store_photo">Store photo</label>
                            <input id="store_photo" type="file" name="store_photo" accept=".jpg,.jpeg,.png,.webp" :required="accountType === 'shop'" :disabled="accountType !== 'shop'" class="field-input" />
                            <x-site.error :messages="$errors->get('store_photo')" />
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="field-label" for="password">Password</label>
                            <input id="password" type="password" name="password" required autocomplete="new-password" class="field-input" />
                            <x-site.error :messages="$errors->get('password')" />
                        </div>
                        <div>
                            <label class="field-label" for="password_confirmation">Confirm password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="field-input" />
                            <x-site.error :messages="$errors->get('password_confirmation')" />
                        </div>
                    </div>

                    <label class="flex cursor-pointer items-start gap-3 text-sm text-muted-foreground">
                        <input type="checkbox" name="terms" value="1" required @checked(old('terms')) class="mt-0.5 h-4 w-4 rounded border-input text-primary focus:ring-ring">
                        <span>I agree to the terms and conditions.</span>
                    </label>
                    <x-site.error :messages="$errors->get('terms')" />

                    <button type="submit" class="btn btn-primary btn-lg w-full">Create account <x-site.icon name="arrow-right" class="h-4 w-4" stroke="2.2" /></button>
                </form>
            @endif

            <p class="mt-10 border-t border-border pt-6 text-center text-sm text-muted-foreground">
                Already registered?
                <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">Sign in</a>
            </p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const serviceAreaSelect = document.getElementById('service_area_id');
        const emailToggle       = document.getElementById('register-email-toggle');
        const phoneToggle       = document.getElementById('register-phone-toggle');
        const emailPanel        = document.getElementById('register-email-panel');
        const phonePanel        = document.getElementById('register-phone-panel');
        const emailInput        = document.getElementById('email');
        const phoneInput        = document.getElementById('mobile');
        const otpForm           = document.getElementById('register-otp-form');
        const otpSendButton     = document.getElementById('register-otp-send');
        const otpError          = document.getElementById('otp-send-error');
        const otpSuccess        = document.getElementById('otp-send-success');
        const otpVerify         = document.getElementById('register-otp-verify');
        const verifyEmailField  = document.getElementById('register-email-otp-field');
        const verifyPhoneField  = document.getElementById('register-phone-otp-field');
        const verifyServiceArea = document.getElementById('register-verify-service-area');
        const verifyEmail       = document.getElementById('register-verify-email');
        const verifyMobile      = document.getElementById('register-verify-mobile');
        const devOtpEmail       = document.getElementById('register-dev-otp-email');
        const devOtpPhone       = document.getElementById('register-dev-otp-phone');
        const csrfToken         = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const idleLabel         = 'Send verification code';

        const setActiveMode = (mode) => {
            if (!emailToggle || !phoneToggle || !emailPanel || !phonePanel) return;
            const isEmail = mode === 'email';
            emailPanel.classList.toggle('hidden', !isEmail);
            phonePanel.classList.toggle('hidden', isEmail);
            emailToggle.dataset.active = String(isEmail);
            phoneToggle.dataset.active = String(!isEmail);
            if (emailInput) emailInput.required = isEmail;
            if (phoneInput) phoneInput.required = !isEmail;
        };

        const updateServiceAreaFields = () => {
            if (!serviceAreaSelect) return;
            const selected   = serviceAreaSelect.options[serviceAreaSelect.selectedIndex];
            const allowEmail = selected?.dataset?.allowEmail === '1';
            const allowPhone = selected?.dataset?.allowPhone === '1';
            emailToggle?.classList.toggle('hidden', !allowEmail);
            phoneToggle?.classList.toggle('hidden', !allowPhone);
            if (!allowEmail && allowPhone) setActiveMode('phone');
            else setActiveMode('email');
            if (!allowPhone && phoneInput) phoneInput.value = '';
            if (!allowEmail && emailInput) emailInput.value = '';
        };

        emailToggle?.addEventListener('click', () => setActiveMode('email'));
        phoneToggle?.addEventListener('click', () => setActiveMode('phone'));
        serviceAreaSelect?.addEventListener('change', updateServiceAreaFields);
        updateServiceAreaFields();
        if (phoneInput?.value && !emailInput?.value && !phoneToggle?.classList.contains('hidden')) setActiveMode('phone');

        if (otpSendButton && otpForm) {
            let cooldownActive = false;
            const setStatus = (element, message) => {
                if (!element) return;
                element.textContent = message || '';
                element.classList.toggle('hidden', !message);
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
                if (!otpForm.reportValidity()) return;
                const action = otpForm.getAttribute('action');
                if (!action) return;
                setStatus(otpError, ''); setStatus(otpSuccess, '');
                devOtpEmail?.classList.add('hidden');
                devOtpPhone?.classList.add('hidden');
                const formData = new FormData(otpForm);
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
                        setStatus(otpError, data.message || (response.status === 419 ? 'Session expired. Refresh and try again.' : 'Unable to send the code. Please try again.'));
                        return;
                    }
                    if (data.redirect) { window.location.assign(data.redirect); return; }
                    const emailOtp = data.dev_otp_register_email || data.dev_otp_email;
                    const phoneOtp = data.dev_otp_register_phone || data.dev_otp_phone;
                    if (emailOtp && devOtpEmail) { devOtpEmail.textContent = `Dev OTP (email): ${emailOtp}`; devOtpEmail.classList.remove('hidden'); }
                    if (phoneOtp && devOtpPhone) { devOtpPhone.textContent = `Dev OTP (phone): ${phoneOtp}`; devOtpPhone.classList.remove('hidden'); }
                    setStatus(otpSuccess, data.status || data.message || 'Code sent.');
                    if (verifyServiceArea) verifyServiceArea.value = data.register_service_area_id || formData.get('service_area_id') || '';
                    if (verifyEmail)  verifyEmail.value  = data.register_email  || formData.get('email')  || '';
                    if (verifyMobile) verifyMobile.value = data.register_mobile || formData.get('mobile') || '';
                    verifyEmailField?.classList.toggle('hidden', !verifyEmail?.value);
                    verifyPhoneField?.classList.toggle('hidden', !verifyMobile?.value);
                    otpVerify?.classList.remove('hidden');
                    (verifyEmail?.value ? document.getElementById('email_otp') : document.getElementById('phone_otp'))?.focus();
                    if (data.cooldown) { startCooldown(data.cooldown); return; }
                } catch (error) {
                    setStatus(otpError, 'Unable to send the code. Please try again.');
                } finally {
                    if (!cooldownActive) { otpSendButton.disabled = false; otpSendButton.textContent = idleLabel; }
                }
            });
        }
    });
</script>
@endsection
