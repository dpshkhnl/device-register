@extends('layouts.app')

@section('content')
@php
    $serviceAreas = $serviceAreas ?? collect();
    $defaultServiceAreaId = $defaultServiceAreaId ?? null;
    $otpEnabled = $otpEnabled ?? false;
    $showOtp = $otpEnabled && (old('email_otp') || $errors->has('email_otp') || $errors->has('phone_otp') || session('otp_purpose') === 'auth_register' || session('dev_otp_register_email') || session('dev_otp_register_phone'));
    $registerVerified = session('register_verified');
    $registerEmail = session('register_email');
    $registerMobile = session('register_mobile');
    $registerServiceAreaId = session('register_service_area_id');
    $otpSent = session('otp_sent_email') || session('otp_sent_phone');
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
                        <h1 class="mt-6 text-3xl font-bold text-foreground">Create your account</h1>
                        <p class="mt-3 text-sm text-muted-foreground">Register a device, transfer ownership, and download certificates.</p>
                    </div>
                    <img src="/images/auth-register.svg" alt="Register illustration" class="mt-10 w-full max-w-sm" />
                </div>
            </div>
            <div class="p-6 sm:p-10">
                @php
                    $registerStep = $registerVerified ? 3 : ($otpSent ? 2 : 1);
                @endphp
                <div class="mb-6">
                    <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Step {{ $registerStep }} of 3</p>
                    <div class="mt-2 flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full {{ $registerStep >= 1 ? 'bg-primary' : 'bg-border' }}"></span>
                        <span class="h-1 w-10 rounded-full {{ $registerStep >= 2 ? 'bg-primary' : 'bg-border' }}"></span>
                        <span class="h-2 w-2 rounded-full {{ $registerStep >= 2 ? 'bg-primary' : 'bg-border' }}"></span>
                        <span class="h-1 w-10 rounded-full {{ $registerStep >= 3 ? 'bg-primary' : 'bg-border' }}"></span>
                        <span class="h-2 w-2 rounded-full {{ $registerStep >= 3 ? 'bg-primary' : 'bg-border' }}"></span>
                    </div>
                </div>
                <div class="space-y-2">
                    <h2 class="text-2xl font-semibold text-foreground">Create account</h2>
                </div>

                @if ($errors->any() && !($errors->has('email_otp') && $errors->count() === 1))
                    <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        Please check the form for errors.
                    </div>
                @endif
                <div id="otp-send-error" class="hidden mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"></div>

                @if (! $registerVerified)
                    <form class="mt-6 space-y-4" method="POST" action="{{ route('register.otp') }}">
                        @csrf
                        <div>
                            <label class="text-sm font-medium text-foreground" for="service_area_id">Service Area</label>
                            <select id="service_area_id" name="service_area_id" required class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-3 text-sm">
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
                            <x-input-error :messages="$errors->get('service_area_id')" class="mt-2" />
                        </div>
                        <div class="rounded-xl border border-border bg-muted/30 p-4">
                            <div class="mb-3 flex items-center gap-2 text-xs font-semibold text-muted-foreground">
                                <button type="button" id="register-email-toggle" class="flex-1 rounded-lg border px-3 py-2 text-center border-primary bg-primary/10 text-primary shadow-sm">
                                    Email
                                </button>
                                <button type="button" id="register-phone-toggle" class="flex-1 rounded-lg border px-3 py-2 text-center border-transparent">
                                    Phone
                                </button>
                            </div>
                            <div id="register-email-panel">
                                <label class="text-sm font-medium text-foreground" for="email">Email</label>
                                <input id="email" type="email" name="email" value="{{ old('email', $registerEmail) }}" required autocomplete="username" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div id="register-phone-panel" class="hidden">
                                <label class="text-sm font-medium text-foreground" for="mobile">Mobile Number</label>
                                <input id="mobile" type="text" name="mobile" value="{{ old('mobile', $registerMobile) }}" autocomplete="tel" inputmode="numeric" pattern="[0-9]{6,15}" placeholder="98XXXXXXXX" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                                <x-input-error :messages="$errors->get('mobile')" class="mt-2" />
                            </div>
                        </div>
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">Send OTP</button>
                    </form>

                    @if ($otpSent)
                        <form class="mt-6 space-y-4" method="POST" action="{{ route('register.verify-otp') }}">
                            @csrf
                            <input type="hidden" name="service_area_id" value="{{ old('service_area_id', $registerServiceAreaId ?? $defaultServiceAreaId) }}">
                            @if ($registerEmail)
                                <input type="hidden" name="email" value="{{ old('email', $registerEmail) }}">
                            @endif
                            @if ($registerMobile)
                                <input type="hidden" name="mobile" value="{{ old('mobile', $registerMobile) }}">
                            @endif
                            <div class="grid gap-4 sm:grid-cols-2">
                                @if ($registerEmail)
                                    <div>
                                        <label class="text-sm font-medium text-foreground" for="email_otp">Email OTP</label>
                                        <input id="email_otp" type="text" name="email_otp" value="{{ old('email_otp') }}" autocomplete="one-time-code" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                                        <x-input-error :messages="$errors->get('email_otp')" class="mt-2" />
                                    </div>
                                @endif
                                @if ($registerMobile)
                                    <div>
                                        <label class="text-sm font-medium text-foreground" for="phone_otp">Phone OTP</label>
                                        <input id="phone_otp" type="text" name="phone_otp" value="{{ old('phone_otp') }}" autocomplete="one-time-code" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                                        <x-input-error :messages="$errors->get('phone_otp')" class="mt-2" />
                                    </div>
                                @endif
                            </div>
                            @if (session('dev_otp_register_email'))
                                <p class="text-xs text-amber-600">Dev OTP (email): {{ session('dev_otp_register_email') }}</p>
                            @endif
                            @if (session('dev_otp_register_phone'))
                                <p class="text-xs text-amber-600">Dev OTP (phone): {{ session('dev_otp_register_phone') }}</p>
                            @endif
                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">Verify OTP</button>
                        </form>
                    @endif
                @else
                    <form class="mt-6 space-y-4" method="POST" action="{{ route('register') }}">
                        @csrf
                        <input type="hidden" name="service_area_id" value="{{ $registerServiceAreaId }}">
                        <input type="hidden" name="email" value="{{ $registerEmail }}">
                        <input type="hidden" name="mobile" value="{{ $registerMobile }}">
                        <div class="rounded-xl border border-border bg-muted/30 px-4 py-3 text-sm text-muted-foreground">
                            Verified contact: <span class="font-semibold text-foreground">{{ $registerEmail ?: $registerMobile }}</span>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-foreground" for="name">Name</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
                            <label class="text-sm font-medium text-foreground" for="password">Password</label>
                            <input id="password" type="password" name="password" required autocomplete="new-password" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                        <div>
                            <label class="text-sm font-medium text-foreground" for="password_confirmation">Confirm Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                        <div>
                            <label class="inline-flex items-start gap-3 text-sm text-muted-foreground">
                                <input type="checkbox" name="terms" value="1" class="mt-1 h-4 w-4 rounded border-border" required {{ old('terms') ? 'checked' : '' }}>
                                <span>I agree to the terms and conditions.</span>
                            </label>
                            <x-input-error :messages="$errors->get('terms')" class="mt-2" />
                        </div>
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">Create account</button>
                    </form>
                @endif

            </div>
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const serviceAreaSelect = document.getElementById('service_area_id');
        const emailToggle = document.getElementById('register-email-toggle');
        const phoneToggle = document.getElementById('register-phone-toggle');
        const emailPanel = document.getElementById('register-email-panel');
        const phonePanel = document.getElementById('register-phone-panel');
        const emailInput = document.getElementById('email');
        const phoneInput = document.getElementById('mobile');

        const setActiveMode = (mode) => {
            if (!emailToggle || !phoneToggle || !emailPanel || !phonePanel) {
                return;
            }

            const isEmail = mode === 'email';
            emailPanel.classList.toggle('hidden', !isEmail);
            phonePanel.classList.toggle('hidden', isEmail);

            emailToggle.classList.toggle('border-primary', isEmail);
            emailToggle.classList.toggle('bg-primary/10', isEmail);
            emailToggle.classList.toggle('text-primary', isEmail);
            emailToggle.classList.toggle('border-transparent', !isEmail);

            phoneToggle.classList.toggle('border-primary', !isEmail);
            phoneToggle.classList.toggle('bg-primary/10', !isEmail);
            phoneToggle.classList.toggle('text-primary', !isEmail);
            phoneToggle.classList.toggle('border-transparent', isEmail);

            if (emailInput) {
                emailInput.required = isEmail;
            }
            if (phoneInput) {
                phoneInput.required = !isEmail;
            }
        };

        const updateServiceAreaFields = () => {
            if (!serviceAreaSelect) {
                return;
            }
            const selected = serviceAreaSelect.options[serviceAreaSelect.selectedIndex];
            const allowEmail = selected?.dataset?.allowEmail === '1';
            const allowPhone = selected?.dataset?.allowPhone === '1';

            if (emailToggle) {
                emailToggle.classList.toggle('hidden', !allowEmail);
            }
            if (phoneToggle) {
                phoneToggle.classList.toggle('hidden', !allowPhone);
            }

            if (!allowEmail && allowPhone) {
                setActiveMode('phone');
            } else {
                setActiveMode('email');
            }

            if (!allowPhone && phoneInput) {
                phoneInput.value = '';
            }
            if (!allowEmail && emailInput) {
                emailInput.value = '';
            }
        };

        emailToggle?.addEventListener('click', () => setActiveMode('email'));
        phoneToggle?.addEventListener('click', () => setActiveMode('phone'));
        serviceAreaSelect?.addEventListener('change', updateServiceAreaFields);
        updateServiceAreaFields();
    });
</script>
@endsection
