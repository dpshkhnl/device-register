@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100 ' . $class]) }}>
    {{ $slot }}
</div>
