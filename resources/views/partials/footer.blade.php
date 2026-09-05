<footer id="contact" style="background: linear-gradient(180deg, #0a1420 0%, #101c2c 100%);">
    {{-- Top border --}}
    <div class="h-px" style="background: linear-gradient(90deg, transparent 0%, #2dd4c8 30%, #c9973e 70%, transparent 100%); opacity: 0.5;"></div>

    <div class="container py-14 lg:py-20">
        <div class="grid gap-10 lg:grid-cols-4">

            {{-- Brand Column --}}
            <div class="lg:col-span-2">
                @php
                    $appName = $settings?->app_name ?? 'DRMS';
                    $contactEmail = $settings?->contact_email ?? 'support@drms.gov';
                    $contactPhone = $settings?->contact_phone ?? '1-800-DRMS-HELP';
                @endphp
                <a href="{{ route('home') }}" class="mb-5 flex items-center gap-3 group">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full border-2 transition-transform group-hover:scale-105"
                         style="border-color: #2dd4c8; color: #2dd4c8;">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span class="font-display text-xl font-semibold text-white">{{ $appName }}</span>
                </a>

                <p class="mb-6 max-w-md text-sm leading-relaxed text-slate-400">
                    Device Registration & IMEI Management System — a secure, centralized platform for device verification, registration, and ownership management.
                </p>

                <div class="flex flex-col gap-3">
                    <a href="mailto:{{ $contactEmail }}"
                       class="group flex items-center gap-2.5 text-sm text-slate-400 transition-colors hover:text-white">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/5 border border-white/10 transition-colors group-hover:bg-teal-500/20 group-hover:border-teal-400/30">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7l9 6 9-6" />
                                <rect x="3" y="5" width="18" height="14" rx="2" />
                            </svg>
                        </span>
                        {{ $contactEmail }}
                    </a>
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contactPhone) }}"
                       class="group flex items-center gap-2.5 text-sm text-slate-400 transition-colors hover:text-white">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/5 border border-white/10 transition-colors group-hover:bg-teal-500/20 group-hover:border-teal-400/30">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M22 16.5v3a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3.5 5.18 2 2 0 0 1 5.5 3h3a2 2 0 0 1 2 1.72l.5 3a2 2 0 0 1-.57 1.73l-1.2 1.2a16 16 0 0 0 6.6 6.6l1.2-1.2a2 2 0 0 1 1.73-.57l3 .5a2 2 0 0 1 1.72 2z" />
                            </svg>
                        </span>
                        {{ $contactPhone }}
                    </a>
                </div>
            </div>

            {{-- Link Groups --}}
            @php
                $footerGroups = $footerLinks ?? collect();
                if ($footerGroups->isEmpty()) {
                    $footerGroups = collect([
                        'Quick Links' => collect([
                            ['label' => 'Home', 'url' => route('home')],
                            ['label' => 'IMEI Verification', 'url' => route('verification')],
                            ['label' => 'Ownership Transfer', 'url' => route('transfer')],
                            ['label' => 'Register Device', 'url' => route('register-device')],
                        ]),
                        'Legal' => collect([
                            ['label' => 'Privacy Policy', 'url' => '#'],
                            ['label' => 'Terms of Service', 'url' => '#'],
                            ['label' => 'Cookie Policy', 'url' => '#'],
                            ['label' => 'Accessibility', 'url' => '#'],
                        ]),
                    ]);
                }
            @endphp

            @foreach ($footerGroups as $group => $links)
                <div>
                    <h4 class="mb-4 text-sm font-semibold uppercase tracking-wider text-slate-300">{{ $group }}</h4>
                    <ul class="space-y-2.5">
                        @foreach ($links as $link)
                            <li>
                                <a href="{{ $link->url ?? $link['url'] }}"
                                   class="text-sm text-slate-400 transition-colors hover:text-white">
                                    {{ $link->label ?? $link['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        {{-- Bottom Bar --}}
        <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-8 sm:flex-row">
            <p class="text-sm text-slate-500">
                &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
            </p>
            <div class="flex items-center gap-1.5">
                <div class="h-1.5 w-1.5 rounded-full bg-green-400"></div>
                <span class="text-xs text-slate-500">All systems operational</span>
            </div>
        </div>
    </div>
</footer>
