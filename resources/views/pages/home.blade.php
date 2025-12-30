@extends('layouts.app')

@section('content')
@php
    $bannerItems = ($banners ?? collect());
    if ($bannerItems->isEmpty()) {
        $bannerItems = collect([
            [
                'title' => 'Official device registry is now live nationwide.',
                'subtitle' => 'Search any IMEI in seconds and verify device status.',
                'link_label' => 'Verify an IMEI',
                'link_url' => route('verification'),
                'image_url' => '/images/banner-1.svg',
            ],
            [
                'title' => 'Fraud protection for second-hand buyers.',
                'subtitle' => 'Check ownership history before you purchase.',
                'link_label' => 'Start a check',
                'link_url' => route('verification'),
                'image_url' => '/images/banner-2.svg',
            ],
        ]);
    }
@endphp

<div
    x-data="{ active: 0, total: {{ $bannerItems->count() }} }"
    x-init="setInterval(() => active = (active + 1) % total, 5000)"
    class="border-b border-border bg-primary/10"
>
    <div class="container py-3">
        @foreach ($bannerItems as $index => $banner)
            <div x-show="active === {{ $index }}" x-transition.opacity class="flex flex-col items-start justify-between gap-3 text-sm text-foreground sm:flex-row sm:items-center">
                <div class="flex items-center gap-3">
                    @if (data_get($banner, 'image_url'))
                        <img src="{{ data_get($banner, 'image_url') }}" alt="Banner" class="h-8 w-8 rounded-lg border border-border bg-white p-1" />
                    @endif
                    <div>
                        <p class="font-semibold">{{ data_get($banner, 'title') }}</p>
                        @if (data_get($banner, 'subtitle'))
                            <p class="text-xs text-muted-foreground">{{ data_get($banner, 'subtitle') }}</p>
                        @endif
                    </div>
                </div>
                @if (data_get($banner, 'link_label') && data_get($banner, 'link_url'))
                    <a href="{{ data_get($banner, 'link_url') }}" class="text-sm font-semibold text-primary hover:underline">{{ data_get($banner, 'link_label') }}</a>
                @endif
            </div>
        @endforeach
    </div>
</div>

