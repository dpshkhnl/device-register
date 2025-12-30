@extends('admin.layouts.app')

@section('page_heading', 'Activity Logs')

@section('content')
    @php
        $logItems = $logs->getCollection();
        $alertCount = $logItems->filter(fn ($log) => str_contains(strtolower($log->action ?? ''), 'alert'))->count();
        $adminCount = $logItems->whereNotNull('actor_id')->count();
    @endphp

    <div class="mx-auto max-w-6xl space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Events Today</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $logs->total() }}</p>
                <x-ui.badge class="mt-3" status="active" label="Live feed" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Security Alerts</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $alertCount }}</p>
                <x-ui.badge class="mt-3" status="lost" label="Critical" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Admin Actions</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $adminCount }}</p>
                <x-ui.badge class="mt-3" status="transferred" label="Audited" />
            </x-ui.card>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Recent activity</h2>
                <p class="text-sm text-slate-500">Audit trail for critical system events.</p>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Action</th>
                        <th class="px-4 py-3">Actor</th>
                        <th class="px-4 py-3">Entity</th>
                        <th class="px-4 py-3">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($logs as $log)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $log->action }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $log->actor?->name ?? 'System' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ class_basename($log->entity_type) }} #{{ $log->entity_id }}</td>
                            <td class="px-4 py-3 text-xs text-slate-400">{{ $log->created_at?->format('M d, Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div>{{ $logs->links() }}</div>
    </div>
@endsection
