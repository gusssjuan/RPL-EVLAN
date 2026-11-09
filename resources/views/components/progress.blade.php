@props([
    'value' => 0,
    'max' => 100,
    'showLabel' => false,
    'color' => 'indigo',
])

@php
    $percentage = $max > 0 ? min(100, max(0, round(($value / $max) * 100))) : 0;

    $colorClasses = [
        'indigo' => 'bg-indigo-600',
        'green' => 'bg-green-600',
        'amber' => 'bg-amber-500',
        'red' => 'bg-red-600',
    ][$color] ?? 'bg-indigo-600';
@endphp

<div class="w-full">
    @if ($showLabel)
        <div class="flex justify-between items-center mb-1 text-xs font-medium text-gray-700">
            <span>Progres</span>
            <span>{{ $percentage }}%</span>
        </div>
    @endif
    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
        <div
            class="{{ $colorClasses }} h-2.5 rounded-full transition-all duration-500 ease-out"
            style="width: {{ $percentage }}%"
        ></div>
    </div>
</div>

