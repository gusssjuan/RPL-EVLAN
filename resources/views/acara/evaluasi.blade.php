<x-layouts.app :title="'Evaluasi & Tutup Acara - ' . $event->nama_event">
    <div class="max-w-4xl mx-auto space-y-6" x-data="{ showConfirmModal: false }">
        <!-- Top Back Navigation -->
        <div class="flex items-center justify-between">
            <a href="{{ route('acara.monitoring', $event->id) }}" class="inline-flex items-center gap-1.5 text-xs text-gray-500 hover:text-indigo-600 transition-colors font-medium">
                <x-icon name="chevron-left" class="w-4 h-4" />
                <span>Kembali ke Monitoring Acara</span>
            </a>
            <span class="text-xs text-gray-400">ID Acara: #{{ $event->id }}</span>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-[12px] text-xs font-medium text-red-700 space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @php
            $tugasSisa = $monitoring['tugas_belum'] + $monitoring['tugas_sedang'];
        @endphp

        <!-- Main Card -->
        <div class="bg-white rounded-[16px] border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                        {{ $event->category?->ikon ?? '📁' }} {{ $event->category?->nama_kategori }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                        Fase Penutupan
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 mt-2">
                    Evaluasi & Penutupan Acara: {{ $event->nama_event }}
                </h1>
                <p class="text-xs text-gray-500 mt-1">
                    Tinjau rekapitulasi pencapaian akhir dan simpan catatan evaluasi untuk arsip laporan pertanggungjawaban.
                </p>
            </div>

            <!-- Warning Callout -->
            <div class="p-5 bg-amber-50/70 border-b border-amber-100 flex items-start gap-3">
                <x-icon name="alert-triangle" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
                <div class="text-xs text-amber-900 leading-relaxed">
                    <p class="font-bold">Konsekuensi Penutupan Acara (Read-Only):</p>
                    <p class="mt-0.5 text-amber-800">
                        Setelah ditutup, status acara akan berubah menjadi <strong>Selesai</strong> dan seluruh data kepanitiaan, tugas, checklist, serta anggaran akan <strong>dikunci permanen</strong>. Anda dan panitia tetap dapat melihat arsip acara ini di menu <strong>Riwayat Acara</strong>.
                    </p>
                    @if ($tugasSisa > 0)
                        <p class="mt-2 font-bold text-red-700 bg-red-100/80 px-2 py-1 rounded inline-block">
                            ⚠️ Perhatian: Masih terdapat {{ $tugasSisa }} tugas yang belum diselesaikan. Anda tetap diperbolehkan menutup acara ini jika pengerjaan memang dihentikan.
                        </p>
                    @endif
                </div>
            </div>

            <!-- Rekapitulasi Metrik Akhir -->
            <div class="p-6 border-b border-gray-100 bg-gray-50/40">
                <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Ringkasan Kesiapan Akhir Acara</h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-white p-4 rounded-[12px] border border-gray-200">
                        <span class="text-[11px] text-gray-500 block">Progres Akhir</span>
                        <span class="text-2xl font-black text-indigo-600 mt-1 block">{{ $monitoring['persentase'] }}%</span>
                        <span class="text-[10px] text-gray-400 mt-0.5 block">{{ $monitoring['tugas_selesai'] }}/{{ $monitoring['total_tugas'] }} tugas selesai</span>
                    </div>

                    <div class="bg-white p-4 rounded-[12px] border border-gray-200">
                        <span class="text-[11px] text-gray-500 block">Tugas Tertunda</span>
                        <span class="text-2xl font-black {{ $tugasSisa > 0 ? 'text-amber-600' : 'text-emerald-600' }} mt-1 block">{{ $tugasSisa }}</span>
                        <span class="text-[10px] text-gray-400 mt-0.5 block">{{ $monitoring['tugas_terlewat'] }} tugas terlewat DL</span>
                    </div>

                    <div class="bg-white p-4 rounded-[12px] border border-gray-200">
                        <span class="text-[11px] text-gray-500 block">Checklist Terpenuhi</span>
                        <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ $monitoring['checklist_terpenuhi'] }}/{{ $monitoring['total_checklist'] }}</span>
                        <span class="text-[10px] text-gray-400 mt-0.5 block">{{ $monitoring['persen_checklist'] }}% logistik siap</span>
                    </div>

                    <div class="bg-white p-4 rounded-[12px] border border-gray-200">
                        <span class="text-[11px] text-gray-500 block">Estimasi Anggaran</span>
                        <span class="text-base font-black text-gray-900 mt-1 block truncate">{{ format_rupiah($monitoring['total_anggaran']) }}</span>
                        <span class="text-[10px] text-gray-400 mt-0.5 block">Total dana tercatat</span>
                    </div>
                </div>
            </div>

            <!-- Form Evaluasi -->
            <form id="close-event-form" action="{{ route('acara.tutup', $event->id) }}" method="POST" class="p-6 space-y-5">
                @csrf

                <div>
                    <label for="evaluasi" class="block text-xs font-bold text-gray-900 mb-1">
                        Catatan Evaluasi Pelaksanaan Acara <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-gray-500 mb-2">
                        Tuliskan evaluasi menyeluruh mengenai jalannya acara, pencapaian target, kendala yang dihadapi, serta saran perbaikan untuk acara berikutnya:
                    </p>
                    <textarea
                        id="evaluasi"
                        name="evaluasi"
                        rows="5"
                        required
                        placeholder="Contoh: Acara seminar berjalan lancar dengan dihadiri 285 peserta dari target 300 peserta. Kendala teknis sound system berhasil diatasi oleh divisi perlengkapan pada gladi bersih. Disarankan konfirmasi narasumber dilakukan minimal H-60 pada acara mendatang."
                        class="w-full text-sm rounded-[10px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('evaluasi') border-red-300 @enderror"
                    >{{ old('evaluasi', $event->evaluasi) }}</textarea>
                    @error('evaluasi')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a
                        href="{{ route('acara.monitoring', $event->id) }}"
                        class="px-4 py-2 border border-gray-300 rounded-[8px] text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Batal
                    </a>
                    <button
                        type="button"
                        @click="showConfirmModal = true"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-[8px] text-xs font-bold shadow-sm transition-colors"
                    >
                        <x-icon name="check-circle" class="w-4 h-4" />
                        <span>Tutup & Kunci Acara Ini</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Modal Konfirmasi Penutupan (S19-V) -->
        <div
            x-show="showConfirmModal"
            x-cloak
            x-transition
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
            @click.self="showConfirmModal = false"
            @keydown.escape.window="showConfirmModal = false"
        >
            <div class="bg-white rounded-[16px] p-6 w-full max-w-md shadow-xl space-y-4">
                <div class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto">
                    <x-icon name="alert-triangle" class="w-6 h-6" />
                </div>

                <div class="text-center">
                    <h3 class="text-base font-bold text-gray-900">Konfirmasi Penutupan Acara</h3>
                    <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                        Apakah Anda yakin ingin menutup acara <strong>{{ $event->nama_event }}</strong>?
                        Setelah ditutup, acara tidak dapat diubah kembali dan berstatus <strong>Selesai</strong>.
                    </p>
                    @if ($tugasSisa > 0)
                        <div class="mt-3 p-3 bg-amber-50 rounded-[8px] text-xs text-amber-800 text-left border border-amber-200">
                            <strong>Catatan:</strong> Terdapat {{ $tugasSisa }} tugas yang belum selesai dan akan diarsipkan dalam keadaan status saat ini.
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button
                        type="button"
                        @click="showConfirmModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-[8px] text-xs font-semibold text-gray-700 hover:bg-gray-50"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        onclick="document.getElementById('close-event-form').submit();"
                        class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-[8px] text-xs font-bold shadow-sm"
                    >
                        Ya, Tutup Acara Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

