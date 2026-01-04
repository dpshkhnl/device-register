@extends('admin.layouts.app')

@section('page_heading', 'Customers')

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
                    <h2 class="text-lg font-semibold text-slate-900">Customer directory</h2>
                    <p class="text-sm text-slate-500">Browse registered users and their details.</p>
                </div>
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex w-full max-w-md items-center gap-2">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Search name, email, phone"
                        class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-xs"
                    />
                    <button class="h-9 rounded-lg bg-[color:var(--brand-600)] px-3 text-xs font-semibold text-white">Search</button>
                </form>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-4">SN</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Contact</th>
                        <th class="px-6 py-4">Package</th>
                        <th class="px-6 py-4">Expiry</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Allowed Devices</th>
                        <th class="px-6 py-4">Joined</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($users as $user)
                        <tr class="group hover:bg-slate-50/80">
                            @php
                                $activePackage = $user->activePackage;
                            @endphp
                            <td class="px-6 py-4 text-xs text-slate-500">{{ $users->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $user->email ?? 'No email' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-slate-900">{{ $user->mobile ?? '—' }}</p>
                                <p class="text-xs text-slate-500">{{ $user->country ?? '—' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">
                                {{ $activePackage?->package?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $activePackage?->ends_at?->format('M d, Y') ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full border border-slate-200 px-2.5 py-1 text-[11px] font-semibold text-slate-600">
                                    {{ $activePackage?->status ?? 'none' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">
                                {{ $activePackage?->device_limit ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">{{ $user->created_at?->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a
                                    class="inline-flex items-center rounded-lg border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-600 hover:border-slate-300 hover:text-slate-800"
                                    href="{{ route('admin.devices.index', ['q' => $user->email ?? $user->name]) }}"
                                >
                                    View devices
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div>{{ $users->links() }}</div>
    </div>
@endsection
