@extends('layouts.app')

@section('content')
@php
    $img = function ($path) {
        if (! $path) return '';
        if (str_starts_with($path, 'http')) return $path;
        if (str_starts_with($path, '/images/')) return asset($path);
        return asset('storage/'.$path);
    };

    $bannerItems = ($banners ?? collect());
    if ($bannerItems->isEmpty()) {
        $bannerItems = collect([
            ['title' => 'Know the phone before you trust it', 'subtitle' => 'Check any IMEI in seconds to see if a device is registered, active, or reported stolen.', 'link_label' => 'Check an IMEI', 'link_url' => route('verification'), 'image_url' => '/images/banners/verify-before-you-buy.jpg'],
            ['title' => 'Buying second-hand? Check it first', 'subtitle' => 'See a device’s registry status before you hand over any money.', 'link_label' => 'Run a free check', 'link_url' => route('verification'), 'image_url' => '/images/banners/second-hand-phones.jpg'],
            ['title' => 'Selling? Transfer ownership in minutes', 'subtitle' => 'Both owners confirm with an SMS code and the record updates instantly.', 'link_label' => 'Transfer a device', 'link_url' => route('transfer'), 'image_url' => '/images/banners/ownership-transfer.jpg'],
            ['title' => 'Lost your phone? Flag it instantly', 'subtitle' => 'Anyone who checks its IMEI will be warned not to buy it.', 'link_label' => 'Report a device', 'link_url' => route('lost-found.index'), 'image_url' => '/images/banners/lost-phone.jpg'],
        ]);
    }
    $slides = $bannerItems->map(fn ($b) => [
        'title' => data_get($b, 'title'),
        'subtitle' => data_get($b, 'subtitle'),
        'link_label' => data_get($b, 'link_label'),
        'link_url' => data_get($b, 'link_url'),
        'image' => $img(data_get($b, 'image_url')) ?: asset('images/banners/verify-before-you-buy.jpg'),
    ])->values();
    $first = $slides->first();

    $heroSecondaryLabel = $settings?->hero_secondary_label ?? 'Register a device';
    $heroSecondaryUrl = $settings?->hero_secondary_url ?? route('register-device');

    $tasks = [
        ['icon' => 'search', 'title' => 'Check an IMEI', 'text' => 'See if a phone is registered, active, or reported stolen.', 'url' => route('verification'), 'bg' => 'from-indigo-50 to-white', 'tile' => 'bg-indigo-600 text-white', 'ring' => 'hover:ring-indigo-200'],
        ['icon' => 'phone', 'title' => 'Register my device', 'text' => 'Put your phone on record under your own name.', 'url' => route('register-device'), 'bg' => 'from-emerald-50 to-white', 'tile' => 'bg-emerald-600 text-white', 'ring' => 'hover:ring-emerald-200'],
        ['icon' => 'transfer', 'title' => 'Sell or give away', 'text' => 'Move ownership to someone else with two SMS codes.', 'url' => route('transfer'), 'bg' => 'from-amber-50 to-white', 'tile' => 'bg-amber-500 text-white', 'ring' => 'hover:ring-amber-200'],
        ['icon' => 'alert', 'title' => 'Report lost or stolen', 'text' => 'Flag your phone so it can’t be resold.', 'url' => route('lost-found.index'), 'bg' => 'from-rose-50 to-white', 'tile' => 'bg-rose-600 text-white', 'ring' => 'hover:ring-rose-200'],
    ];

    $iconMap = ['shield' => 'shield-check', 'transfer' => 'transfer', 'alert' => 'alert', 'certificate' => 'document', 'device' => 'phone', 'list' => 'search'];
@endphp

{{-- ═══ Hero carousel ═══════════════════════════════════ --}}
<section
    x-data="{
        active: 0,
        slides: @js($slides),
        timer: null,
        go(i) { this.active = (i + this.slides.length) % this.slides.length; this.restart(); },
        restart() { clearInterval(this.timer); if (this.slides.length > 1) this.timer = setInterval(() => this.active = (this.active + 1) % this.slides.length, 6500); },
    }"
    x-init="restart()"
    class="relative overflow-hidden bg-[hsl(232_45%_10%)] text-white"
