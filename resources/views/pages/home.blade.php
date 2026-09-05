@extends('layouts.app')

@section('content')
@php
    $bannerItems = ($banners ?? collect());
    if ($bannerItems->isEmpty()) {
        $bannerItems = collect([
            [
                'title' => 'Secure Your Mobile Devices Nationwide',
                'subtitle' => 'Verify any IMEI in seconds and protect yourself from device fraud.',
                'link_label' => 'Verify an IMEI',
                'link_url' => route('verification'),
                'image_url' => '/images/banner-1.svg',
            ],
            [
                'title' => 'Fraud Protection for Second-Hand Buyers',
                'subtitle' => 'Check full ownership history before you purchase any device.',
                'link_label' => 'Start a check',
                'link_url' => route('verification'),
                'image_url' => '/images/banner-2.svg',
            ],
        ]);
    }

    $bannerSlides = $bannerItems->map(function ($banner) {
        $image = data_get($banner, 'image_url');
        $imageUrl = $image
            ? (str_starts_with($image, '/images/') ? asset($image) : asset('storage/'.$image))
            : '';
        return [
            'title'       => data_get($banner, 'title'),
            'subtitle'    => data_get($banner, 'subtitle'),
            'link_label'  => data_get($banner, 'link_label'),
            'link_url'    => data_get($banner, 'link_url'),
            'image_url'   => $imageUrl,
        ];
    })->values();

    $heroTitle         = $settings?->hero_title         ?? 'Secure Your Mobile Devices';
    $heroSubtitle      = $settings?->hero_subtitle      ?? 'Verify IMEI, register devices, and protect yourself from fraud with the official national device registry.';
    $heroPrimaryLabel  = $settings?->hero_primary_label ?? 'Register Device';
    $heroPrimaryUrl    = $settings?->hero_primary_url   ?? route('register-device');
    $heroSecondaryLabel= $settings?->hero_secondary_label ?? 'Check Status';
    $heroSecondaryUrl  = $settings?->hero_secondary_url   ?? route('verification');
@endphp

{{-- ═══════════════════════════════════════════════
     HERO
═══════════════════════════════════════════════ --}}
<section
    x-data="{ active: 0, slides: @json($bannerSlides) }"
    x-init="setInterval(() => active = (active + 1) % slides.length, 6000)"
    class="relative min-h-[75vh] flex items-center overflow-hidden"
    style="background: linear-gradient(160deg, #0a1420 0%, #132132 100%);"
