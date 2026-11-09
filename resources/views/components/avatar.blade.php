@props([
    'name',
    'size' => 'md',
])

@php
    $words = explode(' ', trim($name));
    $initials = '';
    if (count($words) >= 2) {
        $initials = mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1);
    } else {
        $initials = mb_substr($name, 0, 2);
    }
    $initials = strtoupper($initials);

    $sizeClasses = [
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-10 h-10 text-sm font-semibold',
        'lg' => 'w-12 h-12 text-base font-bold',
    ][$size] ?? 'w-10 h-10 text-sm font-semibold';

    // Consistent color hash based on name
    $colors = [
        'bg-indigo-100 text-indigo-700',
        'bg-purple-100 text-purple-700',
        'bg-pink-100 text-pink-700',
        'bg-amber-100 text-amber-700',
        'bg-emerald-100 text-emerald-700',
        'bg-blue-100 text-blue-700',
    ];
    $colorIndex = abs(crc32($name)) % count($colors);
    $colorClass = $colors[$colorIndex];
@endphp

<div {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-full shrink-0 select-none {$sizeClasses} {$colorClass}"]) }}>
    {{ $initials }}
</div>