>
    {{-- Background images --}}
    <template x-for="(slide, i) in slides" :key="'bg' + i">
        <div class="absolute inset-0 transition-opacity duration-1000 ease-out" :class="i === active ? 'opacity-100' : 'opacity-0'">
            <img :src="slide.image" alt="" class="h-full w-full scale-105 object-cover transition-transform duration-[7000ms] ease-out" :class="i === active && 'scale-100'" />
        </div>
    </template>
    <noscript><img src="{{ $first['image'] }}" alt="" class="absolute inset-0 h-full w-full object-cover" /></noscript>
    <div class="absolute inset-0 bg-gradient-to-r from-[hsl(232_50%_8%)] via-[hsl(232_50%_8%/0.82)] to-[hsl(232_50%_8%/0.25)]"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-[hsl(232_50%_8%/0.7)] via-transparent to-transparent"></div>

    <div class="container relative grid min-h-[640px] items-center gap-12 py-20 lg:min-h-[700px] lg:grid-cols-[1.15fr,0.85fr]">
        <div class="max-w-2xl">
            <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 py-1.5 pl-1.5 pr-3 text-xs font-semibold backdrop-blur">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-white text-primary"><x-site.icon name="shield-check" class="h-3 w-3" stroke="2.4" /></span>
                National device registry
            </span>

            <div class="relative mt-6 min-h-[13rem] sm:min-h-[15rem]">
                <template x-for="(slide, i) in slides" :key="'t' + i">
                    <div x-show="i === active"
                         x-transition:enter="transition ease-out duration-700 delay-150" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-200 absolute inset-x-0 top-0" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                        <h1 class="font-display text-balance text-5xl leading-[1.02] sm:text-6xl lg:text-7xl" x-text="slide.title"></h1>
                        <p class="mt-5 max-w-xl text-lg leading-relaxed text-white/75" x-text="slide.subtitle"></p>
                    </div>
                </template>
                <noscript>
                    <h1 class="font-display text-5xl leading-[1.02] sm:text-6xl lg:text-7xl">{{ $first['title'] }}</h1>
                    <p class="mt-5 max-w-xl text-lg text-white/75">{{ $first['subtitle'] }}</p>
                </noscript>
            </div>

            {{-- IMEI search --}}
            <form method="GET" action="{{ route('verification') }}" class="mt-6 max-w-xl">
                <label for="hero-imei" class="sr-only">IMEI or serial number</label>
                <div class="flex items-center gap-2 rounded-2xl bg-white p-2 shadow-[0_20px_50px_-20px_rgba(0,0,0,0.6)] ring-4 ring-white/10">
                    <x-site.icon name="search" class="ml-3 h-5 w-5 shrink-0 text-muted-foreground" />
                    <input id="hero-imei" type="text" name="imei" maxlength="50" autocomplete="off" placeholder="Enter IMEI or serial number"
                           class="h-12 min-w-0 flex-1 border-0 bg-transparent px-1 font-mono text-[15px] tracking-wide text-foreground placeholder:font-sans placeholder:tracking-normal placeholder:text-muted-foreground/70 focus:outline-none focus:ring-0" />
                    <button type="submit" class="btn btn-primary h-12 px-5 sm:px-6">
                        <span class="hidden sm:inline">Check now</span>
                        <x-site.icon name="arrow-right" class="h-4 w-4" stroke="2.2" />
                    </button>
                </div>
                <p class="mt-3 pl-1 text-xs text-white/60">Dial <span class="rounded bg-white/15 px-1.5 py-0.5 font-mono font-semibold text-white">*#06#</span> on any phone to see its IMEI.</p>
            </form>

            <div class="mt-7 flex flex-wrap items-center gap-3">
                <template x-if="slides[active]?.link_url && slides[active]?.link_label">
                    <a :href="slides[active].link_url" class="btn bg-white/95 text-foreground hover:bg-white">
                        <span x-text="slides[active].link_label"></span> <x-site.icon name="arrow-right" class="h-4 w-4" />
                    </a>
                </template>
                <a href="{{ $heroSecondaryUrl }}"
                   x-show="(slides[active]?.link_label || '').toLowerCase() !== @js(strtolower($heroSecondaryLabel))"
                   class="btn border border-white/25 text-white backdrop-blur hover:bg-white/10">{{ $heroSecondaryLabel }}</a>
            </div>
        </div>

        {{-- Floating verdict cards --}}
        <div class="relative hidden h-full lg:block" aria-hidden="true">
            <div class="absolute right-0 top-1/2 w-80 -translate-y-1/2 rotate-[2deg] rounded-3xl bg-white/95 p-6 text-foreground shadow-[0_40px_80px_-30px_rgba(0,0,0,0.7)] backdrop-blur">
                <div class="flex items-center justify-between">
                    <span class="eyebrow">Registry result</span>
                    <span class="pill pill-success">Active</span>
                </div>
                <div class="mt-4 flex items-center gap-3">
                    <span class="icon-tile h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600"><x-site.icon name="shield-check" class="h-6 w-6" /></span>
                    <div>
                        <p class="font-display text-2xl leading-tight">Safe to buy</p>
                        <p class="text-xs text-muted-foreground">Registered · not reported</p>
                    </div>
                </div>
                <dl class="mt-5 space-y-2 text-sm">
                    @foreach (['Device' => 'Galaxy S24 · 256 GB', 'IMEI' => '3519 •••• 8901', 'Since' => 'Mar 2025'] as $k => $v)
                        <div class="flex justify-between rounded-lg bg-muted/70 px-3 py-2">
                            <dt class="text-muted-foreground">{{ $k }}</dt>
                            <dd class="font-semibold {{ $k === 'IMEI' ? 'font-mono text-xs leading-5' : '' }}">{{ $v }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
            <div class="absolute bottom-16 right-56 flex -rotate-3 items-center gap-3 rounded-2xl bg-white/95 px-4 py-3 text-foreground shadow-2xl">
                <span class="icon-tile h-9 w-9 bg-rose-50 text-rose-600"><x-site.icon name="alert" class="h-4 w-4" /></span>
                <div class="text-xs">
                    <p class="font-semibold">Stolen phones flagged</p>
                    <p class="text-muted-foreground">within minutes of a report</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Slide controls --}}
    <div class="container relative flex items-center justify-between gap-4 pb-24" x-show="slides.length > 1">
        <div class="flex flex-1 gap-2">
            <template x-for="(slide, i) in slides" :key="'d' + i">
                <button type="button" @click="go(i)" class="group flex-1 py-2 text-left" :aria-label="'Show slide ' + (i + 1)">
                    <span class="block h-1 overflow-hidden rounded-full bg-white/20">
                        <span class="block h-full rounded-full bg-white transition-all ease-linear"
                              :class="i === active ? 'w-full duration-[6500ms]' : (i < active ? 'w-full duration-0' : 'w-0 duration-0')"></span>
                    </span>
                    <span class="mt-2 hidden truncate text-xs font-medium transition sm:block" :class="i === active ? 'text-white' : 'text-white/50 group-hover:text-white/80'" x-text="slide.title"></span>
                </button>
            </template>
        </div>
        <div class="flex gap-2">
            <button type="button" @click="go(active - 1)" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/25 transition hover:bg-white/10" aria-label="Previous"><x-site.icon name="arrow-left" class="h-4 w-4" /></button>
            <button type="button" @click="go(active + 1)" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/25 transition hover:bg-white/10" aria-label="Next"><x-site.icon name="arrow-right" class="h-4 w-4" /></button>
        </div>
    </div>
