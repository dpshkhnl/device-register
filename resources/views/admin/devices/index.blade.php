@extends('admin.layouts.app')

@section('page_heading', 'Devices')

@section('content')
    @php
        $deviceItems = $devices->getCollection();
        $activeCount = $deviceItems->where('status', 'active')->count();
        $transferredCount = $deviceItems->where('status', 'transferred')->count();
        $flaggedCount = $deviceItems->whereIn('status', ['lost', 'suspicious'])->count();
    @endphp

    <div class="mx-auto max-w-6xl space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Devices</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $devices->total() }}</p>
                <x-ui.badge class="mt-3" status="active" label="Registry count" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Active</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $activeCount }}</p>
                <x-ui.badge class="mt-3" status="active" label="Live" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Transferred</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $transferredCount }}</p>
                <x-ui.badge class="mt-3" status="transferred" label="Ownership change" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Lost / Suspicious</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $flaggedCount }}</p>
                <x-ui.badge class="mt-3" status="lost" label="Needs review" />
            </x-ui.card>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Device registry</h2>
                    <p class="text-sm text-slate-500">Latest devices and status updates.</p>
                </div>
                <div class="flex gap-2">
                    <button class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600">Export</button>
                    <a class="rounded-lg bg-[color:var(--brand-600)] px-3 py-2 text-xs font-semibold text-white" href="{{ route('devices.create') }}">Add Device</a>
                </div>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">IMEI</th>
                        <th class="px-4 py-3">Device</th>
                        <th class="px-4 py-3">Owner</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Update</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($devices as $device)
                        <tr>
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $device->imei }}</td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $device->brand }} {{ $device->model }}</p>
                                <p class="text-xs text-slate-500">{{ $device->device_type ?? 'Device' }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $device->currentOwner?->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <x-ui.badge status="{{ $device->status }}" />
                            </td>
                            <td class="px-4 py-3">
                                <button
                                    type="button"
                                    class="rounded-lg border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-600 hover:border-slate-300 hover:text-slate-800"
                                    x-data
                                    x-on:click="$dispatch('open-modal', 'update-device-status-{{ $device->id }}')"
                                >
                                    Update Status
                                </button>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a class="text-xs font-semibold text-[color:var(--brand-600)]" href="{{ route('admin.devices.show', $device) }}">Review</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @foreach ($devices as $device)
            <x-modal name="update-device-status-{{ $device->id }}" maxWidth="lg" focusable>
                <form method="POST" action="{{ route('admin.devices.update', $device) }}" class="p-6">
                    @csrf
                    @method('PUT')
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Update device status</h3>
                            <p class="text-sm text-slate-500">IMEI {{ $device->imei }} - {{ $device->brand }} {{ $device->model }}</p>
                        </div>
                        <button type="button" class="text-slate-400 hover:text-slate-600" x-on:click="$dispatch('close-modal', 'update-device-status-{{ $device->id }}')">
                            x
                        </button>
                    </div>

                    <div class="mt-4 space-y-4">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Status</label>
                            <select name="status" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                                <option value="active" @selected($device->status === 'active')>Active</option>
                                <option value="transferred" @selected($device->status === 'transferred')>Transferred</option>
                                <option value="lost" @selected($device->status === 'lost')>Lost / Missing</option>
                                <option value="suspicious" @selected($device->status === 'suspicious')>Suspicious</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Reason (optional)</label>
                            <textarea
                                name="reason"
                                rows="3"
                                class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                                placeholder="Add a short note for this status update."
                            ></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600" x-on:click="$dispatch('close-modal', 'update-device-status-{{ $device->id }}')">Cancel</button>
                        <button class="rounded-lg bg-[color:var(--brand-600)] px-4 py-2 text-xs font-semibold text-white">Save Status</button>
                    </div>
                </form>
            </x-modal>
        @endforeach

        <div>{{ $devices->links() }}</div>
    </div>
@endsection
