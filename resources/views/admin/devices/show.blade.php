@extends('admin.layouts.app')

@section('page_heading', 'Device Review')

@section('content')
    <div class="w-full space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <a href="{{ route('admin.devices.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">Back to Devices</a>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900">{{ $device->brand }} {{ $device->model }}</h1>
                <p class="text-sm text-slate-500">IMEI <span class="font-mono text-xs">{{ $device->imei }}</span></p>
                @if ($device->imei2)
                    <p class="text-sm text-slate-500">IMEI 2 <span class="font-mono text-xs">{{ $device->imei2 }}</span></p>
                @endif
            </div>
            <x-ui.badge status="{{ $device->status }}" />
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Owner</p>
                <div class="mt-3 space-y-2 text-sm">
                    <p class="font-semibold text-slate-900">{{ $device->currentOwner?->name ?? 'Unassigned' }}</p>
                    <p class="text-slate-500">{{ $device->currentOwner?->email ?? 'No email on file' }}</p>
                    <p class="text-slate-500">{{ $device->currentOwner?->mobile ?? 'No mobile on file' }}</p>
                </div>
            </x-ui.card>

            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Registration</p>
                <div class="mt-3 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Registered</span>
                        <span class="font-semibold text-slate-900">{{ $device->registered_at?->format('M d, Y') ?? 'Not recorded' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Seller</span>
                        <span class="font-semibold text-slate-900">{{ $device->seller_name ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Invoice</span>
                        <span class="font-semibold text-slate-900">{{ $device->invoice_path ? 'Uploaded' : 'Not provided' }}</span>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <x-ui.card>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Device Profile</p>
                <p class="mt-1 text-sm text-slate-500">Purchase and ownership context.</p>
            </div>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4 text-sm">
                <div class="rounded-xl border border-slate-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Type</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ $device->device_type ?? 'Device' }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Purchase Type</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ $device->purchase_type ?? '-' }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Purchase Date</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ $device->purchase_date?->format('M d, Y') ?? '-' }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Current Status</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ ucfirst($device->status) }}</p>
                </div>
            </div>
        </x-ui.card>

        <div class="grid gap-4 md:grid-cols-3">
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Transfer Requests</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $device->transfer_requests_count }}</p>
                <p class="text-xs text-slate-500">Ownership change attempts.</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Lost Reports</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $device->lost_reports_count }}</p>
                <p class="text-xs text-slate-500">Reported incidents.</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Certificates</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $device->certificates_count }}</p>
                <p class="text-xs text-slate-500">Generated certificates.</p>
            </x-ui.card>
        </div>

        <x-ui.card>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">History Log</p>
                    <p class="mt-1 text-sm text-slate-500">Transfers and lost/found activity.</p>
                </div>
            </div>
            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Transfer History</p>
                    <div class="mt-3 space-y-3 text-sm">
                        @forelse ($device->transferRequests as $transfer)
                            <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-slate-900">{{ ucfirst($transfer->status) }}</p>
                                    <p class="text-xs text-slate-500">{{ $transfer->created_at?->format('M d, Y') }}</p>
                                </div>
                                <p class="mt-2 text-xs text-slate-500">From {{ $transfer->fromUser?->name ?? 'N/A' }} to {{ $transfer->toUser?->name ?? 'N/A' }}</p>
                            </div>
                        @empty
                            <p class="text-xs text-slate-500">No transfer activity recorded.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Lost/Found History</p>
                    <div class="mt-3 space-y-3 text-sm">
                        @forelse ($device->lostReports as $report)
                            <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-slate-900">
                                        {{ $report->type === 'found' ? 'Found' : ucfirst($report->incident_type ?? $report->type) }}
                                    </p>
                                    <p class="text-xs text-slate-500">{{ $report->created_at?->format('M d, Y') }}</p>
                                </div>
                                <p class="mt-2 text-xs text-slate-500">Reported by {{ $report->reporter?->name ?? 'User' }}</p>
                                @if ($report->incident_location)
                                    <p class="mt-1 text-xs text-slate-500">Location: {{ $report->incident_location }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-xs text-slate-500">No lost or found activity recorded.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>
@endsection