</section>

{{-- ═══ Stats bar (overlaps hero) ═══════════════════════ --}}
@php
    $statItems = ($stats ?? collect());
    if ($statItems->isEmpty()) {
        $statItems = collect([
            ['value' => '2.5M+', 'label' => 'Devices verified'],
            ['value' => '500K+', 'label' => 'Registered users'],
            ['value' => '99.9%', 'label' => 'Uptime'],
            ['value' => '24/7', 'label' => 'Support'],
        ]);
    }
@endphp
<section class="container relative z-10 -mt-14">
    <dl class="surface grid grid-cols-2 divide-border overflow-hidden lg:grid-cols-4 lg:divide-x">
        @foreach ($statItems as $i => $stat)
            <div class="px-6 py-6 text-center sm:py-7 {{ $i < 2 ? 'border-b border-border lg:border-b-0' : '' }} {{ $i % 2 === 0 ? 'border-r border-border lg:border-r-0' : '' }}">
                <dd class="font-display text-4xl text-primary sm:text-5xl">{{ data_get($stat, 'value') }}</dd>
                <dt class="mt-1 text-sm font-medium text-muted-foreground">{{ data_get($stat, 'label') }}</dt>
            </div>
        @endforeach
    </dl>
</section>

{{-- ═══ Tasks ═══════════════════════════════════════════ --}}
<section class="container py-20">
    <div class="mx-auto max-w-2xl text-center">
        <p class="eyebrow">Start here</p>
        <h2 class="font-display mt-3 text-4xl text-foreground sm:text-5xl">What would you like to do?</h2>
        <p class="mt-3 text-muted-foreground">Pick a task — we’ll walk you through it step by step.</p>
    </div>
    <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($tasks as $task)
            <a href="{{ $task['url'] }}" class="group relative flex flex-col overflow-hidden rounded-3xl border border-border bg-gradient-to-b {{ $task['bg'] }} p-6 ring-4 ring-transparent transition duration-300 hover:-translate-y-1 hover:shadow-[0_24px_48px_-24px_rgba(20,22,40,0.35)] {{ $task['ring'] }}">
                <span class="icon-tile h-14 w-14 rounded-2xl shadow-lg {{ $task['tile'] }}"><x-site.icon :name="$task['icon']" class="h-6 w-6" /></span>
                <h3 class="mt-8 text-lg font-bold text-foreground">{{ $task['title'] }}</h3>
                <p class="mt-1.5 flex-1 text-sm leading-relaxed text-muted-foreground">{{ $task['text'] }}</p>
                <span class="mt-6 flex items-center justify-between text-sm font-semibold text-foreground">
                    Get started
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-foreground text-background transition group-hover:translate-x-1">
                        <x-site.icon name="arrow-right" class="h-4 w-4" stroke="2.2" />
                    </span>
                </span>
            </a>
        @endforeach
    </div>
    @guest
        <p class="mt-6 text-center text-sm text-muted-foreground">
            Checking an IMEI is open to everyone. Registering, transferring and reporting need a free account —
            <a href="{{ route('register') }}" class="font-semibold text-primary hover:underline">create one in a minute</a>.
        </p>
    @endguest
