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
                <h1 class="text-2xl font-semibold text-foreground">Welcome back</h1>
                <p class="text-sm text-muted-foreground">Sign in to manage your registered devices, transfers, and certificates.</p>
            </div>

            <div class="mt-6 space-y-6">
                <div class="rounded-xl border border-border bg-muted/30 p-4 text-sm text-muted-foreground">
                    <p class="font-semibold text-foreground">Demo accounts</p>
                    <p>User: 7000000000 / User1234</p>
                    <p>Shop: 7111111111 / Shop1234</p>
                    <p>Admin: 7222222222 / Admin1234</p>
                </div>

                <form class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-foreground">Mobile number</label>
                        <input type="text" placeholder="Enter mobile number" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-foreground">Password</label>
                        <input type="password" placeholder="Enter password" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                    </div>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">Sign in</button>
                </form>

                <p class="text-center text-sm text-muted-foreground">
                    Need an account?
                    <a href="{{ route('register') }}" class="font-semibold text-primary hover:underline">Create one</a>
                </p>
            </div>
        </div>
    </div>
</section>
@endsection
