@props(['title', 'description' => null, 'accent' => 'blue'])

@php
    $accentMap = [
        'blue' => 'bg-blue-500',
        'emerald' => 'bg-emerald-500',
        'orange' => 'bg-orange-500',
        'rose' => 'bg-rose-500',
        'purple' => 'bg-purple-500',
    ];
    $dot = $accentMap[$accent] ?? 'bg-blue-500';
@endphp

<div class="relative pl-10">
    <span class="absolute left-3 top-5 h-3 w-3 rounded-full {{ $dot }} shadow"></span>
    <x-ui.card class="p-5">
        <div class="flex items-start gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-50 text-slate-700">
                {{ $icon ?? '' }}
            </div>
            <div>
                <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
                @if ($description)
                    <p class="mt-2 text-sm text-slate-600">{{ $description }}</p>
                @endif
                {{ $slot }}
            </div>
        </div>
    </x-ui.card>
</div>
