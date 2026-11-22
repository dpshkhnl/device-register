@props(['messages'])
@if ($messages)
    @foreach ((array) $messages as $message)
        <p {{ $attributes->merge(['class' => 'field-error']) }}>{{ $message }}</p>
    @endforeach
@endif
