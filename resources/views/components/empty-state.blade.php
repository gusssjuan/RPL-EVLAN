@props([
    'title',
    'description' => null,
    'icon' => 'inbox',
])

<div {{ $attributes->merge(['class' => 'text-center py-12 px-4 rounded-[12px] border-2 border-dashed border-gray-200 bg-gray-50/50']) }}>
    <div class="mx-auto w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-3">
        <x-icon :name="$icon" class="w-6 h-6" />
    </div>
    <h3 class="text-sm font-semibold text-gray-900">{{ $title }}</h3>
    @if ($description)
        <p class="mt-1 text-sm text-gray-500 max-w-sm mx-auto">{{ $description }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-5">
            {{ $slot }}
        </div>
    @endif
</div>

