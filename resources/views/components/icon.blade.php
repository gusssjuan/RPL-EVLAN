@props([
    'name' => 'circle',
    'size' => 'md',
    'label' => null,
    'class' => '',
])

@php
    $aliases = [
        'alert-triangle' => 'triangle-alert',
        'alert-circle' => 'circle-alert',
        'check-circle' => 'circle-check',
        'check-square' => 'square-check',
        'store' => 'store',
        'lock' => 'lock',
    ];

    $sizes = [
        'xs' => 'w-3 h-3',
        'sm' => 'w-3.5 h-3.5',
        'md' => 'w-4 h-4',
        'lg' => 'w-5 h-5',
        'xl' => 'w-6 h-6',
        '2xl' => 'w-8 h-8',
        '3xl' => 'w-10 h-10',
    ];

    $resolvedName = $aliases[$name] ?? $name;
    $iconClass = trim(($class !== '' ? $class : ($sizes[$size] ?? 'w-5 h-5')).' icon');
    $attributes = $attributes->getAttributes();
@endphp

@if ($label)
    <span role="img" aria-label="{{ $label }}" class="inline-flex shrink-0">
        {{ svg('lucide-'.$resolvedName, $iconClass, $attributes + ['aria-hidden' => 'true']) }}
    </span>
@else
    {{ svg('lucide-'.$resolvedName, $iconClass, $attributes + ['aria-hidden' => 'true']) }}
@endif
