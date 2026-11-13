@props([
    'variant' => 'purple',
    'size' => 'md',
])

@php
    $isWhite = $variant === 'white';
    $source = $isWhite ? asset('images/logo/white.svg') : asset('images/logo/purple.svg');
    $intrinsicWidth = $isWhite ? 159 : 264;
    $intrinsicHeight = $isWhite ? 158 : 262;
    $imageHeight = $size === 'lg' ? 'h-9' : 'h-7';
    $wordmarkColor = $isWhite ? 'text-white' : 'text-neutral-900';
    $wordmarkSize = $size === 'lg' ? 'text-xl' : 'text-lg';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2']) }}>
    <img
        src="{{ $source }}"
        width="{{ $intrinsicWidth }}"
        height="{{ $intrinsicHeight }}"
        alt="EVLAN"
        decoding="async"
        class="{{ $imageHeight }} w-auto shrink-0"
    >
    <span aria-hidden="true" class="font-display {{ $wordmarkSize }} font-semibold leading-none {{ $wordmarkColor }}">EVLAN</span>
</span>
