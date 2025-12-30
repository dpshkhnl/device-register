@extends('admin.layouts.app')

@section('page_heading', 'Task Queue')

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            ['label' => 'Open Tasks', 'value' => '18'],
            ['label' => 'High Priority', 'value' => '6'],
            ['label' => 'Completed Today', 'value' => '12'],
        ] as $stat)
            <div class="rounded-2xl border border-border bg-card p-5 shadow-soft">
                <p class="text-xs font-semibold uppercase text-muted-foreground">{{ $stat['label'] }}</p>
                <p class="mt-3 text-2xl font-bold text-foreground">{{ $stat['value'] }}</p>
                <span class="mt-3 inline-flex rounded-full bg-muted px-3 py-1 text-xs font-semibold text-muted-foreground">Operational queue</span>
            </div>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-soft">
        <div class="flex items-center justify-between border-b border-border px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-foreground">Queued tasks</h2>
                <p class="text-sm text-muted-foreground">Prioritize admin work and track completion.</p>
            </div>
            <button class="rounded-lg bg-primary px-3 py-2 text-xs font-semibold text-primary-foreground">Create Task</button>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-muted/50 text-xs uppercase tracking-wider text-muted-foreground">
                <tr>
                    <th class="px-4 py-3">Task</th>
                    <th class="px-4 py-3">Assigned</th>
                    <th class="px-4 py-3">Priority</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @foreach ([
                    ['task' => 'Verify lost/stolen evidence', 'assigned' => 'Audit Team', 'priority' => 'High', 'status' => 'Pending'],
                    ['task' => 'Approve reseller onboarding', 'assigned' => 'Admin Lead', 'priority' => 'Medium', 'status' => 'In Progress'],
                    ['task' => 'Review suspicious IMEI spike', 'assigned' => 'Security', 'priority' => 'High', 'status' => 'Pending'],
                ] as $row)
                    <tr>
                        <td class="px-4 py-3 font-semibold text-foreground">{{ $row['task'] }}</td>
                        <td class="px-4 py-3 text-sm text-muted-foreground">{{ $row['assigned'] }}</td>
                        <td class="px-4 py-3 text-sm text-muted-foreground">{{ $row['priority'] }}</td>
                        <td class="px-4 py-3 text-sm text-muted-foreground">{{ $row['status'] }}</td>
                        <td class="px-4 py-3 text-right"><button class="text-xs font-semibold text-primary">View</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