>
    {{-- Ledger-grid texture --}}
    <div class="absolute inset-0 bg-hero-dots opacity-50 pointer-events-none"></div>

    {{-- Seal ring — the one bold decorative moment --}}
    <div class="pointer-events-none absolute -right-24 -top-24 h-[420px] w-[420px] rounded-full border border-teal-400/10 hidden lg:block"></div>
    <div class="pointer-events-none absolute -right-8 -top-8 h-[300px] w-[300px] rounded-full border border-gold/10 hidden lg:block"></div>

    <div class="container relative z-10 py-12 lg:py-16">
        <div class="mx-auto max-w-5xl text-center">

            {{-- Badge --}}
            <div class="mb-5 inline-flex items-center rounded-md border px-3 py-1 text-xs font-medium tracking-wide"
                 style="border-color: rgba(45,212,200,0.3); color: #7fe4da; background: rgba(45,212,200,0.08);">
                Official device registry platform
            </div>

            {{-- Headline (from slides) --}}
            <h1 class="font-display mb-5 text-5xl font-semibold leading-tight tracking-tight text-white lg:text-7xl text-balance"
                x-text="slides[active]?.title || @json($heroTitle)">
                {!! nl2br(e($heroTitle)) !!}
            </h1>

            {{-- Sub-headline --}}
            <p class="mx-auto mb-7 max-w-2xl text-lg leading-relaxed text-slate-300"
               x-text="slides[active]?.subtitle || @json($heroSubtitle)">
                {{ $heroSubtitle }}
            </p>

            {{-- IMEI Search --}}
            <form method="GET" action="{{ route('verification') }}"
                  class="mx-auto mb-5 flex max-w-2xl flex-col gap-3 sm:flex-row">
                <div class="relative flex-1">
                    <svg class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400 pointer-events-none"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="11" cy="11" r="7"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 20l-3-3"/>
                    </svg>
                    <input
                        type="text"
                        name="imei"
                        placeholder="Enter 15-digit IMEI number…"
                        maxlength="15"
                        class="glass-input h-14 w-full pl-12 pr-4 text-base"
                    />
                </div>
                <button type="submit"
                        class="btn-gradient h-14 px-8 text-sm font-semibold shadow-glow-blue whitespace-nowrap">
                    Verify IMEI
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/>
                    </svg>
                </button>
            </form>

            {{-- CTA Buttons --}}
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="{{ $heroPrimaryUrl }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-semibold text-slate-900 shadow-soft hover:bg-white/90 transition-all hover:-translate-y-px">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="6" y="3" width="12" height="18" rx="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 17h4"/>
                    </svg>
                    {{ $heroPrimaryLabel }}
                </a>
                <a href="{{ $heroSecondaryUrl }}"
                   class="btn-ghost-light px-6 py-3 text-sm font-semibold">
                    {{ $heroSecondaryLabel }}
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            {{-- Slide indicators --}}
            <div class="mt-6 flex items-center justify-center gap-2">
                <template x-for="(slide, index) in slides" :key="index">
                    <button
                        type="button"
                        class="rounded-full transition-all duration-300"
                        :class="index === active ? 'w-8 h-2 bg-white' : 'w-2 h-2 bg-white/30'"
                        @click="active = index"
                    ></button>
                </template>
            </div>
        </div>
    </div>

    {{-- Bottom fade --}}
    <div class="absolute bottom-0 left-0 right-0 h-32 pointer-events-none"
         style="background: linear-gradient(to bottom, transparent, hsl(var(--background)));"></div>
</section>


{{-- ═══════════════════════════════════════════════
     TRUST BADGES
═══════════════════════════════════════════════ --}}
<section class="relative bg-background py-6">
    <div class="container">
        <div class="grid gap-4 sm:grid-cols-3">

            <div class="flex items-center gap-4 rounded-2xl border border-border bg-card px-5 py-4 shadow-soft transition-shadow hover:shadow-card">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl"
                     style="background: rgba(200,151,62,0.12);">
                    <svg class="h-6 w-6" style="color: #9c6b1f;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-foreground">Certified Status</p>
                    <p class="text-xs text-muted-foreground">Download official proof</p>
                </div>
            </div>

            <div class="flex items-center gap-4 rounded-2xl border border-border bg-card px-5 py-4 shadow-soft transition-shadow hover:shadow-card">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl"
                     style="background: rgba(14,124,134,0.1);">
                    <svg class="h-6 w-6" style="color: #0e7c86;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="6" y="3" width="12" height="18" rx="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 17h4M9 7h6M9 11h3"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-foreground">IMEI Verified</p>
                    <p class="text-xs text-muted-foreground">Real-time registry checks</p>
                </div>
            </div>

            <div class="flex items-center gap-4 rounded-2xl border border-border bg-card px-5 py-4 shadow-soft transition-shadow hover:shadow-card">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl"
                     style="background: rgba(179,38,30,0.1);">
                    <svg class="h-6 w-6" style="color: #b3261e;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-foreground">Fraud Protection</p>
                    <p class="text-xs text-muted-foreground">Flag stolen devices fast</p>
                </div>
            </div>

        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
     FEATURES
