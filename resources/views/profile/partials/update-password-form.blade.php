<section>
    <header>
        <h2 class="text-lg font-bold text-foreground">Change password</h2>
        <p class="mt-1 text-sm text-muted-foreground">Use a long, unique password to keep your account secure.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div>
            <label class="field-label" for="update_password_current_password">Current password</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" class="field-input" />
            <x-site.error :messages="$errors->updatePassword->get('current_password')" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="field-label" for="update_password_password">New password</label>
                <input id="update_password_password" name="password" type="password" autocomplete="new-password" class="field-input" />
                <x-site.error :messages="$errors->updatePassword->get('password')" />
            </div>
            <div>
                <label class="field-label" for="update_password_password_confirmation">Confirm new password</label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="field-input" />
                <x-site.error :messages="$errors->updatePassword->get('password_confirmation')" />
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="btn btn-primary">Update password</button>
        </div>
    </form>
</section>
