@extends('admin.layouts.app')

@section('page_heading', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl border border-border bg-card p-6 shadow-soft">
        <p class="text-sm font-medium text-muted-foreground">Dashboard</p>
        <h1 class="mt-2 text-2xl font-bold text-foreground">Administrative overview</h1>
        <p class="mt-2 text-sm text-muted-foreground">Monitor compliance, user activity, and device status updates from the central command panel.</p>
    </div>

    <div class="grid gap-4 lg:grid-cols-4">
        @foreach ([
            ['label' => 'Total Devices', 'value' => '28,420', 'note' => 'Updated today'],
            ['label' => 'Open Transfers', 'value' => '126', 'note' => '12 pending'],
            ['label' => 'Lost / Stolen Flags', 'value' => '34', 'note' => 'Needs review'],
            ['label' => 'Daily Verifications', 'value' => '1,420', 'note' => 'Peak 2:00 PM'],
        ] as $stat)
            <div class="rounded-2xl border border-border bg-card p-5 shadow-soft">
                <p class="text-xs font-semibold uppercase text-muted-foreground">{{ $stat['label'] }}</p>
                <p class="mt-3 text-2xl font-bold text-foreground">{{ $stat['value'] }}</p>
                <span class="mt-3 inline-flex rounded-full bg-muted px-3 py-1 text-xs font-semibold text-muted-foreground">{{ $stat['note'] }}</span>
            </div>
        @endforeach
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-border bg-card p-6 shadow-soft lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-foreground">Priority actions</h2>
                    <p class="text-sm text-muted-foreground">Items that require admin attention today.</p>
                </div>
                <a href="{{ route('admin.activity') }}" class="text-xs font-semibold text-primary">View log -></a>
            </div>
            <div class="mt-4 space-y-3">
                @foreach ([
                    ['title' => 'Verify 12 new lost/stolen submissions', 'meta' => 'Submitted within the last 6 hours'],
                    ['title' => 'Review 8 ownership transfer disputes', 'meta' => 'Awaiting resolution'],
                    ['title' => 'Approve 4 reseller onboarding requests', 'meta' => 'Requires role assignment'],
                ] as $item)
                    <div class="flex items-center justify-between rounded-xl border border-border bg-muted/20 px-4 py-3">
                        <div>
                            <p class="font-medium text-foreground">{{ $item['title'] }}</p>
                            <p class="text-xs text-muted-foreground">{{ $item['meta'] }}</p>
                        </div>
                        <button class="rounded-lg border border-border px-3 py-1 text-xs font-semibold text-muted-foreground">Review</button>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-2xl border border-border bg-card p-6 shadow-soft">
            <h2 class="text-lg font-semibold text-foreground">Compliance status</h2>
            <p class="text-sm text-muted-foreground">System health & audits</p>
            <div class="mt-4 space-y-4">
                @foreach ([
                    ['label' => 'IMEI validation uptime', 'value' => '99.98%'],
                    ['label' => 'OTP delivery', 'value' => '99.4%'],
                    ['label' => 'Audit logs', 'value' => 'No gaps'],
                ] as $item)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-foreground">{{ $item['label'] }}</p>
                            <p class="text-xs text-muted-foreground">{{ $item['value'] }}</p>
                        </div>
                        <span class="rounded-full bg-muted px-3 py-1 text-xs font-semibold text-muted-foreground">Verified</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