<section class="relative overflow-hidden bg-background py-12 lg:py-16">
    @php
        $heroTitle = $settings?->hero_title ?? 'Secure Your Mobile Devices';
        $heroSubtitle = $settings?->hero_subtitle ?? 'Check and register mobile devices to ensure security and verify IMEI status. Protect yourself from fraud and secure your second-hand device transactions.';
        $heroPrimaryLabel = $settings?->hero_primary_label ?? 'Register Device';
        $heroPrimaryUrl = $settings?->hero_primary_url ?? route('register-device');
        $heroSecondaryLabel = $settings?->hero_secondary_label ?? 'Check status';
        $heroSecondaryUrl = $settings?->hero_secondary_url ?? route('verification');
    @endphp
    <div class="container">
        <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
            <div class="max-w-xl animate-fade-in">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-border bg-card px-4 py-1.5 text-sm text-muted-foreground shadow-soft">
                    <svg class="h-4 w-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                    </svg>
                    <span>Official Device Registry</span>
                </div>

                <h1 class="mb-5 text-4xl font-extrabold leading-tight tracking-tight text-foreground sm:text-5xl lg:text-6xl">
                    {!! nl2br(e($heroTitle)) !!}
                </h1>

                <p class="mb-7 text-lg leading-relaxed text-muted-foreground">{{ $heroSubtitle }}</p>

                <form method="GET" action="{{ route('verification') }}" class="mb-7 flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-1">
                        <svg class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="11" cy="11" r="7" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 20l-3-3" />
                        </svg>
                        <input
                            type="text"
                            name="imei"
                            placeholder="Enter 15-digit IMEI number"
                            maxlength="15"
                            class="h-14 w-full rounded-xl border border-border bg-card pl-12 text-base shadow-soft placeholder:text-muted-foreground/60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        />
                    </div>
                    <button type="submit" class="inline-flex h-14 items-center justify-center gap-2 rounded-xl bg-primary px-6 text-base font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">
                        Verify IMEI
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7" />
                        </svg>
                    </button>
                </form>

                <div class="flex flex-wrap items-center gap-4">
                    <a href="{{ $heroPrimaryUrl }}" class="inline-flex items-center justify-center rounded-xl border-2 border-primary/20 bg-card px-6 py-3 text-sm font-semibold text-foreground hover:border-primary/40 hover:bg-muted">
                        {{ $heroPrimaryLabel }}
                    </a>
                    <p class="text-sm text-muted-foreground">
                        Already registered?
                        <a href="{{ $heroSecondaryUrl }}" class="font-medium text-primary hover:underline">{{ $heroSecondaryLabel }}</a>
                    </p>
                </div>
            </div>

            <div class="relative hidden lg:block" style="animation-delay: 0.2s;">
                <div class="relative mx-auto w-full max-w-md">
                    <div class="rounded-2xl border border-border bg-card p-8 shadow-card">
                        <div class="mb-6 flex items-center gap-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-primary/10">
                                <svg class="h-7 w-7 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Device Status</p>
                                <p class="text-lg font-bold text-foreground">Verified & Secure</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between rounded-lg bg-muted/50 px-4 py-3">
                                <span class="text-sm text-muted-foreground">IMEI</span>
                                <span class="font-mono text-sm font-medium text-foreground">352*******8901</span>
                            </div>
                            <div class="flex items-center justify-between rounded-lg bg-muted/50 px-4 py-3">
                                <span class="text-sm text-muted-foreground">Status</span>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-success/10 px-3 py-1 text-xs font-semibold text-success">
                                    <span class="h-1.5 w-1.5 rounded-full bg-success"></span>
                                    Active
                                </span>
                            </div>
                            <div class="flex items-center justify-between rounded-lg bg-muted/50 px-4 py-3">
                                <span class="text-sm text-muted-foreground">Owner</span>
                                <span class="text-sm font-medium text-foreground">J*** D***</span>
                            </div>
                        </div>
                    </div>

                    <div class="absolute -right-4 -top-4 rounded-xl border border-border bg-card px-4 py-2 shadow-elevated">
                        <div class="flex items-center gap-2">
                            <div class="h-2 w-2 rounded-full bg-success"></div>
                            <span class="text-sm font-semibold text-foreground">2.5M+</span>
                            <span class="text-xs text-muted-foreground">Verified</span>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-center gap-4">
                    <img src="/images/hero-device.svg" alt="Verified device" class="h-20 w-auto rounded-xl border border-border bg-card p-3 shadow-soft" />
                    <img src="/images/shield-badge.svg" alt="Trusted registry" class="h-20 w-auto rounded-xl border border-border bg-card p-3 shadow-soft" />
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-muted/30 py-8 lg:py-10">
    <div class="container">
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="flex items-center gap-3 rounded-2xl border border-border bg-card px-4 py-3 shadow-soft">
                <img src="/images/certificate-badge.svg" alt="Certificate badge" class="h-12 w-12" />
                <div>
                    <p class="text-sm font-semibold text-foreground">Certified Status</p>
                    <p class="text-xs text-muted-foreground">Download official proof</p>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-2xl border border-border bg-card px-4 py-3 shadow-soft">
                <img src="/images/hero-device.svg" alt="Device verification" class="h-12 w-12" />
                <div>
                    <p class="text-sm font-semibold text-foreground">IMEI Verified</p>
                    <p class="text-xs text-muted-foreground">Real-time registry checks</p>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-2xl border border-border bg-card px-4 py-3 shadow-soft">
                <img src="/images/shield-badge.svg" alt="Security badge" class="h-12 w-12" />
                <div>
                    <p class="text-sm font-semibold text-foreground">Fraud Protection</p>
                    <p class="text-xs text-muted-foreground">Flag stolen devices fast</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="features" class="bg-muted/30 py-16 lg:py-20">
    <div class="container">
        <div class="mb-10 text-center">
            <h2 class="mb-4 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">Core Features</h2>
            <p class="mx-auto max-w-2xl text-lg text-muted-foreground">Everything you need to manage, verify, and protect your mobile devices</p>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $featureItems = ($features ?? collect());
                if ($featureItems->isEmpty()) {
                    $featureItems = collect([
                        ['title' => 'Secure IMEI Verification', 'description' => "Instantly verify any device's IMEI status. Check if a device is active, transferred, lost, or flagged.", 'button_label' => 'Verify Now', 'icon' => 'shield'],
                        ['title' => 'Ownership Transfer', 'description' => 'Safely transfer device ownership with OTP verification. Complete audit trail for every transaction.', 'button_label' => 'Transfer Device', 'icon' => 'transfer'],
                        ['title' => 'Report Lost / Stolen', 'description' => 'Report missing devices immediately. Flag devices to prevent unauthorized sales and usage.', 'button_label' => 'Report Device', 'icon' => 'alert'],
                        ['title' => 'Status Certificate', 'description' => 'Download official PDF certificates with QR verification for proof of device status.', 'button_label' => 'Get Certificate', 'icon' => 'certificate'],
                    ]);
                }
            @endphp
            @foreach ($featureItems as $feature)
                @php
                    $iconKey = data_get($feature, 'icon', 'shield');
                    $label = data_get($feature, 'button_label', 'Learn More');
                @endphp
                <div class="group rounded-2xl border border-border bg-card p-6 shadow-soft transition-all duration-300 hover:shadow-card">
                    <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
                        @if ($iconKey === 'transfer')
                            <svg class="h-6 w-6 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10m0 0-3-3m3 3-3 3M17 17H7m0 0 3 3m-3-3 3-3"/>
                            </svg>
                        @elseif ($iconKey === 'alert')
                            <svg class="h-6 w-6 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M5 5l14 14M12 3a9 9 0 1 1 0 18 9 9 0 0 1 0-18z"/>
                            </svg>
                        @elseif ($iconKey === 'certificate')
                            <svg class="h-6 w-6 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 4h7l4 4v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 4v4h4"/>
                            </svg>
                        @else
                            <svg class="h-6 w-6 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                            </svg>
                        @endif
                    </div>
                    <h3 class="mb-3 text-lg font-bold text-foreground">{{ data_get($feature, 'title') }}</h3>
                    <p class="mb-5 text-sm leading-relaxed text-muted-foreground">{{ data_get($feature, 'description') }}</p>
                    @if (data_get($feature, 'button_url'))
                        <a href="{{ data_get($feature, 'button_url') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary">
                            {{ $label }}
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7" />
                            </svg>
                        </a>
                    @else
                        <span class="inline-flex items-center gap-2 text-sm font-semibold text-primary">
                            {{ $label }}
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7" />
                            </svg>
                        </span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-background py-16 lg:py-20">
    <div class="container">
        <div class="mb-10 text-center">
            <h2 class="mb-4 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">Trusted by citizens</h2>
            <p class="mx-auto max-w-2xl text-lg text-muted-foreground">Real feedback from people using DRMS to secure their devices.</p>
        </div>

        @php
            $testimonialItems = ($testimonials ?? collect());
            if ($testimonialItems->isEmpty()) {
                $testimonialItems = collect([
                    ['name' => 'Ayesha Rana', 'role' => 'Registered User', 'quote' => 'Verifying my IMEI before buying a phone gave me total peace of mind.'],
                    ['name' => 'Rohan Patel', 'role' => 'Reseller', 'quote' => 'The transfer flow is fast and transparent, which keeps customers confident.'],
                    ['name' => 'Nadia Khan', 'role' => 'Shop Owner', 'quote' => 'Lost device reporting is simple, and the status updates are immediate.'],
                ]);
            }
        @endphp

        <div class="grid gap-6 md:grid-cols-3">
            @foreach ($testimonialItems as $testimonial)
                <div class="rounded-2xl border border-border bg-card p-6 shadow-soft">
                    <p class="text-sm leading-relaxed text-muted-foreground">&quot;{{ data_get($testimonial, 'quote') }}&quot;</p>
                    <div class="mt-6 flex items-center gap-3">
                        @if (!empty(data_get($testimonial, 'avatar_url')))
                            <img src="{{ data_get($testimonial, 'avatar_url') }}" alt="{{ data_get($testimonial, 'name') }}" class="h-10 w-10 rounded-full object-cover" />
                        @else
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-sm font-semibold text-primary">
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

