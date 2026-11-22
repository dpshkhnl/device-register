{{-- One place for success messages after a redirect. Some controllers flash short codes, so map them to sentences. --}}
@php
    $flash = session('status');
    $flash = match ($flash) {
        'profile-updated' => 'Your profile has been saved.',
        'password-updated' => 'Your password has been updated.',
        'kyc-submitted' => 'KYC submitted. You can transfer devices and report lost devices while we review it.',
        'verification-link-sent' => 'A new verification link has been sent to your email address.',
        default => $flash,
    };
@endphp

@if ($flash)
    <div class="container pt-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 9000)"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="callout callout-success shadow-[0_8px_24px_-16px_rgba(5,120,80,0.5)]" role="status" aria-live="polite">
            <x-site.icon name="check-circle" class="mt-px h-5 w-5 shrink-0 text-emerald-600" />
            <p class="flex-1 font-medium">{{ $flash }}</p>
            <button type="button" @click="show = false" class="-m-1 rounded-lg p-1 text-emerald-700/70 hover:bg-emerald-100 hover:text-emerald-800" aria-label="Dismiss">
                <x-site.icon name="x" class="h-4 w-4" />
            </button>
        </div>
    </div>
@endif
