@extends('layouts.app')

@section('title', 'Plans')

@section('content')
@php
    $meter = fn ($used, $limit) => $limit > 0 ? min(100, round($used / $limit * 100)) : 0;
    $activeId = $activePackage?->package_id;
    $featuredId = $packages->where('is_trial', false)->sortBy('price')->skip(1)->first()?->id
        ?? $packages->where('is_trial', false)->first()?->id;
@endphp

<section class="relative overflow-hidden">
    <div class="bg-site-hero pointer-events-none absolute inset-x-0 top-0 h-[26rem]"></div>
    <div class="container relative max-w-6xl py-14 lg:py-20">
        <div class="mx-auto max-w-2xl text-center">
            <p class="eyebrow">Plans</p>
            <h1 class="page-title mt-3">Simple plans, clear limits</h1>
            <p class="page-lead mx-auto">Pick how many devices you want to register and IMEIs you want to check. Activation is instant and you can switch any time.</p>
        </div>

        @if ($errors->any())
            <div class="callout callout-danger mx-auto mt-8 max-w-2xl" role="alert">
                <x-site.icon name="alert" class="mt-px h-5 w-5 shrink-0" />
                <p>{{ $errors->first() }}</p>
            </div>
        @endif

        @if ($activePackage)
            <div class="surface mx-auto mt-10 max-w-3xl p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="icon-tile bg-emerald-50 text-emerald-700"><x-site.icon name="check-circle" /></span>
                        <div>
                            <p class="text-xs font-medium text-muted-foreground">Your current plan</p>
                            <p class="text-base font-bold text-foreground">{{ $activePackage->package->name }}</p>
                        </div>
                    </div>
                    @if ($activePackage->ends_at)
                        <span class="pill pill-neutral pill-plain">Valid until {{ $activePackage->ends_at->format('M d, Y') }}</span>
                    @endif
                </div>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    @foreach ([
                        ['label' => 'Devices registered', 'used' => $activePackage->used_device_count, 'limit' => $activePackage->device_limit],
                        ['label' => 'IMEI checks used', 'used' => $activePackage->used_imei_count, 'limit' => $activePackage->imei_limit],
                    ] as $usage)
                        <div>
                            <div class="mb-1.5 flex justify-between text-xs">
                                <span class="font-medium text-muted-foreground">{{ $usage['label'] }}</span>
                                <span class="font-semibold text-foreground">{{ $usage['used'] }} / {{ $usage['limit'] }}</span>
                            </div>
                            <div class="meter"><span style="width: {{ $meter($usage['used'], $usage['limit']) }}%"></span></div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-12 grid items-stretch gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($packages as $package)
                @php
                    $isCurrent = $activeId === $package->id;
                    $isFeatured = ! $activePackage && $featuredId === $package->id;
                @endphp
                <div class="relative flex flex-col rounded-3xl border p-7 transition {{ $isFeatured ? 'border-primary bg-card shadow-[0_24px_48px_-24px_hsl(var(--primary)/0.45)] ring-1 ring-primary' : 'surface' }}">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="text-lg font-bold text-foreground">{{ $package->name }}</h3>
                        @if ($isCurrent)
                            <span class="pill pill-success">Current</span>
                        @elseif ($package->is_trial)
                            <span class="pill pill-primary pill-plain">Free trial</span>
                        @elseif ($isFeatured)
                            <span class="pill pill-primary pill-plain">Most popular</span>
                        @endif
                    </div>
                    @if ($package->description)
                        <p class="mt-2 text-sm leading-relaxed text-muted-foreground">{{ $package->description }}</p>
                    @endif

                    <p class="mt-6 flex items-baseline gap-1.5">
                        @if ((float) $package->price === 0.0)
                            <span class="font-display text-5xl text-foreground">Free</span>
                        @else
                            <span class="font-display text-5xl text-foreground">{{ number_format($package->price, 2) }}</span>
                            <span class="text-sm text-muted-foreground">/ package</span>
                        @endif
                    </p>

                    <ul class="mt-6 flex-1 space-y-3 text-sm">
                        <li class="flex items-center gap-3"><x-site.icon name="check" class="h-4 w-4 text-primary" stroke="2.4" /> <span><span class="font-semibold text-foreground">{{ $package->device_limit }}</span> device registrations</span></li>
                        <li class="flex items-center gap-3"><x-site.icon name="check" class="h-4 w-4 text-primary" stroke="2.4" /> <span><span class="font-semibold text-foreground">{{ $package->imei_limit }}</span> IMEI checks</span></li>
                        <li class="flex items-center gap-3"><x-site.icon name="check" class="h-4 w-4 text-primary" stroke="2.4" /> <span>{{ $package->duration_days ? 'Valid for '.$package->duration_days.' days' : 'Never expires' }}</span></li>
                        <li class="flex items-center gap-3"><x-site.icon name="check" class="h-4 w-4 text-primary" stroke="2.4" /> <span>Transfers & lost reports included</span></li>
                    </ul>

                    @auth
                        <form method="POST" action="{{ route('packages.purchase', $package) }}" class="mt-8"
                              @if ($activePackage) data-swal-confirm data-swal-title="{{ $isCurrent ? 'Renew' : 'Switch to' }} {{ $package->name }}?" data-swal-text="Your current plan ends now and {{ $package->name }} starts with fresh limits." data-swal-confirm="{{ $isCurrent ? 'Renew' : 'Switch plan' }}" @endif>
                            @csrf
                            <button class="btn w-full {{ $isFeatured ? 'btn-primary' : 'btn-secondary' }}">
                                {{ $isCurrent ? 'Renew this plan' : ($activePackage ? 'Switch to this plan' : 'Choose plan') }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('register') }}" class="btn mt-8 w-full {{ $isFeatured ? 'btn-primary' : 'btn-secondary' }}">Get started</a>
                    @endauth
                </div>
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-border bg-card p-12 text-center text-sm text-muted-foreground">
                    No plans are available right now. Please check back soon.
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
