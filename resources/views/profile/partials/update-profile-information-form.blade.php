<section>
    <header>
        <h2 class="text-lg font-bold text-foreground">Profile information</h2>
        <p class="mt-1 text-sm text-muted-foreground">Update your name and email address.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div>
            <label class="field-label" for="name">Full name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autocomplete="name" class="field-input" />
            <x-site.error :messages="$errors->get('name')" />
        </div>

        <div>
            <label class="field-label" for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="field-input" />
            <x-site.error :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="callout callout-warning mt-3">
                    <x-site.icon name="mail" class="mt-px h-5 w-5 shrink-0" />
                    <p>
                        Your email address is unverified.
                        <button form="send-verification" class="font-semibold underline hover:no-underline">Re-send the verification email</button>
                    </p>
                </div>
            @endif
        </div>

        @if ($user->mobile)
            <div>
                <span class="field-label">Mobile number</span>
                <p class="flex h-12 items-center rounded-xl border border-dashed border-input bg-muted/50 px-4 text-[15px] text-muted-foreground">{{ $user->mobile }}</p>
                <p class="field-help">Used for transfer codes. Contact support to change it.</p>
            </div>
        @endif

        <div class="pt-2">
            <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
    </form>
</section>
