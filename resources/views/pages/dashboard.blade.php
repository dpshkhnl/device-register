@extends('layouts.app')

@section('content')
<section class="py-8 lg:py-12">
    <div class="container">
        <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-bold text-foreground sm:text-3xl">Dashboard</h1>
                <p class="text-muted-foreground">Manage your devices and transfers</p>
            </div>
            <a href="{{ route('register-device') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                </svg>
                Register Device
            </a>
        </div>

        <div class="mb-8 grid gap-4 sm:grid-cols-3">
            @foreach ([
                ['label' => 'Registered Devices', 'value' => $stats['registered']],
                ['label' => 'Pending Transfers', 'value' => $stats['pending_transfers']],
                ['label' => 'Active Devices', 'value' => $stats['active']],
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
                <p class="text-sm text-muted-foreground">Recent registrations and status updates.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($devices as $device)
                    <div class="rounded-xl border border-border bg-card p-5 shadow-soft">
                        <div class="mb-4 flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-muted">
                                    <svg class="h-5 w-5 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <rect x="6" y="3" width="12" height="18" rx="2" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 17h4" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-foreground">{{ $device->brand }}</h3>
                                    <p class="text-sm text-muted-foreground">{{ $device->model }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4 space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-muted-foreground">IMEI</span>
                                <span class="font-mono text-foreground">{{ substr($device->imei, 0, 4) }}***{{ substr($device->imei, -4) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-muted-foreground">Status</span>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-muted px-3 py-1 text-xs font-semibold text-foreground">{{ ucfirst($device->status) }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between border-t border-border pt-4">
                            <span class="text-xs font-semibold text-muted-foreground">{{ $device->purchase_date?->format('M d, Y') ?? 'Registered' }}</span>
                            <a href="{{ route('devices.show', $device) }}" class="text-sm font-medium text-primary hover:underline">Details</a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full rounded-xl border border-dashed border-border bg-card p-10 text-center">
                        <p class="text-sm text-muted-foreground">No devices registered yet.</p>
                        <a href="{{ route('register-device') }}" class="mt-4 inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground">
                            Register your first device
                        </a>
                    </div>
                @endforelse
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
