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

        <div class="mb-10 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10">
                <svg class="h-7 w-7 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10m0 0-3-3m3 3-3 3M17 17H7m0 0 3 3m-3-3 3-3" />
                </svg>
            </div>
            <h1 class="mb-3 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">Ownership Transfer</h1>
            <p class="text-lg text-muted-foreground">Securely transfer device ownership with OTP confirmation</p>
        </div>

        <div class="rounded-2xl border border-border bg-card p-6 shadow-soft sm:p-8">
            <div class="mb-6 grid gap-3 sm:grid-cols-4">
                @foreach ([
                    ['step' => '1', 'label' => 'Select Device'],
                    ['step' => '2', 'label' => 'New Owner'],
                    ['step' => '3', 'label' => 'Verify OTP'],
                    ['step' => '4', 'label' => 'Confirm'],
                ] as $item)
                    <div class="flex flex-col items-center gap-2 rounded-xl border border-border bg-muted/40 px-3 py-4 text-center">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-xs font-semibold text-primary-foreground">{{ $item['step'] }}</span>
                        <span class="text-xs font-medium text-muted-foreground">{{ $item['label'] }}</span>
                    </div>
                @endforeach
            </div>

            <div class="space-y-6">
                <div>
                    <label class="text-sm font-medium text-foreground">Select Device</label>
                    <select class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm">
                        <option>Apple iPhone 15 Pro (353456789012345)</option>
                        <option>Samsung Galaxy S24 Ultra (861234567890123)</option>
                        <option>Google Pixel 8 Pro (490154203237518)</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium text-foreground">New Owner Mobile Number</label>
                    <input type="text" placeholder="Enter mobile number" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                </div>

                <div>
                    <label class="text-sm font-medium text-foreground">OTP Verification</label>
                    <div class="mt-2 flex flex-wrap gap-3">
                        <input type="text" placeholder="Enter 6-digit OTP" class="h-12 flex-1 rounded-lg border border-border bg-background px-4 text-sm" />
                        <button class="inline-flex h-12 items-center justify-center rounded-lg border border-border px-4 text-sm font-semibold text-foreground">Send OTP</button>
                    </div>
                </div>

                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">
                    Initiate Transfer
                </button>
            </div>
        </div>
    </div>
</section>
@endsection
