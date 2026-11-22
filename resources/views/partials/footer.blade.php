@php
    $appName = $settings?->app_name ?? 'DRMS';
    $contactEmail = $settings?->contact_email ?? 'support@drms.gov';
    $contactPhone = $settings?->contact_phone ?? '1-800-DRMS-HELP';

    $footerGroups = $footerLinks ?? collect();
    if ($footerGroups->isEmpty()) {
        $footerGroups = collect([
            'Services' => collect([
                ['label' => 'Check an IMEI', 'url' => route('verification')],
                ['label' => 'Register a device', 'url' => route('register-device')],
                ['label' => 'Transfer ownership', 'url' => route('transfer')],
                ['label' => 'Report lost or stolen', 'url' => route('lost-found.index')],
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

<footer id="contact" class="mt-16 border-t border-border bg-card">
    <div class="container py-14">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-2">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary text-primary-foreground">
                        <x-site.icon name="shield-check" class="h-[18px] w-[18px]" stroke="2" />
                    </span>
                    <span class="font-display text-[1.6rem] leading-none text-foreground">{{ $appName }}</span>
                </a>
                <p class="mt-4 max-w-sm text-sm leading-relaxed text-muted-foreground">
                    The national registry for verifying, registering and protecting mobile devices — so every phone has a known, rightful owner.
                </p>
                <div class="mt-6 flex flex-col gap-2.5 text-sm sm:flex-row sm:gap-6">
                    <a href="mailto:{{ $contactEmail }}" class="inline-flex items-center gap-2 text-muted-foreground transition hover:text-foreground">
                        <x-site.icon name="mail" class="h-4 w-4" /> {{ $contactEmail }}
                    </a>
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contactPhone) }}" class="inline-flex items-center gap-2 text-muted-foreground transition hover:text-foreground">
                        <x-site.icon name="phone-call" class="h-4 w-4" /> {{ $contactPhone }}
                    </a>
                </div>
            </div>

            @foreach ($footerGroups as $group => $links)
                <div>
                    <h4 class="eyebrow mb-4">{{ $group }}</h4>
                    <ul class="space-y-2.5">
                        @foreach ($links as $link)
                            <li>
                                <a href="{{ $link->url ?? $link['url'] }}" class="text-sm text-foreground/80 transition hover:text-primary">
                                    {{ $link->label ?? $link['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        <div class="mt-12 flex flex-col items-start justify-between gap-3 border-t border-border pt-6 text-xs text-muted-foreground sm:flex-row sm:items-center">
            <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
            <p class="inline-flex items-center gap-2">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                All systems operational
            </p>
        </div>
    </div>
</footer>
