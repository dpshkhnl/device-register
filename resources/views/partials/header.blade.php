<header class="sticky top-0 z-50 w-full border-b border-border bg-card/95 backdrop-blur supports-[backdrop-filter]:bg-card/80">
    <div class="container flex h-16 items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary">
                <svg class="h-5 w-5 text-primary-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                </svg>
            </div>
            <span class="text-xl font-bold text-foreground">DRMS</span>
        </a>

        <nav class="hidden items-center gap-8 md:flex">
            <a href="{{ route('home') }}" class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">Home</a>
            <a href="{{ route('verification') }}" class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">IMEI Verification</a>
            <a href="{{ route('transfer') }}" class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">Transfer</a>
            @auth
                <a href="{{ route('dashboard') }}" class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">Dashboard</a>
            @endauth
        </nav>

        <div class="hidden items-center gap-3 md:flex">
            @auth
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-semibold text-foreground hover:bg-muted">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">Log out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-semibold text-foreground hover:bg-muted">Login</a>
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">Sign Up</a>
            @endauth
        </div>

        <details class="group md:hidden">
            <summary class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-lg text-foreground hover:bg-muted">
                <svg class="h-5 w-5 group-open:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg class="hidden h-5 w-5 group-open:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </summary>
            <div class="border-t border-border bg-card">
                <nav class="container flex flex-col gap-1 py-4">
                    <a href="{{ route('home') }}" class="rounded-lg px-4 py-2.5 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted hover:text-foreground">Home</a>
                    <a href="{{ route('verification') }}" class="rounded-lg px-4 py-2.5 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted hover:text-foreground">IMEI Verification</a>
                    <a href="{{ route('transfer') }}" class="rounded-lg px-4 py-2.5 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted hover:text-foreground">Transfer</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-lg px-4 py-2.5 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted hover:text-foreground">Dashboard</a>
                    @endauth
                    <div class="mt-4 flex flex-col gap-2 border-t border-border pt-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex w-full items-center justify-center rounded-lg border border-border px-4 py-2 text-sm font-semibold text-foreground">Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground">Log out</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex w-full items-center justify-center rounded-lg border border-border px-4 py-2 text-sm font-semibold text-foreground">Login</a>
                            <a href="{{ route('register') }}" class="inline-flex w-full items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground">Sign Up</a>
                        @endauth
                    </div>
                </nav>
            </div>
        </details>
    </div>
</header>
