@extends('layouts.app')

@section('content')
<section class="py-12 lg:py-20">
    <div class="container max-w-3xl">
        <a href="{{ route('home') }}" class="mb-8 inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6 6 6" />
            </svg>
            Back to Home
        </a>

        <div class="mb-10 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10">
                <svg class="h-7 w-7 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="11" cy="11" r="7" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 20l-3-3" />
                </svg>
            </div>
            <h1 class="mb-3 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">IMEI Verification</h1>
            <p class="text-lg text-muted-foreground">Enter a 15-digit IMEI number to check device status and ownership</p>
        </div>

        <form class="mb-10">
            <div class="rounded-2xl border border-border bg-card p-6 shadow-soft sm:p-8">
                <label for="imei" class="mb-2 block text-sm font-medium text-foreground">IMEI Number</label>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-1">
                        <svg class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="6" y="3" width="12" height="18" rx="2" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 17h4" />
                        </svg>
                        <input
                            id="imei"
                            name="imei"
                            type="text"
                            placeholder="Enter 15-digit IMEI"
                            class="h-14 w-full rounded-xl border border-border bg-background pl-12 text-lg font-mono shadow-soft focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            maxlength="15"
                        />
                    </div>
                    <button type="submit" class="inline-flex h-14 items-center justify-center gap-2 rounded-xl bg-primary px-6 text-base font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">
                        Verify IMEI
                    </button>
                </div>
                <p class="mt-3 text-sm text-muted-foreground">Enter the 15-digit IMEI number found on your device packaging.</p>
            </div>
        </form>

        <div class="rounded-2xl border border-border bg-card p-6 shadow-soft sm:p-8">
            <div class="mb-6 flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-success/10">
                    <svg class="h-6 w-6 text-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-foreground">Device Found</h2>
                    <p class="text-sm text-muted-foreground">Verified on Dec 20, 2024</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between rounded-lg bg-muted/50 px-4 py-3">
                    <span class="text-sm text-muted-foreground">IMEI</span>
                    <span class="font-mono text-sm font-medium text-foreground">3534********2345</span>
                </div>
                <div class="flex items-center justify-between rounded-lg bg-muted/50 px-4 py-3">
                    <span class="text-sm text-muted-foreground">Device</span>
                    <span class="text-sm font-medium text-foreground">Apple iPhone 15 Pro</span>
                </div>
                <div class="flex items-center justify-between rounded-lg bg-muted/50 px-4 py-3">
                    <span class="text-sm text-muted-foreground">Owner</span>
                    <span class="text-sm font-medium text-foreground">J*** D***</span>
                </div>
                <div class="flex items-center justify-between rounded-lg bg-muted/50 px-4 py-3">
                    <span class="text-sm text-muted-foreground">Status</span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-success/10 px-3 py-1 text-xs font-semibold text-success">Active</span>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