═══════════════════════════════════════════════ --}}
<section id="features" class="bg-muted/40 py-10 lg:py-14">
    <div class="container">

        <div class="mb-8 text-center">
            <span class="section-badge mb-4">Core Features</span>
            <h2 class="font-display mt-4 text-3xl font-semibold tracking-tight text-foreground sm:text-4xl">
                Everything you need to protect your device
            </h2>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-muted-foreground">
                A complete suite of tools for device verification, registration, and protection
            </p>
        </div>

        @php
            $featureItems = ($features ?? collect());
            if ($featureItems->isEmpty()) {
                $featureItems = collect([
                    [
                        'icon' => 'shield',
                        'title' => 'Secure IMEI Verification',
                        'description' => "Instantly verify any device's IMEI status. Check if a device is active, transferred, lost, or flagged.",
                        'button_label' => 'Verify Now',
                        'icon_color' => '#0e7c86',
                        'icon_bg' => 'rgba(14,124,134,0.1)',
                    ],
                    [
                        'icon' => 'transfer',
                        'title' => 'Ownership Transfer',
                        'description' => 'Safely transfer device ownership with OTP verification. Complete audit trail for every transaction.',
                        'button_label' => 'Transfer Device',
                        'icon_color' => '#9c6b1f',
                        'icon_bg' => 'rgba(200,151,62,0.12)',
                    ],
                    [
                        'icon' => 'alert',
                        'title' => 'Report Lost / Stolen',
                        'description' => 'Report missing devices immediately. Flag devices to prevent unauthorized sales and usage.',
                        'button_label' => 'Report Device',
                        'icon_color' => '#b3261e',
                        'icon_bg' => 'rgba(179,38,30,0.1)',
                    ],
                    [
                        'icon' => 'certificate',
                        'title' => 'Status Certificate',
                        'description' => 'Download official PDF certificates with QR verification for proof of device status.',
                        'button_label' => 'Get Certificate',
                        'icon_color' => '#33465b',
                        'icon_bg' => 'rgba(51,70,91,0.1)',
                    ],
                ]);
            }
        @endphp

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($featureItems as $feature)
                @php
                    $iconKey  = data_get($feature, 'icon', 'shield');
                    $label    = data_get($feature, 'button_label', 'Learn More');
                    $iconBg   = data_get($feature, 'icon_bg', 'rgba(14,124,134,0.1)');
                    $iconClr  = data_get($feature, 'icon_color', '#0e7c86');
                @endphp
                <div class="feature-card group flex flex-col">
                    <div class="mb-5 flex h-13 w-13 items-center justify-center rounded-xl p-3"
                         style="background: {{ $iconBg }};">
                        @if ($iconKey === 'transfer')
                            <svg class="h-6 w-6" style="color:{{ $iconClr }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10m0 0-3-3m3 3-3 3M17 17H7m0 0 3 3m-3-3 3-3"/>
                            </svg>
                        @elseif ($iconKey === 'alert')
                            <svg class="h-6 w-6" style="color:{{ $iconClr }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                        @elseif ($iconKey === 'certificate')
                            <svg class="h-6 w-6" style="color:{{ $iconClr }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 4h7l4 4v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 4v4h4M9 12h6M9 15h4"/>
                            </svg>
                        @else
                            <svg class="h-6 w-6" style="color:{{ $iconClr }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z"/>
                            </svg>
                        @endif
                    </div>

                    <h3 class="mb-2 text-base font-bold text-foreground">{{ data_get($feature, 'title') }}</h3>
                    <p class="mb-5 flex-1 text-sm leading-relaxed text-muted-foreground">{{ data_get($feature, 'description') }}</p>

                    @if (data_get($feature, 'button_url'))
                        <a href="{{ data_get($feature, 'button_url') }}"
                           class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary transition-colors hover:text-primary/80">
                            {{ $label }}
                            <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
     HOW IT WORKS
