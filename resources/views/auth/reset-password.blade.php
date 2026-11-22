@extends('layouts.app')

@section('title', 'Choose a new password')

@section('content')
<div class="container flex min-h-[70vh] items-center justify-center py-14">
    <div class="surface w-full max-w-md p-8 sm:p-10">
        <span class="icon-tile h-12 w-12 rounded-2xl bg-accent text-accent-foreground"><x-site.icon name="lock" class="h-6 w-6" /></span>
        <h1 class="font-display mt-6 text-4xl text-foreground">Choose a new password</h1>
        <p class="mt-2 text-sm text-muted-foreground">Pick something long and unique.</p>

        <form class="mt-8 space-y-5" method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <div>
                <label class="field-label" for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" class="field-input" />
                <x-site.error :messages="$errors->get('email')" />
            </div>
            <div>
                <label class="field-label" for="password">New password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" class="field-input" />
                <x-site.error :messages="$errors->get('password')" />
            </div>
            <div>
                <label class="field-label" for="password_confirmation">Confirm new password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="field-input" />
                <x-site.error :messages="$errors->get('password_confirmation')" />
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-full">Reset password</button>
        </form>
    </div>
</div>
@endsection
