@props([
    'variant' => 'info',
])

@php
    $config = [
        'error' => ['icon' => 'circle-alert', 'class' => 'border-bahaya-600/30 bg-bahaya-50 text-bahaya-600', 'role' => 'alert'],
        'success' => ['icon' => 'circle-check', 'class' => 'border-sukses-600/30 bg-sukses-50 text-sukses-600', 'role' => 'status'],
        'warning' => ['icon' => 'triangle-alert', 'class' => 'border-peringatan-600/30 bg-peringatan-50 text-peringatan-600', 'role' => 'status'],
        'info' => ['icon' => 'info', 'class' => 'border-info-600/30 bg-info-50 text-info-600', 'role' => 'status'],
    ][$variant] ?? ['icon' => 'info', 'class' => 'border-info-600/30 bg-info-50 text-info-600', 'role' => 'status'];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-start gap-2.5 rounded-control border p-3 text-sm ' . $config['class']]) }} role="{{ $config['role'] }}">
    <x-icon :name="$config['icon']" class="mt-0.5 h-4 w-4 shrink-0" />
    <div class="min-w-0">
        {{ $slot }}
    </div>
</div>
