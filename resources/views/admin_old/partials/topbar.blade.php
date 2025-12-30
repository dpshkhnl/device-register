<header class="border-b border-border bg-card">
    <div class="flex items-center justify-between px-6 py-4">
        <div>
            <p class="text-xs uppercase tracking-wide text-muted-foreground">DRMS</p>
            <p class="text-sm font-semibold text-foreground">@yield('page_heading', 'Dashboard')</p>
        </div>
        <div class="hidden items-center gap-3 lg:flex">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="11" cy="11" r="7" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 20l-3-3" />
                </svg>
                <input type="text" placeholder="Search users, IMEI, devices" class="h-10 w-64 rounded-full border border-border bg-background pl-10 pr-4 text-sm" />
            </div>
            <button class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-border bg-background text-muted-foreground">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0h6Z" />
                </svg>
            </button>
            <div class="flex items-center gap-2 rounded-full border border-border bg-muted/40 px-3 py-1.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-xs font-semibold text-primary">AD</div>
                <div class="text-left text-xs">
                    <p class="font-semibold text-foreground">Admin User</p>
                    <p class="text-muted-foreground">admin@drms.gov</p>
                </div>
                <span class="rounded-full bg-muted px-2 py-1 text-[11px] font-semibold text-muted-foreground">Admin</span>
            </div>
            <button class="inline-flex items-center gap-2 rounded-full border border-border bg-background px-4 py-2 text-sm font-medium text-muted-foreground">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9l3 3-3 3m3-3H3" />
                </svg>
                Logout
            </button>
        </div>
    </div>
</header>
