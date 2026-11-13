@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 rounded-control';

    $sizeClasses = [
        'sm' => 'min-h-9 px-3 py-1.5 text-sm gap-1.5',
        'md' => 'min-h-11 px-4 py-2.5 text-sm gap-2',
        'lg' => 'min-h-11 px-5 py-2.5 text-base gap-2.5',
    ][$size] ?? 'min-h-11 px-4 py-2.5 text-sm gap-2';

    $variantClasses = [
        'primary' => 'bg-primary-600 text-white hover:bg-primary-700',
        'secondary' => 'bg-surface border border-neutral-200 text-neutral-900 hover:bg-neutral-100',
        'light' => 'bg-surface text-primary-700 hover:bg-neutral-100',
        'danger' => 'bg-bahaya-600 text-white hover:bg-bahaya-600/90',
        'link' => 'bg-transparent text-primary-600 hover:text-primary-700 hover:underline p-0 min-h-0 focus-visible:ring-offset-0',
    ][$variant] ?? 'bg-primary-600 text-white hover:bg-primary-700';
@endphp

<button
    type="{{ $type }}"
    @if ($disabled) disabled @endif
    {{ $attributes->merge(['class' => "{$baseClasses} {$sizeClasses} {$variantClasses}"]) }}
>
    {{ $slot }}
</button>
