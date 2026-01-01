@extends('layouts.app')

@section('content')
<section class="relative overflow-hidden bg-background py-14 lg:py-20">
    <div class="pointer-events-none absolute -left-32 top-10 h-72 w-72 rounded-full bg-primary/15 blur-3xl"></div>
    <div class="pointer-events-none absolute -right-40 top-24 h-80 w-80 rounded-full bg-emerald-200/40 blur-3xl"></div>
    <div class="container max-w-4xl">
        <div class="mb-10 flex flex-wrap items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6 6 6" />
                </svg>
                Back to Home
            </a>
            <div class="rounded-full border border-border bg-card px-4 py-1.5 text-xs font-semibold text-muted-foreground">Registry Lookup</div>
        </div>

        <div class="rounded-3xl border border-border bg-card p-6 shadow-soft sm:p-10">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-muted-foreground">IMEI Check</p>
                    <h1 class="mt-3 text-3xl font-semibold text-foreground">Instant IMEI verification</h1>
                    <p class="mt-3 text-sm text-muted-foreground">Search your device registry to confirm status, ownership, and safety instantly.</p>
                </div>
                <div class="rounded-2xl border border-border bg-muted/40 px-4 py-3 text-xs text-muted-foreground">
                    Trusted registry results for registered devices
                </div>
            </div>

            <form class="mt-8 space-y-4" method="GET" action="{{ route('verification') }}">
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
                    <button type="submit" class="inline-flex h-14 items-center justify-center gap-2 rounded-2xl bg-primary px-8 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">
                        Search
                    </button>
                </div>
                @error('imei')
                    <p class="text-sm text-rose-600">{{ $message }}</p>
                @enderror
                <p class="text-xs text-muted-foreground">Tip: dial <span class="font-mono font-semibold">*#06#</span> on your phone to find your IMEI.</p>
            </form>

            @if ($result === 'found')
                <div class="mt-8 rounded-2xl border border-emerald-200 bg-emerald-50/60 p-6">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100">
                                <svg class="h-6 w-6 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-foreground">Device verified</h2>
                                <p class="text-xs text-muted-foreground">Verified on {{ now()->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Verified</span>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-xl bg-white px-4 py-3">
                            <p class="text-xs text-muted-foreground">IMEI</p>
                            <p class="mt-1 font-mono text-sm font-semibold text-foreground">
                                {{ substr($device->imei, 0, 4) }}********{{ substr($device->imei, -4) }}
                            </p>
                        </div>
                        <div class="rounded-xl bg-white px-4 py-3">
                            <p class="text-xs text-muted-foreground">IMEI 2</p>
                            <p class="mt-1 font-mono text-sm font-semibold text-foreground">
                                {{ $device->imei2 ? substr($device->imei2, 0, 4).'********'.substr($device->imei2, -4) : 'Not provided' }}
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
                        <div class="rounded-xl bg-white px-4 py-3">
                            <p class="text-xs text-muted-foreground">Purchase Type</p>
                            <p class="mt-1 text-sm font-semibold text-foreground">{{ ucfirst($device->purchase_type) }}</p>
                        </div>
                        <div class="rounded-xl bg-white px-4 py-3">
                            <p class="text-xs text-muted-foreground">Registered</p>
                            <p class="mt-1 text-sm font-semibold text-foreground">{{ $device->created_at?->format('M d, Y') ?? 'N/A' }}</p>
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
                            <h2 class="text-lg font-semibold text-foreground">No match found</h2>
                            <p class="text-xs text-muted-foreground">This IMEI is not registered in the system.</p>
                        </div>
                    </div>
                    <p class="text-sm text-muted-foreground">Double-check the IMEI or register the device first.</p>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
