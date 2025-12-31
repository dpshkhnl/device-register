@extends('layouts.app')

@section('content')
<section class="relative overflow-hidden bg-background py-12 lg:py-16">
    <div class="pointer-events-none absolute inset-0 opacity-20"
        style="background-image: url('/images/certificate-badge.svg'); background-repeat: no-repeat; background-position: left 6% top 12%; background-size: 200px;">
    </div>
    <div class="container max-w-6xl">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-foreground">Packages</h1>
            <p class="text-sm text-muted-foreground">Choose a plan for IMEI verification and device registrations.</p>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($activePackage)
            <div class="mb-6 rounded-2xl border border-border bg-card px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Active Package</p>
                <div class="mt-2 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-foreground">{{ $activePackage->package->name }}</p>
                        <p class="text-xs text-muted-foreground">Devices: {{ $activePackage->used_device_count }}/{{ $activePackage->device_limit }} · IMEI checks: {{ $activePackage->used_imei_count }}/{{ $activePackage->imei_limit }}</p>
                    </div>
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Active</span>
                </div>
            </div>
        @endif

        <div class="grid gap-4 md:grid-cols-3">
            @forelse ($packages as $package)
                <div class="rounded-2xl border border-border bg-card p-5 shadow-soft">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-foreground">{{ $package->name }}</h3>
                            <p class="mt-1 text-xs text-muted-foreground">{{ $package->description }}</p>
                        </div>
                        @if ($package->is_trial)
                            <span class="rounded-full bg-primary/10 px-2 py-0.5 text-[10px] font-semibold text-primary">Trial</span>
                        @endif
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-semibold text-foreground">{{ number_format($package->price, 2) }}</p>
                        <p class="text-xs text-muted-foreground">Device limit: {{ $package->device_limit }}</p>
                        <p class="text-xs text-muted-foreground">IMEI checks: {{ $package->imei_limit }}</p>
                        @if ($package->duration_days)
                            <p class="text-xs text-muted-foreground">Duration: {{ $package->duration_days }} days</p>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('packages.purchase', $package) }}" class="mt-5">
                        @csrf
                        <button class="inline-flex w-full items-center justify-center rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">Buy Package</button>
                    </form>
                </div>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-border bg-card p-10 text-center text-sm text-muted-foreground">
                    No packages available right now.
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
