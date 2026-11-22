@extends('layouts.app')

@section('title', 'Reset password')

@section('content')
<div class="container flex min-h-[70vh] items-center justify-center py-14">
    <div class="surface w-full max-w-md p-8 sm:p-10">
        <span class="icon-tile h-12 w-12 rounded-2xl bg-accent text-accent-foreground"><x-site.icon name="lock" class="h-6 w-6" /></span>
        <h1 class="font-display mt-6 text-4xl text-foreground">Forgot your password?</h1>
        <p class="mt-2 text-sm text-muted-foreground">Enter your account email and we’ll send you a link to choose a new one.</p>

        <form class="mt-8 space-y-5" method="POST" action="{{ route('password.email') }}">
            @csrf
            <div>
                <label class="field-label" for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="name@example.com" class="field-input" />
                <x-site.error :messages="$errors->get('email')" />
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-full">Email me a reset link</button>
        </form>

        <p class="mt-8 text-center text-sm text-muted-foreground">
            Remembered it? <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">Back to sign in</a>
        </p>
    </div>
</div>
@endsection
