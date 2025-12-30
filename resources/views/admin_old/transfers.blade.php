@extends('admin.layouts.app')

@section('page_heading', 'Ownership Transfers')

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            ['label' => 'Pending Requests', 'value' => '126'],
            ['label' => 'Accepted Today', 'value' => '84'],
            ['label' => 'Rejected / Disputed', 'value' => '12'],
        ] as $stat)
            <div class="rounded-2xl border border-border bg-card p-5 shadow-soft">
                <p class="text-xs font-semibold uppercase text-muted-foreground">{{ $stat['label'] }}</p>
                <p class="mt-3 text-2xl font-bold text-foreground">{{ $stat['value'] }}</p>
                <span class="mt-3 inline-flex rounded-full bg-muted px-3 py-1 text-xs font-semibold text-muted-foreground">Last 24 hours</span>
            </div>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-soft">
        <div class="flex items-center justify-between border-b border-border px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-foreground">Transfer queue</h2>
                <p class="text-sm text-muted-foreground">Ownership change requests requiring review.</p>
            </div>
            <button class="rounded-lg bg-primary px-3 py-2 text-xs font-semibold text-primary-foreground">Create Transfer</button>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-muted/50 text-xs uppercase tracking-wider text-muted-foreground">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Device</th>
                    <th class="px-4 py-3">IMEI</th>
                    <th class="px-4 py-3">From</th>
                    <th class="px-4 py-3">To</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @foreach ([
                    ['id' => 'T-9001', 'device' => 'iPhone 15 Pro', 'imei' => '353456789012345', 'from' => 'Demo User', 'to' => 'Kite Mobile Shop', 'status' => 'Pending'],
                    ['id' => 'T-9002', 'device' => 'Galaxy S24 Ultra', 'imei' => '861234567890123', 'from' => 'Kite Mobile Shop', 'to' => 'Demo User', 'status' => 'Accepted'],
                ] as $row)
                    <tr>
                        <td class="px-4 py-3 font-medium text-foreground">{{ $row['id'] }}</td>
                        <td class="px-4 py-3 text-sm text-foreground">{{ $row['device'] }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-muted-foreground">{{ $row['imei'] }}</td>
                        <td class="px-4 py-3 text-sm text-muted-foreground">{{ $row['from'] }}</td>
                        <td class="px-4 py-3 text-sm text-muted-foreground">{{ $row['to'] }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-muted px-3 py-1 text-xs font-semibold text-muted-foreground">{{ $row['status'] }}</span>
                        </td>
                        <td class="px-4 py-3 text-right"><button class="text-xs font-semibold text-primary">Review</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
