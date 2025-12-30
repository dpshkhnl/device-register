@extends('admin.layouts.app')

@section('page_heading', 'IMEI Verification Logs')

@section('content')
    @php
        $logItems = $imeiLogs->getCollection();
        $notFoundCount = $logItems->where('result', 'not_found')->count();
        $flaggedCount = $logItems->whereIn('status_returned', ['lost', 'suspicious'])->count();
    @endphp

    <div class="mx-auto max-w-6xl space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Lookups</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $imeiLogs->total() }}</p>
                <x-ui.badge class="mt-3" status="active" label="Live feed" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Not Found</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $notFoundCount }}</p>
                <x-ui.badge class="mt-3" status="transferred" label="Check data" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Flagged</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $flaggedCount }}</p>
                <x-ui.badge class="mt-3" status="lost" label="Review" />
            </x-ui.card>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Recent IMEI checks</h2>
                    <p class="text-sm text-slate-500">Search origin, status, and response outcomes.</p>
                </div>
                <button class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600">Export logs</button>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">IMEI</th>
                        <th class="px-4 py-3">Result</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Channel</th>
                        <th class="px-4 py-3">Checked By</th>
                        <th class="px-4 py-3">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($imeiLogs as $log)
                        <tr>
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $log->imei }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ ucfirst(str_replace('_', ' ', $log->result)) }}</td>
                            <td class="px-4 py-3">
                                <x-ui.badge status="{{ $log->status_returned ?? 'active' }}" label="{{ ucfirst($log->status_returned ?? 'active') }}" />
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ ucfirst($log->channel ?? 'portal') }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $log->checkedBy?->name ?? 'Guest' }}</td>
                            <td class="px-4 py-3 text-xs text-slate-400">{{ $log->created_at?->format('M d, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">No IMEI checks logged yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $imeiLogs->links() }}</div>
    </div>
@endsection
