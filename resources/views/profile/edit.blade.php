@php
    // Open the tab that needs attention after a redirect; a #hash in the URL wins on the client.
    $initialTab = match (true) {
        $errors->kyc->isNotEmpty(), session()->has('kyc_required'), session('status') === 'kyc-submitted' => 'kyc',
        $errors->updatePassword->isNotEmpty(), session('status') === 'password-updated' => 'password',
        $errors->userDeletion->isNotEmpty() => 'account',
        default => 'profile',
    };
    $tabs = [
        'profile' => ['label' => 'Profile', 'icon' => 'user', 'hint' => 'Name and email'],
        'kyc' => ['label' => 'KYC verification', 'icon' => 'id-card', 'hint' => $user->kycStatusLabel()],
        'password' => ['label' => 'Password', 'icon' => 'lock', 'hint' => 'Change your password'],
        'account' => ['label' => 'Delete account', 'icon' => 'trash', 'hint' => 'Permanently remove'],
    ];
    $kycNeedsAction = ! $user->hasKyc();
@endphp

<x-app-layout>
    <div class="container max-w-5xl py-10 lg:py-12"
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
         }">
        <div class="mb-8 flex items-center gap-4">
            <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-accent text-xl font-bold text-accent-foreground">
                {{ strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}
            </span>
            <div>
                <p class="eyebrow">Account</p>
                <h1 class="font-display text-4xl leading-tight text-foreground">{{ $user->name }}</h1>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[15rem,1fr]">
            <nav class="-mx-4 flex gap-2 overflow-x-auto px-4 lg:mx-0 lg:flex-col lg:gap-1 lg:overflow-visible lg:px-0" role="tablist" aria-label="Account sections">
                @foreach ($tabs as $name => $t)
                    <button type="button" role="tab" @click="select('{{ $name }}')" :aria-selected="tab === '{{ $name }}'"
                            :class="tab === '{{ $name }}' ? 'bg-card text-foreground shadow-[0_1px_2px_rgba(20,22,40,.06),0_4px_12px_-4px_rgba(20,22,40,.1)] ring-1 ring-border' : 'text-muted-foreground hover:bg-card/60 hover:text-foreground'"
                            class="flex shrink-0 items-center gap-3 whitespace-nowrap rounded-xl px-3.5 py-2.5 text-left text-sm font-semibold transition lg:w-full">
                        <x-site.icon :name="$t['icon']" class="h-4 w-4 {{ $name === 'account' ? 'text-rose-500' : '' }}" />
                        <span class="flex-1">
                            {{ $t['label'] }}
                            <span class="hidden text-xs font-normal text-muted-foreground lg:block">{{ $t['hint'] }}</span>
                        </span>
                        @if ($name === 'kyc' && $kycNeedsAction)
                            <span class="h-2 w-2 rounded-full bg-amber-500" title="KYC required"></span>
                        @endif
                    </button>
                @endforeach
            </nav>

            <div>
                @foreach ([
                    'profile' => 'profile.partials.update-profile-information-form',
                    'kyc' => 'profile.partials.update-kyc-form',
                    'password' => 'profile.partials.update-password-form',
                    'account' => 'profile.partials.delete-user-form',
                ] as $name => $partial)
                    <div role="tabpanel" x-show="tab === '{{ $name }}'" @style(['display: none' => $name !== $initialTab]) class="surface p-6 sm:p-8">
                        <div class="max-w-xl">
                            @include($partial)
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
