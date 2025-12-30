@extends('layouts.app')

@section('content')
<section class="py-12 lg:py-20">
    <div class="container max-w-3xl">
        <a href="{{ route('dashboard') }}" class="mb-8 inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6 6 6" />
            </svg>
            Back to Dashboard
        </a>

        @if (session('status'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl border border-border bg-card p-6 shadow-soft sm:p-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <p class="text-sm text-muted-foreground">Device Details</p>
                    <h1 class="text-2xl font-bold text-foreground">{{ $device->brand }} {{ $device->model }}</h1>
                </div>
                <span class="rounded-full bg-muted px-3 py-1 text-xs font-semibold text-muted-foreground">{{ ucfirst($device->status) }}</span>
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between rounded-lg bg-muted/50 px-4 py-3">
                    <span class="text-sm text-muted-foreground">IMEI</span>
                    <span class="font-mono text-sm font-medium text-foreground">{{ $device->imei }}</span>
                </div>
                <div class="flex items-center justify-between rounded-lg bg-muted/50 px-4 py-3">
                    <span class="text-sm text-muted-foreground">Device Type</span>
                    <span class="text-sm font-medium text-foreground">{{ ucfirst($device->device_type) }}</span>
                </div>
                <div class="flex items-center justify-between rounded-lg bg-muted/50 px-4 py-3">
                    <span class="text-sm text-muted-foreground">Purchase Type</span>
                    <span class="text-sm font-medium text-foreground">{{ ucfirst($device->purchase_type) }}</span>
                </div>
                <div class="flex items-center justify-between rounded-lg bg-muted/50 px-4 py-3">
                    <span class="text-sm text-muted-foreground">Purchase Date</span>
                    <span class="text-sm font-medium text-foreground">{{ $device->purchase_date?->format('M d, Y') }}</span>
                </div>
                <div class="flex items-center justify-between rounded-lg bg-muted/50 px-4 py-3">
                    <span class="text-sm text-muted-foreground">Invoice</span>
                    <span class="text-sm font-medium text-foreground">
                        {{ $device->invoice_path ? 'Uploaded' : 'Not provided' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