</section>

{{-- ═══ Buying second-hand ══════════════════════════════ --}}
<section class="container pb-20">
    <div class="grid items-center gap-12 overflow-hidden rounded-[2rem] bg-card p-6 ring-1 ring-border sm:p-10 lg:grid-cols-2 lg:gap-16 lg:p-14">
        <div class="relative">
            <img src="{{ asset('images/banners/phone-on-desk.jpg') }}" alt="A smartphone on a wooden desk" loading="lazy"
                 class="aspect-[4/3] w-full rounded-3xl object-cover shadow-[0_30px_60px_-30px_rgba(20,22,40,0.5)]" />
            <div class="absolute -bottom-5 left-5 flex items-center gap-3 rounded-2xl bg-card px-4 py-3 shadow-xl ring-1 ring-border sm:left-8">
                <span class="icon-tile h-10 w-10 bg-rose-50 text-rose-600"><x-site.icon name="alert" class="h-5 w-5" /></span>
                <div>
                    <p class="text-sm font-bold text-foreground">Reported stolen</p>
                    <p class="text-xs text-muted-foreground">Do not buy this device</p>
                </div>
            </div>
            <div class="absolute -top-4 right-5 hidden items-center gap-2 rounded-full bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-lg sm:flex">
                <x-site.icon name="check" class="h-4 w-4" stroke="2.6" /> Checked in 2 seconds
            </div>
        </div>
        <div>
            <p class="eyebrow">Buying second-hand?</p>
            <h2 class="font-display mt-3 text-4xl leading-tight text-foreground sm:text-5xl">Thirty seconds that could save you thousands</h2>
            <p class="mt-4 text-muted-foreground">Stolen phones are often resold within hours. Before you pay, ask for the IMEI and check it here.</p>
            <ul class="mt-8 space-y-4">
                @foreach ([
                    ['Ask the seller to dial *#06#', 'The IMEI appears on screen — no apps needed.'],
                    ['Check it against the registry', 'We’ll tell you if it’s registered, active, or reported.'],
                    ['Transfer ownership on the spot', 'The seller sends it to your name with an SMS code.'],
                ] as $n => [$t, $d])
                    <li class="flex gap-4">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-foreground">{{ $n + 1 }}</span>
                        <div>
                            <p class="font-semibold text-foreground">{{ $t }}</p>
                            <p class="text-sm text-muted-foreground">{{ $d }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
            <a href="{{ route('verification') }}" class="btn btn-primary btn-lg mt-9">
                <x-site.icon name="search" class="h-4 w-4" stroke="2.2" /> Check a phone now
            </a>
        </div>
    </div>
</section>

{{-- ═══ How it works (dark) ═════════════════════════════ --}}
@php
    $stepItems = ($steps ?? collect());
    if ($stepItems->isEmpty()) {
        $stepItems = collect([
            ['title' => 'Check the IMEI', 'description' => 'Enter the 15-digit IMEI to see a device’s status instantly.', 'icon' => 'list'],
            ['title' => 'Register or transfer', 'description' => 'Register your device, or hand it over securely with OTP.', 'icon' => 'device'],
            ['title' => 'Report if lost', 'description' => 'Flag a missing device so nobody else can use or sell it.', 'icon' => 'alert'],
            ['title' => 'Prove ownership', 'description' => 'Your registry record is your proof whenever you need it.', 'icon' => 'certificate'],
        ]);
    }
@endphp
<section id="how-it-works" class="relative scroll-mt-16 overflow-hidden bg-[hsl(232_45%_10%)] py-24 text-white">
    <div class="pointer-events-none absolute -left-40 top-0 h-[30rem] w-[30rem] rounded-full bg-primary/40 blur-[120px]"></div>
    <div class="pointer-events-none absolute -right-20 bottom-0 h-96 w-96 rounded-full bg-amber-400/15 blur-[100px]"></div>
    <div class="pointer-events-none absolute inset-0 opacity-[0.06]" style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 26px 26px;"></div>

    <div class="container relative">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-white/50">How it works</p>
            <h2 class="font-display mt-3 text-4xl leading-tight sm:text-5xl">Four steps to a phone that’s <em class="text-amber-300">provably yours</em></h2>
            <p class="mt-4 text-white/60">No paperwork, no queues. Everything happens online and every change is verified by OTP.</p>
        </div>
        <ol class="relative mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="absolute left-[12%] right-[12%] top-8 hidden h-px bg-gradient-to-r from-transparent via-white/25 to-transparent lg:block"></div>
            @foreach ($stepItems as $index => $step)
                <li class="relative text-center">
                    <span class="relative mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 ring-1 ring-white/20 backdrop-blur">
                        <x-site.icon :name="$iconMap[data_get($step, 'icon', 'list')] ?? 'shield-check'" class="h-7 w-7 text-white" />
                        <span class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-amber-300 text-[11px] font-bold text-[hsl(232_45%_10%)]">{{ $index + 1 }}</span>
                    </span>
                    <h3 class="mt-6 text-lg font-bold">{{ data_get($step, 'title') }}</h3>
                    <p class="mx-auto mt-2 max-w-[16rem] text-sm leading-relaxed text-white/60">{{ data_get($step, 'description') }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- ═══ Features ════════════════════════════════════════ --}}
