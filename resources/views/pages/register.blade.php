@extends('layouts.app')

@section('content')
<section class="py-12 lg:py-16">
    <div class="container max-w-2xl">
        <div class="rounded-2xl border border-border bg-card p-6 shadow-soft sm:p-10">
            <div class="space-y-2">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
                    <svg class="h-6 w-6 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-semibold text-foreground">Create your DRMS account</h1>
                <p class="text-sm text-muted-foreground">Register with your mobile number to access device registration and transfer tools.</p>
            </div>

            <form class="mt-6 space-y-5">
                <div>
                    <label class="text-sm font-medium text-foreground">Full name</label>
                    <input type="text" placeholder="Enter your full name" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                </div>
                <div>
                    <label class="text-sm font-medium text-foreground">Mobile number</label>
                    <input type="text" placeholder="Enter mobile number" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-foreground">Password</label>
                        <input type="password" placeholder="Create password" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-foreground">Confirm password</label>
                        <input type="password" placeholder="Re-enter password" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium text-foreground">Account type</label>
                    <select class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm">
                        <option>Registered User</option>
                        <option>Shop / Reseller</option>
                    </select>
                </div>

                <div class="rounded-xl border border-border bg-muted/30 p-4">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-foreground">Mobile OTP verification</label>
                        <button type="button" class="text-xs font-semibold text-primary">Send OTP</button>
                    </div>
                    <input type="text" placeholder="Enter 6-digit OTP" class="mt-3 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                    <p class="mt-2 text-xs text-muted-foreground">Demo OTP: <span class="font-semibold text-foreground">123456</span></p>
                </div>

                <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">Create account</button>
            </form>

            <p class="mt-6 text-center text-sm text-muted-foreground">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">Sign in</a>
            </p>
        </div>
    </div>
</section>
@endsection
