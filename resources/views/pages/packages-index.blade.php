@extends('layouts.app')

@section('content')
<section class="relative overflow-hidden bg-background py-14 lg:py-20">
    <div class="pointer-events-none absolute -left-32 top-10 h-72 w-72 rounded-full bg-primary/15 blur-3xl"></div>
    <div class="pointer-events-none absolute -right-40 top-24 h-80 w-80 rounded-full bg-emerald-200/40 blur-3xl"></div>
    <div class="container max-w-6xl">
        <div class="mb-10 grid gap-6 lg:grid-cols-[1.3fr,0.7fr] lg:items-end">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground">Pricing</p>
                <h1 class="mt-3 text-3xl font-semibold text-foreground sm:text-4xl">Choose the plan that fits your device workflow</h1>
                <p class="mt-3 max-w-2xl text-sm text-muted-foreground">Transparent limits, instant activation, and clear visibility into your IMEI verification and device registration usage.</p>
            </div>
            <div class="rounded-2xl border border-border bg-card/80 p-5 shadow-soft">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Why Pricing</p>
                <p class="mt-2 text-sm text-foreground">Scale verification credits and device limits per project without overcommitting.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($activePackage)
            <div class="mb-8 rounded-2xl border border-emerald-200 bg-emerald-50/70 px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Active Plan</p>
                <div class="mt-3 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-base font-semibold text-foreground">{{ $activePackage->package->name }}</p>
                        <p class="text-xs text-muted-foreground">Devices: {{ $activePackage->used_device_count }}/{{ $activePackage->device_limit }} · IMEI checks: {{ $activePackage->used_imei_count }}/{{ $activePackage->imei_limit }}</p>
                    </div>
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Running</span>
                </div>
            </div>
        @endif

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($packages as $package)
                <div class="relative flex h-full flex-col rounded-3xl border border-border bg-card p-6 shadow-soft transition hover:-translate-y-1 hover:shadow-lg">
                    @if ($package->is_trial)
                        <span class="absolute right-6 top-5 rounded-full bg-primary/10 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-primary">Trial</span>
                    @endif
                    <div>
                        <h3 class="text-xl font-semibold text-foreground">{{ $package->name }}</h3>
                        <p class="mt-2 text-sm text-muted-foreground">{{ $package->description }}</p>
                    </div>
                    <div class="mt-6 flex items-end gap-2">
                        <p class="text-3xl font-semibold text-foreground">{{ number_format($package->price, 2) }}</p>
                        <span class="pb-1 text-xs text-muted-foreground">per package</span>
                    </div>
                    <div class="mt-5 space-y-2 text-sm text-muted-foreground">
                        <div class="flex items-center justify-between rounded-xl border border-border/60 bg-muted/30 px-3 py-2">
                            <span>Device limit</span>
                            <span class="font-semibold text-foreground">{{ $package->device_limit }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl border border-border/60 bg-muted/30 px-3 py-2">
                            <span>IMEI checks</span>
                            <span class="font-semibold text-foreground">{{ $package->imei_limit }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl border border-border/60 bg-muted/30 px-3 py-2">
                            <span>Duration</span>
                            <span class="font-semibold text-foreground">{{ $package->duration_days ? $package->duration_days.' days' : 'No expiry' }}</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('packages.purchase', $package) }}" class="mt-6">
                        @csrf
                        <button class="inline-flex w-full items-center justify-center rounded-xl bg-primary px-4 py-3 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">
                            Choose plan
                        </button>
                    </form>
                </div>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-border bg-card p-10 text-center text-sm text-muted-foreground">
                    No pricing options available right now.
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
