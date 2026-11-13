@props([
    'name',
    'label' => null,
    'value' => null,
    'required' => false,
    'placeholder' => null,
    'hint' => null,
])

@php
    $hasError = $errors->has($name);
    $describedBy = collect([
        $hint ? $name.'-hint' : null,
        $hasError ? $name.'-error' : null,
    ])->filter()->implode(' ');
@endphp

<div class="w-full" x-data="{ terlihat: false }">
    @if ($label)
        <div class="mb-1.5 flex items-baseline justify-between gap-3">
            <label for="{{ $name }}" class="block text-sm font-medium text-neutral-900">
                {{ $label }}@if ($required)<span class="text-bahaya-600"> *</span>@endif
            </label>
            {{ $labelRight ?? '' }}
        </div>
    @endif

    <div class="relative">
        <input
            type="password"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            x-bind:type="terlihat ? 'text' : 'password'"
            @if ($required) required @endif
            @if ($hasError) aria-invalid="true" @endif
            @if ($describedBy !== '') aria-describedby="{{ $describedBy }}" @endif
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge([
                'class' => 'block min-h-11 w-full rounded-control border bg-surface px-3.5 py-2.5 pr-12 text-base text-neutral-900 placeholder:text-neutral-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2 '
                    .($hasError ? 'border-bahaya-600' : 'border-neutral-200'),
            ]) }}
        >

        <button
            type="button"
            x-on:click="terlihat = ! terlihat"
            x-bind:aria-pressed="terlihat"
            x-bind:aria-label="terlihat ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
            aria-label="Tampilkan kata sandi"
            aria-pressed="false"
            class="absolute inset-y-0 right-0 flex w-11 items-center justify-center rounded-r-control text-neutral-500 transition-colors hover:text-neutral-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2"
        >
            <x-icon name="eye" class="h-5 w-5" x-show="! terlihat" />
            <x-icon name="eye-off" class="h-5 w-5" x-show="terlihat" x-cloak />
        </button>
    </div>

    @if ($hint)
        <p id="{{ $name }}-hint" class="mt-1.5 text-[13px] leading-[18px] text-neutral-600">{{ $hint }}</p>
    @endif

    @if ($hasError)
        <p id="{{ $name }}-error" class="mt-1.5 flex items-start gap-1.5 text-[13px] leading-[18px] text-bahaya-600">
            <x-icon name="circle-alert" class="mt-0.5 h-3.5 w-3.5 shrink-0" />
            <span>{{ $errors->first($name) }}</span>
        </p>
    @endif
</div>
