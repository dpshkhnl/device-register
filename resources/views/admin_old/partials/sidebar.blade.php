<aside class="hidden w-72 flex-col border-r border-border bg-sidebar text-sidebar-foreground lg:flex">
    <div class="border-b border-sidebar-border p-6">
        <div class="flex items-center gap-3 rounded-xl bg-sidebar-accent/60 px-3 py-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-primary-foreground">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold">DRMS Admin</p>
                <p class="text-xs text-sidebar-foreground/70">Control Center</p>
            </div>
        </div>
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto px-4 py-6">
        <div>
            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-sidebar-foreground/50">Overview</p>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">
                <span>Dashboard</span>
            </a>
        </div>

        <div>
            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-sidebar-foreground/50">Management</p>
            <div class="space-y-1">
                <a href="{{ route('admin.devices') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.devices') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">Devices</a>
                <a href="{{ route('admin.transfers') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.transfers') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">Ownership Transfers</a>
                <a href="{{ route('admin.users') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.users') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">Users</a>
                <a href="{{ route('admin.roles') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.roles') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">Users & Roles</a>
            </div>
        </div>

        <div>
            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-sidebar-foreground/50">Compliance</p>
            <div class="space-y-1">
                <a href="{{ route('admin.imei-logs') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.imei-logs') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">IMEI Logs</a>
                <a href="{{ route('admin.lost-stolen') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.lost-stolen') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">Lost / Stolen</a>
                <a href="{{ route('admin.certificates') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.certificates') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">Certificates</a>
            </div>
        </div>

        <div>
            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-sidebar-foreground/50">Insights</p>
            <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.reports') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">Reports</a>
        </div>

        <div>
            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-sidebar-foreground/50">System</p>
            <div class="space-y-1">
                <a href="{{ route('admin.activity') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.activity') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">Activity Log</a>
                <a href="{{ route('admin.security') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.security') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">Security & Access</a>
                <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.settings') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">Settings</a>
            </div>
        </div>

        <div>
            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-sidebar-foreground/50">Utilities</p>
            <a href="{{ route('admin.tasks') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.tasks') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">Task Queue</a>
        </div>
    </nav>

    <div class="border-t border-sidebar-border p-4 text-xs text-sidebar-foreground/70">
        <p class="font-medium text-sidebar-foreground">Admin User</p>
        <p>admin@drms.gov</p>
    </div>
</aside>
