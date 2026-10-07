@php
    // Open the tab that needs attention after a redirect; a #hash in the URL wins on the client.
    $initialTab = match (true) {
        $errors->kyc->isNotEmpty(), session()->has('kyc_required'), session('status') === 'kyc-submitted' => 'kyc',
        $errors->updatePassword->isNotEmpty(), session('status') === 'password-updated' => 'password',
        $errors->userDeletion->isNotEmpty() => 'account',
        default => 'profile',
    };
    $tabs = [
        'profile' => 'Profile',
        'kyc' => 'KYC',
        'password' => 'Password',
        'account' => 'Delete Account',
    ];
    $kycNeedsAction = ! $user->hasKyc();
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div
        class="py-12"
        x-data="{
            tabs: @js(array_keys($tabs)),
            tab: @js($initialTab),
            init() {
                this.fromHash();
                window.addEventListener('hashchange', () => this.fromHash());
            },
            fromHash() {
                const hash = window.location.hash.slice(1);
                if (this.tabs.includes(hash)) this.tab = hash;
            },
            select(name) {
                this.tab = name;
                history.replaceState(null, '', '#' + name);
            },
        }"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="-mx-4 overflow-x-auto px-4 sm:mx-0 sm:px-0">
                <nav class="inline-flex min-w-full gap-1 rounded-xl bg-white p-1 shadow sm:min-w-0" role="tablist">
                    @foreach ($tabs as $name => $label)
                        <button
                            type="button"
                            role="tab"
                            @click="select('{{ $name }}')"
                            :aria-selected="tab === '{{ $name }}'"
                            :class="tab === '{{ $name }}' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100'"
                            class="relative flex-1 whitespace-nowrap rounded-lg px-4 py-2 text-sm font-medium transition sm:flex-none"
                        >
                            {{ $label }}
                            @if ($name === 'kyc' && $kycNeedsAction)
                                <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-rose-500" title="KYC required"></span>
                            @endif
                        </button>
                    @endforeach
                </nav>
            </div>

            @foreach ([
                'profile' => 'profile.partials.update-profile-information-form',
                'kyc' => 'profile.partials.update-kyc-form',
                'password' => 'profile.partials.update-password-form',
                'account' => 'profile.partials.delete-user-form',
            ] as $name => $partial)
                <div
                    role="tabpanel"
                    x-show="tab === '{{ $name }}'"
                    @style(['display: none' => $name !== $initialTab])
                    class="p-4 sm:p-8 bg-white shadow sm:rounded-lg"
                >
                    <div class="max-w-xl">
                        @include($partial)
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
