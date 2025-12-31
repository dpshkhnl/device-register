@extends('layouts.app')

@section('content')
<section class="relative overflow-hidden bg-background py-12 lg:py-16">
    <div class="pointer-events-none absolute inset-0 opacity-20"
        style="background-image: url('/images/shield-badge.svg'); background-repeat: no-repeat; background-position: right 6% top 12%; background-size: 220px;">
    </div>
    <div class="container max-w-5xl">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6 6 6" />
                </svg>
                Back to Dashboard
            </a>
            <div class="rounded-full border border-border bg-card px-4 py-1.5 text-xs font-semibold text-muted-foreground">Device Profile</div>
        </div>

        @if (session('status'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[1.2fr,0.8fr]">
            <div class="rounded-3xl border border-border bg-card p-6 shadow-soft sm:p-8">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Device Details</p>
                        <h1 class="mt-2 text-2xl font-bold text-foreground">{{ $device->brand }} {{ $device->model }}</h1>
                        <p class="mt-2 text-sm text-muted-foreground">IMEI {{ $device->imei }}</p>
                        @if ($device->imei2)
                            <p class="mt-1 text-sm text-muted-foreground">IMEI 2 {{ $device->imei2 }}</p>
                        @endif
                    </div>
                    <span class="rounded-full bg-muted px-3 py-1 text-xs font-semibold text-muted-foreground">{{ ucfirst($device->status) }}</span>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-border bg-muted/20 px-4 py-4">
                        <p class="text-xs text-muted-foreground">Device Type</p>
                        <p class="mt-2 text-sm font-semibold text-foreground">{{ ucfirst($device->device_type) }}</p>
                    </div>
                    <div class="rounded-2xl border border-border bg-muted/20 px-4 py-4">
                        <p class="text-xs text-muted-foreground">Purchase Type</p>
                        <p class="mt-2 text-sm font-semibold text-foreground">{{ ucfirst($device->purchase_type) }}</p>
                    </div>
                    <div class="rounded-2xl border border-border bg-muted/20 px-4 py-4">
                        <p class="text-xs text-muted-foreground">Purchase Date</p>
                        <p class="mt-2 text-sm font-semibold text-foreground">{{ $device->purchase_date?->format('M d, Y') }}</p>
                    </div>
                    <div class="rounded-2xl border border-border bg-muted/20 px-4 py-4">
                        <p class="text-xs text-muted-foreground">Invoice</p>
                        <p class="mt-2 text-sm font-semibold text-foreground">{{ $device->invoice_path ? 'Uploaded' : 'Not provided' }}</p>
                    </div>
                </div>
            </div>

            <aside class="space-y-4">
                <div class="rounded-3xl border border-border bg-muted/40 p-6">
                    <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Security Note</p>
                    <p class="mt-3 text-sm text-muted-foreground">Keep your IMEI private and share only with trusted parties.</p>
                </div>
                <div class="rounded-3xl border border-border bg-card p-6 shadow-soft">
                    <p class="text-sm font-semibold text-foreground">Need help?</p>
                    <p class="mt-2 text-xs text-muted-foreground">Contact support for transfers, lost reports, or certificate queries.</p>
                    <a href="mailto:{{ $settings?->contact_email ?? 'support@drms.gov' }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-primary hover:underline">
                        Email Support
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
