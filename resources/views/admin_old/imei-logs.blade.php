@extends('admin.layouts.app')

@section('page_heading', 'IMEI Verification Logs')

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            ['label' => 'Total Lookups Today', 'value' => '4,620'],
            ['label' => 'Not Found', 'value' => '214'],
            ['label' => 'Flagged', 'value' => '42'],
        ] as $stat)
            <div class="rounded-2xl border border-border bg-card p-5 shadow-soft">
                <p class="text-xs font-semibold uppercase text-muted-foreground">{{ $stat['label'] }}</p>
                <p class="mt-3 text-2xl font-bold text-foreground">{{ $stat['value'] }}</p>
                <span class="mt-3 inline-flex rounded-full bg-muted px-3 py-1 text-xs font-semibold text-muted-foreground">Live feed</span>
            </div>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-soft">
        <div class="flex items-center justify-between border-b border-border px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-foreground">Recent IMEI checks</h2>
                <p class="text-sm text-muted-foreground">Search origin, status, and response outcomes.</p>
            </div>
            <button class="rounded-lg border border-border px-3 py-2 text-xs font-semibold text-muted-foreground">Export logs</button>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-muted/50 text-xs uppercase tracking-wider text-muted-foreground">
                <tr>
                    <th class="px-4 py-3">IMEI</th>
                    <th class="px-4 py-3">Result</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Source</th>
                    <th class="px-4 py-3">Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @foreach ([
                    ['imei' => '353456789012345', 'result' => 'Found', 'status' => 'Active', 'source' => 'Public Portal', 'time' => '11:42 AM'],
                    ['imei' => '490154203237518', 'result' => 'Found', 'status' => 'Lost', 'source' => 'User Dashboard', 'time' => '11:18 AM'],
                    ['imei' => '351756051523999', 'result' => 'Found', 'status' => 'Suspicious', 'source' => 'Shop Terminal', 'time' => '10:55 AM'],
                ] as $row)
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs text-muted-foreground">{{ $row['imei'] }}</td>
                        <td class="px-4 py-3 text-sm text-muted-foreground">{{ $row['result'] }}</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-muted px-3 py-1 text-xs font-semibold text-muted-foreground">{{ $row['status'] }}</span></td>
                        <td class="px-4 py-3 text-sm text-muted-foreground">{{ $row['source'] }}</td>
                        <td class="px-4 py-3 text-xs text-muted-foreground">{{ $row['time'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
