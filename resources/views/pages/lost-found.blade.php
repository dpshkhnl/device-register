@extends('layouts.app')

@section('content')
<section class="relative overflow-hidden bg-background py-12 lg:py-16">
    <div class="pointer-events-none absolute inset-0 opacity-20"
        style="background-image: url('/images/shield-badge.svg'); background-repeat: no-repeat; background-position: right 6% top 12%; background-size: 200px;">
    </div>
    <div class="container max-w-5xl">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-foreground">Lost / Found</h1>
                <p class="text-xs text-muted-foreground">Report lost devices and confirm when found.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6 6 6" />
                </svg>
                Back to Dashboard
            </a>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="mb-6 rounded-3xl border border-border bg-card px-6 py-5 shadow-soft">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="flex flex-col items-center text-center">
                    <div id="lost-step-1" class="flex h-12 w-12 items-center justify-center rounded-full border border-primary bg-primary text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                            <rect x="7" y="2" width="10" height="20" rx="2"></rect>
                            <path d="M11 18h2" stroke-linecap="round" />
                        </svg>
                    </div>
                    <p class="mt-2 text-xs font-semibold text-foreground">Select Device</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div id="lost-step-2" class="flex h-12 w-12 items-center justify-center rounded-full border border-border bg-muted text-muted-foreground">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6l7-3z" />
                            <path d="M9.5 12.5l2 2 3-3" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <p class="mt-2 text-xs font-semibold text-muted-foreground">Report Details</p>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-border bg-card p-6 shadow-soft sm:p-10">
            <div class="space-y-2">
                <h2 class="text-2xl font-semibold text-foreground">Lost / Stolen report</h2>
                <p class="text-xs text-muted-foreground">Follow the steps to submit a lost or stolen report.</p>
            </div>

            <div id="lost-step-1-panel" class="mt-6 space-y-4 rounded-2xl border border-border bg-muted/20 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Step 1</p>
                <h3 class="mt-2 text-sm font-semibold text-foreground">Select device</h3>
                <p class="mt-1 text-xs text-muted-foreground">Choose the device you want to report.</p>

            @forelse ($devices as $device)
                @php
                    $deviceReports = $reports[$device->id] ?? collect();
                    $latestReport = $deviceReports->first();
                    $isLost = $device->status === 'lost';
                @endphp
                <button type="button"
                    class="lost-device-card w-full rounded-2xl border border-border bg-background px-4 py-4 text-left transition hover:border-primary/40"
                    data-device-id="{{ $device->id }}"
                    data-device-name="{{ $device->brand }} {{ $device->model }}"
                    data-device-imei="{{ $device->imei }}"
                    data-device-status="{{ $device->status }}"
                    data-device-is-lost="{{ $isLost ? '1' : '0' }}"
                >
                    <div class="flex items-center gap-4">
                        <span data-role="icon" class="flex h-12 w-12 items-center justify-center rounded-xl bg-muted text-muted-foreground">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                <rect x="7" y="2" width="10" height="20" rx="2"></rect>
                                <path d="M11 18h2" stroke-linecap="round" />
                            </svg>
                        </span>
                        <span class="flex-1">
                            <span class="block text-sm font-semibold text-foreground">{{ $device->brand }} {{ $device->model }}</span>
                            <span class="block text-xs text-muted-foreground">IMEI {{ $device->imei }}</span>
                            <span class="mt-1 inline-flex rounded-full bg-muted px-2 py-0.5 text-[10px] font-semibold text-muted-foreground">
                                {{ ucfirst($device->status) }}
                            </span>
                        </span>
                        <span data-role="check" class="flex h-6 w-6 items-center justify-center rounded-full border border-border text-transparent">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </div>
                    <div class="mt-3 text-xs text-muted-foreground">
                        @if ($latestReport)
                            Last report: {{ ucfirst($latestReport->type) }} ({{ ucfirst($latestReport->status) }})
                        @else
                            No reports yet.
                        @endif
                    </div>
                </button>
            @empty
                <div class="rounded-2xl border border-dashed border-border bg-background px-4 py-5 text-xs text-muted-foreground">
                    No registered devices found.
                </div>
            @endforelse
            </div>

            <div id="lost-step-2-panel" class="hidden mt-6 rounded-2xl border border-border bg-muted/20 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Step 2</p>
                <h3 class="mt-2 text-sm font-semibold text-foreground">Report details</h3>
                <p class="mt-1 text-xs text-muted-foreground">Fill in the details to submit the report.</p>

                <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-foreground" id="lost-selected-name">Select a device</p>
                    <p class="text-xs text-muted-foreground" id="lost-selected-imei">IMEI -</p>
                </div>
                <button type="button" id="lost-back" class="text-xs font-semibold text-muted-foreground hover:text-foreground">Change device</button>
                </div>

            <form method="POST" action="{{ route('lost-found.store') }}" class="mt-4 flex w-full flex-col gap-4" data-swal-password data-swal-title="Confirm status change" data-swal-text="Enter your password to continue.">
                @csrf
                <input type="hidden" name="device_id" id="lost-device-id" value="">
                <input type="hidden" name="action" id="lost-action" value="lost">
                <input type="hidden" name="password" value="">

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Lost/Stolen Mobile Detail</p>
                    <p class="mt-1 text-xs text-muted-foreground">Provide details for your report.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold text-foreground">Contact Numbers in Mobile *</label>
                        <input type="text" name="contact_phone_1" value="{{ old('contact_phone_1') }}" placeholder="Phone Number 1" class="mt-2 h-10 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground" required />
                        @error('contact_phone_1')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-foreground">&nbsp;</label>
                        <input type="text" name="contact_phone_2" value="{{ old('contact_phone_2') }}" placeholder="Phone Number 2" class="mt-2 h-10 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground" />
                        @error('contact_phone_2')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold text-foreground">Lost or Stolen? *</label>
                        <select name="incident_type" class="mt-2 h-10 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground" required>
                            <option value="">-- Please Choose --</option>
                            <option value="lost" @selected(old('incident_type') === 'lost')>Lost</option>
                            <option value="stolen" @selected(old('incident_type') === 'stolen')>Stolen</option>
                        </select>
                        @error('incident_type')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-foreground">Lost/Stolen Date *</label>
                        <input type="date" name="incident_date" value="{{ old('incident_date') }}" class="mt-2 h-10 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground" required />
                        @error('incident_date')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-foreground">Lost/Stolen Location</label>
                    <input type="text" name="incident_location" value="{{ old('incident_location') }}" placeholder="address of lost" class="mt-2 h-10 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground" />
                    @error('incident_location')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <input type="text" name="description" placeholder="Optional note" class="h-9 w-56 rounded-lg border border-border bg-background px-3 text-xs text-foreground" />
                    <button class="inline-flex h-9 items-center justify-center rounded-lg bg-rose-600 px-4 text-xs font-semibold text-white hover:bg-rose-700">
                        Report Lost
                    </button>
                </div>
            </form>
        </div>
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const step1Panel = document.getElementById('lost-step-1-panel');
        const step2Panel = document.getElementById('lost-step-2-panel');
        const step1Badge = document.getElementById('lost-step-1');
        const step2Badge = document.getElementById('lost-step-2');
        const deviceIdInput = document.getElementById('lost-device-id');
        const actionInput = document.getElementById('lost-action');
        const nameLabel = document.getElementById('lost-selected-name');
        const imeiLabel = document.getElementById('lost-selected-imei');
        const backButton = document.getElementById('lost-back');

        const setStep = (step) => {
            const isStep1 = step === 1;
            step1Panel?.classList.toggle('hidden', !isStep1);
            step2Panel?.classList.toggle('hidden', isStep1);
            step1Badge?.classList.toggle('border-primary', isStep1);
            step1Badge?.classList.toggle('bg-primary', isStep1);
            step1Badge?.classList.toggle('text-white', isStep1);
            step1Badge?.classList.toggle('border-border', !isStep1);
            step1Badge?.classList.toggle('bg-muted', !isStep1);
            step1Badge?.classList.toggle('text-muted-foreground', !isStep1);
            step2Badge?.classList.toggle('border-primary', !isStep1);
            step2Badge?.classList.toggle('bg-primary', !isStep1);
            step2Badge?.classList.toggle('text-white', !isStep1);
            step2Badge?.classList.toggle('border-border', isStep1);
            step2Badge?.classList.toggle('bg-muted', isStep1);
            step2Badge?.classList.toggle('text-muted-foreground', isStep1);
        };

        document.querySelectorAll('.lost-device-card').forEach((card) => {
            card.addEventListener('click', () => {
                const deviceId = card.dataset.deviceId;
                const deviceName = card.dataset.deviceName;
                const deviceImei = card.dataset.deviceImei;
                const isLost = card.dataset.deviceIsLost === '1';

                document.querySelectorAll('.lost-device-card').forEach((item) => {
                    item.classList.remove('border-primary', 'bg-primary/5');
                    const icon = item.querySelector('[data-role="icon"]');
                    const check = item.querySelector('[data-role="check"]');
                    if (icon) {
                        icon.classList.remove('bg-primary/10', 'text-primary');
                        icon.classList.add('bg-muted', 'text-muted-foreground');
                    }
                    if (check) {
                        check.classList.remove('border-primary', 'bg-primary', 'text-white');
                        check.classList.add('border-border', 'text-transparent');
                    }
                });

                card.classList.add('border-primary', 'bg-primary/5');
                const cardIcon = card.querySelector('[data-role="icon"]');
                const cardCheck = card.querySelector('[data-role="check"]');
                if (cardIcon) {
                    cardIcon.classList.remove('bg-muted', 'text-muted-foreground');
                    cardIcon.classList.add('bg-primary/10', 'text-primary');
                }
                if (cardCheck) {
                    cardCheck.classList.remove('border-border', 'text-transparent');
                    cardCheck.classList.add('border-primary', 'bg-primary', 'text-white');
                }

                if (deviceIdInput) {
                    deviceIdInput.value = deviceId;
                }
                if (actionInput) {
                    actionInput.value = isLost ? 'found' : 'lost';
                }
                if (nameLabel) {
                    nameLabel.textContent = deviceName;
                }
                if (imeiLabel) {
                    imeiLabel.textContent = `IMEI ${deviceImei}`;
                }
                setStep(2);
            });
        });

        backButton?.addEventListener('click', () => {
            setStep(1);
        });

        document.querySelectorAll('form[data-swal-password]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (form.dataset.swalConfirmed === 'true') {
                    return;
                }
                event.preventDefault();

                const title = form.dataset.swalTitle || 'Confirm action';
                const text = form.dataset.swalText || '';

                if (!window.Swal) {
                    const password = prompt(`${title}\n${text}`);
                    if (!password) {
                        return;
                    }
                    form.querySelector('input[name="password"]').value = password;
                    form.dataset.swalConfirmed = 'true';
                    form.submit();
                    return;
                }

                Swal.fire({
                    title,
                    text,
                    icon: 'warning',
                    input: 'password',
                    inputLabel: 'Password',
                    inputPlaceholder: 'Enter your password',
                    inputAttributes: {
                        autocomplete: 'current-password',
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Continue',
                    cancelButtonText: 'Cancel',
                    preConfirm: (value) => {
                        if (!value) {
                            Swal.showValidationMessage('Password is required');
                            return false;
                        }
                        return value;
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.querySelector('input[name="password"]').value = result.value;
                        form.dataset.swalConfirmed = 'true';
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection
