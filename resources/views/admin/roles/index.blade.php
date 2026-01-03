@extends('admin.layouts.app')

@section('page_heading', 'Users & Roles')

@section('content')
    <div class="w-full space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Roles Defined</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $roleStats['total_roles'] }}</p>
                <x-ui.badge class="mt-3" status="active" label="Active policies" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Role Requests</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $roleStats['pending_requests'] }}</p>
                <x-ui.badge class="mt-3" status="suspicious" label="Pending review" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Privileged Users</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $roleStats['admin_count'] }}</p>
                <x-ui.badge class="mt-3" status="transferred" label="Admin" />
            </x-ui.card>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Role definitions</h2>
                    <p class="text-sm text-slate-500">Access tiers and permissions overview.</p>
                </div>
                <button class="rounded-lg bg-[color:var(--brand-600)] px-3 py-2 text-xs font-semibold text-white">Create Role</button>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Users</th>
                        <th class="px-4 py-3">Permissions</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($roles as $role)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $role['label'] }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $role['count'] }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $role['permissions'] }}</td>
                            <td class="px-4 py-3 text-right">
                                <button class="text-xs font-semibold text-[color:var(--brand-600)]">Edit</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
