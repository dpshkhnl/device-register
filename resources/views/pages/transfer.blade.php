@extends('layouts.app')

@section('content')
<section class="relative overflow-hidden bg-background py-12 lg:py-16">
    <div class="pointer-events-none absolute inset-0 opacity-25"
        style="background-image: url('/images/hero-device.svg'); background-repeat: no-repeat; background-position: left 6% top 12%; background-size: 220px;">
    </div>
    <div class="container max-w-5xl">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6 6 6" />
                </svg>
                Back to Dashboard
            </a>
            <div class="rounded-full border border-border bg-card px-4 py-1.5 text-xs font-semibold text-muted-foreground">Ownership Flow</div>
        </div>

        @php
            $selectedImei = old('device_imei', session('otp_device'));
            $selectedDevice = $devices->firstWhere('imei', $selectedImei);
            $newOtpVerified = session('transfer.new_verified');
            $oldOtpVerified = session('transfer.old_verified');
            $transferConfirmed = session('transfer_confirmed');
            $resendSeconds = (int) ($settings?->otp_resend_seconds ?? 60);
            if ($resendSeconds < 10) {
                $resendSeconds = 10;
            } elseif ($resendSeconds > 600) {
                $resendSeconds = 600;
            }
            $sentRecipient = session('otp_sent_recipient');
            $sentSeconds = (int) session('otp_resend_seconds', 0);
            $cooldownSeconds = (int) session('otp_cooldown', 0);
            $remainingSeconds = $cooldownSeconds > 0 ? $cooldownSeconds : $sentSeconds;
            $newOtpRemaining = $sentRecipient === 'new' ? $remainingSeconds : 0;
            $oldOtpRemaining = $sentRecipient === 'old' ? $remainingSeconds : 0;
            $step1Done = ! empty($selectedImei);
            $step2Done = (bool) $newOtpVerified;
            $step3Done = (bool) $oldOtpVerified;
            $step4Done = (bool) $transferConfirmed;
            $activeStep = $step1Done ? ($step2Done ? ($step3Done ? 4 : 3) : 2) : 1;
            if ($errors->has('device_imei')) {
                $activeStep = 1;
            } elseif ($errors->has('to_mobile') || $errors->has('new_owner_otp')) {
                $activeStep = 2;
            } elseif ($errors->has('old_owner_otp')) {
                $activeStep = 3;
            }
            if (session('otp_sent_recipient') === 'new') {
                $activeStep = 2;
            } elseif (session('otp_sent_recipient') === 'old') {
                $activeStep = 3;
            }
            $steps = [
                ['label' => 'Select Device', 'icon' => 'device', 'done' => $step1Done],
                ['label' => 'New Owner', 'icon' => 'user', 'done' => $step2Done],
                ['label' => 'Verify OTP', 'icon' => 'shield', 'done' => $step3Done],
                ['label' => 'Confirm', 'icon' => 'check', 'done' => $step4Done],
            ];
        @endphp

        <div class="mb-8 rounded-3xl border border-border bg-card px-6 py-5 shadow-soft">
            <div class="grid gap-4 sm:grid-cols-4">
                @foreach ($steps as $index => $step)
                    @php
                        $stepIndex = $index + 1;
                        $isActive = $activeStep === $stepIndex;
                        $isDone = $step['done'];
                    @endphp
                    <div class="flex flex-col items-center text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full border {{ $isDone ? 'border-emerald-500 bg-emerald-500 text-white' : ($isActive ? 'border-primary bg-primary text-primary-foreground' : 'border-border bg-muted text-muted-foreground') }}">
                            @if ($step['icon'] === 'device')
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                    <rect x="7" y="2" width="10" height="20" rx="2"></rect>
                                    <path d="M11 18h2" stroke-linecap="round" />
                                </svg>
                            @elseif ($step['icon'] === 'user')
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                    <circle cx="12" cy="8" r="4" />
                                    <path d="M4 20c1.8-4 13.2-4 16 0" stroke-linecap="round" />
                                </svg>
                            @elseif ($step['icon'] === 'shield')
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                    <path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6l7-3z" />
                                    <path d="M9.5 12.5l2 2 3-3" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            @else
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                    <circle cx="12" cy="12" r="9" />
                                    <path d="M8 12l2.5 2.5L16 9" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            @endif
                        </div>
                        <p class="mt-2 text-xs font-semibold {{ $isActive ? 'text-foreground' : 'text-muted-foreground' }}">{{ $step['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-3xl border border-border bg-card p-6 shadow-soft sm:p-10">
            <div class="space-y-2">
                <h2 class="text-2xl font-semibold text-foreground">Ownership transfer</h2>
                <p class="text-xs text-muted-foreground">Complete each step to transfer your device securely.</p>
            </div>

            @if (session('status'))
                <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="{{ route('transfer.store') }}" class="mt-6 space-y-6" data-transfer-steps data-initial-step="{{ $activeStep }}" data-swal-confirm data-swal-title="Submit transfer request?" data-swal-text="The new owner must accept it to complete." data-swal-confirm="Yes, submit">
                @csrf
                <div class="hidden rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" data-status></div>
                <div class="rounded-2xl border border-border bg-muted/20 p-4 {{ $activeStep === 1 ? '' : 'hidden' }}" data-step="1">
                    <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Step 1</p>
                    <h3 class="mt-2 text-sm font-semibold text-foreground">Select device</h3>
                    <p class="mt-1 text-xs text-muted-foreground">Choose the device you want to transfer.</p>

                    <div class="mt-4 space-y-3">
                        @forelse ($devices as $device)
                            @php
                                $maskedImei = substr($device->imei, 0, 4).'****'.substr($device->imei, -4);
                                $isPending = in_array($device->id, $pendingDeviceIds ?? [], true);
                                $isLost = in_array($device->id, $lostDeviceIds ?? [], true);
                            @endphp
                            <label class="flex cursor-pointer items-center gap-4 rounded-2xl border border-border bg-background px-4 py-4 transition hover:border-primary/40 {{ ($isPending || $isLost) ? 'opacity-60 cursor-not-allowed' : '' }}">
                                <input type="radio" name="device_imei" value="{{ $device->imei }}" class="peer sr-only" {{ $selectedImei === $device->imei ? 'checked' : '' }} {{ ($isPending || $isLost) ? 'disabled' : '' }} />
                                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-muted text-muted-foreground peer-checked:bg-primary/10 peer-checked:text-primary">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                        <rect x="7" y="2" width="10" height="20" rx="2"></rect>
                                        <path d="M11 18h2" stroke-linecap="round" />
                                    </svg>
                                </span>
                                <span class="flex-1">
                                    <span class="block text-sm font-semibold text-foreground">{{ $device->brand }} {{ $device->model }}</span>
                                    <span class="block text-xs text-muted-foreground">IMEI: {{ $maskedImei }}</span>
                                    @if ($isPending)
                                        <span class="mt-1 inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700">Pending transfer</span>
                                    @endif
                                    @if ($isLost)
                                        <span class="mt-1 inline-flex rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-semibold text-rose-700">Marked lost</span>
                                    @endif
                                </span>
                                <span class="flex h-6 w-6 items-center justify-center rounded-full border border-border text-transparent peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </label>
                        @empty
                            <div class="rounded-2xl border border-dashed border-border bg-background px-4 py-5 text-xs text-muted-foreground">
                                No registered devices found for your account.
                            </div>
                        @endforelse
                    </div>

                    @error('device_imei')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror

                    <p class="mt-2 text-xs text-rose-600 hidden" data-step1-warning>Please select a device to continue.</p>

                    <div class="mt-4 flex items-center justify-end">
                        <button type="button" class="inline-flex h-11 items-center justify-center rounded-xl bg-primary px-5 text-xs font-semibold text-primary-foreground shadow-soft hover:bg-primary/90" data-step-next="2" data-step1-next data-require-selection="true">
                            Continue
                        </button>
                    </div>
                </div>

                <div class="rounded-2xl border border-border bg-muted/20 p-4 {{ $activeStep === 2 ? '' : 'hidden' }}" data-step="2">
                    @if (session('status'))
                        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            {{ session('status') }}
                        </div>
                    @endif
                    <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Step 2</p>
                    <h3 class="mt-2 text-sm font-semibold text-foreground">New owner OTP</h3>
                    <p class="mt-1 text-xs text-muted-foreground">Enter the new owner's mobile number and send OTP.</p>
                    <input
                        type="text"
                        name="to_mobile"
                        value="{{ old('to_mobile') }}"
                        placeholder="Enter mobile number"
                        class="mt-4 h-12 w-full rounded-xl border border-border bg-background px-4 text-sm"
                    />
                    <input
                        type="text"
                        name="new_owner_otp"
                        value="{{ old('new_owner_otp') }}"
                        placeholder="Enter new owner OTP"
                        class="mt-3 h-12 w-full rounded-xl border border-border bg-background px-4 text-sm"
                        inputmode="numeric"
                    />
                    <div class="mt-3 flex flex-wrap items-center gap-3">
                        <button
                            type="button"
                            name="recipient"
                            value="new"
                            formaction="{{ route('transfer.otp') }}"
                            formmethod="POST"
                            class="inline-flex h-11 items-center justify-center rounded-xl border border-border px-4 text-xs font-semibold text-foreground"
                            data-resend-label="new"
                            data-resend-remaining="{{ $newOtpRemaining }}"
                            data-ajax-action="send"
                            data-recipient="new"
                            {{ $devices->isEmpty() || $newOtpRemaining > 0 ? 'disabled' : '' }}
                        >
                            Send OTP to New Owner
                        </button>
                        <button type="button" class="inline-flex h-11 items-center justify-center rounded-xl border border-border px-4 text-xs font-semibold text-foreground" data-step-prev="1">
                            Back
                        </button>
                        <button
                            type="button"
                            formaction="{{ route('transfer.verify.new') }}"
                            formmethod="POST"
                            class="inline-flex h-11 items-center justify-center rounded-xl bg-primary px-4 text-xs font-semibold text-primary-foreground shadow-soft hover:bg-primary/90"
                            data-ajax-action="verify"
                            data-recipient="new"
                        >
                            Continue
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-rose-600 {{ $newOtpRemaining > 0 ? '' : 'hidden' }}" data-resend-timer="new">
                        Resend available in {{ $newOtpRemaining }}s
                    </p>
                    @if (session('dev_otp_new') || session('otp_hint'))
                        <p class="mt-2 text-xs text-amber-600">Dev OTP (new owner): {{ session('dev_otp_new') ?? session('otp_hint') }}</p>
                    @endif
                    <p class="mt-2 hidden text-xs text-amber-600" data-dev-otp="new"></p>
                    @error('to_mobile')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                    @error('new_owner_otp')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 hidden text-xs text-rose-600" data-error="to_mobile"></p>
                    <p class="mt-2 hidden text-xs text-rose-600" data-error="new_owner_otp"></p>
                </div>

                <div class="rounded-2xl border border-border bg-muted/20 p-4 {{ $activeStep === 3 ? '' : 'hidden' }}" data-step="3">
                    @if (session('status'))
                        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            {{ session('status') }}
                        </div>
                    @endif
                    <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Step 3</p>
                    <h3 class="mt-2 text-sm font-semibold text-foreground">Old owner OTP</h3>
                    <p class="mt-1 text-xs text-muted-foreground">We will send an OTP to your number ({{ auth()->user()?->mobile ?? 'mobile not set' }}).</p>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <input
                            type="text"
                            name="old_owner_otp"
                            value="{{ old('old_owner_otp') }}"
                            placeholder="Enter old owner OTP"
                            class="h-12 flex-1 rounded-xl border border-border bg-background px-4 text-sm"
                            inputmode="numeric"
                        />
                        <button
                            type="button"
                            name="recipient"
                            value="old"
                            formaction="{{ route('transfer.otp') }}"
                            formmethod="POST"
                            class="inline-flex h-12 items-center justify-center rounded-xl border border-border px-4 text-sm font-semibold text-foreground"
                            data-resend-label="old"
                            data-resend-remaining="{{ $oldOtpRemaining }}"
                            data-ajax-action="send"
                            data-recipient="old"
                            {{ $devices->isEmpty() || $oldOtpRemaining > 0 ? 'disabled' : '' }}
                        >
                            Send OTP to Old Owner
                        </button>
                        <button type="button" class="inline-flex h-12 items-center justify-center rounded-xl border border-border px-4 text-sm font-semibold text-foreground" data-step-prev="2">
                            Back
                        </button>
                        <button
                            type="button"
                            formaction="{{ route('transfer.verify.old') }}"
                            formmethod="POST"
                            class="inline-flex h-12 items-center justify-center rounded-xl bg-primary px-4 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90"
                            data-ajax-action="verify"
                            data-recipient="old"
                        >
                            Continue
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-rose-600 {{ $oldOtpRemaining > 0 ? '' : 'hidden' }}" data-resend-timer="old">
                        Resend available in {{ $oldOtpRemaining }}s
                    </p>
                    @if (session('dev_otp_old'))
                        <p class="mt-2 text-xs text-amber-600">Dev OTP (old owner): {{ session('dev_otp_old') }}</p>
                    @endif
                    <p class="mt-2 hidden text-xs text-amber-600" data-dev-otp="old"></p>
                    @error('old_owner_otp')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 hidden text-xs text-rose-600" data-error="old_owner_otp"></p>
                </div>

                <div class="rounded-2xl border border-border bg-card p-4 {{ $activeStep === 4 ? '' : 'hidden' }}" data-step="4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Step 4</p>
                    <h3 class="mt-2 text-sm font-semibold text-foreground">Confirm transfer</h3>
                    <p class="mt-1 text-xs text-muted-foreground">Review the details before submitting.</p>
                    <div class="mt-4 space-y-2 text-xs text-muted-foreground">
                        <div class="flex items-center justify-between">
                            <span>Device</span>
                            <span class="font-semibold text-foreground">
                                {{ $selectedDevice ? $selectedDevice->brand.' '.$selectedDevice->model : 'Not selected' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>IMEI</span>
                            <span class="font-semibold text-foreground">{{ $selectedImei ?: 'Not selected' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>New owner</span>
                            <span class="font-semibold text-foreground">{{ old('to_mobile') ?: 'Not provided' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Old owner</span>
                            <span class="font-semibold text-foreground">{{ auth()->user()?->mobile ?? 'Not set' }}</span>
                        </div>
                    </div>
                    @error('new_owner_otp')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror

                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <button type="submit" class="inline-flex flex-1 items-center justify-center gap-2 rounded-2xl bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90" {{ $devices->isEmpty() ? 'disabled' : '' }}>
                            Initiate Transfer
                        </button>
                        <button type="button" class="inline-flex h-12 items-center justify-center rounded-xl border border-border px-4 text-sm font-semibold text-foreground" data-step-prev="3">
                            Back
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.querySelector('[data-transfer-steps]');
        if (!container) return;

        const panels = Array.from(container.querySelectorAll('[data-step]'));
        const step1Warning = container.querySelector('[data-step1-warning]');
        const nextBtn = container.querySelector('[data-step1-next]');
        const radios = container.querySelectorAll('input[name="device_imei"]');
        const statusBox = container.querySelector('[data-status]');
        const devOtpNew = container.querySelector('[data-dev-otp="new"]');
        const devOtpOld = container.querySelector('[data-dev-otp="old"]');
        const errorFields = ['to_mobile', 'new_owner_otp', 'old_owner_otp', 'device_imei'];
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const showStep = (step) => {
            panels.forEach((panel) => {
                const panelStep = parseInt(panel.dataset.step || '0', 10);
                panel.classList.toggle('hidden', panelStep !== step);
            });
        };

        const setStatus = (message) => {
            if (!statusBox) return;
            if (!message) {
                statusBox.classList.add('hidden');
                statusBox.textContent = '';
                return;
            }
            statusBox.textContent = message;
            statusBox.classList.remove('hidden');
        };

        const clearErrors = () => {
            errorFields.forEach((field) => {
                const el = container.querySelector(`[data-error="${field}"]`);
                if (el) {
                    el.textContent = '';
                    el.classList.add('hidden');
                }
            });
        };

        const setErrors = (errors) => {
            if (!errors) return;
            Object.entries(errors).forEach(([field, messages]) => {
                const el = container.querySelector(`[data-error="${field}"]`);
                if (!el) return;
                const message = Array.isArray(messages) ? messages[0] : messages;
                el.textContent = message;
                el.classList.remove('hidden');
            });
        };

        const startCountdown = (recipient, seconds) => {
            const button = container.querySelector(`[data-resend-label="${recipient}"]`);
            const label = container.querySelector(`[data-resend-timer="${recipient}"]`);
            if (!button || !label || !seconds || seconds <= 0) return;

            let remaining = seconds;
            const tick = () => {
                if (remaining <= 0) {
                    button.disabled = false;
                    label.classList.add('hidden');
                    return;
                }
                button.disabled = true;
                label.classList.remove('hidden');
                label.textContent = `Resend available in ${remaining}s`;
                remaining -= 1;
                setTimeout(tick, 1000);
            };
            tick();
        };

        const initialStep = parseInt(container.dataset.initialStep || '1', 10);
        showStep(initialStep);

        const toggleNextState = () => {
            if (!nextBtn) return;
            const selected = container.querySelector('input[name="device_imei"]:checked');
            nextBtn.disabled = !selected;
        };

        toggleNextState();
        radios.forEach((radio) => {
            radio.addEventListener('change', () => {
                if (step1Warning) step1Warning.classList.add('hidden');
                toggleNextState();
            });
        });

        container.querySelectorAll('[data-step-next]').forEach((button) => {
            button.addEventListener('click', () => {
                const target = parseInt(button.dataset.stepNext || '1', 10);
                if (button.dataset.requireSelection === 'true') {
                    const selected = container.querySelector('input[name="device_imei"]:checked');
                    if (!selected) {
                        if (step1Warning) step1Warning.classList.remove('hidden');
                        return;
                    }
                }
                showStep(target);
            });
        });

        container.querySelectorAll('[data-step-prev]').forEach((button) => {
            button.addEventListener('click', () => {
                const target = parseInt(button.dataset.stepPrev || '1', 10);
                showStep(target);
            });
        });

        const resendButtons = container.querySelectorAll('[data-resend-remaining]');
        resendButtons.forEach((button) => {
            const labelKey = button.dataset.resendLabel;
            const remaining = parseInt(button.dataset.resendRemaining || '0', 10);
            startCountdown(labelKey, remaining);
        });

        const ajaxButtons = container.querySelectorAll('[data-ajax-action]');
        ajaxButtons.forEach((button) => {
            button.addEventListener('click', async () => {
                const action = button.getAttribute('formaction');
                if (!action) return;

                clearErrors();
                setStatus('');
                if (devOtpNew) devOtpNew.classList.add('hidden');
                if (devOtpOld) devOtpOld.classList.add('hidden');

                const formData = new FormData(container);
                const recipient = button.dataset.recipient;
                if (recipient) {
                    formData.set('recipient', recipient);
                }

                const response = await fetch(action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                    },
                    body: formData,
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    if (response.status === 429 && data.cooldown && recipient) {
                        startCountdown(recipient, data.cooldown);
                        return;
                    }
                    setErrors(data.errors);
                    if (data.message) setStatus(data.message);
                    return;
                }

                if (button.dataset.ajaxAction === 'send') {
                    if (data.status) setStatus(data.status);
                    if (recipient === 'new' && devOtpNew) {
                        devOtpNew.textContent = `Dev OTP (new owner): ${data.dev_otp}`;
                        devOtpNew.classList.remove('hidden');
                    }
                    if (recipient === 'old' && devOtpOld) {
                        devOtpOld.textContent = `Dev OTP (old owner): ${data.dev_otp}`;
                        devOtpOld.classList.remove('hidden');
                    }
                    startCountdown(recipient, data.resend_seconds);
                    return;
                }

                if (button.dataset.ajaxAction === 'verify') {
                    if (data.message) setStatus(data.message);
                    if (recipient === 'new') {
                        showStep(3);
                    } else if (recipient === 'old') {
                        showStep(4);
                    }
                }
            });
        });
    });
</script>
@endsection
