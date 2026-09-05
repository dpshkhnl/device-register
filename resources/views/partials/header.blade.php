<header
    x-data="{ open: false, scrolled: false }"
    x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 16 })"
    :class="scrolled ? 'bg-white/90 dark:bg-slate-950/90 shadow-soft' : 'bg-white/60 dark:bg-slate-950/60'"
    class="sticky top-0 z-50 w-full border-b border-border backdrop-blur-xl transition-all duration-300"
>
    <div class="container flex h-16 items-center justify-between">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
            <div class="flex h-9 w-9 items-center justify-center rounded-full border-2 transition-transform duration-200 group-hover:scale-105"
                 style="border-color: #0e7c86; color: #0e7c86;">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            @php $appName = $settings?->app_name ?? 'DRMS'; @endphp
            <span class="font-display text-xl font-semibold tracking-tight text-foreground">{{ $appName }}</span>
        </a>

        {{-- Desktop Nav --}}
        <nav class="hidden items-center gap-1 md:flex">
            @php
                $navLinks = [
                    ['label' => 'Home', 'route' => 'home'],
                    ['label' => 'IMEI Verify', 'route' => 'verification'],
                    ['label' => 'Pricing', 'route' => 'packages.index'],
                    ['label' => 'Transfer', 'route' => 'transfer'],
                    ['label' => 'Lost & Found', 'route' => 'lost-found.index'],
                ];
            @endphp
            @foreach ($navLinks as $link)
                <a href="{{ route($link['route']) }}"
                   class="relative px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground group">
                    {{ $link['label'] }}
                    <span class="absolute bottom-0 left-3 right-3 h-0.5 rounded-full bg-primary scale-x-0 transition-transform duration-200 group-hover:scale-x-100 origin-left"></span>
                </a>
            @endforeach
            @auth
                <a href="{{ route('dashboard') }}"
                   class="relative px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground group">
                    Dashboard
                    <span class="absolute bottom-0 left-3 right-3 h-0.5 rounded-full bg-primary scale-x-0 transition-transform duration-200 group-hover:scale-x-100 origin-left"></span>
                </a>
            @endauth
        </nav>

        {{-- Desktop Actions --}}
        <div class="hidden items-center gap-2 md:flex">
            @auth
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-semibold text-foreground transition-colors hover:bg-muted">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>
                    </svg>
                    Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-border px-4 py-2 text-sm font-semibold text-foreground transition-all hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                        Log out
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-semibold text-foreground transition-colors hover:bg-muted">
                    Sign in
                </a>
                <a href="{{ route('register') }}"
                   class="inline-flex items-center justify-center gap-1.5 rounded-lg px-4 py-2 text-sm font-semibold text-white shadow-soft transition-all hover:shadow-md hover:-translate-y-px"
                   style="background: #0e7c86;">
                    Get Started
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/>
                    </svg>
                </a>
            @endauth
        </div>

        {{-- Mobile Hamburger --}}
        <button
            type="button"
            @click="open = !open"
            class="flex h-10 w-10 items-center justify-center rounded-lg text-foreground hover:bg-muted md:hidden transition-colors"
        >
            <svg x-show="!open" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <svg x-show="open" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Mobile Menu --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="border-t border-border bg-card md:hidden"
    >
        <nav class="container flex flex-col gap-0.5 py-3">
            @foreach ($navLinks as $link)
                <a href="{{ route($link['route']) }}"
                   class="rounded-lg px-4 py-2.5 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted hover:text-foreground">
                    {{ $link['label'] }}
                </a>
            @endforeach
            @auth
                <a href="{{ route('dashboard') }}"
                   class="rounded-lg px-4 py-2.5 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted hover:text-foreground">
                    Dashboard
                </a>
            @endauth
        </nav>
        <div class="container flex flex-col gap-2 border-t border-border py-3">
            @auth
                <a href="{{ route('dashboard') }}"
                   class="inline-flex w-full items-center justify-center rounded-xl border border-border px-4 py-2.5 text-sm font-semibold text-foreground hover:bg-muted transition-colors">
                    Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl border border-border px-4 py-2.5 text-sm font-semibold text-foreground hover:bg-muted transition-colors">
                        Log out
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                   class="inline-flex w-full items-center justify-center rounded-xl border border-border px-4 py-2.5 text-sm font-semibold text-foreground hover:bg-muted transition-colors">
                    Sign in
                </a>
                <a href="{{ route('register') }}"
                   class="inline-flex w-full items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition-all"
                   style="background: #0e7c86;">
                    Get Started
                </a>
            @endauth
        </div>
    </div>
</header>
