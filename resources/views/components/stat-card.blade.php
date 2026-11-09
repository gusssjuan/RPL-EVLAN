@props([
    'title',
    'value',
    'subtext' => null,
    'icon' => null,
    'iconBg' => 'bg-indigo-50 text-indigo-600',
])

<x-card class="relative overflow-hidden">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $title }}</p>
            <p class="mt-1.5 text-2xl font-bold text-gray-900 tracking-tight">{{ $value }}</p>
            @if ($subtext)
                <p class="mt-1 text-xs text-gray-500">{{ $subtext }}</p>
            @endif
        </div>
        @if ($icon)
            <div class="w-12 h-12 rounded-[10px] flex items-center justify-center {{ $iconBg }} shrink-0">
                <x-icon :name="$icon" class="w-6 h-6" />
            </div>
        @endif
    </div>
</x-card>

