@props([
    'kategori' => null,
    'name' => null,
    'class' => 'w-5 h-5',
])

@php
    $nama = $name ?? $kategori?->nama_kategori;
    $ikon = $nama ? (config('ikon.kategori')[$nama] ?? null) : null;
    $tersimpan = $kategori?->ikon;

    if (! $ikon && is_string($tersimpan) && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $tersimpan)) {
        $ikon = $tersimpan;
    }

    $ikon ??= config('ikon.kategori_default', 'tag');
@endphp

<x-icon :name="$ikon" :class="$class" />
