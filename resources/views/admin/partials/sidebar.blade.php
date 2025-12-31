<aside class="w-64 bg-white border-r border-slate-200 hidden lg:flex flex-col">
    <div class="p-6 border-b border-slate-200">
        <div class="flex flex-col items-center text-center gap-3">
            <div class="h-14 w-14 rounded-2xl bg-[color:var(--brand-100)] text-[color:var(--brand-600)] flex items-center justify-center text-lg font-semibold">
                {{ strtoupper(substr($settings?->app_name ?? 'DRMS', 0, 1)) }}
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-900">{{ $settings?->app_name ?? 'DRMS' }}</p>
                <p class="text-xs text-slate-500">Device Registry</p>
                <p class="text-xs text-slate-400">Admin Portal</p>
            </div>
        </div>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-6">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-3">Overview</p>
            <div class="space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-[color:var(--brand-100)] text-[color:var(--brand-600)]' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="h-5 w-5 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 3l9 7.5v9a1.5 1.5 0 0 1-1.5 1.5H6A1.5 1.5 0 0 1 4.5 19.5v-9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 22V12h6v10"/>
                    </svg>
                    Dashboard
                </a>
            </div>
        </div>

        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-3">Management</p>
            <div class="space-y-1">
                <a href="{{ route('admin.devices.index') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.devices.*') ? 'bg-[color:var(--brand-100)] text-[color:var(--brand-600)]' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="h-5 w-5 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="6" y="3" width="12" height="18" rx="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 17h4"/>
                    </svg>
                    Devices
                </a>
                <a href="{{ route('admin.transfers.index') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.transfers.*') ? 'bg-[color:var(--brand-100)] text-[color:var(--brand-600)]' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="h-5 w-5 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10m0 0-3-3m3 3-3 3M17 17H7m0 0 3 3m-3-3 3-3"/>
                    </svg>
                    Ownership Transfers
                </a>
                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-[color:var(--brand-100)] text-[color:var(--brand-600)]' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="h-5 w-5 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 20a8 8 0 0 1 16 0"/>
                    </svg>
                    Users
                </a>
                <a href="{{ route('admin.packages.index') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.packages.*') ? 'bg-[color:var(--brand-100)] text-[color:var(--brand-600)]' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="h-5 w-5 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8 4-8-4"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7l8-4 8 4v10l-8 4-8-4V7z"/>
                    </svg>
                    Packages
                </a>
                <a href="{{ route('admin.roles.index') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.roles.*') ? 'bg-[color:var(--brand-100)] text-[color:var(--brand-600)]' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="h-5 w-5 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 16v-2m8-6h-2M6 12H4m12.36-6.36-1.41 1.41M9.05 14.95l-1.41 1.41m0-10.6 1.41 1.41m7.9 7.9 1.41 1.41"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    Users & Roles
                </a>
            </div>
        </div>

        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-3">Compliance</p>
            <div class="space-y-1">
                <a href="{{ route('admin.imei-logs.index') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.imei-logs.*') ? 'bg-[color:var(--brand-100)] text-[color:var(--brand-600)]' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="h-5 w-5 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 13l2 2 4-4"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 4h12a2 2 0 0 1 2 2v14l-4-2-4 2-4-2-4 2V6a2 2 0 0 1 2-2z"/>
                    </svg>
                    IMEI Logs
                </a>
                <a href="{{ route('admin.lost-stolen.index') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.lost-stolen.*') ? 'bg-[color:var(--brand-100)] text-[color:var(--brand-600)]' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="h-5 w-5 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M5 5l14 14M12 3a9 9 0 1 1 0 18 9 9 0 0 1 0-18z"/>
                    </svg>
                    Lost / Stolen
                </a>
                <a href="{{ route('admin.certificates.index') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.certificates.*') ? 'bg-[color:var(--brand-100)] text-[color:var(--brand-600)]' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="h-5 w-5 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 4h7l4 4v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 4v4h4"/>
                    </svg>
                    Certificates
                </a>
            </div>
        </div>

        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-3">Insights</p>
            <div class="space-y-1">
                <a href="{{ route('admin.reports.index') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.reports.*') ? 'bg-[color:var(--brand-100)] text-[color:var(--brand-600)]' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="h-5 w-5 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18v6H3z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 9v12m5-12v12m5-12v12"/>
                    </svg>
                    Reports
                </a>
            </div>
        </div>

        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-3">Frontend</p>
            <div class="space-y-1">
                <a href="{{ route('admin.settings.edit') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.settings.*') ? 'bg-[color:var(--brand-100)] text-[color:var(--brand-600)]' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="h-5 w-5 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a7.98 7.98 0 0 0 .1-2 7.98 7.98 0 0 0-.1-2l2.06-1.6-2-3.46-2.42.98a8.08 8.08 0 0 0-3.46-2l-.37-2.6h-4l-.37 2.6a8.08 8.08 0 0 0-3.46 2l-2.42-.98-2 3.46L4.5 11a7.98 7.98 0 0 0-.1 2 7.98 7.98 0 0 0 .1 2l-2.06 1.6 2 3.46 2.42-.98a8.08 8.08 0 0 0 3.46 2l.37 2.6h4l.37-2.6a8.08 8.08 0 0 0 3.46-2l2.42.98 2-3.46L19.4 15z"/>
                    </svg>
                    Hero & CTA
                </a>
                <a href="{{ route('admin.home-banners.index') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.home-banners.*') ? 'bg-[color:var(--brand-100)] text-[color:var(--brand-600)]' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="h-5 w-5 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h16" />
                    </svg>
                    Banner Slider
                </a>
                <a href="{{ route('admin.home-features.index') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.home-features.*') ? 'bg-[color:var(--brand-100)] text-[color:var(--brand-600)]' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="h-5 w-5 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                    </svg>
                    Features
                </a>
                <a href="{{ route('admin.home-steps.index') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.home-steps.*') ? 'bg-[color:var(--brand-100)] text-[color:var(--brand-600)]' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="h-5 w-5 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10" />
                    </svg>
                    Steps
                </a>
                <a href="{{ route('admin.home-stats.index') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.home-stats.*') ? 'bg-[color:var(--brand-100)] text-[color:var(--brand-600)]' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="h-5 w-5 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18v6H3z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 9v12m5-12v12m5-12v12"/>
                    </svg>
                    Stats
                </a>
                <a href="{{ route('admin.testimonials.index') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.testimonials.*') ? 'bg-[color:var(--brand-100)] text-[color:var(--brand-600)]' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="h-5 w-5 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 9h8M8 13h6M5 5h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H7l-4 3V7a2 2 0 0 1 2-2z" />
                    </svg>
                    Testimonials
                </a>
                <a href="{{ route('admin.footer-links.index') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.footer-links.*') ? 'bg-[color:var(--brand-100)] text-[color:var(--brand-600)]' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="h-5 w-5 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    Footer Links
                </a>
            </div>
        </div>

        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-3">System</p>
            <div class="space-y-1">
                <a href="{{ route('admin.activity-logs.index') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.activity-logs.*') ? 'bg-[color:var(--brand-100)] text-[color:var(--brand-600)]' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="h-5 w-5 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5h6m-6 4h6m-8 4h10m-10 4h10"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 3h12a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
                    </svg>
                    Activity Log
                </a>
                <a href="{{ route('admin.service-areas.index') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.service-areas.*') ? 'bg-[color:var(--brand-100)] text-[color:var(--brand-600)]' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="h-5 w-5 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l6 3v6c0 5-4 8-6 9-2-1-6-4-6-9V6l6-3z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 11h6m-6 4h4"/>
                    </svg>
                    Service Areas
                </a>
                <a href="{{ route('admin.security.index') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.security.*') ? 'bg-[color:var(--brand-100)] text-[color:var(--brand-600)]' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="h-5 w-5 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/>
                    </svg>
                    Security & Access
                </a>
            </div>
        </div>

        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-3">Utilities</p>
            <div class="space-y-1">
                <a href="{{ route('admin.tasks.index') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.tasks.*') ? 'bg-[color:var(--brand-100)] text-[color:var(--brand-600)]' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="h-5 w-5 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5h10M9 12h10M9 19h10"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 6h.01M5 12h.01M5 18h.01"/>
                    </svg>
                    Task Queue
                </a>
            </div>
        </div>
    </nav>

    <div class="border-t border-slate-200 p-4 text-xs text-slate-500">
        <p class="font-medium text-slate-700">{{ auth()->user()->name ?? 'Admin User' }}</p>
        <p>{{ auth()->user()->email ?? auth()->user()->mobile }}</p>
    </div>
</aside>
