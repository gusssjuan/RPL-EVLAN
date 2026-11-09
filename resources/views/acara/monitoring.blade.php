<x-layouts.app :title="'Monitoring - ' . $event->nama_event">
    <div class="space-y-6">
        <x-acara.partials.header :event="$event" activeTab="monitoring" />

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[12px] text-sm font-medium text-emerald-800 flex items-center gap-2.5">
                <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- Action Bar: Evaluasi & Tutup Acara -->
        <div class="bg-gradient-to-r from-indigo-900 to-indigo-800 rounded-[16px] p-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-sm">
            <div>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/20 text-white mb-1">
                    Dashboard Monitoring Terpadu
                </span>
                <h2 class="text-xl font-bold">Pantau Kesiapan Acara Real-Time</h2>
                <p class="text-xs text-indigo-200 mt-1">
                    Seluruh kalkulasi progres tugas, divisi, checklist, dan anggaran diperbarui secara otomatis.
                </p>
            </div>

            @if ($event->status_event === \App\Enums\StatusEvent::Perencanaan)
                <div>
                    <a
                        href="{{ route('acara.evaluasi', $event->id) }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-[8px] text-xs font-bold shadow transition-colors whitespace-nowrap"
                    >
                        <x-icon name="check-circle" class="w-4 h-4" />
                        <span>Evaluasi & Tutup Acara</span>
                    </a>
                </div>
            @else
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 text-emerald-300 rounded-[8px] text-xs font-bold">
                    <x-icon name="check-circle" class="w-4 h-4" />
                    <span>Acara Telah Ditutup (Selesai)</span>
                </div>
            @endif
        </div>

        <!-- 1. Ringkasan Progres & 4 Stat Status -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <!-- Kartu Cincin Progres Utama -->
            <div class="lg:col-span-5 bg-white rounded-[16px] border border-gray-200 p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Progres Persiapan Keseluruhan</span>
                    <div class="flex items-baseline gap-3 mt-3">
                        <span class="text-5xl font-black text-indigo-600">{{ $monitoring['persentase'] }}%</span>
                        <div class="text-xs text-gray-500">
                            <span class="font-bold text-gray-900">{{ $monitoring['tugas_selesai'] }}</span> dari <span class="font-bold text-gray-900">{{ $monitoring['total_tugas'] }}</span> tugas selesai
                        </div>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3 mt-4 overflow-hidden">
                        <div class="bg-indigo-600 h-3 rounded-full transition-all duration-500" style="width: {{ $monitoring['persentase'] }}%"></div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 grid grid-cols-2 gap-3 text-xs mt-4">
                    <div>
                        <span class="text-gray-400 block text-[11px]">Checklist Terpenuhi</span>
                        <span class="font-bold text-gray-900 text-sm">{{ $monitoring['checklist_terpenuhi'] }}/{{ $monitoring['total_checklist'] }} ({{ $monitoring['persen_checklist'] }}%)</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[11px]">Estimasi Anggaran</span>
                        <span class="font-bold text-gray-900 text-sm">{{ format_rupiah($monitoring['total_anggaran']) }}</span>
                    </div>
                </div>
            </div>

            <!-- 4 Stat Card Tugas -->
            <div class="lg:col-span-7 grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-[16px] border border-gray-200 p-5 shadow-sm flex flex-col justify-between">
                    <span class="text-xs font-semibold text-gray-500">Belum Selesai</span>
                    <div class="mt-2">
                        <span class="text-2xl font-black text-gray-800">{{ $monitoring['tugas_belum'] }}</span>
                        <span class="text-[10px] text-gray-400 block mt-0.5">tugas antrean</span>
                    </div>
                    <div class="w-2 h-2 rounded-full bg-gray-400 mt-3"></div>
                </div>

                <div class="bg-white rounded-[16px] border border-gray-200 p-5 shadow-sm flex flex-col justify-between">
                    <span class="text-xs font-semibold text-amber-700">Sedang Proses</span>
                    <div class="mt-2">
                        <span class="text-2xl font-black text-amber-600">{{ $monitoring['tugas_sedang'] }}</span>
                        <span class="text-[10px] text-gray-400 block mt-0.5">dikerjakan tim</span>
                    </div>
                    <div class="w-2 h-2 rounded-full bg-amber-500 mt-3"></div>
                </div>

                <div class="bg-white rounded-[16px] border border-gray-200 p-5 shadow-sm flex flex-col justify-between">
                    <span class="text-xs font-semibold text-emerald-700">Diselesaikan</span>
                    <div class="mt-2">
                        <span class="text-2xl font-black text-emerald-600">{{ $monitoring['tugas_selesai'] }}</span>
                        <span class="text-[10px] text-gray-400 block mt-0.5">tuntas 100%</span>
                    </div>
                    <div class="w-2 h-2 rounded-full bg-emerald-500 mt-3"></div>
                </div>

                <div class="bg-white rounded-[16px] border border-gray-200 p-5 shadow-sm flex flex-col justify-between {{ $monitoring['tugas_terlewat'] > 0 ? 'bg-red-50/30 border-red-200' : '' }}">
                    <span class="text-xs font-semibold {{ $monitoring['tugas_terlewat'] > 0 ? 'text-red-700 font-bold' : 'text-gray-500' }}">Terlewat DL</span>
                    <div class="mt-2">
                        <span class="text-2xl font-black {{ $monitoring['tugas_terlewat'] > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $monitoring['tugas_terlewat'] }}</span>
                        <span class="text-[10px] text-gray-400 block mt-0.5">lewat tenggat</span>
                    </div>
                    <div class="w-2 h-2 rounded-full {{ $monitoring['tugas_terlewat'] > 0 ? 'bg-red-500' : 'bg-gray-200' }} mt-3"></div>
                </div>
            </div>
        </div>

        <!-- 2. Progres per Divisi (S18) -->
        <div class="bg-white rounded-[16px] border border-gray-200 p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-gray-900">Kemajuan Tugas per Divisi</h3>
                <span class="text-xs text-gray-400">{{ $monitoring['progres_divisi']->count() }} Divisi Terdaftar</span>
            </div>

            @if ($monitoring['progres_divisi']->isEmpty())
                <p class="text-xs text-gray-400 text-center py-4">Belum ada divisi yang dibuat.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($monitoring['progres_divisi'] as $div)
                        <div class="p-4 rounded-[12px] border border-gray-100 bg-gray-50/50 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-800">{{ $div['nama_divisi'] }}</span>
                                <span class="text-xs font-black text-indigo-600">{{ $div['persentase'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: {{ $div['persentase'] }}%"></div>
                            </div>
                            <div class="text-[11px] text-gray-500 flex justify-between">
                                <span>{{ $div['selesai'] }} dari {{ $div['total'] }} tugas selesai</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- 3. Dua Kolom: Tenggat & Checklist/Anggaran vs Aktivitas Anggota -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Kolom Kiri -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Card Deadline Terdekat & Terlewat -->
                <div class="bg-white rounded-[16px] border border-gray-200 p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-2">
                            <x-icon name="clock" class="w-4 h-4 text-amber-500" />
                            <h3 class="text-sm font-bold text-gray-900">Tenggat Waktu Terdekat & Mendesak</h3>
                        </div>
                        <a href="{{ route('acara.tugas.index', $event->id) }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                            Kelola Tugas
                        </a>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse ($monitoring['deadline_terdekat'] as $dl)
                            @php
                                $isOverdue = $dl->is_terlewat;
                                $dlDate = \Carbon\Carbon::parse($dl->deadline);
                            @endphp
                            <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3 text-xs">
                                <div class="min-w-0">
                                    <p class="font-bold text-gray-900 truncate">{{ $dl->judul }}</p>
                                    <p class="text-[11px] text-gray-500 mt-0.5">
                                        <span>{{ $dl->division?->nama_divisi ?? 'Umum' }}</span>
                                        <span> • </span>
                                        <span>Penerima: <strong>{{ $dl->penerima?->nama ?? '-' }}</strong></span>
                                    </p>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold shrink-0 {{ $isOverdue ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-700' }}">
                                    @if ($isOverdue)▲ Terlewat @endif {{ $dlDate->isoFormat('D MMM Y') }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 text-center py-4">Semua tugas telah diselesaikan!</p>
                        @endforelse
                    </div>
                </div>

                <!-- Kartu Ringkasan Checklist & Anggaran -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-white rounded-[16px] border border-gray-200 p-5 shadow-sm space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-700">Checklist Kebutuhan</span>
                            <span class="text-xs font-bold text-emerald-600">{{ $monitoring['checklist_terpenuhi'] }}/{{ $monitoring['total_checklist'] }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $monitoring['persen_checklist'] }}%"></div>
                        </div>
                        <div class="pt-1">
                            <a href="{{ route('acara.checklist.index', $event->id) }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                                Buka Checklist Kebutuhan →
                            </a>
                        </div>
                    </div>

                    <div class="bg-white rounded-[16px] border border-gray-200 p-5 shadow-sm space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-700">Estimasi Anggaran</span>
                            <span class="text-xs font-bold text-indigo-600">{{ format_rupiah($monitoring['total_anggaran']) }}</span>
                        </div>
                        <p class="text-[11px] text-gray-400">Total estimasi pengeluaran vendor & logistik.</p>
                        <div class="pt-1">
                            <a href="{{ route('acara.anggaran', $event->id) }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                                Lihat Rincian Anggaran →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Aktivitas Anggota (NFR-A1) -->
            <div class="lg:col-span-5 bg-white rounded-[16px] border border-gray-200 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2">
                        <x-icon name="activity" class="w-4 h-4 text-indigo-600" />
                        <h3 class="text-sm font-bold text-gray-900">Aktivitas Anggota (Audit Log)</h3>
                    </div>
                    <span class="text-xs text-gray-400">10 Terakhir</span>
                </div>

                <div class="space-y-3.5">
                    @forelse ($aktivitasAnggota as $log)
                        <div class="text-xs flex items-start gap-2.5">
                            <div class="w-6 h-6 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">
                                {{ strtoupper(substr($log->user?->nama ?? 'U', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-gray-800 leading-snug">
                                    <strong class="text-gray-900">{{ $log->user?->nama ?? 'Pengguna' }}</strong>
                                    {{ $log->deskripsi }}
                                </p>
                                <span class="text-[10px] text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-6">Belum ada aktivitas tercatat.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
