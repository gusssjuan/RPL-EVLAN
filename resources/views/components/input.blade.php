@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'required' => false,
    'placeholder' => null,
    'hint' => null,
    'error' => null,
    'showError' => true,
])

@php
    $hasError = $errors->has($name) || $error;
    $describedBy = collect([
        $hint ? $name.'-hint' : null,
        $hasError ? $name.'-error' : null,
    ])->filter()->implode(' ');
@endphp

<div class="w-full">
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-neutral-900">
            {{ $label }}@if ($required)<span class="text-bahaya-600"> *</span>@endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        @if ($required) required @endif
        @if ($hasError) aria-invalid="true" @endif
        @if ($describedBy !== '') aria-describedby="{{ $describedBy }}" @endif
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge([
            'class' => 'block min-h-11 w-full rounded-control border bg-surface px-3.5 py-2.5 text-base text-neutral-900 placeholder:text-neutral-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2 '
                .($hasError ? 'border-bahaya-600' : 'border-neutral-200'),
        ]) }}
    >

    @if ($hint)
        <p id="{{ $name }}-hint" class="mt-1.5 text-[13px] leading-[18px] text-neutral-600">{{ $hint }}</p>
    @endif

    @if ($showError && $hasError)
        <p id="{{ $name }}-error" class="mt-1.5 flex items-start gap-1.5 text-[13px] leading-[18px] text-bahaya-600">
            <x-icon name="circle-alert" class="mt-0.5 h-3.5 w-3.5 shrink-0" />
            <span>{{ $errors->first($name) ?: $error }}</span>
        </p>
    @endif
</div>