@php
    $featureItems = ($features ?? collect());
    if ($featureItems->isEmpty()) {
        $featureItems = collect([
            ['icon' => 'shield', 'title' => 'Secure IMEI verification', 'description' => 'Instantly check whether a device is active, transferred, lost, or flagged.', 'button_label' => 'Verify now', 'button_url' => route('verification')],
            ['icon' => 'transfer', 'title' => 'Ownership transfer', 'description' => 'OTP verification on both sides and a full audit trail.', 'button_label' => 'Transfer a device', 'button_url' => route('transfer')],
            ['icon' => 'alert', 'title' => 'Lost & stolen reports', 'description' => 'Report missing devices to stop unauthorised sale and use.', 'button_label' => 'Report a device', 'button_url' => route('lost-found.index')],
            ['icon' => 'certificate', 'title' => 'Status records', 'description' => 'A trusted record buyers and authorities can rely on.', 'button_label' => 'Check a status', 'button_url' => route('verification')],
        ]);
    }
    $featureTones = ['bg-indigo-600', 'bg-amber-500', 'bg-rose-600', 'bg-emerald-600'];
@endphp
<section id="features" class="container py-24">
    <div class="grid gap-12 lg:grid-cols-[0.8fr,1.2fr] lg:items-start">
        <div class="lg:sticky lg:top-28">
            <p class="eyebrow">Everything in one place</p>
            <h2 class="font-display mt-3 text-4xl leading-tight text-foreground sm:text-5xl">Built to protect every device</h2>
            <p class="mt-4 max-w-md text-muted-foreground">Government-backed records with tamper-proof logs, integrated with law enforcement nationwide.</p>
            <div class="mt-8 flex flex-wrap gap-2">
                @foreach (['Prevent fraud', 'Secure transactions', 'Verified records', 'Authority backed'] as $tag)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-card px-3 py-1.5 text-xs font-semibold text-foreground ring-1 ring-border">
                        <x-site.icon name="check" class="h-3.5 w-3.5 text-primary" stroke="2.6" /> {{ $tag }}
                    </span>
                @endforeach
            </div>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            @foreach ($featureItems as $i => $feature)
                <div class="surface group flex flex-col p-7 transition duration-300 hover:-translate-y-1 {{ $i % 2 === 1 ? 'sm:translate-y-8 sm:hover:translate-y-7' : '' }}">
                    <span class="icon-tile h-12 w-12 rounded-2xl text-white shadow-lg {{ $featureTones[$i % 4] }}">
                        <x-site.icon :name="$iconMap[data_get($feature, 'icon', 'shield')] ?? 'shield-check'" />
                    </span>
                    <h3 class="mt-6 text-lg font-bold text-foreground">{{ data_get($feature, 'title') }}</h3>
                    <p class="mt-2 flex-1 text-sm leading-relaxed text-muted-foreground">{{ data_get($feature, 'description') }}</p>
                    @if (data_get($feature, 'button_url'))
                        <a href="{{ data_get($feature, 'button_url') }}" class="mt-6 inline-flex items-center gap-1 text-sm font-semibold text-primary hover:underline">
                            {{ data_get($feature, 'button_label', 'Learn more') }} <x-site.icon name="arrow-right" class="h-4 w-4" />
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══ Testimonials ════════════════════════════════════ --}}
@php
    $testimonialItems = ($testimonials ?? collect());
    if ($testimonialItems->isEmpty()) {
        $testimonialItems = collect([
            ['name' => 'Ayesha Rana', 'role' => 'Registered user', 'quote' => 'Checking the IMEI before buying a phone gave me total peace of mind. It took seconds.'],
            ['name' => 'Rohan Patel', 'role' => 'Reseller', 'quote' => 'The transfer flow is fast and transparent, which keeps my customers confident on every sale.'],
            ['name' => 'Nadia Khan', 'role' => 'Shop owner', 'quote' => 'Lost device reporting is simple and the status updates immediately. It saved me from a fraudulent deal.'],
        ]);
    }
    $avatarTones = ['bg-indigo-100 text-indigo-700', 'bg-amber-100 text-amber-800', 'bg-emerald-100 text-emerald-700', 'bg-rose-100 text-rose-700'];
