@extends('admin.layouts.app')

@section('page_heading', 'Users')

@section('content')
    @php
        $userItems = $users->getCollection();
        $shopCount = $userItems->where('role', 'shop')->count();
        $authorityCount = $userItems->where('role', 'authority')->count();
        $adminCount = $userItems->where('role', 'admin')->count();
    @endphp

    <div class="mx-auto max-w-6xl space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Users</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $users->total() }}</p>
                <x-ui.badge class="mt-3" status="active" label="Active" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Shops / Resellers</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $shopCount }}</p>
                <x-ui.badge class="mt-3" status="transferred" label="Verified" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Authorities</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $authorityCount }}</p>
                <x-ui.badge class="mt-3" status="active" label="Read-only" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Admins</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $adminCount }}</p>
                <x-ui.badge class="mt-3" status="suspicious" label="Privileged" />
            </x-ui.card>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">User directory</h2>
                    <p class="text-sm text-slate-500">Manage roles and access levels.</p>
                </div>
                <button class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600">Export</button>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Mobile</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Joined</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($users as $user)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $user->mobile }}</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="role" class="rounded-lg border border-slate-200 px-2 py-1 text-xs">
                                        <option value="user" @selected($user->role === 'user')>User</option>
                                        <option value="shop" @selected($user->role === 'shop')>Shop</option>
                                        <option value="authority" @selected($user->role === 'authority')>Authority</option>
                                        <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                    </select>
                                    <button class="rounded-lg bg-[color:var(--brand-600)] px-3 py-1 text-xs font-semibold text-white">Save</button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ $user->created_at?->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-right">
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
