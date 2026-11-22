@extends('layouts.app')

@section('title', trim($device->brand.' '.$device->model))

@section('content')
@php
    $isLost = $device->status === 'lost';
    $hasPendingTransfer = $device->transferRequests()->where('status', 'pending')->exists();
    $details = [
        ['label' => 'Device type', 'value' => ucfirst($device->device_type), 'icon' => 'phone'],
        ['label' => 'Purchase type', 'value' => $device->purchase_type === 'secondhand' ? 'Second-hand' : ucfirst($device->purchase_type), 'icon' => 'tag'],
        ['label' => $device->device_age ? 'Device age' : 'Purchase date', 'value' => $device->device_age ?? $device->purchase_date?->format('M d, Y') ?? '—', 'icon' => 'calendar'],
        ['label' => 'Registered on', 'value' => $device->created_at?->format('M d, Y') ?? '—', 'icon' => 'clock'],
    ];
@endphp

<div class="container max-w-5xl py-10 lg:py-12">
    <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm -ml-3 mb-4">
        <x-site.icon name="arrow-left" class="h-4 w-4" /> Dashboard
    </a>

    @if ($isLost)
        <div class="callout callout-danger mb-6" role="alert">
            <x-site.icon name="alert" class="mt-px h-5 w-5 shrink-0" />
            <div class="flex flex-1 flex-wrap items-center justify-between gap-3">
                <p><span class="font-semibold">This device is reported lost or stolen.</span> Anyone checking its IMEI will see a warning.</p>
                <a href="{{ route('lost-found.index', ['device' => $device->id]) }}" class="btn btn-sm btn-success">Mark as found</a>
            </div>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1fr,20rem]">
        <section class="surface overflow-hidden">
            <div class="flex flex-wrap items-start gap-5 border-b border-border p-6 sm:p-8">
                <span class="icon-tile h-16 w-16 rounded-2xl {{ $isLost ? 'bg-rose-50 text-rose-600' : 'bg-accent text-accent-foreground' }}">
                    <x-site.icon name="phone" class="h-8 w-8" stroke="1.4" />
                </span>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-site.status :status="$hasPendingTransfer ? 'pending' : $device->status" />
                        @if ($hasPendingTransfer)<span class="text-xs text-muted-foreground">Transfer in progress</span>@endif
                    </div>
                    <h1 class="font-display mt-2 text-4xl leading-tight text-foreground">{{ $device->brand }} {{ $device->model }}</h1>
                    @if ($device->storage)<p class="text-sm text-muted-foreground">{{ $device->storage }}</p>@endif
                </div>
            </div>

            <div class="grid gap-px bg-border sm:grid-cols-2">
                <div class="bg-card px-6 py-5 sm:px-8">
                    <p class="eyebrow">{{ $device->identifierLabel() }}</p>
                    <p class="mt-1.5 font-mono text-base font-semibold tracking-wide text-foreground">{{ $device->imei }}</p>
                </div>
                <div class="bg-card px-6 py-5 sm:px-8">
                    <p class="eyebrow">Second IMEI</p>
                    <p class="mt-1.5 font-mono text-base font-semibold tracking-wide {{ $device->imei2 ? 'text-foreground' : 'text-muted-foreground' }}">{{ $device->imei2 ?: 'None' }}</p>
                </div>
                @foreach ($details as $item)
                    <div class="flex items-center gap-3 bg-card px-6 py-5 sm:px-8">
                        <x-site.icon :name="$item['icon']" class="h-5 w-5 text-muted-foreground" />
                        <div>
                            <p class="text-xs text-muted-foreground">{{ $item['label'] }}</p>
                            <p class="text-sm font-semibold text-foreground">{{ $item['value'] }}</p>
                        </div>
                    </div>
                @endforeach
                <div class="flex items-center gap-3 bg-card px-6 py-5 sm:col-span-2 sm:px-8">
                    <x-site.icon name="document" class="h-5 w-5 text-muted-foreground" />
                    <div class="flex flex-1 items-center justify-between gap-3">
                        <div>
                            <p class="text-xs text-muted-foreground">Invoice</p>
                            <p class="text-sm font-semibold text-foreground">{{ $device->invoice_path ? 'Uploaded' : 'Not provided' }}</p>
                        </div>
                        @if ($device->invoice_path)
                            <a href="{{ asset('storage/'.$device->invoice_path) }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm">
                                <x-site.icon name="eye" class="h-4 w-4" /> View
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <aside class="space-y-4">
            <section class="surface p-6">
                <h2 class="text-sm font-bold text-foreground">Actions</h2>
                <div class="mt-4 grid gap-2">
                    @if (! $isLost && ! $hasPendingTransfer)
                        <a href="{{ route('transfer', ['device' => $device->imei]) }}" class="btn btn-secondary justify-start">
                            <x-site.icon name="transfer" class="h-4 w-4 text-amber-600" /> Transfer ownership
                        </a>
                    @endif
                    @if ($isLost)
                        <a href="{{ route('lost-found.index', ['device' => $device->id]) }}" class="btn btn-secondary justify-start">
                            <x-site.icon name="check-circle" class="h-4 w-4 text-emerald-600" /> Mark as found
                        </a>
                    @else
                        <a href="{{ route('lost-found.index', ['device' => $device->id]) }}" class="btn btn-secondary justify-start">
                            <x-site.icon name="alert" class="h-4 w-4 text-rose-600" /> Report lost or stolen
                        </a>
                    @endif
                </div>
                @if ($hasPendingTransfer)
                    <p class="mt-3 text-xs text-muted-foreground">A transfer is pending. You can cancel it from your <a href="{{ route('dashboard') }}" class="font-semibold text-primary hover:underline">dashboard</a>.</p>
                @endif
            </section>

            <section class="surface-muted p-6">
                <div class="flex items-center gap-2 text-sm font-semibold text-foreground">
                    <x-site.icon name="lock" class="h-4 w-4" /> Keep your IMEI private
                </div>
                <p class="mt-2 text-xs leading-relaxed text-muted-foreground">Only share it with people you trust, like a buyer you’re transferring to.</p>
            </section>

            <section class="surface p-6">
                <p class="text-sm font-semibold text-foreground">Need help?</p>
                <p class="mt-1 text-xs text-muted-foreground">Contact support about transfers, lost reports or records.</p>
                <a href="mailto:{{ $settings?->contact_email ?? 'support@drms.gov' }}" class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:underline">
                    Email support <x-site.icon name="arrow-right" class="h-4 w-4" />
                </a>
            </section>
        </aside>
    </div>
</div>
@endsection
