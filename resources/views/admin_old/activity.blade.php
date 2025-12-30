@extends('admin.layouts.app')

@section('page_heading', 'Activity Log')

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            ['label' => 'Events Today', 'value' => '428'],
            ['label' => 'Security Alerts', 'value' => '6'],
            ['label' => 'Admin Actions', 'value' => '118'],
        ] as $stat)
            <div class="rounded-2xl border border-border bg-card p-5 shadow-soft">
                <p class="text-xs font-semibold uppercase text-muted-foreground">{{ $stat['label'] }}</p>
                <p class="mt-3 text-2xl font-bold text-foreground">{{ $stat['value'] }}</p>
                <span class="mt-3 inline-flex rounded-full bg-muted px-3 py-1 text-xs font-semibold text-muted-foreground">Last 24 hours</span>
            </div>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-soft">
        <div class="border-b border-border px-6 py-4">
            <h2 class="text-lg font-semibold text-foreground">Recent activity</h2>
            <p class="text-sm text-muted-foreground">Audit trail for critical system events and admin actions.</p>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-muted/50 text-xs uppercase tracking-wider text-muted-foreground">
                <tr>
                    <th class="px-4 py-3">Action</th>
                    <th class="px-4 py-3">Actor</th>
                    <th class="px-4 py-3">Entity</th>
                    <th class="px-4 py-3">Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @foreach ([
                    ['action' => 'Device flagged suspicious', 'actor' => 'System', 'entity' => 'Device #1027', 'time' => '10:12 AM'],
                    ['action' => 'User role updated', 'actor' => 'Admin', 'entity' => 'User #1004', 'time' => '09:45 AM'],
                    ['action' => 'Transfer rejected', 'actor' => 'System', 'entity' => 'Transfer #9003', 'time' => '09:20 AM'],
                ] as $row)
                    <tr>
                        <td class="px-4 py-3 font-semibold text-foreground">{{ $row['action'] }}</td>
                        <td class="px-4 py-3 text-sm text-muted-foreground">{{ $row['actor'] }}</td>
                        <td class="px-4 py-3 text-sm text-muted-foreground">{{ $row['entity'] }}</td>
                        <td class="px-4 py-3 text-xs text-muted-foreground">{{ $row['time'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
