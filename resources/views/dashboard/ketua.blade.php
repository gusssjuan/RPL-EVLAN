<x-layouts.app title="Dashboard Ketua Panitia">
    <div class="space-y-6">
        <!-- Greeting & CTA Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    Halo, {{ $user->nama }} 👋
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Pantau kemajuan persiapan acara, tenggat waktu tugas, dan aktivitas kepanitiaan Anda.
                </p>
            </div>
            <div>
                <a
                    href="{{ route('acara.buat.kategori') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-sm font-semibold shadow-sm transition-colors"
                >
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Buat Acara Baru</span>
                </a>
            </div>
        </div>

        <!-- 4 Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card
                title="Acara Aktif"
                value="{{ $statAcaraAktif }}"
                icon="calendar"
                color="indigo"
            />
            <x-stat-card
                title="Total Tugas Tim"
                value="{{ $statTotalTugas }}"
                icon="clipboard-list"
                color="info"
            />
            <x-stat-card
                title="Tugas Selesai"
                value="{{ $statTugasSelesai }}"
                icon="check-circle"
                color="emerald"
            />
            <x-stat-card
                title="Total Estimasi Anggaran"
                value="{{ format_rupiah($statTotalAnggaran) }}"
                icon="credit-card"
                color="amber"
            />
        </div>

        <!-- Primary Active Event Section (S05) -->
        @if ($acaraUtama)
            @php
                $progres = $acaraUtama->progres_persen;
            @endphp
            <div class="bg-white rounded-[16px] border border-gray-200 p-6 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-100 pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                                {{ $acaraUtama->category?->ikon ?? '📁' }} {{ $acaraUtama->category?->nama_kategori }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700">
                                {{ $acaraUtama->hari_menuju }}
                            </span>
                        </div>
                        <h2 class="text-xl font-extrabold text-gray-900 mt-2">
                            {{ $acaraUtama->nama_event }}
                        </h2>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500 mt-1">
                            <span>📅 {{ $acaraUtama->tanggal->isoFormat('D MMMM Y') }}</span>
                            <span>📍 {{ $acaraUtama->lokasi }}</span>
                            <span>👥 {{ number_format($acaraUtama->jumlah_peserta, 0, ',', '.') }} Peserta</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <a
                            href="{{ route('acara.ringkasan', $acaraUtama->id) }}"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-xs font-semibold transition-colors"
                        >
                            <span>Kelola Acara Ini</span>
                            <x-icon name="arrow-right" class="w-3.5 h-3.5" />
                        </a>
                    </div>
                </div>

                <!-- Progress Bar & Breakdown -->
                <div>
                    <div class="flex items-center justify-between text-xs font-semibold text-gray-700 mb-1.5">
                        <span>Progres Persiapan Keseluruhan</span>
                        <span class="text-indigo-600 text-sm font-black">{{ $progres }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                        <div class="bg-indigo-600 h-3 rounded-full transition-all duration-500" style="width: {{ $progres }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-gray-400 mt-2">
                        <span>{{ $acaraUtama->tasks->where('status.value', 'sudah_diselesaikan')->count() }} dari {{ $acaraUtama->tasks->count() }} tugas diselesaikan</span>
                        <a href="{{ route('acara.ringkasan', $acaraUtama->id) }}" class="text-indigo-600 hover:underline font-medium">Lihat detail progres →</a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Two Columns: Upcoming Deadlines & Recent Activities -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Kolom Kiri: Deadline Terdekat -->
            <div class="lg:col-span-7 bg-white rounded-[16px] border border-gray-200 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2">
                        <x-icon name="clock" class="w-4 h-4 text-amber-500" />
                        <h3 class="text-sm font-bold text-gray-900">Tenggat Waktu Terdekat</h3>
                    </div>
                    <span class="text-xs text-gray-400">Tugas Belum Selesai</span>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($deadlines as $dl)
                        @php
                            $dlDate = \Carbon\Carbon::parse($dl->deadline);
                            $isOverdue = $dlDate->isPast() && ! $dlDate->isToday();
                            $isToday = $dlDate->isToday();
                        @endphp
                        <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0">
                                <p class="font-bold text-gray-900 truncate">{{ $dl->judul }}</p>
                                <p class="text-[11px] text-gray-500 mt-0.5">
                                    <span>{{ $dl->event?->nama_event }}</span>
                                    <span> • </span>
                                    <span>Penerima: <strong>{{ $dl->penerima?->nama ?? '-' }}</strong></span>
                                </p>
                            </div>

                            <div class="shrink-0 text-right">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $isOverdue ? 'bg-red-100 text-red-800' : ($isToday ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700') }}">
                                    {{ $dlDate->isoFormat('D MMM Y') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-gray-400 text-xs">
                            <x-icon name="check-circle" class="w-7 h-7 mx-auto text-emerald-500 mb-1" />
                            <p class="font-medium text-gray-600">Tidak ada tenggat waktu mendesak.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Kolom Kanan: Aktivitas Terbaru Tim -->
            <div class="lg:col-span-5 bg-white rounded-[16px] border border-gray-200 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2">
                        <x-icon name="activity" class="w-4 h-4 text-indigo-600" />
                        <h3 class="text-sm font-bold text-gray-900">Aktivitas Tim Terbaru</h3>
                    </div>
                    <span class="text-xs text-gray-400">Log Aktivitas</span>
                </div>

                <div class="space-y-3">
                    @forelse ($aktivitasTerbaru as $log)
                        <div class="text-xs flex items-start gap-2.5">
                            <div class="w-6 h-6 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">
                                {{ strtoupper(substr($log->user?->nama ?? 'U', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-gray-800 font-medium">
                                    <strong class="text-gray-900">{{ $log->user?->nama ?? 'Pengguna' }}</strong>
                                    {{ $log->deskripsi }}
                                </p>
                                <span class="text-[10px] text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-gray-400 text-xs">
                            <p>Belum ada rekaman aktivitas kepanitiaan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>