@php
    $appName = $settings?->app_name ?? 'DRMS';
    $user = auth()->user();
    $navLinks = $user
        ? [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard|devices.show'],
            ['label' => 'Check IMEI', 'route' => 'verification', 'active' => 'verification'],
            ['label' => 'Transfer', 'route' => 'transfer', 'active' => 'transfer'],
            ['label' => 'Lost & Found', 'route' => 'lost-found.index', 'active' => 'lost-found.*'],
            ['label' => 'Plans', 'route' => 'packages.index', 'active' => 'packages.*'],
        ]
        : [
            ['label' => 'Check IMEI', 'route' => 'verification', 'active' => 'verification'],
            ['label' => 'How it works', 'url' => route('home').'#how-it-works', 'active' => null],
            ['label' => 'Plans', 'route' => 'packages.index', 'active' => 'packages.*'],
        ];
    $isActive = fn ($link) => $link['active'] && request()->routeIs(...explode('|', $link['active']));
@endphp

<header
    x-data="{ open: false, scrolled: window.scrollY > 8 }"
    @scroll.window="scrolled = window.scrollY > 8"
    @keydown.escape.window="open = false"
    :class="scrolled ? 'bg-background/85 shadow-[0_1px_0_hsl(var(--border)),0_8px_24px_-16px_rgba(20,22,40,0.18)]' : 'bg-background/60'"
    class="sticky top-0 z-50 w-full backdrop-blur-xl transition-all duration-300"
>
    <div class="container flex h-16 items-center justify-between gap-6">
        {{-- Brand --}}
        <a href="{{ route('home') }}" class="group flex shrink-0 items-center gap-2.5" aria-label="{{ $appName }} home">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary text-primary-foreground shadow-[0_6px_16px_-6px_hsl(var(--primary)/0.7)] transition-transform duration-200 group-hover:-rotate-6">
                <x-site.icon name="shield-check" class="h-[18px] w-[18px]" stroke="2" />
            </span>
            <span class="font-display text-[1.6rem] leading-none text-foreground">{{ $appName }}</span>
        </a>

        {{-- Desktop nav --}}
        <nav class="hidden items-center gap-0.5 lg:flex" aria-label="Main">
            @foreach ($navLinks as $link)
                <a href="{{ $link['url'] ?? route($link['route']) }}" class="nav-link" @if ($isActive($link)) aria-current="page" @endif>
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Actions --}}
        <div class="flex items-center gap-2">
            @auth
                <a href="{{ route('register-device') }}" class="btn btn-primary btn-sm hidden sm:inline-flex">
                    <x-site.icon name="plus" class="h-4 w-4" stroke="2.2" />
                    Register device
                </a>

                <div class="relative hidden md:block" x-data="{ menu: false }" @click.outside="menu = false">
                    <button type="button" @click="menu = !menu" :aria-expanded="menu"
                            class="flex items-center gap-2 rounded-full border border-border bg-card py-1 pl-1 pr-2.5 text-sm font-semibold text-foreground transition hover:bg-muted">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-accent text-xs font-bold text-accent-foreground">
                            {{ strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}
                        </span>
                        <span class="max-w-[8rem] truncate">{{ \Illuminate\Support\Str::of($user->name ?? 'Account')->explode(' ')->first() }}</span>
                        <x-site.icon name="chevron-down" class="h-3.5 w-3.5 text-muted-foreground" stroke="2" />
                    </button>
                    <div x-show="menu" x-cloak
                         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute right-0 mt-2 w-64 overflow-hidden rounded-2xl border border-border bg-card p-1.5 shadow-[0_20px_40px_-16px_rgba(20,22,40,0.25)]">
                        <div class="px-3 py-2.5">
                            <p class="truncate text-sm font-semibold text-foreground">{{ $user->name }}</p>
                            <p class="truncate text-xs text-muted-foreground">{{ $user->email ?: $user->mobile }}</p>
                        </div>
                        <div class="my-1 h-px bg-border"></div>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm text-foreground hover:bg-muted">
                            <x-site.icon name="user" class="h-4 w-4 text-muted-foreground" /> Profile
                        </a>
                        <a href="{{ route('profile.edit') }}#kyc" class="flex items-center justify-between gap-2.5 rounded-xl px-3 py-2 text-sm text-foreground hover:bg-muted">
                            <span class="flex items-center gap-2.5"><x-site.icon name="id-card" class="h-4 w-4 text-muted-foreground" /> KYC verification</span>
                            @unless ($user->hasKyc())
                                <span class="pill pill-warning pill-plain">Required</span>
                            @endunless
                        </a>
                        <a href="{{ route('packages.index') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm text-foreground hover:bg-muted">
                            <x-site.icon name="tag" class="h-4 w-4 text-muted-foreground" /> Plans & usage
                        </a>
                        <div class="my-1 h-px bg-border"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2 text-left text-sm text-rose-600 hover:bg-rose-50">
                                <x-site.icon name="logout" class="h-4 w-4" /> Log out
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm hidden sm:inline-flex">Sign in</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                    Get started
                    <x-site.icon name="arrow-right" class="h-3.5 w-3.5" stroke="2.2" />
                </a>
            @endauth

            <button type="button" @click="open = !open" :aria-expanded="open" aria-label="Toggle menu"
                    class="flex h-10 w-10 items-center justify-center rounded-xl text-foreground transition hover:bg-muted lg:hidden">
                <x-site.icon name="menu" x-show="!open" />
                <x-site.icon name="x" x-show="open" x-cloak />
            </button>
        </div>
    </div>

    {{-- Mobile / tablet menu --}}
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
         class="border-t border-border bg-card lg:hidden">
        <nav class="container grid gap-1 py-3" aria-label="Mobile">
            @foreach ($navLinks as $link)
                <a href="{{ $link['url'] ?? route($link['route']) }}" @click="open = false"
                   class="rounded-xl px-4 py-3 text-[15px] font-medium {{ $isActive($link) ? 'bg-accent text-accent-foreground' : 'text-foreground hover:bg-muted' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
            @auth
                <a href="{{ route('profile.edit') }}" class="rounded-xl px-4 py-3 text-[15px] font-medium text-foreground hover:bg-muted">Profile & KYC</a>
            @endauth
        </nav>
        <div class="container grid gap-2 border-t border-border py-4">
            @auth
                <a href="{{ route('register-device') }}" class="btn btn-primary w-full">
                    <x-site.icon name="plus" class="h-4 w-4" stroke="2.2" /> Register device
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary w-full">Log out</button>
                </form>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary w-full">Create free account</a>
                <a href="{{ route('login') }}" class="btn btn-secondary w-full">Sign in</a>
            @endauth
        </div>
    </div>
</header>
