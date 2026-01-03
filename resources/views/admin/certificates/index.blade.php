@extends('admin.layouts.app')

@section('page_heading', 'Certificates')

@section('content')
    <div class="w-full space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Certificates Issued</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $certificates->total() }}</p>
                <x-ui.badge class="mt-3" status="active" label="All time" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Issued This Week</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $certificates->getCollection()->count() }}</p>
                <x-ui.badge class="mt-3" status="transferred" label="Recent" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Pending Requests</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $pendingRequests }}</p>
                <x-ui.badge class="mt-3" status="suspicious" label="Needs review" />
            </x-ui.card>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Latest certificates</h2>
                    <p class="text-sm text-slate-500">Track generated certificates for audits.</p>
                </div>
                <button class="rounded-lg bg-[color:var(--brand-600)] px-3 py-2 text-xs font-semibold text-white">Issue Certificate</button>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Certificate</th>
                        <th class="px-4 py-3">IMEI</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Issued To</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($certificates as $certificate)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $certificate->certificate_no }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $certificate->device?->imei }}</td>
                            <td class="px-4 py-3">
                                <x-ui.badge status="{{ $certificate->status_at_issue ?? 'active' }}" label="{{ ucfirst($certificate->status_at_issue ?? 'active') }}" />
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $certificate->issuedTo?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs text-slate-400">{{ $certificate->created_at?->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <button class="text-xs font-semibold text-[color:var(--brand-600)]">Download</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">No certificates issued yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $certificates->links() }}</div>
    </div>
@endsection
