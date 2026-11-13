@props([
    'name',
    'label' => null,
    'required' => false,
    'error' => null,
])

@php
    $hasError = $errors->has($name) || $error;
@endphp

<div class="w-full">
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-neutral-900">
            {{ $label }}@if ($required)<span class="text-bahaya-600"> *</span>@endif
        </label>
    @endif

    <div class="relative">
        <select
            name="{{ $name }}"
            id="{{ $name }}"
            @if ($required) required @endif
            @if ($hasError) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
            {{ $attributes->merge([
                'class' => 'block min-h-11 w-full appearance-none rounded-control border bg-surface px-3.5 py-2.5 pr-10 text-base text-neutral-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2 '
                    .($hasError ? 'border-bahaya-600' : 'border-neutral-200'),
            ]) }}
        >
            {{ $slot }}
        </select>
        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-neutral-500">
            <x-icon name="chevron-down" class="h-4 w-4" />
        </span>
    </div>

    @if ($hasError)
        <p id="{{ $name }}-error" class="mt-1.5 flex items-start gap-1.5 text-[13px] leading-[18px] text-bahaya-600">
            <x-icon name="circle-alert" class="mt-0.5 h-3.5 w-3.5 shrink-0" />
            <span>{{ $errors->first($name) ?: $error }}</span>
        </p>
    @endif
</div>
