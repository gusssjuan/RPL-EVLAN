@props([
    'status',
])

@php
    $config = match (strtolower(str_replace(' ', '_', (string) $status))) {
        'belum_dikerjakan' => ['label' => 'Belum dikerjakan', 'icon' => 'circle', 'variant' => 'neutral'],
        'sedang_dikerjakan' => ['label' => 'Sedang dikerjakan', 'icon' => 'circle-dashed', 'variant' => 'warning'],
        'sudah_diselesaikan' => ['label' => 'Sudah diselesaikan', 'icon' => 'check', 'variant' => 'success'],
        'terlewat' => ['label' => 'Terlewat', 'icon' => 'triangle-alert', 'variant' => 'danger'],
        'menunggu' => ['label' => 'Menunggu', 'icon' => 'clock', 'variant' => 'warning'],
        'diterima' => ['label' => 'Diterima', 'icon' => 'circle-check', 'variant' => 'success'],
        'terverifikasi' => ['label' => 'Terverifikasi', 'icon' => 'badge-check', 'variant' => 'success'],
        'ditolak' => ['label' => 'Ditolak', 'icon' => 'x', 'variant' => 'danger'],
        'aktif' => ['label' => 'Aktif', 'icon' => 'check', 'variant' => 'success'],
        'nonaktif' => ['label' => 'Nonaktif', 'icon' => 'ban', 'variant' => 'neutral'],
        'perencanaan' => ['label' => 'Perencanaan', 'icon' => 'clipboard-list', 'variant' => 'info'],
        'selesai' => ['label' => 'Selesai', 'icon' => 'flag', 'variant' => 'success'],
        default => ['label' => (string) $status, 'icon' => 'circle', 'variant' => 'neutral'],
    };
@endphp

<x-badge :variant="$config['variant']">
    <x-icon :name="$config['icon']" class="w-3.5 h-3.5 shrink-0" />
    <span>{{ $config['label'] }}</span>
</x-badge>

