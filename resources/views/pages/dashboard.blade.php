@extends('layouts.app')

@section('content')
<section class="py-8 lg:py-12">
    <div class="container">
        <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-bold text-foreground sm:text-3xl">Dashboard</h1>
                <p class="text-muted-foreground">Manage your devices and transfers</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('transfer') }}" class="inline-flex items-center gap-2 rounded-xl border border-border px-4 py-2.5 text-sm font-semibold text-foreground hover:bg-muted">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10m0 0-3-3m3 3-3 3M17 17H7m0 0 3 3m-3-3 3-3" />
                    </svg>
                    Transfer
                </a>
                <a href="{{ route('lost-found.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-border px-4 py-2.5 text-sm font-semibold text-foreground hover:bg-muted">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                    </svg>
                    Lost / Found
                </a>
                <a href="{{ route('register-device') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                    </svg>
                    Register Device
                </a>
            </div>
        </div>

        <div class="mb-8 grid gap-4 sm:grid-cols-4">
            @foreach ([
                ['label' => 'Registered Devices', 'value' => $stats['registered']],
                ['label' => 'Pending Transfers', 'value' => $stats['pending_transfers']],
                ['label' => 'Active Devices', 'value' => $stats['active']],
                ['label' => 'Lost / Stolen', 'value' => $stats['lost']],
            ] as $stat)
                <div class="flex items-center gap-4 rounded-xl border border-border bg-card p-5 shadow-soft">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
                        <svg class="h-6 w-6 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="6" y="3" width="12" height="18" rx="2" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 17h4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-foreground">{{ $stat['value'] }}</p>
                        <p class="text-sm text-muted-foreground">{{ $stat['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="space-y-6">
            <div>
                <h2 class="text-lg font-bold text-foreground">Your Devices</h2>
                <p class="text-sm text-muted-foreground">Latest registrations with live status.</p>
            </div>

            <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-soft">
                <table class="w-full text-left text-sm">
                    <thead class="bg-muted text-xs uppercase tracking-wider text-muted-foreground">
                        <tr>
                            <th class="px-4 py-3">Device</th>
                            <th class="px-4 py-3">IMEI</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse ($devices as $device)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-foreground">
                                    {{ $device->brand }} {{ $device->model }}
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-muted-foreground">
                                    {{ substr($device->imei, 0, 4) }}***{{ substr($device->imei, -4) }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $device->status === 'lost' ? 'bg-rose-100 text-rose-700' : 'bg-muted text-muted-foreground' }}">
                                        {{ ucfirst($device->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('devices.show', $device) }}" class="text-sm font-medium text-primary hover:underline">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-muted-foreground">No devices registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <div class="rounded-xl border border-border bg-card p-5 shadow-soft">
                    <h3 class="text-lg font-bold text-foreground">Pending Transfers</h3>
                    <p class="text-sm text-muted-foreground">Accept incoming ownership requests.</p>
                    <div class="mt-4 space-y-3">
                        @forelse ($pendingTransfers as $transfer)
                            <div class="rounded-lg border border-border px-4 py-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-medium text-foreground">{{ $transfer->device?->brand }} {{ $transfer->device?->model }}</p>
                                        <p class="text-xs text-muted-foreground">From {{ $transfer->fromUser?->name ?? 'Owner' }}</p>
                                        <p class="text-xs text-muted-foreground">IMEI {{ $transfer->device?->imei }}</p>
                                    </div>
                                    <span class="text-xs font-semibold text-amber-600">Pending</span>
                                </div>
                                <form method="POST" action="{{ route('transfer.accept', $transfer) }}" class="mt-3 flex justify-end" data-swal-confirm data-swal-title="Accept transfer request?" data-swal-text="This will transfer the device to your account." data-swal-confirm="Yes, accept">
                                    @csrf
                                    <button class="rounded-lg bg-primary px-3 py-2 text-xs font-semibold text-primary-foreground">Accept Transfer</button>
                                </form>
                            </div>
                        @empty
                            <div class="rounded-lg border border-dashed border-border px-4 py-6 text-center text-sm text-muted-foreground">
                                No pending transfers right now.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-xl border border-border bg-card p-5 shadow-soft">
                    <h3 class="text-lg font-bold text-foreground">Outgoing Pending Transfers</h3>
                    <p class="text-sm text-muted-foreground">Requests you have sent and can cancel.</p>
                    <div class="mt-4 space-y-3">
                        @forelse ($outgoingPendingTransfers as $transfer)
                            <div class="rounded-lg border border-border px-4 py-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-medium text-foreground">
                                            {{ $transfer->device?->brand }} {{ $transfer->device?->model }}
                                        </p>
                                        <p class="text-xs text-muted-foreground">
                                            To {{ $transfer->toUser?->name ?? $transfer->to_mobile }}
                                        </p>
                                    </div>
                                    <span class="text-xs font-semibold text-amber-600">Pending</span>
                                </div>
                                <form method="POST" action="{{ route('transfer.cancel', $transfer) }}" class="mt-3 flex justify-end" data-swal-confirm data-swal-title="Cancel transfer request?" data-swal-text="The request will be removed from pending transfers." data-swal-confirm="Yes, cancel">
                                    @csrf
                                    <button class="rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-700">Cancel Transfer</button>
                                </form>
                            </div>
                        @empty
                            <div class="rounded-lg border border-dashed border-border px-4 py-6 text-center text-sm text-muted-foreground">
                                No outgoing pending transfers.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
