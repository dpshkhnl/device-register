@extends('admin.layouts.app')

@section('page_heading', 'Lost / Stolen Reports')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Open Reports</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $reports->total() }}</p>
                <x-ui.badge class="mt-3" status="suspicious" label="Pending review" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Verified Today</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $reports->getCollection()->where('status', 'approved')->count() }}</p>
                <x-ui.badge class="mt-3" status="active" label="Approved" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Awaiting Evidence</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $reports->getCollection()->where('status', 'pending')->count() }}</p>
                <x-ui.badge class="mt-3" status="lost" label="Investigate" />
            </x-ui.card>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Incident queue</h2>
                    <p class="text-sm text-slate-500">Lost and stolen submissions awaiting review.</p>
                </div>
                <button class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600">Export</button>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Device</th>
                        <th class="px-4 py-3">IMEI</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Submitted By</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($reports as $report)
                        <tr>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900">
                                {{ $report->device?->brand }} {{ $report->device?->model }}
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $report->device?->imei }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ ucfirst($report->type) }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $report->reporter?->name ?? 'Reporter' }}</td>
                            <td class="px-4 py-3">
                                <x-ui.badge status="{{ $report->status === 'approved' ? 'lost' : 'suspicious' }}" label="{{ ucfirst($report->status) }}" />
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('admin.lost-stolen.update', $report) }}" class="inline-flex items-center gap-2" data-swal-confirm data-swal-title="Update report status?" data-swal-text="This will update the device status as well." data-swal-confirm="Yes, update">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="rounded-lg border border-slate-200 px-2 py-1 text-xs">
                                        <option value="approved" @selected($report->status === 'approved')>Approve</option>
                                        <option value="rejected" @selected($report->status === 'rejected')>Reject</option>
                                    </select>
                                    <button class="rounded-lg bg-[color:var(--brand-600)] px-3 py-1 text-xs font-semibold text-white">Update</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div>{{ $reports->links() }}</div>
    </div>
@endsection
