@extends('layouts.app')

@section('content')
<section class="py-12 lg:py-20">
    <div class="container max-w-2xl">
        <a href="{{ route('dashboard') }}" class="mb-8 inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6 6 6" />
            </svg>
            Back to Dashboard
        </a>

        <div class="mb-10 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10">
                <svg class="h-7 w-7 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="6" y="3" width="12" height="18" rx="2" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 17h4" />
                </svg>
            </div>
            <h1 class="mb-3 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">Register Device</h1>
            <p class="text-lg text-muted-foreground">Add a new device to your account for verification and protection</p>
        </div>

        <div class="rounded-2xl border border-border bg-card p-6 shadow-soft sm:p-8">
            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    Please check the form for errors.
                </div>
            @endif
            <form class="space-y-6" method="POST" action="{{ route('devices.store') }}" enctype="multipart/form-data">
                @csrf
                <div>
                    <label class="text-sm font-medium text-foreground">IMEI Number *</label>
                    <input
                        type="text"
                        name="imei"
                        placeholder="Enter 15-digit IMEI"
                        maxlength="15"
                        value="{{ old('imei') }}"
                        class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-base font-mono focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                    />
                    @error('imei')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-muted-foreground">Dial <span class="font-mono font-medium">*#06#</span> on your phone to find your IMEI</p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-foreground">Brand *</label>
                        <select name="brand" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm">
                            <option value="">Select brand</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand }}" @selected(old('brand') === $brand)>{{ $brand }}</option>
                            @endforeach
                        </select>
                        @error('brand')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-foreground">Model *</label>
                        <input
                            type="text"
                            name="model"
                            value="{{ old('model') }}"
                            placeholder="e.g., iPhone 15 Pro"
                            class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm"
                        />
                        @error('model')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-foreground">Device Type *</label>
                    <select name="device_type" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm">
                        <option value="">Select device type</option>
                        @foreach ($deviceTypes as $type)
                            <option value="{{ $type['value'] }}" @selected(old('device_type') === $type['value'])>{{ $type['label'] }}</option>
                        @endforeach
                    </select>
                    @error('device_type')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-sm font-medium text-foreground">Purchase Type *</label>
                    <div class="mt-3 grid gap-4 sm:grid-cols-2">
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-border bg-muted/30 p-4">
                            <input type="radio" name="purchase_type" value="new" class="h-4 w-4" @checked(old('purchase_type', 'new') === 'new') />
                            <div>
                                <p class="font-medium text-foreground">Brand New</p>
                                <p class="text-xs text-muted-foreground">Purchased from authorized seller</p>
                            </div>
                        </label>
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-border bg-muted/30 p-4">
                            <input type="radio" name="purchase_type" value="secondhand" class="h-4 w-4" @checked(old('purchase_type') === 'secondhand') />
                            <div>
                                <p class="font-medium text-foreground">Second-hand</p>
                                <p class="text-xs text-muted-foreground">Ownership confirmation required</p>
                            </div>
                        </label>
                    </div>
                    @error('purchase_type')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-foreground">Purchase Date *</label>
                        <input type="date" name="purchase_date" value="{{ old('purchase_date') }}" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                        @error('purchase_date')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-foreground">Invoice (optional)</label>
                        <input type="file" name="invoice" class="mt-2 w-full rounded-lg border border-border bg-background px-4 py-2 text-sm" />
                        @error('invoice')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">
                    Register Device
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
