<x-layouts.app :title="'Ringkasan - ' . $event->nama_event">
    <div class="space-y-6">
        <!-- Shared Event Header & Tabs -->
        <x-acara.partials.header :event="$event" activeTab="ringkasan" />

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[12px] text-sm font-medium text-emerald-800 flex items-center gap-2.5">
                <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- 1. Ringkasan Progres & Metrik Utama (S07 / S07-V) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- Card Progres Persiapan -->
            <div class="bg-white p-6 rounded-[16px] border border-gray-200 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Progres Persiapan</span>
                    <div class="flex items-baseline gap-2 mt-2">
                        <span class="text-3xl font-black text-indigo-600">{{ $persenProgres }}%</span>
                        <span class="text-xs text-gray-500">keseluruhan</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        {{ $selesaiChecklist }} dari {{ $totalChecklist }} kebutuhan logistik terpenuhi • {{ $selesaiTugas }} dari {{ $totalTugas }} tugas diselesaikan.
                    </p>
                </div>

                <div class="w-full bg-gray-100 rounded-full h-2.5 mt-4 overflow-hidden">
                    <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $persenProgres }}%"></div>
                </div>
            </div>

            <!-- Card Agenda Terdekat -->
            <div class="bg-white p-6 rounded-[16px] border border-gray-200 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Agenda Terdekat</span>
                    @if ($agendaTerdekat)
                        <div class="mt-2">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700">
                                {{ $agendaTerdekat->label }}
                            </span>
                            <p class="text-sm font-bold text-gray-900 mt-1 line-clamp-2">
                                {{ $agendaTerdekat->kegiatan }}
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                📅 {{ \Carbon\Carbon::parse($agendaTerdekat->tanggal)->isoFormat('D MMMM Y') }}
                            </p>
                        </div>
                    @else
                        <div class="mt-4 text-center py-2 text-gray-400">
                            <p class="text-xs">Semua agenda timeline telah lewat atau belum ada.</p>
                        </div>
                    @endif
                </div>

                <div class="pt-2">
                    <a href="{{ route('acara.timeline.index', $event->id) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                        Lihat Seluruh Timeline →
                    </a>
                </div>
            </div>

            <!-- Card Kebutuhan Logistik -->
            <div class="bg-white p-6 rounded-[16px] border border-gray-200 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status Logistik</span>
                    <div class="flex items-baseline gap-2 mt-2">
                        <span class="text-3xl font-black text-emerald-600">{{ $selesaiChecklist }}/{{ $totalChecklist }}</span>
                        <span class="text-xs text-gray-500">terpenuhi</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        Tersisa {{ $totalChecklist - $selesaiChecklist }} item perlengkapan/kebutuhan yang harus dipersiapkan.
                    </p>
                </div>

                <div class="pt-2">
                    <a href="{{ route('acara.checklist.index', $event->id) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                        Buka Checklist Kebutuhan →
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. Konten Spesifik Peran (Ketua vs Anggota) -->
        @if ($isLeader)
            <!-- Tampilan Ringkasan Ketua (S07) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Kolom Kiri: Checklist Belum Selesai Prioritas -->
                <div class="lg:col-span-7 bg-white p-6 rounded-[16px] border border-gray-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h2 class="text-base font-bold text-gray-900">Kebutuhan Belum Terpenuhi</h2>
                        <a href="{{ route('acara.checklist.index', $event->id) }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                            Kelola Semua
                        </a>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse ($event->checklists->where('status_selesai', false)->take(5) as $cb)
                            <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-3.5 h-3.5 rounded border border-gray-300 shrink-0"></span>
                                    <span class="font-medium text-gray-800 truncate">{{ $cb->nama_kebutuhan }}</span>
                                </div>
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-[10px] shrink-0">
                                    {{ $cb->kelompok ?? 'Umum' }}
                                </span>
                            </div>
                        @empty
                            <div class="py-6 text-center text-gray-400">
                                <x-icon name="check-circle" class="w-8 h-8 mx-auto text-emerald-500 mb-1" />
                                <p class="text-xs font-medium text-gray-600">Luar biasa! Seluruh kebutuhan logistik telah terpenuhi.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Kolom Kanan: Tim Kepanitiaan Cepat -->
                <div class="lg:col-span-5 bg-white p-6 rounded-[16px] border border-gray-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h2 class="text-base font-bold text-gray-900">Kepanitiaan Acara</h2>
                        <a href="{{ url('/acara/'.$event->id.'/kepanitiaan') }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                            Struktur Lengkap
                        </a>
                    </div>

                    <div class="space-y-3">
                        <p class="text-xs text-gray-500">
                            Struktur divisi dan pembagian panitia untuk koordinasi tugas.
                        </p>

                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 bg-amber-50 text-amber-800 rounded-[8px] text-xs font-bold">
                                {{ $event->divisions->count() }} Divisi
                            </span>
                            <span class="px-2.5 py-1 bg-indigo-50 text-indigo-800 rounded-[8px] text-xs font-bold">
                                {{ $event->tasks->count() }} Tugas Dibagi
                            </span>
                        </div>

                        <div class="pt-3 border-t border-gray-100">
                            <a
                                href="{{ url('/acara/'.$event->id.'/kepanitiaan') }}"
                                class="inline-flex items-center justify-center w-full py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-[8px] text-xs font-bold transition-colors"
                            >
                                + Atur Divisi & Anggota Tim
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Tampilan Ringkasan Varian Anggota Panitia (S07-V) -->
            <div class="bg-white p-6 sm:p-8 rounded-[16px] border border-gray-200 shadow-sm space-y-4">
                <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Tugas Saya di Acara Ini</h2>
                        <p class="text-xs text-gray-500">Tanggung jawab jobdesk yang didelegasikan ketua panitia kepada Anda.</p>
                    </div>
                    <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs font-bold">
                        {{ $tugasSaya->count() }} Tugas
                    </span>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($tugasSaya as $tgs)
                        <div class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $tgs->judul }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Deadline: <strong>{{ \Carbon\Carbon::parse($tgs->deadline)->isoFormat('D MMMM Y') }}</strong>
                                </p>
                            </div>
                            <span class="px-2.5 py-0.5 rounded text-xs font-semibold {{ $tgs->status->value === 'sudah_diselesaikan' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ ucfirst(str_replace('_', ' ', $tgs->status->value)) }}
                            </span>
                        </div>
                    @empty
                        <div class="py-8 text-center text-gray-400">
                            <x-icon name="check-circle" class="w-8 h-8 mx-auto text-gray-300 mb-2" />
                            <p class="text-sm font-medium text-gray-600">Belum ada tugas yang ditugaskan kepada Anda</p>
                            <p class="text-xs text-gray-400 mt-0.5">Ketua panitia akan mendelegasikan tugas sesuai divisi Anda.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
