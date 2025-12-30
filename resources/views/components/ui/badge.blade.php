@props(['status' => 'active', 'label' => null])

@php
    $map = [
        'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
        'transferred' => 'bg-blue-50 text-blue-700 ring-blue-100',
        'lost' => 'bg-rose-50 text-rose-700 ring-rose-100',
        'suspicious' => 'bg-orange-50 text-orange-700 ring-orange-100',
    ];
    $classes = $map[$status] ?? 'bg-slate-100 text-slate-700 ring-slate-200';
    $text = $label ?? ucwords(str_replace('_', ' ', $status));
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset ' . $classes]) }}>
    {{ $text }}
</span>
