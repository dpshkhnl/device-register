@extends('admin.layouts.app')

@section('page_heading', 'Reports')

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['label' => 'Reports Generated', 'value' => '84'],
            ['label' => 'Scheduled Exports', 'value' => '12'],
            ['label' => 'Authorities Shared', 'value' => '6'],
            ['label' => 'Outstanding Reviews', 'value' => '4'],
        ] as $stat)
            <div class="rounded-2xl border border-border bg-card p-5 shadow-soft">
                <p class="text-xs font-semibold uppercase text-muted-foreground">{{ $stat['label'] }}</p>
                <p class="mt-3 text-2xl font-bold text-foreground">{{ $stat['value'] }}</p>
                <span class="mt-3 inline-flex rounded-full bg-muted px-3 py-1 text-xs font-semibold text-muted-foreground">This month</span>
            </div>
        @endforeach
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        @foreach ([
            ['title' => 'Devices by Status', 'desc' => 'Active, transferred, lost, suspicious distribution.', 'action' => 'Open Report'],
            ['title' => 'IMEI Verification Logs', 'desc' => 'Search volumes, success rate, anomalies.', 'action' => 'View Logs'],
            ['title' => 'Lost / Stolen Trends', 'desc' => 'Monthly comparisons and hotspots.', 'action' => 'Analyze'],
            ['title' => 'Transfer Requests', 'desc' => 'Acceptance, rejection, and dispute rates.', 'action' => 'Review'],
        ] as $report)
            <div class="rounded-2xl border border-border bg-card p-6 shadow-soft">
                <h2 class="text-lg font-semibold text-foreground">{{ $report['title'] }}</h2>
                <p class="mt-2 text-sm text-muted-foreground">{{ $report['desc'] }}</p>
                <button class="mt-4 rounded-lg border border-border px-3 py-2 text-xs font-semibold text-muted-foreground">{{ $report['action'] }}</button>
            </div>
        @endforeach
    </div>
</div>
@endsection
