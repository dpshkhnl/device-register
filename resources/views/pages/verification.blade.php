@extends('layouts.app')

@section('title', 'Check an IMEI')

@section('content')
@php
    $mask = fn ($v) => $v ? substr($v, 0, 4).' •••• •••• '.substr($v, -4) : null;

    $verdict = null;
    if ($result === 'found') {
        $verdict = match ($device->status) {
            'lost' => ['tone' => 'danger', 'icon' => 'alert', 'title' => 'Reported lost or stolen', 'text' => 'Do not buy this device. If someone is offering it to you, contact the police.'],
            'suspicious' => ['tone' => 'warning', 'icon' => 'alert', 'title' => 'Flagged as suspicious', 'text' => 'This device is under review. Be cautious and ask the seller for proof of ownership.'],
            'transferred' => ['tone' => 'neutral', 'icon' => 'transfer', 'title' => 'Ownership recently transferred', 'text' => 'This device changed hands through the registry. Confirm the seller is the current owner.'],
            default => ['tone' => 'success', 'icon' => 'shield-check', 'title' => 'Registered and active', 'text' => 'This device is on record with a registered owner and has not been reported lost or stolen.'],
        };
    }
    $toneClasses = [
        'success' => ['wrap' => 'border-emerald-200 bg-emerald-50/70', 'tile' => 'bg-emerald-100 text-emerald-700'],
        'danger' => ['wrap' => 'border-rose-200 bg-rose-50/80', 'tile' => 'bg-rose-100 text-rose-700'],
        'warning' => ['wrap' => 'border-amber-200 bg-amber-50/80', 'tile' => 'bg-amber-100 text-amber-700'],
        'neutral' => ['wrap' => 'border-border bg-muted/60', 'tile' => 'bg-card text-foreground/70'],
    ];
@endphp

