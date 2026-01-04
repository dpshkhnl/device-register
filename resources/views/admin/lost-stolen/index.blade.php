@extends('admin.layouts.app')

@section('page_heading', 'Lost / Stolen Reports')

@section('content')
    <div class="w-full space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Lost and stolen devices</h2>
                    <p class="text-sm text-slate-500">Review incident details and contact information.</p>
                </div>
                <button class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600">Export</button>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-4">SN</th>
                        <th class="px-6 py-4">Device</th>
                        <th class="px-6 py-4">IMEI</th>
                        <th class="px-6 py-4">Incident</th>
                        <th class="px-6 py-4">Submitted By</th>
                        <th class="px-6 py-4">Contact</th>
                        <th class="px-6 py-4">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($reports as $report)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-6 py-4 text-xs text-slate-500">{{ $reports->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">
                                {{ $report->device?->brand }} {{ $report->device?->model }}
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $report->device?->imei }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ ucfirst($report->incident_type ?? 'lost') }}
                                <p class="mt-1 text-xs text-slate-400">{{ $report->incident_date?->format('M d, Y') ?? '—' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $report->reporter?->name ?? 'Reporter' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                <p>{{ $report->contact_phone_1 ?? '—' }}</p>
                                <p class="text-xs text-slate-400">{{ $report->contact_phone_2 ?? '' }}</p>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                <p>{{ $report->incident_location ?? 'Location not provided' }}</p>
                                <p class="mt-1 text-slate-400">{{ $report->description ?? 'No description' }}</p>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div>{{ $reports->links() }}</div>
    </div>
@endsection
