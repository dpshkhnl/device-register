@extends('admin.layouts.app')

@section('page_heading', 'Devices')

@section('content')
    @php
        $deviceItems = $devices->getCollection();
        $activeCount = $deviceItems->where('status', 'active')->count();
        $transferredCount = $deviceItems->where('status', 'transferred')->count();
        $flaggedCount = $deviceItems->whereIn('status', ['lost', 'suspicious'])->count();
    @endphp

    <div class="w-full space-y-6">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Device registry</h2>
                    <p class="text-sm text-slate-500">Latest devices and status updates.</p>
                </div>
                <div class="flex gap-2">
                    <a class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600" href="{{ route('admin.devices.export', request()->only('q', 'status')) }}">Export</a>
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
                        <th class="px-6 py-4">SN</th>
                        <th class="px-6 py-4">Device</th>
                        <th class="px-6 py-4">IMEI</th>
                        <th class="px-6 py-4">Owner</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($devices as $device)
                        <tr class="group hover:bg-slate-50/80">
                            <td class="px-6 py-4 text-xs text-slate-500">{{ $devices->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-500">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                            <rect x="7" y="2" width="10" height="20" rx="2"></rect>
                                            <path d="M11 18h2" stroke-linecap="round" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $device->brand }} {{ $device->model }}</p>
                                        <p class="text-xs text-slate-500">{{ $device->device_type ?? 'Device' }} · {{ $device->purchase_type ?? 'Unknown' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-mono text-xs text-slate-600">{{ $device->imei }}</p>
                                @if ($device->imei2)
                                    <p class="mt-1 font-mono text-[11px] text-slate-400">IMEI2 {{ $device->imei2 }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-slate-900">{{ $device->currentOwner?->name ?? '—' }}</p>
                                <p class="text-xs text-slate-500">{{ $device->currentOwner?->email ?? 'No email' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-2">
                                    <x-ui.badge status="{{ $device->status }}" />
                                    <button
                                        type="button"
                                        class="inline-flex w-fit items-center rounded-full border border-slate-200 px-3 py-1 text-[10px] font-semibold text-slate-600 hover:border-slate-300 hover:text-slate-800"
                                        x-data
                                        x-on:click="$dispatch('open-modal', 'update-device-status-{{ $device->id }}')"
                                    >
                                        Update status
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-3 py-1 text-xs text-slate-500">
                                    <a class="font-semibold text-slate-600 hover:text-slate-800" href="{{ route('admin.devices.edit', $device) }}">Edit</a>
                                    <span class="text-slate-300">|</span>
                                    <a class="font-semibold text-[color:var(--brand-600)]" href="{{ route('admin.devices.show', $device) }}">Review</a>
                                    <span class="text-slate-300">|</span>
                                    <form method="POST" action="{{ route('admin.devices.destroy', $device) }}" class="inline" data-delete-form>
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="font-semibold text-rose-600 hover:text-rose-700" data-delete-button>Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @foreach ($devices as $device)
            <x-modal name="update-device-status-{{ $device->id }}" maxWidth="lg" focusable>
                <form method="POST" action="{{ route('admin.devices.update', $device) }}" class="p-6" x-data="{ status: '{{ $device->status }}' }">
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
                            <select name="status" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" x-model="status">
                                <option value="active" @selected($device->status === 'active')>Active</option>
                                <option value="transferred" @selected($device->status === 'transferred')>Transferred</option>
                                <option value="lost" @selected($device->status === 'lost')>Lost / Missing</option>
                                <option value="suspicious" @selected($device->status === 'suspicious')>Suspicious</option>
                            </select>
                        </div>

                        <div x-show="status === 'transferred'" x-cloak>
                            <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Assign New Owner</label>
                            <select name="new_owner_id" class="js-new-owner-select mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                                <option value="" disabled @selected(old('new_owner_id') === null)>Select a user</option>
                                @foreach ($users as $user)
                                    @continue($user->id === $device->current_owner_id)
                                    <option value="{{ $user->id }}" @selected(old('new_owner_id') == $user->id)>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('new_owner_id')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-slate-500">Required when status is transferred.</p>
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

@push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/select2/select2.min.css') }}">
    <style>
        .select2-container .select2-selection--single {
            height: 2.5rem;
            border-radius: 0.5rem;
            border-color: #e2e8f0;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 2.5rem;
            padding-left: 0.75rem;
            padding-right: 2rem;
            font-size: 0.875rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 2.5rem;
            right: 0.5rem;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof window.jQuery !== 'undefined') {
                window.jQuery('.js-new-owner-select').each(function () {
                    const select = window.jQuery(this);
                    const modalRoot = select.closest('.fixed.inset-0');
                    select.select2({
                        width: '100%',
                        placeholder: 'Select a user',
                        dropdownParent: modalRoot.length ? modalRoot : undefined,
                    });
                });
            }

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
