<x-layouts.app title="Rancangan Acara - Buat Acara">
    <div class="max-w-5xl mx-auto space-y-8 pb-24">
        <!-- Wizard Header & Stepper -->
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Langkah 3 dari 3</span>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 mt-1">
                        Rancangan & Pratinjau Acara
                    </h1>
                    <p class="text-sm text-gray-500">
                        Periksa rancangan otomatisasi template sebelum acara resmi disimpan ke sistem.
                    </p>
                </div>
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300">
                        <x-icon name="alert-circle" class="w-4 h-4 text-amber-600" />
                        <span>Draf Belum Disimpan</span>
                    </span>
                </div>
            </div>

            <!-- Stepper Bar -->
            <div class="grid grid-cols-3 gap-2 sm:gap-4 pt-2">
                <a href="{{ route('acara.buat.kategori') }}" class="p-3 bg-white border border-gray-200 rounded-[10px] flex items-center gap-3 hover:bg-gray-50 transition-colors">
                    <span class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0"><x-icon name="check" class="w-4 h-4" /></span>
                    <div class="min-w-0 hidden sm:block">
                        <p class="text-xs font-bold text-gray-900 truncate">Kategori</p>
                        <p class="text-[10px] text-gray-500 truncate">{{ $category->nama_kategori }}</p>
                    </div>
                </a>

                <a href="{{ route('acara.buat.detail') }}" class="p-3 bg-white border border-gray-200 rounded-[10px] flex items-center gap-3 hover:bg-gray-50 transition-colors">
                    <span class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0"><x-icon name="check" class="w-4 h-4" /></span>
                    <div class="min-w-0 hidden sm:block">
                        <p class="text-xs font-bold text-gray-900 truncate">Detail Acara</p>
                        <p class="text-[10px] text-gray-500 truncate">{{ $draft['nama_event'] }}</p>
                    </div>
                </a>

                <div class="p-3 bg-indigo-50 border-2 border-indigo-600 rounded-[10px] flex items-center gap-3">
                    <span class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0">3</span>
                    <div class="min-w-0 hidden sm:block">
                        <p class="text-xs font-bold text-indigo-900 truncate">Rancangan</p>
                        <p class="text-[10px] text-indigo-600 truncate">Konfirmasi buat</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1. Ringkasan Informasi Acara -->
        <div class="bg-white p-6 rounded-[16px] border border-gray-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <x-icon name="calendar" class="w-5 h-5 text-indigo-600" />
                    <span>Informasi Acara</span>
                </h2>
                <a href="{{ route('acara.buat.detail') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                    Ubah Detail
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 text-sm">
                <div>
                    <span class="text-xs text-gray-400 block">Nama Acara</span>
                    <span class="font-bold text-gray-900">{{ $draft['nama_event'] }}</span>
                </div>

                <div>
                    <span class="text-xs text-gray-400 block">Kategori Acara</span>
                    <span class="font-semibold text-gray-800 flex items-center gap-1.5 mt-0.5">
                        <x-category-icon :kategori="$category" class="w-4 h-4" />
                        <span>{{ $category->nama_kategori }}</span>
                    </span>
                </div>

                <div>
                    <span class="text-xs text-gray-400 block">Tanggal Pelaksanaan</span>
                    <span class="font-bold text-indigo-700">
                        {{ \Carbon\Carbon::parse($draft['tanggal'])->isoFormat('dddd, D MMMM Y') }}
                    </span>
                    @if (!empty($draft['waktu_mulai']))
                        <span class="text-xs text-gray-500 block">
                            Pukul {{ $draft['waktu_mulai'] }} {{ !empty($draft['waktu_selesai']) ? '- ' . $draft['waktu_selesai'] : '' }} WITA
                        </span>
                    @endif
                </div>

                <div>
                    <span class="text-xs text-gray-400 block">Lokasi</span>
                    <span class="font-medium text-gray-800">{{ $draft['lokasi'] }}</span>
                </div>

                <div>
                    <span class="text-xs text-gray-400 block">Target Peserta</span>
                    <span class="font-medium text-gray-800">{{ $draft['target_peserta'] }}</span>
                </div>

                <div>
                    <span class="text-xs text-gray-400 block">Jumlah Peserta</span>
                    <span class="font-bold text-gray-900">{{ number_format($draft['jumlah_peserta'], 0, ',', '.') }} Orang</span>
                </div>

                <div class="sm:col-span-2 lg:col-span-3">
                    <span class="text-xs text-gray-400 block">Tujuan Acara</span>
                    <p class="text-xs text-gray-600 mt-1 leading-relaxed">{{ $draft['tujuan'] }}</p>
                </div>
            </div>
        </div>

        <!-- 2. Ringkasan Metrik Otomatisasi -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <x-stat-card
                title="Checklist Kebutuhan"
                value="{{ $template ? $template->kebutuhan->count() : 0 }}"
                icon="check-circle"
                color="indigo"
                description="Otomatis digandakan"
            />

            <x-stat-card
                title="Agenda Timeline"
                value="{{ count($timelinePreview) }}"
                icon="calendar"
                color="emerald"
                description="Hitung tanggal H-n"
            />

            <x-stat-card
                title="Rekomendasi Divisi"
                value="{{ $template ? $template->divisi->count() : 0 }}"
                icon="users"
                color="amber"
                description="Panduan kepanitiaan"
            />

            <x-stat-card
                title="Rekomendasi Jobdesk"
                value="{{ $template ? $template->jobdesk->count() : 0 }}"
                icon="briefcase"
                color="purple"
                description="Panduan tugas panitia"
            />
        </div>

        <!-- 3. Dua Kolom: Kebutuhan & Timeline -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Kolom Kiri: Checklist Kebutuhan Otomatis (14 item pada Seminar) -->
            <div class="bg-white rounded-[16px] border border-gray-200 shadow-sm p-6 space-y-4">
                <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Checklist Kebutuhan Logistik</h3>
                        <p class="text-xs text-gray-500">Akan dibuat otomatis dengan status belum terpenuhi.</p>
                    </div>
                    <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs font-bold">
                        {{ $template ? $template->kebutuhan->count() : 0 }} Item
                    </span>
                </div>

                <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto pr-1">
                    @if ($template && $template->kebutuhan->isNotEmpty())
                        @foreach ($template->kebutuhan as $kb)
                            <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-4 h-4 rounded border border-gray-300 bg-gray-50 flex items-center justify-center shrink-0"></span>
                                    <span class="font-medium text-gray-800 truncate">{{ $kb->nama_item }}</span>
                                </div>
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-[10px] font-medium shrink-0">
                                    {{ $kb->kelompok ?? 'Umum' }}
                                </span>
                            </div>
                        @endforeach
                    @else
                        <div class="py-8 text-center text-gray-400">
                            <p class="text-xs">Tidak ada item kebutuhan dalam template ini.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Kolom Kanan: Timeline H-n & Tanggal Absolut -->
            <div class="bg-white rounded-[16px] border border-gray-200 shadow-sm p-6 space-y-4">
                <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Timeline Countdown Persiapan</h3>
                        <p class="text-xs text-gray-500">Tanggal absolut dihitung mundur dari tanggal acara.</p>
                    </div>
                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-bold">
                        {{ count($timelinePreview) }} Agenda
                    </span>
                </div>

                <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto pr-1">
                    @forelse ($timelinePreview as $tm)
                        <div class="py-2.5 first:pt-0 last:pb-0 flex items-start justify-between gap-3 text-xs">
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900">{{ $tm['kegiatan'] }}</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">{{ $tm['tanggal'] }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold shrink-0 {{ $tm['offset_hari'] == 0 ? 'bg-indigo-600 text-white' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                {{ $tm['label'] }}
                            </span>
                        </div>
                    @empty
                        <div class="py-8 text-center text-gray-400">
                            <p class="text-xs">Tidak ada agenda timeline.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 4. Penjelasan Kepanitiaan D-10 -->
        <div class="p-4 bg-purple-50 border border-purple-200 rounded-[12px] flex items-start gap-3">
            <x-icon name="users" class="w-5 h-5 text-purple-600 shrink-0 mt-0.5" />
            <div class="text-xs text-purple-900 space-y-1">
                <p class="font-bold">Rekomendasi Struktur Kepanitiaan & Jobdesk (Catatan D-10)</p>
                <p class="text-purple-800">
                    Sesuai spesifikasi sistem, struktur divisi dan uraian jobdesk tidak dibuat otomatis ke database sekarang. Anda dapat menambahkan seluruh rekomendasi divisi & jobdesk secara fleksibel saat mengelola kepanitiaan di dalam acara.
                </p>
            </div>
        </div>

        <!-- Fixed Bottom Action Bar -->
        <div class="fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur border-t border-gray-200 py-3.5 px-6 z-30 shadow-lg">
            <div class="max-w-5xl mx-auto flex items-center justify-between">
                <a
                    href="{{ route('acara.buat.detail') }}"
                    class="px-4 py-2 border border-gray-300 rounded-[8px] text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    <x-icon name="arrow-left" class="w-4 h-4" />
                    Kembali Ubah Detail
                </a>

                <form method="POST" action="{{ route('acara.buat.simpan') }}">
                    @csrf
                    <x-button type="submit" variant="primary" class="px-6 py-2.5 shadow-md">
                        Buat Acara Sekarang
                    </x-button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>

