@extends('layouts.app')

@section('content')
<section class="py-12 lg:py-16">
    <div class="container max-w-5xl">
        <div class="grid overflow-hidden rounded-3xl border border-border bg-card shadow-soft lg:grid-cols-2">
            <div class="relative hidden bg-muted/40 p-10 lg:block">
                <div class="absolute inset-0 bg-gradient-to-br from-primary/20 via-transparent to-transparent"></div>
                <div class="relative z-10 flex h-full flex-col justify-between">
                    <div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
                            <svg class="h-6 w-6 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                            </svg>
                        </div>
                        <h1 class="mt-6 text-3xl font-bold text-foreground">Create your account</h1>
                        <p class="mt-3 text-sm text-muted-foreground">Register a device, transfer ownership, and download certificates.</p>
                    </div>
                    <img src="/images/auth-register.svg" alt="Register illustration" class="mt-10 w-full max-w-sm" />
                </div>
            </div>
            <div class="p-6 sm:p-10">
                <div class="space-y-2">
                    <h2 class="text-2xl font-semibold text-foreground">Sign up</h2>
                    <p class="text-sm text-muted-foreground">Create your DRMS profile to get started.</p>
                </div>

                @if ($errors->any())
                    <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        Please check the form for errors.
                    </div>
                @endif

                <form class="mt-6 space-y-4" method="POST" action="{{ route('register') }}">
                    @csrf
                    <div>
                        <label class="text-sm font-medium text-foreground" for="name">Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-foreground" for="mobile">Mobile Number</label>
                        <input id="mobile" type="text" name="mobile" value="{{ old('mobile') }}" required autocomplete="tel" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                        <x-input-error :messages="$errors->get('mobile')" class="mt-2" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-foreground" for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-foreground" for="password">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-foreground" for="password_confirmation">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="mt-2 h-12 w-full rounded-lg border border-border bg-background px-4 text-sm" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">Create account</button>
                </form>

                <p class="mt-6 text-center text-sm text-muted-foreground">
                    Already registered?
                    <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</section>
@endsection
