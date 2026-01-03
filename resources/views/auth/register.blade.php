@extends('layouts.app')

@section('content')
@php
    $serviceAreas = $serviceAreas ?? collect();
    $defaultServiceAreaId = $defaultServiceAreaId ?? null;
    $otpEnabled = $otpEnabled ?? false;
    $showOtp = $otpEnabled && (old('otp') || $errors->has('otp') || session('otp_purpose') === 'auth_register' || session('dev_otp_register'));
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
                <div class="space-y-2">
                    <h2 class="text-2xl font-semibold text-foreground">Sign up</h2>
                    <p class="text-sm text-muted-foreground">Create your DRMS profile to get started.</p>
                </div>

                @if ($errors->any() && !($errors->has('otp') && $errors->count() === 1))
                    <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        Please check the form for errors.
                    </div>
                @endif
                <div id="otp-send-error" class="hidden mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"></div>

                <form id="register-form" class="mt-6 space-y-4" method="POST" action="{{ route('register') }}">
                    @csrf
                    <div>
                        <label class="text-sm font-medium text-foreground" for="name">Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-foreground">Account Type *</label>
                        <div class="mt-2 grid gap-3 sm:grid-cols-2">
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-border bg-muted/30 p-3 text-sm">
                                <input type="radio" name="role" value="user" class="h-4 w-4" @checked(old('role', 'user') === 'user') />
                                <div>
                                    <p class="font-semibold text-foreground">Customer</p>
                                    <p class="text-xs text-muted-foreground">Register devices for personal use.</p>
                                </div>
                            </label>
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-border bg-muted/30 p-3 text-sm">
                                <input type="radio" name="role" value="shop" class="h-4 w-4" @checked(old('role') === 'shop') />
                                <div>
                                    <p class="font-semibold text-foreground">Shop</p>
                                    <p class="text-xs text-muted-foreground">Manage customers and registrations.</p>
                                </div>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-foreground" for="service_area_id">Service Area</label>
                        <select id="service_area_id" name="service_area_id" required class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-3 text-sm">
                            @forelse ($serviceAreas as $serviceArea)
                                <option value="{{ $serviceArea->id }}" {{ (string) old('service_area_id', $defaultServiceAreaId) === (string) $serviceArea->id ? 'selected' : '' }}>
                                    {{ $serviceArea->name }} ({{ $serviceArea->dial_code }})
                                </option>
                            @empty
                                <option value="" disabled selected>Add service areas in admin</option>
                            @endforelse
                        </select>
                        <x-input-error :messages="$errors->get('service_area_id')" class="mt-2" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-foreground" for="mobile">Mobile Number</label>
                        <input id="mobile" type="text" name="mobile" value="{{ old('mobile') }}" required autocomplete="tel" inputmode="numeric" pattern="[0-9]{6,15}" placeholder="98XXXXXXXX" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                        <x-input-error :messages="$errors->get('mobile')" class="mt-2" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-foreground" for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
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

                    @if ($otpEnabled)
                        <div id="otp-modal" class="{{ $showOtp ? '' : 'hidden' }} fixed inset-0 z-40 flex items-center justify-center bg-slate-900/60 p-4">
                            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                                <div class="mb-4 flex items-start justify-between gap-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-slate-900">Verify OTP</h3>
                                        <p class="text-sm text-slate-500">Enter the OTP sent to your mobile to finish registration.</p>
                                    </div>
                                    <button type="button" id="otp-close" class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 text-slate-500">
                                        <span class="sr-only">Close</span>
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6l-12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                <p id="otp-status" class="mb-3 text-xs text-slate-500 hidden"></p>
                                <div>
                                    <label class="text-sm font-medium text-slate-700" for="otp">OTP</label>
                                    <input id="otp" type="text" name="otp" value="{{ old('otp') }}" autocomplete="one-time-code" class="mt-2 h-12 w-full rounded-lg border border-slate-200 px-3 text-sm" />
                                    <x-input-error :messages="$errors->get('otp')" class="mt-2" />
                                </div>
                                <p id="otp-dev" data-dev-otp="{{ session('dev_otp_register') }}" class="mt-2 text-xs text-amber-600 {{ session('dev_otp_register') ? '' : 'hidden' }}">Dev OTP: {{ session('dev_otp_register') }}</p>
                                <p id="otp-cooldown" data-seconds="{{ session('otp_cooldown') && session('otp_purpose') === 'auth_register' ? session('otp_cooldown') : 0 }}" class="mt-2 text-xs text-rose-600 {{ session('otp_cooldown') && session('otp_purpose') === 'auth_register' ? '' : 'hidden' }}">
                                    Resend available in {{ session('otp_cooldown') }}s
                                </p>
                                <div class="mt-5 flex flex-wrap items-center gap-3">
                                    <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white">Verify & Create</button>
                                    <button type="button" id="otp-resend" class="text-sm font-semibold text-slate-600 hover:text-slate-900">Resend OTP</button>
                                </div>
                            </div>
                        </div>
                    @endif
                </form>

                <p class="mt-6 text-center text-sm text-muted-foreground">
                    Already registered?
                    <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</section>
