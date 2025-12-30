@php
    $notificationCount = auth()->user()?->unreadNotifications()->count() ?? 0;
    $recentNotifications = auth()->user()?->unreadNotifications()->latest()->take(5)->get() ?? collect();
@endphp

<header class="bg-white border-b border-slate-200">
    <div class="flex items-center justify-between px-6 py-4">
        <div>
            <p class="text-xs uppercase tracking-wide text-slate-400">{{ $settings?->app_name ?? 'DRMS' }}</p>
            <p class="text-sm font-semibold text-slate-900">@yield('page_heading', 'Dashboard')</p>
        </div>
        <div class="hidden items-center gap-3 lg:flex">
            <div class="relative">
                <svg viewBox="0 0 24 24" class="h-4 w-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="11" cy="11" r="7" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 20l-3-3" />
                </svg>
                <input
                    type="text"
                    placeholder="Search users, IMEI, devices"
                    class="h-10 w-72 rounded-full border border-slate-200 bg-white pl-10 pr-4 text-sm text-slate-700 placeholder:text-slate-400"
                />
            </div>
        </div>
        <div class="flex items-center gap-3" x-data="{ open: false }">
            <div class="relative">
                <button type="button"
                    class="relative inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 hover:bg-slate-100"
                    @click="open = !open">
                    @if ($notificationCount > 0)
                        <span class="absolute -top-1 -right-1 h-5 w-5 rounded-full bg-rose-500 text-xs text-white flex items-center justify-center">
                            {{ $notificationCount }}
                        </span>
                    @endif
                    <span class="sr-only">Notifications</span>
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0h6Z" />
                    </svg>
                </button>
                <div class="absolute right-0 mt-2 w-80 origin-top-right rounded-2xl border border-slate-200 bg-white shadow-lg z-50"
                    x-show="open" x-transition @click.away="open = false">
                    <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                        <p class="text-sm font-semibold text-slate-800">Notifications</p>
                        <span class="text-xs text-slate-400">{{ $notificationCount }} unread</span>
                    </div>
                    <div class="max-h-72 overflow-y-auto">
                        @forelse ($recentNotifications as $notification)
                            <div class="border-b border-slate-100 px-4 py-3">
                                <p class="text-sm font-semibold text-slate-800">{{ $notification->data['title'] ?? 'Update' }}</p>
                                <p class="text-xs text-slate-500 mt-1">{{ $notification->data['message'] ?? '—' }}</p>
                                <p class="text-[11px] text-slate-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                        @empty
                            <div class="px-4 py-6 text-center text-sm text-slate-500">
                                No notifications yet.
                            </div>
                        @endforelse
                    </div>
                    <div class="border-t border-slate-200 px-4 py-3 text-right">
                        <a href="{{ route('admin.activity-logs.index') }}"
                            class="text-xs font-semibold text-[color:var(--brand-600)] hover:underline">
                            View activity
                        </a>
                    </div>
                </div>
            </div>
            <div class="hidden items-center gap-3 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 md:flex">
                <div class="h-8 w-8 rounded-full bg-[color:var(--brand-100)] text-[color:var(--brand-600)] flex items-center justify-center text-xs font-semibold">
                    {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                </div>
                <div class="text-left text-xs">
                    <p class="font-semibold text-slate-900">{{ auth()->user()->name ?? 'Admin User' }}</p>
                    <p class="text-slate-500">{{ auth()->user()->email ?? auth()->user()->mobile }}</p>
                </div>
                <span class="rounded-full bg-slate-200 px-2 py-1 text-[11px] font-semibold text-slate-600">Admin</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9l3 3-3 3m3-3H3" />
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>