<section class="bg-background py-16 lg:py-20">
    <div class="container">
        <div class="mb-10 text-center">
            <h2 class="mb-4 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">How It Works</h2>
            <p class="mx-auto max-w-2xl text-lg text-muted-foreground">Simple steps to verify, register, and protect your mobile devices</p>
        </div>

        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $stepItems = ($steps ?? collect());
                if ($stepItems->isEmpty()) {
                    $stepItems = collect([
                        ['title' => 'IMEI Verification', 'description' => 'Enter the 15-digit IMEI number to check device status and history', 'icon' => 'list'],
                        ['title' => 'Register / Transfer', 'description' => 'Register your device or transfer ownership with OTP verification', 'icon' => 'device'],
                        ['title' => 'Report Lost / Stolen', 'description' => 'Report missing devices to flag them and prevent unauthorized use', 'icon' => 'alert'],
                        ['title' => 'Get Certificate', 'description' => 'Download official status certificate with QR code verification', 'icon' => 'certificate'],
                    ]);
                }
            @endphp
            @foreach ($stepItems as $index => $step)
                @php
                    $stepIcon = data_get($step, 'icon', 'list');
                    $stepNumber = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
                @endphp
                <div class="relative">
                    <div class="relative flex flex-col items-center text-center">
                        <div class="relative mb-6">
                            <div class="flex h-20 w-20 items-center justify-center rounded-2xl border border-border bg-card shadow-soft">
                                @if ($stepIcon === 'device')
                                    <svg class="h-8 w-8 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <rect x="6" y="3" width="12" height="18" rx="2" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 17h4" />
                                    </svg>
                                @elseif ($stepIcon === 'alert')
                                    <svg class="h-8 w-8 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M5 5l14 14M12 3a9 9 0 1 1 0 18 9 9 0 0 1 0-18z"/>
                                    </svg>
                                @elseif ($stepIcon === 'certificate')
                                    <svg class="h-8 w-8 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 4h7l4 4v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 4v4h4"/>
                                    </svg>
                                @else
                                    <svg class="h-8 w-8 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10" />
                                    </svg>
                                @endif
                            </div>
                            <span class="absolute -right-2 -top-2 flex h-7 w-7 items-center justify-center rounded-full bg-primary text-xs font-bold text-primary-foreground">
                                {{ $stepNumber }}
                            </span>
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-foreground">{{ data_get($step, 'title') }}</h3>
                        <p class="text-sm leading-relaxed text-muted-foreground">{{ data_get($step, 'description') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-muted/30 py-16 lg:py-20">
    <div class="container">
        <div class="grid gap-12 lg:grid-cols-2 lg:gap-16">
            <div>
                <h2 class="mb-4 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">Why Trust DRMS?</h2>
                <p class="mb-10 text-lg text-muted-foreground">Built for security, designed for trust. Our platform ensures complete transparency in device verification.</p>

                <div class="grid gap-6 sm:grid-cols-2">
                    @foreach ([
                        ['title' => 'Prevent Fraud', 'desc' => 'Verify device authenticity before purchase to avoid stolen or counterfeit devices'],
                        ['title' => 'Secure Transactions', 'desc' => 'Safe second-hand device purchases with verified ownership history'],
                        ['title' => 'Official Records', 'desc' => 'Government-backed verification records with tamper-proof audit trails'],
                        ['title' => 'Authority Supported', 'desc' => 'Integrated with law enforcement for comprehensive device tracking'],
                    ] as $benefit)
                        <div class="flex gap-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                                <svg class="h-5 w-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="mb-1 font-bold text-foreground">{{ $benefit['title'] }}</h3>
                                <p class="text-sm leading-relaxed text-muted-foreground">{{ $benefit['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-center">
                <div class="w-full max-w-md rounded-2xl border border-border bg-card p-8 shadow-card">
                    <h3 class="mb-8 text-center text-lg font-bold text-foreground">Platform Statistics</h3>
                    <div class="grid grid-cols-2 gap-8">
                        @php
                            $statItems = ($stats ?? collect());
                            if ($statItems->isEmpty()) {
                                $statItems = collect([
                                    ['value' => '2.5M+', 'label' => 'Devices Verified'],
                                    ['value' => '500K+', 'label' => 'Registered Users'],
                                    ['value' => '99.9%', 'label' => 'Uptime'],
                                    ['value' => '24/7', 'label' => 'Support'],
                                ]);
                            }
                        @endphp
                        @foreach ($statItems as $stat)
                            <div class="text-center">
                                <p class="mb-1 text-3xl font-extrabold text-primary">{{ data_get($stat, 'value') }}</p>
                                <p class="text-sm text-muted-foreground">{{ data_get($stat, 'label') }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-8 rounded-xl bg-muted/50 p-4 text-center">
                        <p class="text-sm text-muted-foreground">Trusted by government agencies and law enforcement nationwide</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-background py-16 lg:py-20">
    <div class="container">
        @php
            $ctaTitle = $settings?->cta_title ?? 'Ready to protect your mobile devices?';
            $ctaSubtitle = $settings?->cta_subtitle ?? 'Join thousands of users who trust DRMS for device verification and security. Register today and start protecting your devices.';
            $ctaPrimaryLabel = $settings?->cta_primary_label ?? 'Register Device';
            $ctaPrimaryUrl = $settings?->cta_primary_url ?? route('register-device');
            $ctaSecondaryLabel = $settings?->cta_secondary_label ?? 'Sign Up Now';
            $ctaSecondaryUrl = $settings?->cta_secondary_url ?? route('register');
        @endphp
        <div class="mx-auto max-w-3xl rounded-2xl border border-border bg-card p-8 text-center shadow-card sm:p-12">
            <div class="mx-auto mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10">
                <svg class="h-7 w-7 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                </svg>
            </div>
            <h2 class="mb-4 text-2xl font-bold tracking-tight text-foreground sm:text-3xl">{{ $ctaTitle }}</h2>
            <p class="mb-8 text-lg text-muted-foreground">{{ $ctaSubtitle }}</p>
            <div class="flex flex-col justify-center gap-4 sm:flex-row">
                <a href="{{ $ctaPrimaryUrl }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">
                    {{ $ctaPrimaryLabel }}
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7" />
                    </svg>
                </a>
                <a href="{{ $ctaSecondaryUrl }}" class="inline-flex items-center justify-center rounded-xl border-2 border-primary/20 bg-card px-6 py-3 text-sm font-semibold text-foreground hover:border-primary/40 hover:bg-muted">{{ $ctaSecondaryLabel }}</a>
            </div>
        </div>
    </div>
</section>
@endsection
