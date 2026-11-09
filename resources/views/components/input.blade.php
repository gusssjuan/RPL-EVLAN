@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'required' => false,
    'placeholder' => null,
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
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            {{ $required ? 'required' : '' }}
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge([
                'class' => 'w-full px-3.5 py-2.5 bg-white border ' .
                    ($errors->has($name) || $error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-indigo-600 focus:ring-indigo-600') .
                    ' rounded-[8px] text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 transition-colors'
            ]) }}
        >
    </div>

    @error($name)
        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
            <x-icon name="alert-triangle" class="w-3.5 h-3.5 shrink-0" />
            <span>{{ $message }}</span>
        </p>
    @else
        @if ($error)
            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                <x-icon name="alert-triangle" class="w-3.5 h-3.5 shrink-0" />
                <span>{{ $error }}</span>
            </p>
        @endif
    @enderror
</div>

