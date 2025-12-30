@extends('admin.layouts.app')

@section('page_heading', 'Certificates')

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            ['label' => 'Certificates Issued', 'value' => '1,840'],
            ['label' => 'Issued This Week', 'value' => '64'],
            ['label' => 'Pending Requests', 'value' => '9'],
        ] as $stat)
            <div class="rounded-2xl border border-border bg-card p-5 shadow-soft">
                <p class="text-xs font-semibold uppercase text-muted-foreground">{{ $stat['label'] }}</p>
                <p class="mt-3 text-2xl font-bold text-foreground">{{ $stat['value'] }}</p>
                <span class="mt-3 inline-flex rounded-full bg-muted px-3 py-1 text-xs font-semibold text-muted-foreground">Updated today</span>
            </div>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-soft">
        <div class="flex items-center justify-between border-b border-border px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-foreground">Latest certificates</h2>
                <p class="text-sm text-muted-foreground">Track generated certificates for audits.</p>
            </div>
            <button class="rounded-lg bg-primary px-3 py-2 text-xs font-semibold text-primary-foreground">Issue Certificate</button>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-muted/50 text-xs uppercase tracking-wider text-muted-foreground">
                <tr>
                    <th class="px-4 py-3">Certificate</th>
                    <th class="px-4 py-3">IMEI</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Issued To</th>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @foreach ([
                    ['id' => 'C-4411', 'imei' => '353456789012345', 'status' => 'Active', 'to' => 'Demo User', 'date' => 'Dec 26, 2024'],
                    ['id' => 'C-4412', 'imei' => '861234567890123', 'status' => 'Transferred', 'to' => 'Kite Mobile Shop', 'date' => 'Dec 25, 2024'],
                ] as $row)
                    <tr>
                        <td class="px-4 py-3 font-semibold text-foreground">{{ $row['id'] }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-muted-foreground">{{ $row['imei'] }}</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-muted px-3 py-1 text-xs font-semibold text-muted-foreground">{{ $row['status'] }}</span></td>
                        <td class="px-4 py-3 text-sm text-muted-foreground">{{ $row['to'] }}</td>
                        <td class="px-4 py-3 text-xs text-muted-foreground">{{ $row['date'] }}</td>
                        <td class="px-4 py-3 text-right"><button class="text-xs font-semibold text-primary">Download</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