═══════════════════════════════════════════════ --}}
<section class="bg-background py-10 lg:py-14">
    <div class="container">

        <div class="mb-8 text-center">
            <span class="section-badge mb-4">Simple Process</span>
            <h2 class="font-display mt-4 text-3xl font-semibold tracking-tight text-foreground sm:text-4xl">
                How it works
            </h2>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-muted-foreground">
                Four simple steps to verify, register, and protect your mobile devices
            </p>
        </div>

        @php
            $stepItems = ($steps ?? collect());
            if ($stepItems->isEmpty()) {
                $stepItems = collect([
                    ['title' => 'IMEI Verification', 'description' => 'Enter the 15-digit IMEI number to instantly check device status and history.', 'icon' => 'list'],
                    ['title' => 'Register / Transfer', 'description' => 'Register your device or transfer ownership securely with OTP verification.', 'icon' => 'device'],
                    ['title' => 'Report Lost / Stolen', 'description' => 'Report missing devices to flag them and prevent unauthorized use.', 'icon' => 'alert'],
                    ['title' => 'Get Certificate', 'description' => 'Download your official status certificate with QR code verification.', 'icon' => 'certificate'],
                ]);
            }
        @endphp

        <div class="relative grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Connecting line (desktop only) --}}
            <div class="absolute top-10 left-[12.5%] right-[12.5%] hidden h-px lg:block"
                 style="background: linear-gradient(90deg, transparent 0%, #0e7c86 25%, #c9973e 75%, transparent 100%); opacity: 0.35;"></div>

            @foreach ($stepItems as $index => $step)
                @php
                    $stepIcon   = data_get($step, 'icon', 'list');
                    $stepNumber = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
                @endphp
                <div class="relative flex flex-col items-center text-center">

                    <div class="relative mb-6">
                        <div class="flex h-20 w-20 items-center justify-center rounded-2xl border border-border bg-card shadow-soft">
                            @if ($stepIcon === 'device')
                                <svg class="h-8 w-8 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="6" y="3" width="12" height="18" rx="2"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 17h4"/>
                                </svg>
                            @elseif ($stepIcon === 'alert')
                                <svg class="h-8 w-8 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                </svg>
                            @elseif ($stepIcon === 'certificate')
                                <svg class="h-8 w-8 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 4h7l4 4v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 4v4h4"/>
                                </svg>
                            @else
                                <svg class="h-8 w-8 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10"/>
                                </svg>
                            @endif
                        </div>
                        <span class="absolute -right-2 -top-2 flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold text-white shadow-soft"
                              style="background: #0e7c86;">
                            {{ $stepNumber }}
                        </span>
                    </div>

                    <h3 class="mb-2 text-base font-bold text-foreground">{{ data_get($step, 'title') }}</h3>
                    <p class="text-sm leading-relaxed text-muted-foreground">{{ data_get($step, 'description') }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
     STATS — dark gradient strip
═══════════════════════════════════════════════ --}}
<section class="relative overflow-hidden py-10 lg:py-12"
         style="background: linear-gradient(160deg, #0a1420 0%, #132132 100%);">
    <div class="absolute inset-0 bg-hero-dots opacity-30 pointer-events-none"></div>

    <div class="container relative z-10">
        <div class="mb-7 text-center">
            <h2 class="font-display text-2xl font-semibold text-white sm:text-3xl">
                Trusted by millions across the nation
            </h2>
        </div>

        @php
            $statItems = ($stats ?? collect());
            if ($statItems->isEmpty()) {
                $statItems = collect([
                    ['value' => '2.5M+', 'label' => 'Devices Verified'],
                    ['value' => '500K+', 'label' => 'Registered Users'],
                    ['value' => '99.9%', 'label' => 'Uptime'],
                    ['value' => '24/7',  'label' => 'Support'],
                ]);
            }
        @endphp

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ($statItems as $stat)
                <div class="stat-card">
                    <p class="mb-1 text-3xl font-extrabold text-white lg:text-4xl">
                        {{ data_get($stat, 'value') }}
                    </p>
                    <p class="text-sm text-slate-400">{{ data_get($stat, 'label') }}</p>
                </div>
            @endforeach
        </div>

        <p class="mt-8 text-center text-sm text-slate-500">
            Trusted by government agencies and law enforcement nationwide
        </p>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
     TESTIMONIALS
