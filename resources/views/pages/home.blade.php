@extends('layouts.app')

@section('content')
<section class="relative overflow-hidden bg-background py-16 lg:py-24">
    <div class="container">
        <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
            <div class="max-w-xl animate-fade-in">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-border bg-card px-4 py-1.5 text-sm text-muted-foreground shadow-soft">
                    <svg class="h-4 w-4 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                    </svg>
                    <span>Official Device Registry</span>
                </div>

                <h1 class="mb-6 text-4xl font-extrabold leading-tight tracking-tight text-foreground sm:text-5xl lg:text-6xl">
                    Secure Your<br />
                    <span class="text-primary">Mobile Devices</span>
                </h1>

                <p class="mb-8 text-lg leading-relaxed text-muted-foreground">
                    Check and register mobile devices to ensure security and verify
                    IMEI status. Protect yourself from fraud and secure your
                    second-hand device transactions.
                </p>

                <form method="GET" action="{{ route('verification') }}" class="mb-8 flex flex-col gap-3 sm:flex-row">
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
                    <a href="{{ route('register-device') }}" class="inline-flex items-center justify-center rounded-xl border-2 border-primary/20 bg-card px-6 py-3 text-sm font-semibold text-foreground hover:border-primary/40 hover:bg-muted">
                        Register Device
                    </a>
                    <p class="text-sm text-muted-foreground">
                        Already registered?
                        <a href="{{ route('verification') }}" class="font-medium text-primary hover:underline">Check status</a>
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
            </div>
        </div>
    </div>
</section>

<section id="features" class="bg-muted/30 py-20 lg:py-28">
    <div class="container">
        <div class="mb-14 text-center">
            <h2 class="mb-4 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">Core Features</h2>
            <p class="mx-auto max-w-2xl text-lg text-muted-foreground">Everything you need to manage, verify, and protect your mobile devices</p>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['title' => 'Secure IMEI Verification', 'desc' => "Instantly verify any device's IMEI status. Check if a device is active, transferred, lost, or flagged.", 'cta' => 'Verify Now'],
                ['title' => 'Ownership Transfer', 'desc' => 'Safely transfer device ownership with OTP verification. Complete audit trail for every transaction.', 'cta' => 'Transfer Device'],
                ['title' => 'Report Lost / Stolen', 'desc' => 'Report missing devices immediately. Flag devices to prevent unauthorized sales and usage.', 'cta' => 'Report Device'],
                ['title' => 'Status Certificate', 'desc' => 'Download official PDF certificates with QR verification for proof of device status.', 'cta' => 'Get Certificate'],
            ] as $feature)
                <div class="group rounded-2xl border border-border bg-card p-6 shadow-soft transition-all duration-300 hover:shadow-card">
                    <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
                        <svg class="h-6 w-6 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                        </svg>
                    </div>
                    <h3 class="mb-3 text-lg font-bold text-foreground">{{ $feature['title'] }}</h3>
                    <p class="mb-5 text-sm leading-relaxed text-muted-foreground">{{ $feature['desc'] }}</p>
                    <span class="inline-flex items-center gap-2 text-sm font-semibold text-primary">
                        {{ $feature['cta'] }}
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7" />
                        </svg>
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-background py-20 lg:py-28">
    <div class="container">
        <div class="mb-14 text-center">
            <h2 class="mb-4 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">How It Works</h2>
            <p class="mx-auto max-w-2xl text-lg text-muted-foreground">Simple steps to verify, register, and protect your mobile devices</p>
        </div>

        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['number' => '01', 'title' => 'IMEI Verification', 'desc' => 'Enter the 15-digit IMEI number to check device status and history'],
                ['number' => '02', 'title' => 'Register / Transfer', 'desc' => 'Register your device or transfer ownership with OTP verification'],
                ['number' => '03', 'title' => 'Report Lost / Stolen', 'desc' => 'Report missing devices to flag them and prevent unauthorized use'],
                ['number' => '04', 'title' => 'Get Certificate', 'desc' => 'Download official status certificate with QR code verification'],
            ] as $step)
                <div class="relative">
                    <div class="relative flex flex-col items-center text-center">
                        <div class="relative mb-6">
                            <div class="flex h-20 w-20 items-center justify-center rounded-2xl border border-border bg-card shadow-soft">
                                <svg class="h-8 w-8 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10" />
                                </svg>
                            </div>
                            <span class="absolute -right-2 -top-2 flex h-7 w-7 items-center justify-center rounded-full bg-primary text-xs font-bold text-primary-foreground">
                                {{ $step['number'] }}
                            </span>
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-foreground">{{ $step['title'] }}</h3>
                        <p class="text-sm leading-relaxed text-muted-foreground">{{ $step['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-muted/30 py-20 lg:py-28">
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
                        @foreach ([
                            ['value' => '2.5M+', 'label' => 'Devices Verified'],
                            ['value' => '500K+', 'label' => 'Registered Users'],
                            ['value' => '99.9%', 'label' => 'Uptime'],
                            ['value' => '24/7', 'label' => 'Support'],
                        ] as $stat)
                            <div class="text-center">
                                <p class="mb-1 text-3xl font-extrabold text-primary">{{ $stat['value'] }}</p>
                                <p class="text-sm text-muted-foreground">{{ $stat['label'] }}</p>
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

<section class="bg-background py-20 lg:py-28">
    <div class="container">
        <div class="mx-auto max-w-3xl rounded-2xl border border-border bg-card p-8 text-center shadow-card sm:p-12">
            <div class="mx-auto mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10">
                <svg class="h-7 w-7 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                </svg>
            </div>
            <h2 class="mb-4 text-2xl font-bold tracking-tight text-foreground sm:text-3xl">Ready to protect your mobile devices?</h2>
            <p class="mb-8 text-lg text-muted-foreground">Join thousands of users who trust DRMS for device verification and security. Register today and start protecting your devices.</p>
            <div class="flex flex-col justify-center gap-4 sm:flex-row">
                <a href="{{ route('register-device') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">
                    Register Device
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7" />
                    </svg>
                </a>
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-xl border-2 border-primary/20 bg-card px-6 py-3 text-sm font-semibold text-foreground hover:border-primary/40 hover:bg-muted">Sign Up Now</a>
            </div>
        </div>
    </div>
</section>
@endsection
