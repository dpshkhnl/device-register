@extends('admin.layouts.app')

@section('page_heading', 'Users & Roles')

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            ['label' => 'Roles Defined', 'value' => '3'],
            ['label' => 'Custom Policies', 'value' => '12'],
            ['label' => 'Pending Role Requests', 'value' => '8'],
        ] as $stat)
            <div class="rounded-2xl border border-border bg-card p-5 shadow-soft">
                <p class="text-xs font-semibold uppercase text-muted-foreground">{{ $stat['label'] }}</p>
                <p class="mt-3 text-2xl font-bold text-foreground">{{ $stat['value'] }}</p>
                <span class="mt-3 inline-flex rounded-full bg-muted px-3 py-1 text-xs font-semibold text-muted-foreground">Last updated today</span>
            </div>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-soft">
        <div class="flex items-center justify-between border-b border-border px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-foreground">Role definitions</h2>
                <p class="text-sm text-muted-foreground">Manage permissions for each account type.</p>
            </div>
            <button class="rounded-lg bg-primary px-3 py-2 text-xs font-semibold text-primary-foreground">Create Role</button>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-muted/50 text-xs uppercase tracking-wider text-muted-foreground">
                <tr>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Users</th>
                    <th class="px-4 py-3">Permissions</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @foreach ([
                    ['role' => 'Admin', 'users' => '6', 'permissions' => 'Full system control', 'status' => 'Active'],
                    ['role' => 'Shop / Reseller', 'users' => '2140', 'permissions' => 'Verification and registration', 'status' => 'Active'],
                    ['role' => 'Registered User', 'users' => '16100', 'permissions' => 'Register devices and transfers', 'status' => 'Active'],
                ] as $row)
                    <tr>
                        <td class="px-4 py-3 font-semibold text-foreground">{{ $row['role'] }}</td>
                        <td class="px-4 py-3 text-sm text-muted-foreground">{{ $row['users'] }}</td>
                        <td class="px-4 py-3 text-sm text-muted-foreground">{{ $row['permissions'] }}</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-muted px-3 py-1 text-xs font-semibold text-muted-foreground">{{ $row['status'] }}</span></td>
                        <td class="px-4 py-3 text-right"><button class="text-xs font-semibold text-primary">Edit</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
