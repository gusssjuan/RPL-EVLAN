@props([
    'padding' => 'p-6',
])

<div {{ $attributes->merge(['class' => "bg-white border border-gray-200 rounded-[12px] shadow-sm {$padding}"]) }}>
    {{ $slot }}
</div>

