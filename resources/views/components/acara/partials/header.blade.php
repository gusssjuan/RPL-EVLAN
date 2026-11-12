@props(['event', 'activeTab' => 'ringkasan'])

@php
    $isLeader = $event->isLeader(auth()->user());
@endphp

<div class="space-y-4">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between text-xs text-gray-500">
        <a href="{{ route('acara.index') }}" class="inline-flex items-center gap-1.5 hover:text-indigo-600 transition-colors font-medium">
            <x-icon name="chevron-left" class="w-4 h-4" />
            <span>Kembali ke Daftar Acara</span>
        </a>

        @if ($isLeader)
            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800">
                Anda Ketua Acara
            </span>
        @else
            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700">
                Anda Anggota Panitia
            </span>
        @endif
    </div>

    <!-- Main Header Card -->
    <div class="bg-white p-6 rounded-[16px] border border-gray-200 shadow-sm space-y-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 rounded-full text-xs font-semibold">
                        <x-category-icon :kategori="$event->category" class="w-3.5 h-3.5" />
                        {{ $event->category?->nama_kategori }}
                    </span>
                    <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 rounded-full text-xs font-bold">
                        {{ $event->hari_menuju }}
                    </span>
                    @if ($event->is_publik)
                        <span class="px-2 py-0.5 bg-purple-50 text-purple-700 rounded-full text-[10px] font-semibold">
                            Publik
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900 mt-2">
                    {{ $event->nama_event }}
                </h1>

                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500 mt-1.5">
                    <span class="inline-flex items-center gap-1">
                        <x-icon name="calendar" class="w-3.5 h-3.5" />
                        {{ $event->tanggal->isoFormat('D MMMM Y') }}
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <x-icon name="map-pin" class="w-3.5 h-3.5" />
                        {{ $event->lokasi }}
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <x-icon name="users" class="w-3.5 h-3.5" />
                        {{ number_format($event->jumlah_peserta, 0, ',', '.') }} Peserta
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <x-icon name="crown" class="w-3.5 h-3.5" />
                        Ketua: {{ $event->leader?->nama ?? '-' }}
                    </span>
                </div>
            </div>

            @if ($isLeader)
                <div class="flex items-center gap-2 shrink-0">
                    <a
                        href="{{ route('acara.edit', $event->id) }}"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-[8px] text-xs font-semibold shadow-sm transition-colors"
                    >
                        <x-icon name="edit" class="w-3.5 h-3.5" />
                        <span>Edit Detail Acara</span>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Tabbed Navigation Bar -->
    <div class="bg-white rounded-[12px] border border-gray-200 shadow-sm overflow-x-auto">
        <nav class="flex divide-x divide-gray-100 whitespace-nowrap text-sm font-semibold">
            <a
                href="{{ route('acara.ringkasan', $event->id) }}"
                class="px-5 py-3 transition-colors {{ $activeTab === 'ringkasan' ? 'text-indigo-600 bg-indigo-50/40 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
            >
                Ringkasan
            </a>

            <a
                href="{{ route('acara.checklist.index', $event->id) }}"
                class="px-5 py-3 transition-colors {{ $activeTab === 'checklist' ? 'text-indigo-600 bg-indigo-50/40 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
            >
                Checklist Kebutuhan
            </a>

            <a
                href="{{ route('acara.timeline.index', $event->id) }}"
                class="px-5 py-3 transition-colors {{ $activeTab === 'timeline' ? 'text-indigo-600 bg-indigo-50/40 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
            >
                Timeline Persiapan
            </a>

            <a
                href="{{ route('acara.kepanitiaan.index', $event->id) }}"
                class="px-5 py-3 transition-colors {{ $activeTab === 'kepanitiaan' ? 'text-indigo-600 bg-indigo-50/40 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
            >
                Kepanitiaan
            </a>

            <a
                href="{{ route('acara.tugas.index', $event->id) }}"
                class="px-5 py-3 transition-colors {{ $activeTab === 'tugas' ? 'text-indigo-600 bg-indigo-50/40 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
            >
                Tugas Tim
            </a>

            @if ($isLeader)
                <a
                    href="{{ url('/acara/'.$event->id.'/monitoring') }}"
                    class="px-5 py-3 transition-colors {{ $activeTab === 'monitoring' ? 'text-indigo-600 bg-indigo-50/40 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                >
                    Monitoring
                </a>

                <a
                    href="{{ url('/acara/'.$event->id.'/anggaran') }}"
                    class="px-5 py-3 transition-colors {{ $activeTab === 'anggaran' ? 'text-indigo-600 bg-indigo-50/40 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
                >
                    Anggaran
                </a>
            @endif
        </nav>
    </div>
</div>

