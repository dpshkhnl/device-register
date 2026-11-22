@props(['status'])
@php
    [$variant, $label] = match ($status) {
        'active' => ['pill-success', 'Active'],
        'lost' => ['pill-danger', 'Lost / Stolen'],
        'suspicious' => ['pill-warning', 'Suspicious'],
        'transferred' => ['pill-neutral', 'Transferred'],
        'pending' => ['pill-warning', 'Pending'],
        default => ['pill-neutral', ucfirst((string) $status)],
    };
@endphp
<span {{ $attributes->merge(['class' => "pill {$variant}"]) }}>{{ $label }}</span>
