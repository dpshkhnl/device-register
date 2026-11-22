{{-- Left panel shared by sign-in / sign-up. Pass $title, $text, and optionally $points (list) or $steps + $currentStep. --}}
<aside class="relative hidden overflow-hidden rounded-[2rem] bg-primary p-10 text-primary-foreground lg:flex lg:flex-col lg:justify-between">
    <div class="pointer-events-none absolute -right-24 -top-24 h-80 w-80 rounded-full bg-white/10 blur-2xl"></div>
    <div class="pointer-events-none absolute -bottom-32 -left-16 h-96 w-96 rounded-full bg-amber-300/20 blur-3xl"></div>
    <div class="pointer-events-none absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 22px 22px;"></div>

    <div class="relative flex items-center gap-2.5">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/25">
            <x-site.icon name="shield-check" class="h-[18px] w-[18px]" stroke="2" />
        </span>
        <span class="font-display text-[1.6rem] leading-none">{{ $settings?->app_name ?? 'DRMS' }}</span>
    </div>

    <div class="relative">
        <h2 class="font-display text-5xl leading-[1.05]">{{ $title }}</h2>
        <p class="mt-4 max-w-sm text-[15px] leading-relaxed text-white/75">{{ $text }}</p>

        @isset($points)
            <ul class="mt-8 space-y-3">
                @foreach ($points as $point)
                    <li class="flex items-center gap-3 text-sm text-white/90">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-white/15"><x-site.icon name="check" class="h-3.5 w-3.5" stroke="2.6" /></span>
                        {{ $point }}
                    </li>
                @endforeach
            </ul>
        @endisset

        @isset($steps)
            <ol class="mt-10 space-y-5" data-auth-steps>
                @foreach ($steps as $i => $s)
                    @php $n = $i + 1; @endphp
                    <li class="flex items-center gap-4" data-auth-step="{{ $n }}" data-state="{{ $n < $currentStep ? 'done' : ($n === $currentStep ? 'active' : 'idle') }}">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold ring-1 transition
                                     {{ $n < $currentStep ? 'bg-white text-primary ring-white' : ($n === $currentStep ? 'bg-white/20 text-white ring-white/60' : 'bg-transparent text-white/50 ring-white/20') }}">
                            {{ $n < $currentStep ? '✓' : $n }}
                        </span>
                        <div>
                            <p class="text-sm font-semibold {{ $n <= $currentStep ? 'text-white' : 'text-white/55' }}">{{ $s['label'] }}</p>
                            <p class="text-xs {{ $n <= $currentStep ? 'text-white/70' : 'text-white/40' }}">{{ $s['desc'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        @endisset
    </div>

    <p class="relative text-xs text-white/50">Device Registry &amp; Management System</p>
</aside>
