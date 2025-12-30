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

            <x-auth-session-status class="mt-4" :status="session('status')" />

            @if ($errors->any())
                <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    Please check your credentials and try again.
                </div>
            @endif

            <form class="mt-6 space-y-4" method="POST" action="{{ route('login') }}">
                @csrf
                <div>
                    <label class="text-sm font-medium text-foreground" for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm font-medium text-foreground" for="password">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div class="flex items-center justify-between text-sm">
                    <label class="inline-flex items-center gap-2 text-muted-foreground">
                        <input type="checkbox" name="remember" class="h-4 w-4 rounded border-border" />
                        Remember me
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="font-semibold text-primary hover:underline">Forgot password?</a>
                    @endif
                </div>
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">Sign in</button>
            </form>

            <p class="mt-6 text-center text-sm text-muted-foreground">
                Need an account?
                <a href="{{ route('register') }}" class="font-semibold text-primary hover:underline">Create one</a>
            </p>
        </div>
    </div>
</section>
@endsection