<section class="relative overflow-hidden">
    <div class="bg-site-hero pointer-events-none absolute inset-x-0 top-0 h-[28rem]"></div>
    <div class="container relative max-w-3xl py-14 lg:py-20">
        <div class="text-center">
            <p class="eyebrow">Registry lookup</p>
            <h1 class="page-title mt-3">Check any phone in seconds</h1>
            <p class="page-lead mx-auto">Enter an IMEI or serial number to see if the device is registered — and whether it’s been reported lost or stolen.</p>
        </div>

        <form method="GET" action="{{ route('verification') }}" class="mt-10">
            <label for="imei" class="sr-only">IMEI or serial number</label>
            <div class="surface flex items-center gap-2 p-2 focus-within:border-primary focus-within:shadow-[0_0_0_4px_hsl(var(--ring)/0.14)]">
                <x-site.icon name="phone" class="ml-3 h-5 w-5 shrink-0 text-muted-foreground" />
                <input id="imei" name="imei" type="text" minlength="4" maxlength="50" autocomplete="off" autofocus
                       value="{{ old('imei', $imei ?? '') }}" placeholder="e.g. 356938035643809"
                       class="h-14 min-w-0 flex-1 border-0 bg-transparent px-1 font-mono text-lg tracking-wider text-foreground placeholder:font-sans placeholder:text-base placeholder:tracking-normal placeholder:text-muted-foreground/60 focus:outline-none focus:ring-0" />
                <button type="submit" class="btn btn-primary h-14 rounded-xl px-6">
                    <x-site.icon name="search" class="h-4 w-4" stroke="2.2" />
                    <span class="hidden sm:inline">Check</span>
                </button>
            </div>
            @error('imei')
                <div class="callout callout-danger mt-3">
                    <x-site.icon name="alert" class="mt-px h-5 w-5 shrink-0" />
                    <div class="flex flex-1 flex-wrap items-center justify-between gap-3">
                        <p>{{ $message }}</p>
                        @if (str_contains($message, 'package') || str_contains($message, 'limit'))
                            <a href="{{ route('packages.index') }}" class="btn btn-sm btn-danger">See plans</a>
                        @endif
                    </div>
                </div>
            @enderror
            <div class="mt-3 flex flex-wrap items-center justify-between gap-2 px-1 text-xs text-muted-foreground">
                <p>Tip: dial <span class="font-mono font-semibold text-foreground">*#06#</span> on the phone to see its IMEI.</p>
                @if ($remainingChecks !== null)
                    <p><span class="font-semibold text-foreground">{{ number_format($remainingChecks) }}</span> checks left on your plan</p>
                @endif
            </div>
        </form>

        @if ($verdict)
            @php $tc = $toneClasses[$verdict['tone']]; @endphp
            <div class="mt-10 overflow-hidden rounded-2xl border {{ $tc['wrap'] }}" role="status">
                <div class="flex items-start gap-4 p-6 sm:p-8">
                    <span class="icon-tile h-14 w-14 rounded-2xl {{ $tc['tile'] }}"><x-site.icon :name="$verdict['icon']" class="h-7 w-7" /></span>
                    <div class="flex-1">
                        <h2 class="font-display text-3xl leading-tight text-foreground">{{ $verdict['title'] }}</h2>
                        <p class="mt-1.5 text-sm leading-relaxed text-foreground/75">{{ $verdict['text'] }}</p>
                    </div>
                </div>
                <dl class="grid gap-px border-t border-black/5 bg-black/5 sm:grid-cols-2">
                    @foreach ([
                        'Device' => trim($device->brand.' '.$device->model),
                        'Status' => null,
                        'IMEI' => $mask($device->imei),
                        'Second IMEI' => $mask($device->imei2) ?? 'Not provided',
                        'Registered owner' => $device->ownerName ?? 'Registered user',
                        'Purchase type' => $device->purchaseType === 'secondhand' ? 'Second-hand' : ucfirst((string) $device->purchaseType),
                        'Registered on' => $device->registeredAt?->format('M d, Y') ?? 'N/A',
                        'Checked on' => now()->format('M d, Y'),
                    ] as $label => $value)
                        <div class="bg-card/90 px-6 py-4 sm:px-8">
                            <dt class="text-xs text-muted-foreground">{{ $label }}</dt>
                            <dd class="mt-1 text-sm font-semibold text-foreground {{ in_array($label, ['IMEI', 'Second IMEI']) ? 'font-mono' : '' }}">
                                @if ($label === 'Status') <x-site.status :status="$device->status" /> @else {{ $value }} @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        @elseif ($result === 'not_found')
            <div class="mt-10 rounded-2xl border border-border bg-card p-6 sm:p-8" role="status">
                <div class="flex items-start gap-4">
                    <span class="icon-tile h-14 w-14 rounded-2xl bg-muted text-foreground/70"><x-site.icon name="info" class="h-7 w-7" /></span>
                    <div class="flex-1">
                        <h2 class="font-display text-3xl leading-tight text-foreground">Not in the registry</h2>
                        <p class="mt-1.5 text-sm leading-relaxed text-muted-foreground">
                            We couldn’t find <span class="font-mono font-semibold text-foreground">{{ $imei }}</span>. Double-check the number.
                            An unregistered device isn’t necessarily stolen — but if you’re buying it, ask the seller to register it and transfer it to you.
                        </p>
                        <div class="mt-5 flex flex-wrap gap-2">
                            <a href="{{ route('register-device') }}" class="btn btn-primary btn-sm">
                                <x-site.icon name="plus" class="h-4 w-4" stroke="2.2" /> It’s mine — register it
                            </a>
                            <a href="{{ route('verification') }}" class="btn btn-secondary btn-sm">Check another</a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="mt-12 grid gap-4 sm:grid-cols-3">
                @foreach ([
                    ['icon' => 'shield-check', 'tone' => 'bg-emerald-50 text-emerald-700', 'title' => 'Registered', 'text' => 'Safe — on record with an owner.'],
                    ['icon' => 'alert', 'tone' => 'bg-rose-50 text-rose-700', 'title' => 'Lost / stolen', 'text' => 'Don’t buy — reported by its owner.'],
                    ['icon' => 'info', 'tone' => 'bg-muted text-foreground/70', 'title' => 'Not found', 'text' => 'Ask the seller to register it first.'],
                ] as $hint)
                    <div class="flex gap-3 rounded-2xl border border-border bg-card/70 p-4">
                        <span class="icon-tile h-9 w-9 {{ $hint['tone'] }}"><x-site.icon :name="$hint['icon']" class="h-4 w-4" /></span>
                        <div>
                            <p class="text-sm font-semibold text-foreground">{{ $hint['title'] }}</p>
                            <p class="text-xs text-muted-foreground">{{ $hint['text'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
