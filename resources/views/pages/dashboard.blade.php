@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $user = auth()->user();
    $firstName = \Illuminate\Support\Str::of($user->name ?? '')->explode(' ')->first() ?: 'there';
    $hasKyc = $user->hasKyc();
    $outgoingDeviceIds = $outgoingPendingTransfers->pluck('device_id')->all();

    $checklist = [
        ['done' => (bool) $activePackage, 'title' => 'Choose a plan', 'text' => 'A plan sets how many devices you can register and IMEIs you can check.', 'url' => route('packages.index'), 'cta' => 'See plans'],
        ['done' => $hasKyc, 'title' => 'Verify your identity (KYC)', 'text' => 'Needed before you can transfer a device or report it lost.', 'url' => route('profile.edit').'#kyc', 'cta' => 'Complete KYC'],
        ['done' => $devices->isNotEmpty(), 'title' => 'Register your first device', 'text' => 'Takes about a minute. Have the IMEI ready (dial *#06#).', 'url' => route('register-device'), 'cta' => 'Register device'],
    ];
    $doneCount = collect($checklist)->where('done', true)->count();

    $meter = fn ($used, $limit) => $limit > 0 ? min(100, round($used / $limit * 100)) : 0;
@endphp

<div class="container py-10 lg:py-12">
    {{-- Greeting --}}
    <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="eyebrow">Dashboard</p>
            <h1 class="page-title mt-2">Welcome back, {{ $firstName }}</h1>
            <p class="page-lead">Everything about your devices in one place.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('verification') }}" class="btn btn-secondary">
                <x-site.icon name="search" class="h-4 w-4" /> Check IMEI
            </a>
            <a href="{{ route('register-device') }}" class="btn btn-primary">
                <x-site.icon name="plus" class="h-4 w-4" stroke="2.2" /> Register device
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="callout callout-danger mt-6" role="alert">
            <x-site.icon name="alert" class="mt-px h-5 w-5 shrink-0" />
            <p>{{ $errors->first() }}</p>
        </div>
    @endif

    {{-- Setup checklist --}}
    @if ($doneCount < count($checklist))
        <section class="surface mt-8 overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-border px-6 py-4">
                <div class="flex items-center gap-3">
                    <span class="icon-tile h-9 w-9 bg-accent text-accent-foreground"><x-site.icon name="sparkle" class="h-4 w-4" /></span>
                    <div>
                        <h2 class="text-sm font-bold text-foreground">Finish setting up</h2>
                        <p class="text-xs text-muted-foreground">{{ $doneCount }} of {{ count($checklist) }} done</p>
                    </div>
                </div>
                <div class="meter w-40"><span style="width: {{ round($doneCount / count($checklist) * 100) }}%"></span></div>
            </div>
            <ol class="grid divide-y divide-border md:grid-cols-3 md:divide-x md:divide-y-0">
                @foreach ($checklist as $i => $item)
                    <li class="flex gap-4 p-6 {{ $item['done'] ? 'opacity-70' : '' }}">
                        <span class="step-dot" data-state="{{ $item['done'] ? 'done' : 'idle' }}">
                            @if ($item['done']) <x-site.icon name="check" class="h-4 w-4" stroke="2.4" /> @else {{ $i + 1 }} @endif
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-foreground {{ $item['done'] ? 'line-through decoration-muted-foreground/40' : '' }}">{{ $item['title'] }}</p>
                            <p class="mt-1 text-xs leading-relaxed text-muted-foreground">{{ $item['text'] }}</p>
                            @unless ($item['done'])
                                <a href="{{ $item['url'] }}" class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline">
                                    {{ $item['cta'] }} <x-site.icon name="arrow-right" class="h-3.5 w-3.5" />
                                </a>
                            @endunless
                        </div>
                    </li>
                @endforeach
            </ol>
        </section>
    @endif

    {{-- Incoming transfers need a decision, so they go first --}}
    @if ($pendingTransfers->isNotEmpty())
        <section class="mt-8 space-y-3">
            @foreach ($pendingTransfers as $transfer)
                <div class="surface flex flex-col gap-4 border-amber-200 bg-amber-50/60 p-5 sm:flex-row sm:items-center">
                    <span class="icon-tile bg-amber-100 text-amber-700"><x-site.icon name="inbox" /></span>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-foreground">
                            {{ $transfer->fromUser?->name ?? 'Someone' }} wants to transfer a device to you
                        </p>
                        <p class="mt-0.5 text-sm text-muted-foreground">
                            {{ $transfer->device?->brand }} {{ $transfer->device?->model }}
                            · <span class="font-mono text-xs">IMEI {{ $transfer->device?->imei }}</span>
                        </p>
                    </div>
                    <form method="POST" action="{{ route('transfer.accept', $transfer) }}"
                          data-swal-confirm data-swal-title="Accept this device?" data-swal-text="It will be moved to your account and listed under your name." data-swal-confirm="Yes, accept">
                        @csrf
                        <button class="btn btn-success w-full sm:w-auto">
                            <x-site.icon name="check" class="h-4 w-4" stroke="2.2" /> Accept transfer
                        </button>
                    </form>
                </div>
            @endforeach
        </section>
    @endif

    {{-- Stats --}}
    <dl class="mt-8 grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach ([
            ['label' => 'Registered devices', 'value' => $stats['registered'], 'icon' => 'phone', 'tone' => 'bg-accent text-accent-foreground'],
            ['label' => 'Active', 'value' => $stats['active'], 'icon' => 'shield-check', 'tone' => 'bg-emerald-50 text-emerald-700'],
            ['label' => 'Pending transfers', 'value' => $stats['pending_transfers'], 'icon' => 'transfer', 'tone' => 'bg-amber-50 text-amber-700'],
            ['label' => 'Lost / stolen', 'value' => $stats['lost'], 'icon' => 'alert', 'tone' => 'bg-rose-50 text-rose-700'],
        ] as $stat)
            <div class="surface flex items-center gap-4 p-5">
                <span class="icon-tile {{ $stat['tone'] }}"><x-site.icon :name="$stat['icon']" /></span>
                <div>
                    <dd class="text-2xl font-bold leading-none text-foreground">{{ $stat['value'] }}</dd>
                    <dt class="mt-1.5 text-xs font-medium text-muted-foreground">{{ $stat['label'] }}</dt>
                </div>
            </div>
        @endforeach
    </dl>

    <div class="mt-8 grid gap-6 lg:grid-cols-[1fr,22rem]">
        {{-- Devices --}}
        <section id="devices" class="surface self-start overflow-hidden">
            <div class="flex items-center justify-between gap-3 border-b border-border px-6 py-4">
                <div>
                    <h2 class="text-base font-bold text-foreground">My devices</h2>
                    <p class="text-xs text-muted-foreground">Tap a device for details and actions.</p>
                </div>
                <a href="{{ route('register-device') }}" class="btn btn-secondary btn-sm">
                    <x-site.icon name="plus" class="h-3.5 w-3.5" stroke="2.2" /> Add
                </a>
            </div>

            @forelse ($devices as $device)
                @php
                    $isLost = $device->status === 'lost';
                    $isOutgoing = in_array($device->id, $outgoingDeviceIds, true);
                @endphp
                <div class="group flex flex-wrap items-center gap-4 border-b border-border px-6 py-4 last:border-b-0 hover:bg-muted/40">
                    <a href="{{ route('devices.show', $device) }}" class="flex min-w-0 flex-1 items-center gap-4">
                        <span class="icon-tile {{ $isLost ? 'bg-rose-50 text-rose-600' : 'bg-muted text-foreground/70' }}">
                            <x-site.icon name="phone" />
                        </span>
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-semibold text-foreground">{{ $device->brand }} {{ $device->model }} <span class="font-normal text-muted-foreground">{{ $device->storage }}</span></span>
                            <span class="mt-0.5 block font-mono text-xs text-muted-foreground">{{ $device->identifierLabel() }} {{ substr($device->imei, 0, 4) }}•••{{ substr($device->imei, -4) }}</span>
                        </span>
                    </a>
                    <div class="flex items-center gap-2">
                        @if ($isOutgoing)
                            <x-site.status status="pending" />
                        @else
                            <x-site.status :status="$device->status" />
                        @endif
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button type="button" @click="open = !open" class="btn btn-ghost btn-sm px-2" aria-label="Device actions">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><circle cx="5" cy="12" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="19" cy="12" r="1.6"/></svg>
                            </button>
                            <div x-show="open" x-cloak x-transition.opacity
                                 class="absolute right-0 z-20 mt-1 w-52 rounded-xl border border-border bg-card p-1 shadow-[0_16px_32px_-12px_rgba(20,22,40,0.25)]">
                                <a href="{{ route('devices.show', $device) }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm hover:bg-muted"><x-site.icon name="eye" class="h-4 w-4 text-muted-foreground" /> View details</a>
                                @if (! $isLost && ! $isOutgoing)
                                    <a href="{{ route('transfer', ['device' => $device->imei]) }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm hover:bg-muted"><x-site.icon name="transfer" class="h-4 w-4 text-muted-foreground" /> Transfer ownership</a>
                                @endif
                                @if ($isLost)
                                    <a href="{{ route('lost-found.index', ['device' => $device->id]) }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-emerald-700 hover:bg-emerald-50"><x-site.icon name="check-circle" class="h-4 w-4" /> Mark as found</a>
                                @else
                                    <a href="{{ route('lost-found.index', ['device' => $device->id]) }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-rose-600 hover:bg-rose-50"><x-site.icon name="alert" class="h-4 w-4" /> Report lost / stolen</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-14 text-center">
                    <span class="icon-tile mx-auto h-14 w-14 rounded-2xl bg-accent text-accent-foreground"><x-site.icon name="phone" class="h-6 w-6" /></span>
                    <h3 class="mt-4 text-base font-bold text-foreground">No devices yet</h3>
                    <p class="mx-auto mt-1 max-w-xs text-sm text-muted-foreground">Register your phone so it’s on record as yours if it’s ever lost or sold.</p>
                    <a href="{{ route('register-device') }}" class="btn btn-primary mt-5">
                        <x-site.icon name="plus" class="h-4 w-4" stroke="2.2" /> Register your first device
                    </a>
                </div>
            @endforelse
        </section>

        {{-- Side column --}}
        <aside class="space-y-6">
            {{-- Plan --}}
            <section class="surface p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold text-foreground">Your plan</h2>
                    <a href="{{ route('packages.index') }}" class="text-xs font-semibold text-primary hover:underline">{{ $activePackage ? 'Change' : 'Choose' }}</a>
                </div>
                @if ($activePackage)
                    <p class="font-display mt-3 text-3xl text-foreground">{{ $activePackage->package->name }}</p>
                    @if ($activePackage->ends_at)
                        <p class="text-xs text-muted-foreground">Valid until {{ $activePackage->ends_at->format('M d, Y') }}</p>
                    @endif
                    <div class="mt-5 space-y-4">
                        @foreach ([
                            ['label' => 'Devices', 'used' => $activePackage->used_device_count, 'limit' => $activePackage->device_limit],
                            ['label' => 'IMEI checks', 'used' => $activePackage->used_imei_count, 'limit' => $activePackage->imei_limit],
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
                @else
                    <p class="mt-2 text-sm text-muted-foreground">You don’t have an active plan, so you can’t register devices yet.</p>
                    <a href="{{ route('packages.index') }}" class="btn btn-primary btn-sm mt-4 w-full">See plans</a>
                @endif
            </section>

            {{-- Outgoing --}}
            <section class="surface p-6">
                <h2 class="text-sm font-bold text-foreground">Transfers you started</h2>
                <p class="text-xs text-muted-foreground">Waiting for the new owner to join.</p>
                <div class="mt-4 space-y-3">
                    @forelse ($outgoingPendingTransfers as $transfer)
                        <div class="rounded-xl border border-border p-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-foreground">{{ $transfer->device?->brand }} {{ $transfer->device?->model }}</p>
                                    <p class="text-xs text-muted-foreground">To {{ $transfer->toUser?->name ?? $transfer->to_mobile }}</p>
                                </div>
                                <x-site.status status="pending" />
                            </div>
                            <form method="POST" action="{{ route('transfer.cancel', $transfer) }}" class="mt-2"
                                  data-swal-confirm data-swal-danger="true" data-swal-title="Cancel this transfer?" data-swal-text="The device stays with you and the request is removed." data-swal-confirm="Yes, cancel it" data-swal-cancel="Keep it">
                                @csrf
                                <button class="text-xs font-semibold text-rose-600 hover:underline">Cancel transfer</button>
                            </form>
                        </div>
                    @empty
                        <p class="rounded-xl border border-dashed border-border px-4 py-5 text-center text-xs text-muted-foreground">Nothing pending.</p>
                    @endforelse
                </div>
            </section>

            {{-- Recent activity --}}
            @if ($recentTransfers->isNotEmpty())
                <section class="surface p-6">
                    <h2 class="text-sm font-bold text-foreground">Recent transfer activity</h2>
                    <ul class="mt-4 space-y-4">
                        @foreach ($recentTransfers as $transfer)
                            @php $outgoing = $transfer->from_user_id === $user->id; @endphp
                            <li class="flex gap-3">
                                <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full {{ $outgoing ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">
                                    <x-site.icon :name="$outgoing ? 'arrow-right' : 'arrow-left'" class="h-3.5 w-3.5" stroke="2.2" />
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm text-foreground">
                                        <span class="font-semibold">{{ $transfer->device?->brand }} {{ $transfer->device?->model }}</span>
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ $outgoing ? 'Sent to '.($transfer->to_mobile ?? 'new owner') : 'From '.($transfer->fromUser?->name ?? 'previous owner') }}
                                        · {{ $transfer->created_at?->diffForHumans() }}
                                    </p>
                                </div>
                                <span class="pill pill-plain self-start {{ match ($transfer->status) { 'accepted', 'completed' => 'pill-success', 'pending' => 'pill-warning', default => 'pill-neutral' } }}">{{ ucfirst($transfer->status) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </aside>
    </div>
</div>
@endsection
