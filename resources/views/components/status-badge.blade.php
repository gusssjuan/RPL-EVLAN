@props([
    'status',
])

@php
    $config = match (strtolower(str_replace(' ', '_', (string) $status))) {
        'belum_dikerjakan' => ['label' => 'Belum dikerjakan', 'icon' => '○', 'variant' => 'neutral'],
        'sedang_dikerjakan' => ['label' => 'Sedang dikerjakan', 'icon' => '◐', 'variant' => 'warning'],
        'sudah_diselesaikan' => ['label' => 'Sudah diselesaikan', 'icon' => '✓', 'variant' => 'success'],
        'terlewat' => ['label' => 'Terlewat', 'icon' => '▲', 'variant' => 'danger'],
        'menunggu' => ['label' => 'Menunggu', 'icon' => '⏳', 'variant' => 'warning'],
        'terverifikasi' => ['label' => 'Terverifikasi', 'icon' => '✓', 'variant' => 'success'],
        'ditolak' => ['label' => 'Ditolak', 'icon' => '✕', 'variant' => 'danger'],
        'aktif' => ['label' => 'Aktif', 'icon' => '●', 'variant' => 'success'],
        'nonaktif' => ['label' => 'Nonaktif', 'icon' => '○', 'variant' => 'neutral'],
        'perencanaan' => ['label' => 'Perencanaan', 'icon' => '📋', 'variant' => 'info'],
        'selesai' => ['label' => 'Selesai', 'icon' => '✓', 'variant' => 'success'],
        default => ['label' => (string) $status, 'icon' => '●', 'variant' => 'neutral'],
    };
@endphp

<x-badge :variant="$config['variant']">
    <span class="font-bold text-[11px] leading-none">{{ $config['icon'] }}</span>
    <span>{{ $config['label'] }}</span>
</x-badge>

