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
        <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
            <x-ui.card class="p-3 text-center">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Total Devices</p>
                <p class="mt-1 text-xl font-semibold text-slate-900">{{ $devices->total() }}</p>
                <x-ui.badge class="mt-1 inline-flex justify-center" status="active" label="Registry count" />
            </x-ui.card>
            <x-ui.card class="p-3 text-center">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Active</p>
                <p class="mt-1 text-xl font-semibold text-slate-900">{{ $activeCount }}</p>
                <x-ui.badge class="mt-1 inline-flex justify-center" status="active" label="Live" />
            </x-ui.card>
            <x-ui.card class="p-3 text-center">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Transferred</p>
                <p class="mt-1 text-xl font-semibold text-slate-900">{{ $transferredCount }}</p>
                <x-ui.badge class="mt-1 inline-flex justify-center" status="transferred" label="Ownership change" />
            </x-ui.card>
            <x-ui.card class="p-3 text-center">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Lost / Suspicious</p>
                <p class="mt-1 text-xl font-semibold text-slate-900">{{ $flaggedCount }}</p>
                <x-ui.badge class="mt-1 inline-flex justify-center" status="lost" label="Needs review" />
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
                    <a class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 hover:border-slate-300 hover:text-slate-800" href="{{ route('admin.devices.import') }}">Bulk Upload</a>
                    <a class="rounded-lg bg-[color:var(--brand-600)] px-3 py-2 text-xs font-semibold text-white" href="{{ route('admin.devices.create') }}">Add Device</a>
                </div>
            </div>
            <div class="border-b border-slate-200 bg-slate-50/60 px-6 py-4">
                <form method="GET" action="{{ route('admin.devices.index') }}" class="grid gap-3 md:grid-cols-12">
                    <div class="md:col-span-7">
                        <label class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Search</label>
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Search IMEI, brand, model, owner"
                            class="mt-2 h-10 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm"
                        />
                    </div>
                    <div class="md:col-span-3">
                        <label class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Status</label>
                        <select name="status" class="mt-2 h-10 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm">
                            <option value="all" @selected(request('status', 'all') === 'all')>All</option>
                            <option value="active" @selected(request('status') === 'active')>Active</option>
                            <option value="transferred" @selected(request('status') === 'transferred')>Transferred</option>
                            <option value="lost" @selected(request('status') === 'lost')>Lost / Missing</option>
                            <option value="suspicious" @selected(request('status') === 'suspicious')>Suspicious</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2 md:col-span-2">
                        <button class="h-10 w-full rounded-lg bg-[color:var(--brand-600)] px-3 text-xs font-semibold text-white">Filter</button>
                        <a class="h-10 w-full rounded-lg border border-slate-200 px-3 text-xs font-semibold text-slate-600 flex items-center justify-center" href="{{ route('admin.devices.index') }}">Reset</a>
                    </div>
                </form>
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
                                <div class="flex items-center justify-end gap-3">
                                    <form method="POST" action="{{ route('admin.devices.destroy', $device) }}" class="inline" data-delete-form>
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="text-xs font-semibold text-rose-600 hover:text-rose-700" data-delete-button>Delete</button>
                                    </form>
                                    <a class="text-xs font-semibold text-slate-500 hover:text-slate-700" href="{{ route('admin.devices.edit', $device) }}">Edit</a>
                                    <a class="text-xs font-semibold text-[color:var(--brand-600)]" href="{{ route('admin.devices.show', $device) }}">Review</a>
                                </div>
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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Swal === 'undefined') {
                return;
            }
            document.querySelectorAll('[data-delete-button]').forEach((button) => {
                button.addEventListener('click', () => {
                    const form = button.closest('[data-delete-form]');
                    if (!form) {
                        return;
                    }
                    Swal.fire({
                        title: 'Delete this device?',
                        text: 'This action cannot be undone.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#dc2626',
                        focusCancel: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
