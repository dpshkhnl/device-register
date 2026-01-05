@extends('admin.layouts.app')

@section('page_heading', 'Shop Applications')

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
                    <h2 class="text-lg font-semibold text-slate-900">Shop applications</h2>
                    <p class="text-sm text-slate-500">Review shop requests and approve or reject.</p>
                </div>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Applicant</th>
                        <th class="px-6 py-4">Shop</th>
                        <th class="px-6 py-4">Documents</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($applications as $application)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-900">{{ $application->user?->name }}</p>
                                <p class="text-xs text-slate-500">{{ $application->user?->email }}</p>
                                <p class="text-xs text-slate-500">{{ $application->user?->mobile }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-slate-900">{{ $application->shop_name }}</p>
                                <p class="text-xs text-slate-500">{{ $application->address }}</p>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                @if ($application->business_registration_path)
                                    <a class="text-[color:var(--brand-600)] hover:underline" href="{{ Storage::url($application->business_registration_path) }}" target="_blank">Business Registration</a>
                                @endif
                                <br />
                                @if ($application->store_photo_path)
                                    <a class="text-[color:var(--brand-600)] hover:underline" href="{{ Storage::url($application->store_photo_path) }}" target="_blank">Store Photo</a>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full border border-slate-200 px-2.5 py-1 text-[11px] font-semibold text-slate-600">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if ($application->status === 'pending')
                                    <form method="POST" action="{{ route('admin.shop-applications.update', $application) }}" class="inline-flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="approved" />
                                        <button class="rounded-lg bg-[color:var(--brand-600)] px-3 py-1 text-xs font-semibold text-white">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.shop-applications.update', $application) }}" class="inline-flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="rejected" />
                                        <button class="rounded-lg border border-rose-200 px-3 py-1 text-xs font-semibold text-rose-600">Reject</button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-500">Reviewed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500">No shop applications yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $applications->links() }}</div>
    </div>
@endsection
