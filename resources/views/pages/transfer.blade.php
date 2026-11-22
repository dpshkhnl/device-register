@extends('layouts.app')

@section('title', 'Transfer ownership')

@section('content')
@php
    $blockedIds = array_merge($pendingDeviceIds ?? [], $lostDeviceIds ?? []);
    $requestedImei = request('device');
    if ($requestedImei && ! $devices->contains(fn ($d) => $d->imei === $requestedImei && ! in_array($d->id, $blockedIds, true))) {
        $requestedImei = null;
    }
    $selectedImei = old('device_imei', session('otp_device', $requestedImei));
    $selectedDevice = $devices->firstWhere('imei', $selectedImei);
    $newOtpVerified = session('transfer.new_verified');
    $oldOtpVerified = session('transfer.old_verified');
    $sentRecipient = session('otp_sent_recipient');
    $sentSeconds = (int) session('otp_resend_seconds', 0);
    $cooldownSeconds = (int) session('otp_cooldown', 0);
    $remainingSeconds = $cooldownSeconds > 0 ? $cooldownSeconds : $sentSeconds;
    $newOtpRemaining = $sentRecipient === 'new' ? $remainingSeconds : 0;
    $oldOtpRemaining = $sentRecipient === 'old' ? $remainingSeconds : 0;
    $step1Done = ! empty($selectedImei);
    $step2Done = (bool) $newOtpVerified;
    $step3Done = (bool) $oldOtpVerified;
    $activeStep = $step1Done ? ($step2Done ? ($step3Done ? 4 : 3) : 2) : 1;
    if ($errors->has('device_imei')) {
        $activeStep = 1;
    } elseif ($errors->has('to_mobile') || $errors->has('new_owner_otp')) {
        $activeStep = 2;
    } elseif ($errors->has('old_owner_otp')) {
        $activeStep = 3;
    }
    if ($sentRecipient === 'new') {
        $activeStep = 2;
    } elseif ($sentRecipient === 'old') {
        $activeStep = 3;
    }
    $myMobile = auth()->user()?->mobile;
    $steps = ['Choose device', 'New owner', 'Your code', 'Confirm'];
@endphp

