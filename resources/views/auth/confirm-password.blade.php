@extends('layouts.app')

@section('title', 'Confirm password')

@section('content')
<div class="container flex min-h-[70vh] items-center justify-center py-14">
    <div class="surface w-full max-w-md p-8 sm:p-10">
        <span class="icon-tile h-12 w-12 rounded-2xl bg-accent text-accent-foreground"><x-site.icon name="shield-check" class="h-6 w-6" /></span>
        <h1 class="font-display mt-6 text-4xl text-foreground">Confirm it’s you</h1>
        <p class="mt-2 text-sm text-muted-foreground">This is a secure area. Please confirm your password to continue.</p>

        <form class="mt-8 space-y-5" method="POST" action="{{ route('password.confirm') }}">
            @csrf
            <div>
                <label class="field-label" for="password">Password</label>
                <input id="password" type="password" name="password" required autofocus autocomplete="current-password" class="field-input" />
                <x-site.error :messages="$errors->get('password')" />
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-full">Confirm</button>
        </form>
    </div>
</div>
@endsection
