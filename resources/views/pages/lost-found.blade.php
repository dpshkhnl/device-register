@extends('layouts.app')

@section('title', 'Lost & Found')

@section('content')
@php
    $hasKyc = auth()->user()->hasKyc();
    $deviceData = $devices->map(function ($device) use ($reports) {
        $latest = ($reports[$device->id] ?? collect())->first();
        return [
            'id' => $device->id,
            'name' => trim($device->brand.' '.$device->model),
            'imei' => $device->imei,
            'lost' => $device->status === 'lost',
            'last' => $latest ? ucfirst($latest->type).' · '.$latest->created_at?->format('M d, Y') : null,
        ];
    })->values();
    $initialId = (int) old('device_id', request('device'));
    if (! $devices->contains('id', $initialId)) {
        $initialId = null;
    }
@endphp

<div class="container max-w-3xl py-10 lg:py-12"
     x-data="{
         devices: @js($deviceData),
         selectedId: @js($initialId),
         get selected() { return this.devices.find(d => d.id === this.selectedId) ?? null; },
         pick(id) { this.selectedId = id; window.scrollTo({ top: 0, behavior: 'smooth' }); },
     }">
    <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm -ml-3 mb-4">
        <x-site.icon name="arrow-left" class="h-4 w-4" /> Dashboard
    </a>
    <div class="mb-8">
        <p class="eyebrow">Lost & Found</p>
        <h1 class="page-title mt-2">Lost your phone?</h1>
        <p class="page-lead">Report it here and it’s flagged straight away — anyone who checks its IMEI will be warned not to buy it. When you get it back, mark it as found.</p>
    </div>

    @if ($errors->any())
        <div class="callout callout-danger mb-6" role="alert">
            <x-site.icon name="alert" class="mt-px h-5 w-5 shrink-0" />
            <p>{{ $errors->first() }}</p>
        </div>
    @endif

    @if ($devices->isEmpty())
        <div class="surface px-6 py-14 text-center">
            <span class="icon-tile mx-auto h-14 w-14 rounded-2xl bg-accent text-accent-foreground"><x-site.icon name="phone" class="h-6 w-6" /></span>
            <h2 class="mt-4 text-base font-bold text-foreground">No registered devices</h2>
            <p class="mx-auto mt-1 max-w-xs text-sm text-muted-foreground">Only registered devices can be reported. Register yours now so you’re covered.</p>
            <a href="{{ route('register-device') }}" class="btn btn-primary mt-5"><x-site.icon name="plus" class="h-4 w-4" stroke="2.2" /> Register a device</a>
        </div>
    @else
        {{-- Step 1: choose --}}
        <section x-show="!selected" @style(['display: none' => $initialId]) class="surface p-6 sm:p-8">
            <h2 class="text-lg font-bold text-foreground">Which device?</h2>
            <p class="mt-1 text-sm text-muted-foreground">Choose the device you want to report or mark as found.</p>
            <div class="mt-5 space-y-3">
                <template x-for="device in devices" :key="device.id">
                    <button type="button" @click="pick(device.id)" class="choice w-full text-left">
                        <span class="icon-tile" :class="device.lost ? 'bg-rose-50 text-rose-600' : 'bg-muted text-foreground/70'">
                            <x-site.icon name="phone" />
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-sm font-semibold text-foreground" x-text="device.name"></span>
                            <span class="block font-mono text-xs text-muted-foreground" x-text="'IMEI ' + device.imei"></span>
                            <span class="mt-1 block text-xs text-muted-foreground" x-text="device.last ? 'Last report: ' + device.last : 'No reports yet'"></span>
                        </span>
                        <span class="pill" :class="device.lost ? 'pill-danger' : 'pill-success'" x-text="device.lost ? 'Lost / Stolen' : 'Active'"></span>
                        <x-site.icon name="chevron-right" class="h-4 w-4 text-muted-foreground" />
                    </button>
                </template>
            </div>
        </section>

        {{-- Step 2 --}}
        <template x-if="selected">
            <section class="surface overflow-hidden">
                <div class="flex items-center gap-4 border-b border-border bg-muted/40 px-6 py-4 sm:px-8">
                    <span class="icon-tile h-10 w-10" :class="selected.lost ? 'bg-rose-50 text-rose-600' : 'bg-card text-foreground/70 ring-1 ring-border'">
                        <x-site.icon name="phone" class="h-5 w-5" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-foreground" x-text="selected.name"></p>
                        <p class="font-mono text-xs text-muted-foreground" x-text="'IMEI ' + selected.imei"></p>
                    </div>
                    <button type="button" @click="selectedId = null" class="btn btn-ghost btn-sm">Change</button>
                </div>

                {{-- Mark found --}}
                <div x-show="selected.lost" class="p-6 sm:p-8">
                    <div class="flex items-start gap-4">
                        <span class="icon-tile h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-700"><x-site.icon name="check-circle" class="h-6 w-6" /></span>
                        <div>
                            <h2 class="text-lg font-bold text-foreground">Got it back?</h2>
                            <p class="mt-1 text-sm text-muted-foreground">Marking it found removes the lost/stolen warning from the registry.</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('lost-found.store') }}" class="mt-6 space-y-5" data-swal-password data-swal-title="Mark this device as found?" data-swal-text="Enter your password to confirm.">
                        @csrf
                        <input type="hidden" name="device_id" :value="selected.id">
                        <input type="hidden" name="action" value="found">
                        <input type="hidden" name="password" value="">
                        <div>
                            <label class="field-label" for="found_description">Note <span class="font-normal text-muted-foreground">(optional)</span></label>
                            <input id="found_description" type="text" name="description" maxlength="500" placeholder="e.g. Returned by a taxi driver" class="field-input" />
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="btn btn-success btn-lg"><x-site.icon name="check" class="h-5 w-5" stroke="2.2" /> Mark as found</button>
                        </div>
                    </form>
                </div>

                {{-- Report lost --}}
                <div x-show="!selected.lost" class="p-6 sm:p-8">
                    @unless ($hasKyc)
                        <div class="callout callout-warning mb-6">
                            <x-site.icon name="id-card" class="mt-px h-5 w-5 shrink-0" />
                            <div class="flex flex-1 flex-wrap items-center justify-between gap-3">
                                <p><span class="font-semibold">Verify your identity first.</span> KYC is required before reporting a device lost.</p>
                                <a href="{{ route('profile.edit') }}#kyc" class="btn btn-sm bg-amber-600 text-white hover:bg-amber-700">Complete KYC</a>
                            </div>
                        </div>
                    @endunless

                    <form method="POST" action="{{ route('lost-found.store') }}" class="space-y-6" data-swal-password data-swal-title="Report this device?" data-swal-text="It will be flagged immediately. Enter your password to confirm.">
                        @csrf
                        <input type="hidden" name="device_id" :value="selected.id">
                        <input type="hidden" name="action" value="lost">
                        <input type="hidden" name="password" value="">

                        <div>
                            <span class="field-label">What happened? <span class="req">*</span></span>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <label class="choice">
                                    <input type="radio" name="incident_type" value="lost" class="sr-only" required @checked(old('incident_type') === 'lost') />
                                    <span class="icon-tile h-10 w-10 bg-amber-50 text-amber-700"><x-site.icon name="map-pin" class="h-5 w-5" /></span>
                                    <span class="flex-1"><span class="block text-sm font-semibold text-foreground">I lost it</span><span class="block text-xs text-muted-foreground">Misplaced or left somewhere</span></span>
                                    <span class="choice-dot"><x-site.icon name="check" class="h-3 w-3" stroke="3" /></span>
                                </label>
                                <label class="choice">
                                    <input type="radio" name="incident_type" value="stolen" class="sr-only" required @checked(old('incident_type') === 'stolen') />
                                    <span class="icon-tile h-10 w-10 bg-rose-50 text-rose-700"><x-site.icon name="alert" class="h-5 w-5" /></span>
                                    <span class="flex-1"><span class="block text-sm font-semibold text-foreground">It was stolen</span><span class="block text-xs text-muted-foreground">Taken without permission</span></span>
                                    <span class="choice-dot"><x-site.icon name="check" class="h-3 w-3" stroke="3" /></span>
                                </label>
                            </div>
                            @error('incident_type') <p class="field-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="field-label" for="incident_date">When? <span class="req">*</span></label>
                                <input id="incident_date" type="date" name="incident_date" value="{{ old('incident_date') }}" max="{{ now('Asia/Kathmandu')->toDateString() }}" required class="field-input" />
                                @error('incident_date') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="field-label" for="incident_location">Where? <span class="req">*</span></label>
                                <input id="incident_location" type="text" name="incident_location" value="{{ old('incident_location') }}" maxlength="255" required placeholder="Area, street or landmark" class="field-input" />
                                @error('incident_location') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <span class="field-label">Numbers saved in the phone <span class="req">*</span></span>
                            <p class="-mt-1 mb-2 text-xs text-muted-foreground">Contacts that may help identify the device — e.g. a family member’s number.</p>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <input type="tel" name="contact_phone_1" value="{{ old('contact_phone_1') }}" maxlength="20" required placeholder="Phone number 1" aria-label="Phone number 1" class="field-input" />
                                    @error('contact_phone_1') <p class="field-error">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <input type="tel" name="contact_phone_2" value="{{ old('contact_phone_2') }}" maxlength="20" placeholder="Phone number 2 (optional)" aria-label="Phone number 2" class="field-input" />
                                    @error('contact_phone_2') <p class="field-error">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="field-label" for="lost_description">Anything else? <span class="font-normal text-muted-foreground">(optional)</span></label>
                            <textarea id="lost_description" name="description" rows="3" maxlength="500" placeholder="Case colour, distinguishing marks, police report number…" class="field-input">{{ old('description') }}</textarea>
                        </div>

                        <div class="flex flex-col-reverse gap-3 border-t border-border pt-6 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-xs text-muted-foreground">You’ll be asked for your password to confirm.</p>
                            <button type="submit" class="btn btn-danger btn-lg" @disabled(! $hasKyc)>
                                <x-site.icon name="alert" class="h-5 w-5" /> Report device
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </template>
    @endif
</div>

<script>
    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!form.matches('form[data-swal-password]') || form.dataset.swalConfirmed === 'true') return;
        event.preventDefault();

        const title = form.dataset.swalTitle || 'Confirm action';
        const text = form.dataset.swalText || '';
        const submitWith = (password) => {
            form.querySelector('input[name="password"]').value = password;
            form.dataset.swalConfirmed = 'true';
            form.submit();
        };

        if (!window.Swal) {
            const password = prompt(`${title}\n${text}`);
            if (password) submitWith(password);
            return;
        }

        siteSwal({
            title,
            text,
            icon: 'warning',
            input: 'password',
            inputPlaceholder: 'Your password',
            inputAttributes: { autocomplete: 'current-password' },
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
            preConfirm: (value) => {
                if (!value) {
                    Swal.showValidationMessage('Password is required');
                    return false;
                }
                return value;
            },
        }).then((result) => {
            if (result.isConfirmed) submitWith(result.value);
        });
    });
</script>
@endsection
