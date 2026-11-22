{{-- Thumb-reach bottom bar for signed-in users on small screens. --}}
@php
    $tabs = [
        ['label' => 'Home', 'icon' => 'grid', 'route' => 'dashboard', 'active' => ['dashboard', 'devices.show']],
        ['label' => 'Check', 'icon' => 'search', 'route' => 'verification', 'active' => ['verification']],
        ['label' => 'Register', 'icon' => 'plus', 'route' => 'register-device', 'active' => ['register-device', 'devices.create'], 'primary' => true],
        ['label' => 'Transfer', 'icon' => 'transfer', 'route' => 'transfer', 'active' => ['transfer']],
        ['label' => 'Lost', 'icon' => 'alert', 'route' => 'lost-found.index', 'active' => ['lost-found.*']],
    ];
@endphp
<nav class="fixed inset-x-0 bottom-0 z-40 border-t border-border bg-card/95 backdrop-blur-xl pb-safe md:hidden" aria-label="Quick actions">
    <div class="mx-auto grid max-w-md grid-cols-5 px-2">
        @foreach ($tabs as $tab)
            @php $on = request()->routeIs(...$tab['active']); @endphp
            <a href="{{ route($tab['route']) }}" @if ($on) aria-current="page" @endif
               class="flex flex-col items-center gap-1 py-2 text-[11px] font-semibold {{ $on ? 'text-primary' : 'text-muted-foreground' }}">
                @if (! empty($tab['primary']))
                    <span class="-mt-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-primary text-primary-foreground shadow-[0_10px_20px_-8px_hsl(var(--primary)/0.8)] ring-4 ring-background">
                        <x-site.icon :name="$tab['icon']" class="h-5 w-5" stroke="2.2" />
                    </span>
                @else
                    <span class="flex h-7 w-10 items-center justify-center rounded-full {{ $on ? 'bg-accent' : '' }}">
                        <x-site.icon :name="$tab['icon']" class="h-5 w-5" />
                    </span>
                @endif
                {{ $tab['label'] }}
            </a>
        @endforeach
    </div>
</nav>
