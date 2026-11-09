@props([
    'variant' => 'neutral',
])

@php
    $variants = [
        'neutral' => 'bg-gray-100 text-gray-700 border-gray-200',
        'primary' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'success' => 'bg-green-50 text-green-700 border-green-200',
        'warning' => 'bg-amber-50 text-amber-700 border-amber-200',
        'danger' => 'bg-red-50 text-red-700 border-red-200',
        'info' => 'bg-blue-50 text-blue-700 border-blue-200',
    ][$variant] ?? 'bg-gray-100 text-gray-700 border-gray-200';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium border {$variants}"]) }}>
    {{ $slot }}
</span>

