@extends('admin.layouts.app')

@section('page_heading', 'Ownership Transfers')

@section('content')
    @php
        $transferItems = $transfers->getCollection();
        $pendingCount = $transferItems->where('status', 'pending')->count();
        $acceptedCount = $transferItems->where('status', 'accepted')->count();
        $rejectedCount = $transferItems->whereIn('status', ['rejected', 'cancelled'])->count();
    @endphp

    <div class="w-full space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Pending Requests</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $pendingCount }}</p>
                <x-ui.badge class="mt-3" status="suspicious" label="Awaiting OTP" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Accepted</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $acceptedCount }}</p>
                <x-ui.badge class="mt-3" status="active" label="Completed" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Rejected / Disputed</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $rejectedCount }}</p>
                <x-ui.badge class="mt-3" status="lost" label="Needs review" />
            </x-ui.card>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Transfer queue</h2>
                    <p class="text-sm text-slate-500">Ownership change requests requiring review.</p>
                </div>
                <button class="rounded-lg bg-[color:var(--brand-600)] px-3 py-2 text-xs font-semibold text-white">Create Transfer</button>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Device</th>
                        <th class="px-4 py-3">IMEI</th>
                        <th class="px-4 py-3">From</th>
                        <th class="px-4 py-3">To</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transfers as $transfer)
                        <tr>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900">
                                {{ $transfer->device?->brand }} {{ $transfer->device?->model }}
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $transfer->device?->imei }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $transfer->fromUser?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $transfer->toUser?->name ?? $transfer->to_mobile }}</td>
                            <td class="px-4 py-3">
                                <x-ui.badge status="{{ $transfer->status === 'accepted' ? 'active' : (in_array($transfer->status, ['rejected', 'cancelled'], true) ? 'lost' : 'suspicious') }}" label="{{ ucfirst($transfer->status) }}" />
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button class="text-xs font-semibold text-[color:var(--brand-600)]">Review</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">No transfer requests yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $transfers->links() }}</div>
    </div>
@endsection
