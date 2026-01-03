@extends('admin.layouts.app')

@section('page_heading', 'Customers')

@section('content')
    @php
        $userItems = $users->getCollection();
        $customerCount = $userItems->where('role', 'user')->count();
        $shopCount = $userItems->where('role', 'shop')->count();
        $adminCount = $userItems->where('role', 'admin')->count();
    @endphp

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
                    <p class="text-sm text-slate-500">Manage roles and access levels.</p>
                </div>
                <div class="flex gap-2 text-xs text-slate-500">
                    <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1">Customers {{ $customerCount }}</span>
                    <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1">Shops {{ $shopCount }}</span>
                    <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1">Admins {{ $adminCount }}</span>
                </div>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Contact</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Joined</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($users as $user)
                        <tr class="group hover:bg-slate-50/80">
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
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="role" class="rounded-lg border border-slate-200 px-2 py-1 text-xs">
                                        <option value="user" @selected($user->role === 'user')>Customer</option>
                                        <option value="shop" @selected($user->role === 'shop')>Shop</option>
                                        <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                    </select>
                                    <button class="rounded-lg bg-[color:var(--brand-600)] px-3 py-1 text-xs font-semibold text-white">Save</button>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">{{ $user->created_at?->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a class="text-xs font-semibold text-[color:var(--brand-600)]" href="{{ route('admin.roles.index') }}">Manage</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div>{{ $users->links() }}</div>
    </div>
@endsection
