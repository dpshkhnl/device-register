@extends('layouts.app')

@section('content')
<section class="relative overflow-hidden bg-background py-12 lg:py-16">
    <div class="pointer-events-none absolute inset-0 opacity-30"
        style="background-image: url('/images/shield-badge.svg'); background-repeat: no-repeat; background-position: right 6% top 8%; background-size: 240px;">
    </div>
    <div class="container max-w-5xl">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6 6 6" />
                </svg>
                Back to Home
            </a>
            <div class="rounded-full border border-border bg-card px-4 py-1.5 text-xs font-semibold text-muted-foreground">Registry Search</div>
        </div>

        <div class="grid gap-8 lg:grid-cols-[1.2fr,0.8fr]">
            <div class="rounded-3xl border border-border bg-card p-6 shadow-soft sm:p-10">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10">
                        <svg class="h-6 w-6 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="11" cy="11" r="7" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 20l-3-3" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-foreground">IMEI Verification</h1>
                        <p class="mt-2 text-xs text-muted-foreground">Check a 15-digit IMEI to confirm device status, owner, and safety.</p>
                    </div>
                </div>

                <form class="mt-6 space-y-4" method="GET" action="{{ route('verification') }}">
                    <label for="imei" class="text-xs font-semibold text-foreground">IMEI Number</label>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <div class="relative flex-1">
                            <svg class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <rect x="6" y="3" width="12" height="18" rx="2" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 17h4" />
                            </svg>
                            <input
                                id="imei"
                                name="imei"
                                type="text"
                                placeholder="Enter 15-digit IMEI"
                                class="h-14 w-full rounded-2xl border border-border bg-background pl-12 text-base font-mono shadow-soft focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                inputmode="numeric"
                                pattern="[0-9]{15}"
                                minlength="15"
                                maxlength="15"
                                value="{{ old('imei', $imei ?? '') }}"
                            />
                        </div>
                        <button type="submit" class="inline-flex h-14 items-center justify-center gap-2 rounded-2xl bg-primary px-6 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">
                            Verify IMEI
                        </button>
                    </div>
                    @error('imei')
                        <p class="text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-muted-foreground">Tip: dial <span class="font-mono font-semibold">*#06#</span> on your phone to find your IMEI.</p>
                </form>

                @if ($result === 'found')
                    <div class="mt-8 rounded-2xl border border-emerald-200 bg-emerald-50 p-6">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100">
                                <svg class="h-6 w-6 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-foreground">Device Found</h2>
                                <p class="text-xs text-muted-foreground">Verified on {{ now()->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-xl bg-white px-4 py-3">
                                <p class="text-xs text-muted-foreground">IMEI</p>
                                <p class="mt-1 font-mono text-sm font-semibold text-foreground">
                                    {{ substr($device->imei, 0, 4) }}********{{ substr($device->imei, -4) }}
                                </p>
                            </div>
                            <div class="rounded-xl bg-white px-4 py-3">
                                <p class="text-xs text-muted-foreground">Status</p>
                                <p class="mt-1 text-sm font-semibold text-foreground">{{ ucfirst($device->status) }}</p>
                            </div>
                            <div class="rounded-xl bg-white px-4 py-3">
                                <p class="text-xs text-muted-foreground">Device</p>
                                <p class="mt-1 text-sm font-semibold text-foreground">{{ $device->brand }} {{ $device->model }}</p>
                            </div>
                            <div class="rounded-xl bg-white px-4 py-3">
                                <p class="text-xs text-muted-foreground">Owner</p>
                                <p class="mt-1 text-sm font-semibold text-foreground">{{ $device->currentOwner?->name ?? 'Registered User' }}</p>
                            </div>
                        </div>
                    </div>
                @elseif ($result === 'not_found')
                    <div class="mt-8 rounded-2xl border border-rose-200 bg-rose-50 p-6">
                        <div class="mb-3 flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-100">
                                <svg class="h-6 w-6 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-foreground">No device found</h2>
                                <p class="text-xs text-muted-foreground">We could not locate this IMEI in the registry.</p>
                            </div>
                        </div>
                        <p class="text-sm text-muted-foreground">Double-check the IMEI or register the device first.</p>
                    </div>
                @endif
            </div>

            <aside class="space-y-4">
                <div class="rounded-3xl border border-border bg-muted/40 p-6">
                    <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Verification Checklist</p>
                    <ul class="mt-4 space-y-3 text-sm text-muted-foreground">
                        <li class="flex items-start gap-2">
                            <span class="mt-1 inline-flex h-2 w-2 rounded-full bg-primary"></span>
                            Confirm the 15-digit IMEI from device settings.
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1 inline-flex h-2 w-2 rounded-full bg-primary"></span>
                            Review ownership and status before purchase.
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1 inline-flex h-2 w-2 rounded-full bg-primary"></span>
                            Report lost devices immediately if flagged.
                        </li>
                    </ul>
                </div>
                <div class="rounded-3xl border border-border bg-card p-6 shadow-soft">
                    <p class="text-sm font-semibold text-foreground">Need help?</p>
                    <p class="mt-2 text-xs text-muted-foreground">Contact support for guidance on transfers or verification.</p>
                    <a href="mailto:{{ $settings?->contact_email ?? 'support@drms.gov' }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-primary hover:underline">
                        Email Support
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