@if ($otpEnabled)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('register-form');
            const modal = document.getElementById('otp-modal');
            const otpInput = document.getElementById('otp');
            const otpError = document.getElementById('otp-send-error');
            const otpDev = document.getElementById('otp-dev');
            const otpCooldown = document.getElementById('otp-cooldown');
            const otpStatus = document.getElementById('otp-status');
            const otpResend = document.getElementById('otp-resend');
            const otpClose = document.getElementById('otp-close');
            const passwordInput = document.getElementById('password');
            const passwordConfirmInput = document.getElementById('password_confirmation');
            let cooldownTimer = null;
            if (!form) {
                return;
            }

            const restorePasswords = () => {
                const savedPassword = sessionStorage.getItem('register_password');
                const savedConfirm = sessionStorage.getItem('register_password_confirmation');
                if (passwordInput && savedPassword) {
                    passwordInput.value = savedPassword;
                }
                if (passwordConfirmInput && savedConfirm) {
                    passwordConfirmInput.value = savedConfirm;
                }
            };

            @if ($errors->has('otp'))
                restorePasswords();
            @endif

            const resetOtpMessages = () => {
                if (otpError) {
                    otpError.classList.add('hidden');
                    otpError.textContent = '';
                }
                if (otpStatus) {
                    otpStatus.classList.add('hidden');
                }
            };

            const setCooldown = (seconds) => {
                if (!otpCooldown) {
                    return;
                }
                const total = Number(seconds || 0);
                if (!total || total <= 0) {
                    otpCooldown.classList.add('hidden');
                    otpResend?.removeAttribute('disabled');
                    otpResend?.classList.remove('pointer-events-none', 'opacity-50');
                    return;
                }

                let remaining = total;
                otpCooldown.textContent = `Resend available in ${remaining}s`;
                otpCooldown.classList.remove('hidden');
                otpResend?.setAttribute('disabled', 'disabled');
                otpResend?.classList.add('pointer-events-none', 'opacity-50');

                if (cooldownTimer) {
                    clearInterval(cooldownTimer);
                }

                cooldownTimer = setInterval(() => {
                    remaining -= 1;
                    if (remaining <= 0) {
                        clearInterval(cooldownTimer);
                        cooldownTimer = null;
                        otpCooldown.classList.add('hidden');
                        otpResend?.removeAttribute('disabled');
                        otpResend?.classList.remove('pointer-events-none', 'opacity-50');
                        return;
                    }
                    otpCooldown.textContent = `Resend available in ${remaining}s`;
                }, 1000);
            };

            if (otpCooldown?.dataset?.seconds) {
                setCooldown(Number(otpCooldown.dataset.seconds));
            }

            const showDevOtp = (otpValue) => {
                if (!otpDev) {
                    return;
                }
                const value = otpValue || otpDev.dataset.devOtp;
                if (!value) {
                    return;
                }
                otpDev.textContent = `Dev OTP: ${value}`;
                otpDev.classList.remove('hidden');
            };

            showDevOtp();

            const sendOtp = async () => {
                const formData = new FormData(form);
                formData.delete('otp');

                try {
                    const response = await fetch("{{ route('register.otp') }}", {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    const payload = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        if (payload?.cooldown && otpCooldown) {
                            setCooldown(payload.cooldown);
                            return;
                        }

                        const message = payload?.errors?.mobile?.[0] || payload?.errors?.service_area_id?.[0] || 'Unable to send OTP.';
                        if (otpError) {
                            otpError.textContent = message;
                            otpError.classList.remove('hidden');
                        }
                        return;
                    }

                    if (otpDev && payload?.dev_otp) {
                        showDevOtp(payload.dev_otp);
                    }
                    if (otpCooldown && payload?.cooldown) {
                        setCooldown(payload.cooldown);
                    }
                } catch (error) {
                    if (otpError) {
                        otpError.textContent = 'Unable to send OTP. Please try again.';
                        otpError.classList.remove('hidden');
                    }
                }
            };

            form.addEventListener('submit', async (event) => {
                if (!otpInput || otpInput.value) {
                    if (otpInput?.value && passwordInput) {
                        sessionStorage.setItem('register_password', passwordInput.value);
                    }
                    if (otpInput?.value && passwordConfirmInput) {
                        sessionStorage.setItem('register_password_confirmation', passwordConfirmInput.value);
                    }
                    return;
                }

                event.preventDefault();

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                if (modal) {
                    modal.classList.remove('hidden');
                }

                resetOtpMessages();
                if (otpStatus) {
                    otpStatus.textContent = 'Sending OTP...';
                    otpStatus.classList.remove('hidden');
                }

                await sendOtp();
                if (otpStatus) {
                    otpStatus.classList.add('hidden');
                }
            });

            if (otpResend) {
                otpResend.addEventListener('click', async () => {
                    resetOtpMessages();
                    if (otpStatus) {
                        otpStatus.textContent = 'Sending OTP...';
                        otpStatus.classList.remove('hidden');
                    }
                    await sendOtp();
                    if (otpStatus) {
                        otpStatus.classList.add('hidden');
                    }
                });
            }

            if (modal) {
                modal.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        event.preventDefault();
                    }
                });
            }

            if (otpClose && modal) {
                otpClose.addEventListener('click', () => {
                    modal.classList.add('hidden');
                });
            }
        });
    </script>
@endif
@endsection
