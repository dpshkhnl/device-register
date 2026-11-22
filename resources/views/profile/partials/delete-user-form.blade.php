<section x-data="{ confirming: @js($errors->userDeletion->isNotEmpty()) }">
    <header>
        <h2 class="text-lg font-bold text-foreground">Delete account</h2>
        <p class="mt-1 text-sm text-muted-foreground">
            Once your account is deleted, all of its data is permanently removed. Download anything you want to keep first.
        </p>
    </header>

    <button type="button" x-show="!confirming" @click="confirming = true; $nextTick(() => $refs.password.focus())" class="btn btn-secondary mt-6 text-rose-600 hover:bg-rose-50">
        <x-site.icon name="trash" class="h-4 w-4" /> Delete my account
    </button>

    <form x-show="confirming" x-cloak method="post" action="{{ route('profile.destroy') }}" class="mt-6 rounded-2xl border border-rose-200 bg-rose-50/60 p-5">
        @csrf
        @method('delete')

        <p class="text-sm font-semibold text-rose-800">Are you sure? This can’t be undone.</p>
        <p class="mt-1 text-sm text-rose-700/80">Enter your password to permanently delete your account.</p>

        <div class="mt-4">
            <label for="password" class="sr-only">Password</label>
            <input id="password" name="password" type="password" x-ref="password" placeholder="Password" autocomplete="current-password" class="field-input max-w-sm" />
            <x-site.error :messages="$errors->userDeletion->get('password')" />
        </div>

        <div class="mt-5 flex flex-wrap gap-2">
            <button type="submit" class="btn btn-danger">Delete account permanently</button>
            <button type="button" @click="confirming = false" class="btn btn-secondary">Cancel</button>
        </div>
    </form>
</section>
