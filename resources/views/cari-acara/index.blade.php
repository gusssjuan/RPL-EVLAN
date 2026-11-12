@php
    $palette = [
        ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'accent' => 'bg-indigo-600', 'soft' => 'bg-indigo-100'],
        ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'accent' => 'bg-rose-600', 'soft' => 'bg-rose-100'],
        ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'accent' => 'bg-amber-500', 'soft' => 'bg-amber-100'],
        ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'accent' => 'bg-emerald-600', 'soft' => 'bg-emerald-100'],
        ['bg' => 'bg-sky-50', 'text' => 'text-sky-700', 'accent' => 'bg-sky-600', 'soft' => 'bg-sky-100'],
        ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'accent' => 'bg-violet-600', 'soft' => 'bg-violet-100'],
    ];

    $baseQuery = collect(request()->query())
        ->except(['kategori', 'page'])
        ->filter(fn ($value) => filled($value))
        ->all();
@endphp

<x-layouts.app title="Cari Acara">
    <div class="space-y-6" x-data="{ memuat: false }">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Temukan Acara Menarik</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Temukan acara publik yang terbuka untuk peserta dan lihat detailnya.
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 self-start px-3 py-1.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold">
                <x-icon name="globe" class="w-3.5 h-3.5" />
                <span>Acara publik</span>
            </span>
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[12px] text-sm font-medium text-emerald-800 flex items-center gap-2.5">
                <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-[12px] text-sm text-red-700">
                <div class="flex items-center gap-2 font-semibold">
                    <x-icon name="alert-circle" class="w-4 h-4 shrink-0" />
                    <span>Filter belum valid.</span>
                </div>
                <ul class="mt-1.5 list-disc list-inside space-y-0.5 pl-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white p-4 sm:p-5 rounded-[12px] border border-gray-200 shadow-sm space-y-4">
            <form method="GET" action="{{ route('cari-acara.index') }}" class="space-y-3" x-on:submit="memuat = true">
                <div class="flex flex-col lg:flex-row gap-3">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <x-icon name="search" class="w-4 h-4" />
                        </span>
                        <input
                            type="text"
                            name="cari"
                            value="{{ $search }}"
                            placeholder="Cari nama acara, penyelenggara, atau lokasi…"
                            class="w-full pl-9 pr-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 transition-colors"
                        >
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 lg:w-auto">
                        <input
                            type="text"
                            name="lokasi"
                            value="{{ $lokasi }}"
                            placeholder="Lokasi"
                            class="w-full sm:w-40 px-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600"
                        >
                        <input
                            type="date"
                            name="dari"
                            value="{{ $dari }}"
                            aria-label="Tanggal mulai"
                            class="w-full sm:w-40 px-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm text-gray-900 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600"
                        >
                        <input
                            type="date"
                            name="sampai"
                            value="{{ $sampai }}"
                            aria-label="Tanggal akhir"
                            class="w-full sm:w-40 px-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm text-gray-900 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600"
                        >
                    </div>

                    <input type="hidden" name="kategori" value="{{ $kategori }}">

                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-xs font-semibold shadow-sm transition-colors">
                            <x-icon name="search" class="w-3.5 h-3.5" />
                            <span>Cari</span>
                        </button>
                        @if ($adaFilter)
                            <a href="{{ route('cari-acara.index') }}" class="inline-flex items-center justify-center px-3.5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-[8px] text-xs font-semibold shadow-sm transition-colors">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <div class="flex items-center gap-1.5 overflow-x-auto pb-1">
                <a
                    href="{{ route('cari-acara.index', $baseQuery) }}"
                    class="px-3 py-1.5 rounded-[8px] text-xs font-semibold whitespace-nowrap transition-colors {{ $kategori === null ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                >
                    Semua Kategori
                </a>
                @foreach ($categories as $category)
                    <a
                        href="{{ route('cari-acara.index', array_merge($baseQuery, ['kategori' => $category->id])) }}"
                        class="px-3 py-1.5 rounded-[8px] text-xs font-semibold whitespace-nowrap transition-colors {{ (string) $kategori === (string) $category->id ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                    >
                        <x-category-icon :kategori="$category" class="w-3.5 h-3.5" />
                        {{ $category->nama_kategori }}
                    </a>
                @endforeach
            </div>
        </div>

        <div x-show="memuat" style="display: none;" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @for ($i = 0; $i < 3; $i++)
                <div class="bg-white rounded-[12px] border border-gray-200 p-5 animate-pulse space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-[10px] bg-gray-100"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-3 w-24 bg-gray-100 rounded"></div>
                            <div class="h-3 w-16 bg-gray-100 rounded"></div>
                        </div>
                    </div>
                    <div class="h-4 w-3/4 bg-gray-100 rounded"></div>
                    <div class="h-3 w-full bg-gray-100 rounded"></div>
                    <div class="h-3 w-2/3 bg-gray-100 rounded"></div>
                </div>
            @endfor
        </div>

        <div x-show="!memuat">
            @if (! $adaAcara && ! $adaFilter)
                <x-empty-state
                    title="Belum ada acara publik"
                    description="Saat ini belum ada acara yang dipublikasikan. Silakan kembali lagi nanti."
                    icon="calendar"
                />
            @elseif ($events->isEmpty())
                <x-empty-state
                    title="Tidak ada acara yang cocok"
                    description="Coba ubah kata kunci, kategori, lokasi, atau rentang tanggal pencarian."
                    icon="search"
                >
                    <a href="{{ route('cari-acara.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-xs font-semibold shadow-sm transition-colors">
                        <span>Tampilkan Semua Acara</span>
                    </a>
                </x-empty-state>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach ($events as $event)
                        @php
                            $color = $palette[$event->category_id % count($palette)];
                            $kuotaPenuh = $event->kuota !== null && $event->diterima_count >= $event->kuota;
                            $batasLewat = $event->batas_pendaftaran && $event->batas_pendaftaran->lt(today());
                            $pendaftaranTerbuka = $event->pendaftaran_dibuka && ! $kuotaPenuh && ! $batasLewat;
                            $persenKuota = $event->kuota
                                ? min(100, (int) round(($event->diterima_count / $event->kuota) * 100))
                                : 0;
                        @endphp

                        <article class="bg-white rounded-[12px] border border-gray-200 shadow-sm overflow-hidden flex flex-col hover:border-indigo-200 hover:shadow-md transition-all">
                            <div class="h-1.5 {{ $color['accent'] }}"></div>

                            <div class="p-5 space-y-4 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-11 h-11 rounded-[10px] {{ $color['bg'] }} flex items-center justify-center text-xl shrink-0">
                                            <x-category-icon :kategori="$event->category" class="w-5 h-5" />
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-semibold {{ $color['text'] }} truncate">
                                                {{ $event->category?->nama_kategori }}
                                            </p>
                                            <p class="text-[11px] text-gray-500">
                                                {{ $event->hari_menuju }}
                                            </p>
                                        </div>
                                    </div>

                                    @if ($pendaftaranTerbuka)
                                        <x-badge variant="success">Pendaftaran dibuka</x-badge>
                                    @elseif ($kuotaPenuh)
                                        <x-badge variant="danger">Kuota penuh</x-badge>
                                    @else
                                        <x-badge variant="neutral">Pendaftaran ditutup</x-badge>
                                    @endif
                                </div>

                                <div>
                                    <a href="{{ route('cari-acara.show', $event->id) }}" class="block text-base font-bold text-gray-900 leading-snug hover:text-indigo-700 transition-colors">
                                        {{ $event->nama_event }}
                                    </a>
                                    <div class="mt-2 space-y-1 text-xs text-gray-500">
                                        <p class="flex items-center gap-1.5">
                                            <x-icon name="calendar" class="w-3.5 h-3.5 text-gray-400 shrink-0" />
                                            <span>{{ $event->tanggal->isoFormat('D MMMM Y') }}</span>
                                        </p>
                                        <p class="flex items-center gap-1.5">
                                            <x-icon name="map-pin" class="w-3.5 h-3.5 text-gray-400 shrink-0" />
                                            <span class="truncate">{{ $event->lokasi }}</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="font-medium text-gray-500">Kuota terisi</span>
                                        <span class="font-bold text-gray-800">
                                            {{ $event->diterima_count }}/{{ $event->kuota ?? '∞' }}
                                        </span>
                                    </div>
                                    <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-2 rounded-full {{ $kuotaPenuh ? 'bg-red-500' : $color['accent'] }}" style="width: {{ $event->kuota ? $persenKuota : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="px-5 py-3.5 border-t border-gray-100 bg-gray-50/60 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Penyelenggara</p>
                                    <p class="text-xs font-semibold text-gray-700 truncate">
                                        {{ $event->penyelenggara ?: $event->leader?->nama }}
                                    </p>
                                </div>
                                <a href="{{ route('cari-acara.show', $event->id) }}" class="shrink-0 inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-[8px] text-[11px] font-semibold shadow-sm transition-colors">
                                    <span>Lihat Detail</span>
                                    <x-icon name="chevron-right" class="w-3 h-3" />
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
