@extends('layouts.app')

@section('title', 'Verify your email')

@section('content')
<div class="container flex min-h-[70vh] items-center justify-center py-14">
    <div class="surface w-full max-w-md p-8 text-center sm:p-10">
        <span class="icon-tile mx-auto h-14 w-14 rounded-2xl bg-accent text-accent-foreground"><x-site.icon name="mail" class="h-7 w-7" /></span>
        <h1 class="font-display mt-6 text-4xl text-foreground">Check your inbox</h1>
        <p class="mt-3 text-sm leading-relaxed text-muted-foreground">
            Thanks for signing up! Please verify your email by clicking the link we just sent you. Didn’t get it? We can send another.
        </p>

        <div class="mt-8 flex flex-col gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary w-full">Resend verification email</button>
            </form>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-ghost w-full">Log out</button>
            </form>
        </div>
    </div>
</div>
@endsection