<div class="container max-w-3xl py-10 lg:py-12">
    <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm -ml-3 mb-4">
        <x-site.icon name="arrow-left" class="h-4 w-4" /> Dashboard
    </a>
    <div class="mb-8">
        <p class="eyebrow">Ownership transfer</p>
        <h1 class="page-title mt-2">Transfer a device</h1>
        <p class="page-lead">Selling or giving your phone away? Move it to the new owner’s name. Both of you confirm with a one-time code sent by SMS.</p>
    </div>

    @if ($devices->isEmpty())
        <div class="surface px-6 py-14 text-center">
            <span class="icon-tile mx-auto h-14 w-14 rounded-2xl bg-accent text-accent-foreground"><x-site.icon name="phone" class="h-6 w-6" /></span>
            <h2 class="mt-4 text-base font-bold text-foreground">You have no devices to transfer</h2>
            <p class="mx-auto mt-1 max-w-xs text-sm text-muted-foreground">Register a device first, then you can transfer it to someone else.</p>
            <a href="{{ route('register-device') }}" class="btn btn-primary mt-5"><x-site.icon name="plus" class="h-4 w-4" stroke="2.2" /> Register a device</a>
        </div>
    @else
    <form method="POST" action="{{ route('transfer.store') }}" class="surface overflow-hidden" data-transfer-steps data-initial-step="{{ $activeStep }}"
          data-swal-confirm data-swal-danger="true" data-swal-title="Transfer this device?" data-swal-text="Ownership moves to the new owner. This cannot be undone." data-swal-confirm="Yes, transfer">
        @csrf

        {{-- Stepper --}}
        <ol class="flex items-center gap-2 border-b border-border bg-muted/40 px-6 py-5 sm:px-8">
            @foreach ($steps as $i => $label)
                <li class="flex flex-1 items-center gap-2 last:flex-none">
                    <span class="step-dot" data-step-dot="{{ $i + 1 }}" data-state="{{ $i + 1 < $activeStep ? 'done' : ($i + 1 === $activeStep ? 'active' : 'idle') }}">
                        <span data-num>{{ $i + 1 }}</span>
                        <x-site.icon name="check" class="hidden h-4 w-4" stroke="2.6" data-tick />
                    </span>
                    <span class="hidden text-xs font-semibold text-muted-foreground sm:inline" data-step-label="{{ $i + 1 }}">{{ $label }}</span>
                    @if (! $loop->last)<span class="mx-1 h-px flex-1 bg-border"></span>@endif
                </li>
            @endforeach
        </ol>

        <div class="p-6 sm:p-8">
            <div class="callout callout-success mb-6 hidden" data-status role="status"></div>

            {{-- Step 1 --}}
            <div class="{{ $activeStep === 1 ? '' : 'hidden' }}" data-step="1">
                <h2 class="text-lg font-bold text-foreground">Which device are you transferring?</h2>
                <p class="mt-1 text-sm text-muted-foreground">Devices that are lost or already being transferred can’t be selected.</p>

                <div class="mt-5 space-y-3">
                    @foreach ($devices as $device)
                        @php
                            $isPending = in_array($device->id, $pendingDeviceIds ?? [], true);
                            $isLost = in_array($device->id, $lostDeviceIds ?? [], true);
                        @endphp
                        <label class="choice">
                            <input type="radio" name="device_imei" value="{{ $device->imei }}" class="sr-only"
                                   data-name="{{ $device->brand }} {{ $device->model }}"
                                   @checked($selectedImei === $device->imei) @disabled($isPending || $isLost) />
                            <span class="icon-tile bg-muted text-foreground/70"><x-site.icon name="phone" /></span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-semibold text-foreground">{{ $device->brand }} {{ $device->model }}</span>
                                <span class="block font-mono text-xs text-muted-foreground">IMEI {{ substr($device->imei, 0, 4) }}•••{{ substr($device->imei, -4) }}</span>
                            </span>
                            @if ($isPending)
                                <x-site.status status="pending" />
                            @elseif ($isLost)
                                <x-site.status status="lost" />
                            @else
                                <span class="choice-dot"><x-site.icon name="check" class="h-3 w-3" stroke="3" /></span>
                            @endif
                        </label>
                    @endforeach
                </div>
                @error('device_imei') <p class="field-error">{{ $message }}</p> @enderror
                <p class="field-error hidden" data-step1-warning>Please choose a device to continue.</p>

                <div class="mt-8 flex justify-end">
                    <button type="button" class="btn btn-primary" data-step-next="2" data-step1-next data-require-selection="true">
                        Continue <x-site.icon name="arrow-right" class="h-4 w-4" />
                    </button>
                </div>
            </div>

            {{-- Step 2 --}}
            <div class="{{ $activeStep === 2 ? '' : 'hidden' }}" data-step="2">
                <h2 class="text-lg font-bold text-foreground">Who is the new owner?</h2>
                <p class="mt-1 text-sm text-muted-foreground">We’ll text a 6-digit code to their phone. Ask them to read it to you.</p>

                <div class="mt-6 space-y-5">
                    <div>
                        <label class="field-label" for="to_mobile">New owner’s mobile number</label>
                        <div class="flex flex-col gap-2 sm:flex-row">
                            <input id="to_mobile" type="tel" name="to_mobile" value="{{ old('to_mobile') }}" placeholder="98XXXXXXXX" inputmode="tel" autocomplete="off" class="field-input flex-1" />
                            <button type="button" name="recipient" value="new" formaction="{{ route('transfer.otp') }}" formmethod="POST"
                                    class="btn btn-secondary h-12" data-resend-label="new" data-resend-remaining="{{ $newOtpRemaining }}" data-ajax-action="send" data-recipient="new"
                                    @disabled($newOtpRemaining > 0)>
                                <x-site.icon name="mail" class="h-4 w-4" /> Send code
                            </button>
                        </div>
                        <p class="field-help {{ $newOtpRemaining > 0 ? '' : 'hidden' }}" data-resend-timer="new">You can resend in {{ $newOtpRemaining }}s</p>
                        @error('to_mobile') <p class="field-error">{{ $message }}</p> @enderror
                        <p class="field-error hidden" data-error="to_mobile"></p>
                    </div>
                    <div>
                        <label class="field-label" for="new_owner_otp">Code from the new owner</label>
                        <input id="new_owner_otp" type="text" name="new_owner_otp" value="{{ old('new_owner_otp') }}" placeholder="••••••" inputmode="numeric" maxlength="6" autocomplete="one-time-code" class="field-input otp-input max-w-xs" />
                        @if (session('dev_otp_new') || session('otp_hint'))
                            <p class="field-help text-amber-600">Dev OTP (new owner): {{ session('dev_otp_new') ?? session('otp_hint') }}</p>
                        @endif
                        <p class="field-help hidden text-amber-600" data-dev-otp="new"></p>
                        @error('new_owner_otp') <p class="field-error">{{ $message }}</p> @enderror
                        <p class="field-error hidden" data-error="new_owner_otp"></p>
                    </div>
                </div>

                <div class="mt-8 flex justify-between gap-3">
                    <button type="button" class="btn btn-ghost" data-step-prev="1"><x-site.icon name="arrow-left" class="h-4 w-4" /> Back</button>
                    <button type="button" formaction="{{ route('transfer.verify.new') }}" formmethod="POST" class="btn btn-primary" data-ajax-action="verify" data-recipient="new">
                        Verify & continue <x-site.icon name="arrow-right" class="h-4 w-4" />
                    </button>
                </div>
            </div>

            {{-- Step 3 --}}
            <div class="{{ $activeStep === 3 ? '' : 'hidden' }}" data-step="3">
                <h2 class="text-lg font-bold text-foreground">Confirm it’s you</h2>
                <p class="mt-1 text-sm text-muted-foreground">
                    @if ($myMobile)
                        We’ll text a code to your number, <span class="font-semibold text-foreground">{{ $myMobile }}</span>.
                    @else
                        Your account has no mobile number, so we can’t send you a code. Please contact support to add one.
                    @endif
                </p>

                <div class="mt-6">
                    <label class="field-label" for="old_owner_otp">Your code</label>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <input id="old_owner_otp" type="text" name="old_owner_otp" value="{{ old('old_owner_otp') }}" placeholder="••••••" inputmode="numeric" maxlength="6" autocomplete="one-time-code" class="field-input otp-input sm:max-w-xs" />
                        <button type="button" name="recipient" value="old" formaction="{{ route('transfer.otp') }}" formmethod="POST"
                                class="btn btn-secondary h-12" data-resend-label="old" data-resend-remaining="{{ $oldOtpRemaining }}" data-ajax-action="send" data-recipient="old"
                                @disabled($oldOtpRemaining > 0)>
                            <x-site.icon name="mail" class="h-4 w-4" /> Send my code
                        </button>
                    </div>
                    <p class="field-help {{ $oldOtpRemaining > 0 ? '' : 'hidden' }}" data-resend-timer="old">You can resend in {{ $oldOtpRemaining }}s</p>
                    @if (session('dev_otp_old'))
                        <p class="field-help text-amber-600">Dev OTP (old owner): {{ session('dev_otp_old') }}</p>
                    @endif
                    <p class="field-help hidden text-amber-600" data-dev-otp="old"></p>
                    @error('old_owner_otp') <p class="field-error">{{ $message }}</p> @enderror
                    <p class="field-error hidden" data-error="old_owner_otp"></p>
                </div>

                <div class="mt-8 flex justify-between gap-3">
                    <button type="button" class="btn btn-ghost" data-step-prev="2"><x-site.icon name="arrow-left" class="h-4 w-4" /> Back</button>
                    <button type="button" formaction="{{ route('transfer.verify.old') }}" formmethod="POST" class="btn btn-primary" data-ajax-action="verify" data-recipient="old">
                        Verify & continue <x-site.icon name="arrow-right" class="h-4 w-4" />
                    </button>
                </div>
            </div>

            {{-- Step 4 --}}
            <div class="{{ $activeStep === 4 ? '' : 'hidden' }}" data-step="4">
                <h2 class="text-lg font-bold text-foreground">Review and transfer</h2>
                <p class="mt-1 text-sm text-muted-foreground">Both codes are verified. Check the details, then confirm.</p>

                <dl class="mt-6 divide-y divide-border rounded-2xl border border-border">
                    <div class="flex items-center justify-between gap-4 px-5 py-4">
                        <dt class="text-sm text-muted-foreground">Device</dt>
                        <dd class="text-right text-sm font-semibold text-foreground" data-summary="device">{{ $selectedDevice ? $selectedDevice->brand.' '.$selectedDevice->model : 'Not selected' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 px-5 py-4">
                        <dt class="text-sm text-muted-foreground">IMEI</dt>
                        <dd class="font-mono text-sm font-semibold text-foreground" data-summary="imei">{{ $selectedImei ?: 'Not selected' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 px-5 py-4">
                        <dt class="text-sm text-muted-foreground">From (you)</dt>
                        <dd class="text-sm font-semibold text-foreground">{{ $myMobile ?? 'Not set' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 bg-accent/50 px-5 py-4">
                        <dt class="text-sm text-muted-foreground">To (new owner)</dt>
                        <dd class="text-sm font-semibold text-foreground" data-summary="to">{{ old('to_mobile') ?: 'Not provided' }}</dd>
                    </div>
                </dl>
                <p class="mt-4 flex items-start gap-2 text-xs text-muted-foreground">
                    <x-site.icon name="info" class="h-4 w-4 shrink-0" />
                    If the new owner already has an account, the device moves to them right away. Otherwise it completes automatically when they sign up with this number.
                </p>
                @error('new_owner_otp') <p class="field-error">{{ $message }}</p> @enderror

                <div class="mt-8 flex justify-between gap-3">
                    <button type="button" class="btn btn-ghost" data-step-prev="3"><x-site.icon name="arrow-left" class="h-4 w-4" /> Back</button>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <x-site.icon name="transfer" class="h-5 w-5" /> Transfer device
                    </button>
                </div>
            </div>
        </div>
    </form>
    @endif
</div>

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

        const setStatus = (message, isError = false) => {
            if (!statusBox) return;
            statusBox.textContent = message || '';
            statusBox.classList.toggle('hidden', !message);
            statusBox.classList.toggle('callout-success', !isError);
            statusBox.classList.toggle('callout-danger', isError);
        };

        const fillSummary = () => {
            const selected = container.querySelector('input[name="device_imei"]:checked');
            const mobile = container.querySelector('input[name="to_mobile"]')?.value.trim();
            const set = (key, value) => { const el = container.querySelector(`[data-summary="${key}"]`); if (el) el.textContent = value; };
            set('device', selected?.dataset.name || 'Not selected');
            set('imei', selected?.value || 'Not selected');
            set('to', mobile || 'Not provided');
        };

        const showStep = (step) => {
            panels.forEach((panel) => panel.classList.toggle('hidden', parseInt(panel.dataset.step, 10) !== step));
            container.querySelectorAll('[data-step-dot]').forEach((dot) => {
                const n = parseInt(dot.dataset.stepDot, 10);
                const state = n < step ? 'done' : (n === step ? 'active' : 'idle');
                dot.dataset.state = state;
                dot.querySelector('[data-num]')?.classList.toggle('hidden', state === 'done');
                dot.querySelector('[data-tick]')?.classList.toggle('hidden', state !== 'done');
            });
            container.querySelectorAll('[data-step-label]').forEach((label) => {
                label.classList.toggle('text-foreground', parseInt(label.dataset.stepLabel, 10) === step);
            });
            if (step === 4) fillSummary();
            panels.find((p) => parseInt(p.dataset.step, 10) === step)?.querySelector('input:not([type=radio]):not([type=hidden])')?.focus({ preventScroll: true });
        };

        const clearErrors = () => {
            errorFields.forEach((field) => {
                const el = container.querySelector(`[data-error="${field}"]`);
                if (el) { el.textContent = ''; el.classList.add('hidden'); }
            });
        };

        const setErrors = (errors) => {
            if (!errors) return;
            Object.entries(errors).forEach(([field, messages]) => {
                const el = container.querySelector(`[data-error="${field}"]`);
                if (!el) return;
                el.textContent = Array.isArray(messages) ? messages[0] : messages;
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
                label.textContent = `You can resend in ${remaining}s`;
                remaining -= 1;
                setTimeout(tick, 1000);
            };
            tick();
        };

        showStep(parseInt(container.dataset.initialStep || '1', 10));

        // Enter should advance the visible step, not submit the whole transfer.
        container.addEventListener('keydown', (event) => {
            if (event.key !== 'Enter' || event.target.tagName !== 'INPUT') return;
            const panel = panels.find((p) => !p.classList.contains('hidden'));
            if (!panel || panel.dataset.step === '4') return;
            event.preventDefault();
            panel.querySelector('.btn-primary')?.click();
        });

        const toggleNextState = () => {
            if (nextBtn) nextBtn.disabled = !container.querySelector('input[name="device_imei"]:checked');
        };
        toggleNextState();
        radios.forEach((radio) => radio.addEventListener('change', () => {
            step1Warning?.classList.add('hidden');
            toggleNextState();
        }));

        container.querySelectorAll('[data-step-next]').forEach((button) => {
            button.addEventListener('click', () => {
                if (button.dataset.requireSelection === 'true' && !container.querySelector('input[name="device_imei"]:checked')) {
                    step1Warning?.classList.remove('hidden');
                    return;
                }
                setStatus('');
                showStep(parseInt(button.dataset.stepNext || '1', 10));
            });
        });

        container.querySelectorAll('[data-step-prev]').forEach((button) => {
            button.addEventListener('click', () => {
                setStatus('');
                showStep(parseInt(button.dataset.stepPrev || '1', 10));
            });
        });

        container.querySelectorAll('[data-resend-remaining]').forEach((button) => {
            startCountdown(button.dataset.resendLabel, parseInt(button.dataset.resendRemaining || '0', 10));
        });

        container.querySelectorAll('[data-ajax-action]').forEach((button) => {
            button.addEventListener('click', async () => {
                const action = button.getAttribute('formaction');
                if (!action) return;

                clearErrors();
                setStatus('');
                devOtpNew?.classList.add('hidden');
                devOtpOld?.classList.add('hidden');

                const formData = new FormData(container);
                const recipient = button.dataset.recipient;
                if (recipient) formData.set('recipient', recipient);

                const wasDisabled = button.disabled;
                button.disabled = true;
                let data = {};
                let response;
                try {
                    response = await fetch(action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                        },
                        body: formData,
                    });
                    data = await response.json().catch(() => ({}));
                } catch (e) {
                    button.disabled = wasDisabled;
                    setStatus('Network error. Please try again.', true);
                    return;
                }
                button.disabled = wasDisabled;

                if (!response.ok) {
                    if (response.status === 429 && data.cooldown && recipient) {
                        startCountdown(recipient, data.cooldown);
                        return;
                    }
                    setErrors(data.errors);
                    if (data.message) setStatus(data.message, true);
                    return;
                }

                if (button.dataset.ajaxAction === 'send') {
                    if (data.status) setStatus(data.status);
                    const devEl = recipient === 'new' ? devOtpNew : devOtpOld;
                    if (devEl && data.dev_otp) {
                        devEl.textContent = `Dev OTP (${recipient === 'new' ? 'new' : 'old'} owner): ${data.dev_otp}`;
                        devEl.classList.remove('hidden');
                    }
                    startCountdown(recipient, data.resend_seconds);
                    return;
                }

                if (button.dataset.ajaxAction === 'verify') {
                    if (data.message) setStatus(data.message);
                    showStep(recipient === 'new' ? 3 : 4);
                }
            });
        });
    });
</script>
@endsection
