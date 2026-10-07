@extends('admin.layouts.app')

@section('page_heading', 'KYC Verification')

@section('content')
<div class="py-8">
    <div class="w-full sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-slate-900">KYC Verification</h1>
            <p class="text-sm text-slate-500">Review customer photos, government IDs and alternate numbers.</p>
        </div>

        @if (session('status'))
            <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="mb-4 flex flex-wrap items-center gap-2">
            @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All submitted'] as $key => $label)
                <a href="{{ route('admin.kyc.index', ['status' => $key]) }}"
                    class="rounded-full border px-3 py-1.5 text-xs font-semibold {{ $status === $key ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 text-slate-600' }}">
                    {{ $label }}
                    @if ($key !== 'all')
                        <span class="ml-1 opacity-70">{{ $counts[$key] ?? 0 }}</span>
                    @endif
                </a>
            @endforeach
            <form method="GET" class="ml-auto flex gap-2">
                <input type="hidden" name="status" value="{{ $status }}">
                <input name="search" value="{{ request('search') }}" placeholder="Name, mobile or ID number" class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm">
                <button class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-600">Search</button>
            </form>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3">Mobile / Alternate</th>
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Submitted</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                                <p class="text-xs text-slate-500">{{ $user->email ?? 'No email' }}</p>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">
                                {{ $user->mobile ?? '—' }}<br>{{ $user->alternate_mobile ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">
                                {{ \App\Models\User::KYC_ID_TYPES[$user->kyc_id_type] ?? '—' }}<br>{{ $user->kyc_id_number }}
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ $user->kyc_submitted_at?->format('M d, Y H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs font-semibold text-slate-600">{{ $user->kycStatusLabel() }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.kyc.show', $user) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">No KYC submissions here.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $users->links() }}</div>
    </div>
</div>
@endsection