@endphp
<section class="bg-gradient-to-b from-muted/70 to-background py-24">
    <div class="container">
        <div class="mx-auto max-w-2xl text-center">
            <p class="eyebrow">In their words</p>
            <h2 class="font-display mt-3 text-4xl text-foreground sm:text-5xl">People who checked first</h2>
        </div>
        <div class="mt-14 grid gap-5 md:grid-cols-3">
            @foreach ($testimonialItems as $i => $testimonial)
                <figure class="surface relative flex flex-col p-8">
                    <span class="font-display absolute right-6 top-2 text-8xl leading-none text-primary/10">”</span>
                    <div class="flex gap-0.5 text-amber-500">
                        @for ($s = 0; $s < 5; $s++)
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="m12 3.5 2.6 5.3 5.9.9-4.3 4.1 1 5.8L12 16.9l-5.2 2.7 1-5.8L3.5 9.7l5.9-.9L12 3.5Z"/></svg>
                        @endfor
                    </div>
                    <blockquote class="font-display relative mt-5 flex-1 text-[1.4rem] leading-snug text-foreground">“{{ data_get($testimonial, 'quote') }}”</blockquote>
                    <figcaption class="mt-8 flex items-center gap-3 border-t border-border pt-5">
                        @if (! empty(data_get($testimonial, 'avatar_url')))
                            <img src="{{ data_get($testimonial, 'avatar_url') }}" alt="" class="h-11 w-11 rounded-full object-cover ring-2 ring-border" />
                        @else
                            <span class="flex h-11 w-11 items-center justify-center rounded-full text-sm font-bold {{ $avatarTones[$i % 4] }}">
                                {{ strtoupper(mb_substr(data_get($testimonial, 'name', ''), 0, 1)) }}
                            </span>
                        @endif
                        <div>
                            <p class="text-sm font-semibold text-foreground">{{ data_get($testimonial, 'name') }}</p>
                            <p class="text-xs text-muted-foreground">{{ data_get($testimonial, 'role') }}</p>
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══ CTA with image ══════════════════════════════════ --}}
@php
    $ctaTitle = $settings?->cta_title ?? 'Protect your phone today';
    $ctaSubtitle = $settings?->cta_subtitle ?? 'Register in minutes. If it’s ever lost or stolen, you’ll be glad you did.';
    $ctaPrimaryLabel = $settings?->cta_primary_label ?? 'Register a device';
    $ctaPrimaryUrl = $settings?->cta_primary_url ?? route('register-device');
    $ctaSecondaryLabel = $settings?->cta_secondary_label ?? 'Create free account';
    $ctaSecondaryUrl = $settings?->cta_secondary_url ?? route('register');
@endphp
<section class="container pb-4">
    <div class="relative overflow-hidden rounded-[2rem] text-white">
        <img src="{{ asset('images/banners/lost-phone.jpg') }}" alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover object-right" />
        <div class="absolute inset-0 bg-gradient-to-r from-primary via-primary/90 to-primary/30"></div>
        <div class="relative px-8 py-16 sm:px-14 lg:py-20">
            <div class="max-w-xl">
                <h2 class="font-display text-4xl leading-tight sm:text-6xl">{{ $ctaTitle }}</h2>
                <p class="mt-4 text-lg text-white/80">{{ $ctaSubtitle }}</p>
                <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ $ctaPrimaryUrl }}" class="btn btn-lg bg-white text-primary shadow-lg hover:bg-white/90">
                        {{ $ctaPrimaryLabel }} <x-site.icon name="arrow-right" class="h-4 w-4" stroke="2.2" />
                    </a>
                    @guest
                        <a href="{{ $ctaSecondaryUrl }}" class="btn btn-lg border border-white/40 text-white hover:bg-white/10">{{ $ctaSecondaryLabel }}</a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
