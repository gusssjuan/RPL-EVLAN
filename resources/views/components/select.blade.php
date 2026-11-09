@props([
    'name',
    'label' => null,
    'required' => false,
    'error' => null,
])

<div class="w-full">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-semibold text-gray-800 mb-1.5">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <select
            name="{{ $name }}"
            id="{{ $name }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge([
                'class' => 'w-full px-3.5 py-2.5 bg-white border ' .
                    ($errors->has($name) || $error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-indigo-600 focus:ring-indigo-600') .
                    ' rounded-[8px] text-sm text-gray-900 focus:outline-none focus:ring-1 transition-colors appearance-none pr-10'
            ]) }}
        >
            {{ $slot }}
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
            <x-icon name="chevron-down" class="w-4 h-4" />
        </div>
    </div>

    @error($name)
        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
            <x-icon name="alert-triangle" class="w-3.5 h-3.5 shrink-0" />
            <span>{{ $message }}</span>
        </p>
    @enderror
</div>