═══════════════════════════════════════════════ --}}
<section class="bg-muted/40 py-10 lg:py-14">
    <div class="container">

        <div class="mb-8 text-center">
            <span class="section-badge mb-4">User Stories</span>
            <h2 class="mt-4 font-display text-3xl font-semibold tracking-tight text-foreground sm:text-4xl">
                Trusted by citizens
            </h2>
            <p class="mx-auto mt-4 max-w-xl text-lg text-muted-foreground">
                Real feedback from people using DRMS to secure their devices
            </p>
        </div>

        @php
            $testimonialItems = ($testimonials ?? collect());
            if ($testimonialItems->isEmpty()) {
                $testimonialItems = collect([
                    ['name' => 'Ayesha Rana',  'role' => 'Registered User', 'quote' => 'Verifying my IMEI before buying a phone gave me total peace of mind. The process was instant and the certificate looked professional.'],
                    ['name' => 'Rohan Patel',  'role' => 'Reseller',        'quote' => 'The transfer flow is fast and transparent, which keeps my customers confident. I use it for every second-hand sale.'],
                    ['name' => 'Nadia Khan',   'role' => 'Shop Owner',      'quote' => 'Lost device reporting is simple, and the status updates are immediate. It saved me from a fraudulent transaction last month.'],
                ]);
            }
        @endphp

        <div class="grid gap-6 md:grid-cols-3">
            @foreach ($testimonialItems as $testimonial)
                <div class="flex flex-col rounded-2xl border border-border bg-card p-6 shadow-soft transition-all hover:shadow-card hover:-translate-y-1 duration-300">
                    {{-- Stars --}}
                    <div class="mb-4 flex gap-0.5">
                        @for ($i = 0; $i < 5; $i++)
                            <svg class="h-4 w-4 text-amber-400" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        @endfor
                    </div>

                    {{-- Quote --}}
                    <p class="flex-1 text-sm leading-relaxed text-muted-foreground">
                        &ldquo;{{ data_get($testimonial, 'quote') }}&rdquo;
                    </p>

                    {{-- Author --}}
                    <div class="mt-6 flex items-center gap-3 border-t border-border pt-4">
                        @if (!empty(data_get($testimonial, 'avatar_url')))
                            <img src="{{ data_get($testimonial, 'avatar_url') }}"
                                 alt="{{ data_get($testimonial, 'name') }}"
                                 class="h-10 w-10 rounded-full object-cover ring-2 ring-border"/>
                        @else
                            <div class="flex h-10 w-10 items-center justify-center rounded-full text-sm font-bold text-white"
                                 style="background: #0e7c86;">
                                {{ strtoupper(substr(data_get($testimonial, 'name', ''), 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <p class="text-sm font-semibold text-foreground">{{ data_get($testimonial, 'name') }}</p>
                            <p class="text-xs text-muted-foreground">{{ data_get($testimonial, 'role') }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
     WHY TRUST DRMS
═══════════════════════════════════════════════ --}}
<section class="bg-background py-10 lg:py-14">
    <div class="container">
        <div class="grid gap-12 lg:grid-cols-2 lg:gap-16">

            <div>
                <span class="section-badge mb-4">Why Choose Us</span>
                <h2 class="mt-4 font-display text-3xl font-semibold tracking-tight text-foreground sm:text-4xl">
                    Built for security, designed for trust
                </h2>
                <p class="mt-4 mb-6 text-lg text-muted-foreground">
                    Our platform ensures complete transparency in device verification with government-backed records.
                </p>

                <div class="grid gap-6 sm:grid-cols-2">
                    @foreach ([
                        ['title' => 'Prevent Fraud',       'desc' => 'Verify device authenticity before purchase to avoid stolen or counterfeit devices.', 'icon_bg' => 'rgba(179,38,30,0.1)', 'icon_clr' => '#b3261e'],
                        ['title' => 'Secure Transactions', 'desc' => 'Safe second-hand purchases with verified ownership history and audit trails.', 'icon_bg' => 'rgba(14,124,134,0.1)', 'icon_clr' => '#0e7c86'],
                        ['title' => 'Official Records',    'desc' => 'Government-backed records with tamper-proof logs and digital certificates.', 'icon_bg' => 'rgba(200,151,62,0.12)', 'icon_clr' => '#9c6b1f'],
                        ['title' => 'Authority Backed',    'desc' => 'Integrated with law enforcement for comprehensive device tracking nationwide.', 'icon_bg' => 'rgba(51,70,91,0.1)', 'icon_clr' => '#33465b'],
                    ] as $benefit)
                        <div class="flex gap-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl"
                                 style="background: {{ $benefit['icon_bg'] }};">
                                <svg class="h-5 w-5" style="color:{{ $benefit['icon_clr'] }}"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="mb-1 text-sm font-bold text-foreground">{{ $benefit['title'] }}</h3>
                                <p class="text-sm leading-relaxed text-muted-foreground">{{ $benefit['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Live Demo Card --}}
            <div class="flex items-center justify-center">
                <div class="w-full max-w-md rounded-2xl border border-border bg-card p-8 shadow-card">
                    <div class="mb-6 flex items-center justify-between">
                        <h3 class="text-base font-bold text-foreground">Device Status Preview</h3>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-success/10 px-3 py-1 text-xs font-semibold text-success">
                            <span class="h-1.5 w-1.5 rounded-full bg-success animate-pulse"></span>
                            Live
                        </span>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between rounded-xl border border-border bg-muted/30 px-4 py-3">
                            <span class="text-sm text-muted-foreground">IMEI</span>
                            <span class="font-mono text-sm font-semibold text-foreground">352•••••••8901</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl border border-border bg-muted/30 px-4 py-3">
                            <span class="text-sm text-muted-foreground">Status</span>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-success/10 px-3 py-1 text-xs font-semibold text-success">
                                <span class="h-1.5 w-1.5 rounded-full bg-success"></span>
                                Active
                            </span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl border border-border bg-muted/30 px-4 py-3">
                            <span class="text-sm text-muted-foreground">Owner</span>
                            <span class="text-sm font-semibold text-foreground">J••• D•••</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl border border-border bg-muted/30 px-4 py-3">
                            <span class="text-sm text-muted-foreground">Registered</span>
                            <span class="text-sm font-semibold text-foreground">Mar 2024</span>
                        </div>
                    </div>

                    <a href="{{ route('verification') }}"
                       class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl py-3 text-sm font-semibold text-white shadow-soft transition-all hover:shadow-md hover:-translate-y-px"
                       style="background: #0e7c86;">
                        Try IMEI Lookup
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
     CTA
═══════════════════════════════════════════════ --}}
<section class="relative overflow-hidden py-10 lg:py-14"
         style="background: linear-gradient(160deg, #0a1420 0%, #132132 100%);">
    <div class="absolute inset-0 bg-hero-dots opacity-30 pointer-events-none"></div>

    <div class="container relative z-10">
        @php
            $ctaTitle         = $settings?->cta_title          ?? 'Ready to protect your mobile devices?';
            $ctaSubtitle      = $settings?->cta_subtitle        ?? 'Join thousands of users who trust DRMS for device verification and security. Register today.';
            $ctaPrimaryLabel  = $settings?->cta_primary_label   ?? 'Register Device';
            $ctaPrimaryUrl    = $settings?->cta_primary_url     ?? route('register-device');
            $ctaSecondaryLabel= $settings?->cta_secondary_label ?? 'Sign Up Free';
            $ctaSecondaryUrl  = $settings?->cta_secondary_url   ?? route('register');
        @endphp

        <div class="mx-auto max-w-3xl text-center">
            <div class="mx-auto mb-6 inline-flex items-center justify-center rounded-full border-2 p-4"
                 style="border-color: #2dd4c8; color: #2dd4c8;">
                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z"/>
                </svg>
            </div>

            <h2 class="mb-4 font-display text-3xl font-semibold text-white sm:text-4xl">
                {{ $ctaTitle }}
            </h2>
            <p class="mb-7 text-lg text-slate-300">
                {{ $ctaSubtitle }}
            </p>

            <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                <a href="{{ $ctaPrimaryUrl }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-white px-8 py-3.5 text-sm font-semibold text-slate-900 shadow-soft hover:bg-white/90 transition-all hover:-translate-y-px">
                    {{ $ctaPrimaryLabel }}
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="{{ $ctaSecondaryUrl }}"
                   class="btn-ghost-light px-8 py-3.5 text-sm font-semibold">
                    {{ $ctaSecondaryLabel }}
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
