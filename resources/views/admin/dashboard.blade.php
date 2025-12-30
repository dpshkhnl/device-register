@extends('admin.layouts.app')

@section('page_heading', 'Dashboard')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Admin Overview</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900 sm:text-3xl">Administrative Dashboard</h1>
            <p class="mt-2 text-sm text-slate-600">Monitor compliance, user activity, and device status updates.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Devices</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $stats['devices'] }}</p>
                <x-ui.badge class="mt-3" status="active" label="Updated today" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Open Transfers</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $stats['pending_transfers'] }}</p>
                <x-ui.badge class="mt-3" status="suspicious" label="Pending" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Lost Reports</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $stats['pending_reports'] }}</p>
                <x-ui.badge class="mt-3" status="lost" label="Needs review" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Daily Verifications</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $stats['active_devices'] }}</p>
                <x-ui.badge class="mt-3" status="active" label="Peak 2:00 PM" />
            </x-ui.card>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Priority actions</h2>
                        <p class="text-sm text-slate-500">Items that require admin attention today.</p>
                    </div>
                    <a class="text-xs font-semibold text-[color:var(--brand-600)]" href="{{ route('admin.activity-logs.index') }}">View log →</a>
                </div>
                <div class="mt-4 space-y-3">
                    @foreach ([
                        ['title' => 'Verify new lost/stolen submissions', 'meta' => 'Submitted within the last 6 hours'],
                        ['title' => 'Review ownership transfer disputes', 'meta' => 'Awaiting resolution'],
                        ['title' => 'Approve reseller onboarding requests', 'meta' => 'Requires role assignment'],
                    ] as $item)
                        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $item['title'] }}</p>
                                <p class="text-xs text-slate-500">{{ $item['meta'] }}</p>
                            </div>
                            <button class="rounded-lg border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">Review</button>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Compliance status</h2>
                <p class="text-sm text-slate-500">System health & audit readiness.</p>
                <div class="mt-4 space-y-4">
                    @foreach ([
                        ['label' => 'IMEI validation uptime', 'value' => '99.98%', 'status' => 'active'],
                        ['label' => 'OTP delivery', 'value' => '99.4%', 'status' => 'transferred'],
                        ['label' => 'Audit logs', 'value' => 'No gaps', 'status' => 'active'],
                    ] as $item)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $item['label'] }}</p>
                                <p class="text-xs text-slate-500">{{ $item['value'] }}</p>
                            </div>
                            <x-ui.badge status="{{ $item['status'] }}" label="Verified" />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
