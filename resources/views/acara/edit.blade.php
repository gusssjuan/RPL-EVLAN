<x-layouts.app :title="'Edit Detail Acara - ' . $event->nama_event">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Top Back Navigation -->
        <div class="flex items-center justify-between">
            <a href="{{ route('acara.ringkasan', $event->id) }}" class="inline-flex items-center gap-1.5 text-xs text-gray-500 hover:text-indigo-600 transition-colors font-medium">
                <x-icon name="chevron-left" class="w-4 h-4" />
                <span>Kembali ke Ringkasan Acara</span>
            </a>
            <span class="text-xs text-gray-400">
                ID Acara: #{{ $event->id }}
            </span>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-[16px] border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h1 class="text-xl font-bold text-gray-900">Edit Detail Acara</h1>
                <p class="text-xs text-gray-500 mt-1">
                    Perbarui informasi dasar dan pengaturan publikasi acara Anda.
                </p>
            </div>

            <form action="{{ route('acara.update', $event->id) }}" method="POST" class="p-6 space-y-8">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="p-4 bg-red-50 border border-red-200 rounded-[12px] text-xs font-medium text-red-700 space-y-1">
                        <div class="flex items-center gap-2 font-bold text-red-800">
                            <x-icon name="alert-circle" class="w-4 h-4" />
                            <span>Mohon periksa kembali formulir berikut:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 pl-2 text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Bagian 1: Informasi Dasar -->
                <div class="space-y-4">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider pb-1 border-b border-gray-100 flex items-center gap-2">
                        <x-icon name="calendar" class="w-4 h-4 text-indigo-600" />
                        <span>1. Informasi Utama Acara</span>
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="nama_event" class="block text-xs font-semibold text-gray-700 mb-1">
                                Nama Acara <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="nama_event"
                                name="nama_event"
                                value="{{ old('nama_event', $event->nama_event) }}"
                                required
                                class="w-full text-sm rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('nama_event') border-red-300 @enderror"
                            />
                            @error('nama_event')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="tujuan" class="block text-xs font-semibold text-gray-700 mb-1">
                                Tujuan Acara <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                id="tujuan"
                                name="tujuan"
                                rows="3"
                                required
                                class="w-full text-sm rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('tujuan') border-red-300 @enderror"
                            >{{ old('tujuan', $event->tujuan) }}</textarea>
                            @error('tujuan')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tanggal" class="block text-xs font-semibold text-gray-700 mb-1">
                                Tanggal Pelaksanaan <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                id="tanggal"
                                name="tanggal"
                                value="{{ old('tanggal', $event->tanggal->toDateString()) }}"
                                required
                                class="w-full text-sm rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('tanggal') border-red-300 @enderror"
                            />
                            <p class="text-[11px] text-amber-600 mt-1 flex items-center gap-1">
                                <x-icon name="info" class="w-3.5 h-3.5 shrink-0" />
                                <span>Mengubah tanggal akan otomatis menghitung ulang tanggal seluruh agenda timeline (D-11).</span>
                            </p>
                            @error('tanggal')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label for="waktu_mulai" class="block text-xs font-semibold text-gray-700 mb-1">
                                    Waktu Mulai
                                </label>
                                <input
                                    type="time"
                                    id="waktu_mulai"
                                    name="waktu_mulai"
                                    value="{{ old('waktu_mulai', $event->waktu_mulai ? \Carbon\Carbon::parse($event->waktu_mulai)->format('H:i') : '') }}"
                                    class="w-full text-sm rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                            <div>
                                <label for="waktu_selesai" class="block text-xs font-semibold text-gray-700 mb-1">
                                    Waktu Selesai
                                </label>
                                <input
                                    type="time"
                                    id="waktu_selesai"
                                    name="waktu_selesai"
                                    value="{{ old('waktu_selesai', $event->waktu_selesai ? \Carbon\Carbon::parse($event->waktu_selesai)->format('H:i') : '') }}"
                                    class="w-full text-sm rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label for="lokasi" class="block text-xs font-semibold text-gray-700 mb-1">
                                Lokasi Pelaksanaan <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="lokasi"
                                name="lokasi"
                                value="{{ old('lokasi', $event->lokasi) }}"
                                required
                                class="w-full text-sm rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('lokasi') border-red-300 @enderror"
                            />
                            @error('lokasi')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="target_peserta" class="block text-xs font-semibold text-gray-700 mb-1">
                                Target / Segmen Peserta <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="target_peserta"
                                name="target_peserta"
                                value="{{ old('target_peserta', $event->target_peserta) }}"
                                required
                                placeholder="Contoh: Mahasiswa & Umum"
                                class="w-full text-sm rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('target_peserta') border-red-300 @enderror"
                            />
                            @error('target_peserta')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="jumlah_peserta" class="block text-xs font-semibold text-gray-700 mb-1">
                                Estimasi Jumlah Peserta <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="jumlah_peserta"
                                name="jumlah_peserta"
                                min="1"
                                value="{{ old('jumlah_peserta', $event->jumlah_peserta) }}"
                                required
                                class="w-full text-sm rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('jumlah_peserta') border-red-300 @enderror"
                            />
                            @error('jumlah_peserta')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Bagian 2: Pengaturan Publikasi & Pendaftaran (T4.6 / T9.x) -->
                <div class="space-y-4 pt-4 border-t border-gray-100" x-data="{ isPublik: {{ old('is_publik', $event->is_publik) ? 'true' : 'false' }}, bukaDaftar: {{ old('pendaftaran_dibuka', $event->pendaftaran_dibuka) ? 'true' : 'false' }} }">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider pb-1 border-b border-gray-100 flex items-center gap-2">
                        <x-icon name="globe" class="w-4 h-4 text-indigo-600" />
                        <span>2. Pengaturan Publikasi & Pendaftaran</span>
                    </h2>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-[10px] border border-gray-200">
                            <div>
                                <label for="is_publik" class="text-sm font-semibold text-gray-900 cursor-pointer">
                                    Tampilkan di Eksplorasi Publik
                                </label>
                                <p class="text-xs text-gray-500">
                                    Jika aktif, acara akan muncul pada halaman Cari Acara publik (S08).
                                </p>
                            </div>
                            <input
                                type="checkbox"
                                id="is_publik"
                                name="is_publik"
                                value="1"
                                x-model="isPublik"
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-show="isPublik" x-transition>
                            <div class="md:col-span-2">
                                <label for="penyelenggara" class="block text-xs font-semibold text-gray-700 mb-1">
                                    Nama Organisasi / Penyelenggara
                                </label>
                                <input
                                    type="text"
                                    id="penyelenggara"
                                    name="penyelenggara"
                                    value="{{ old('penyelenggara', $event->penyelenggara) }}"
                                    placeholder="Contoh: BEM Fakultas Ilmu Komputer"
                                    class="w-full text-sm rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>

                            <div class="md:col-span-2 flex items-center justify-between p-4 bg-purple-50/60 rounded-[10px] border border-purple-200">
                                <div>
                                    <label for="pendaftaran_dibuka" class="text-sm font-semibold text-purple-900 cursor-pointer">
                                        Buka Pendaftaran Peserta Terbuka
                                    </label>
                                    <p class="text-xs text-purple-700">
                                        Izinkan peserta umum mendaftar langsung secara daring melalui sistem.
                                    </p>
                                </div>
                                <input
                                    type="checkbox"
                                    id="pendaftaran_dibuka"
                                    name="pendaftaran_dibuka"
                                    value="1"
                                    x-model="bukaDaftar"
                                    class="h-4 w-4 rounded border-purple-300 text-indigo-600 focus:ring-indigo-500"
                                />
                            </div>

                            <div x-show="bukaDaftar" x-transition>
                                <label for="batas_pendaftaran" class="block text-xs font-semibold text-gray-700 mb-1">
                                    Batas Akhir Pendaftaran
                                </label>
                                <input
                                    type="date"
                                    id="batas_pendaftaran"
                                    name="batas_pendaftaran"
                                    value="{{ old('batas_pendaftaran', $event->batas_pendaftaran ? \Carbon\Carbon::parse($event->batas_pendaftaran)->toDateString() : '') }}"
                                    class="w-full text-sm rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>

                            <div x-show="bukaDaftar" x-transition>
                                <label for="kuota" class="block text-xs font-semibold text-gray-700 mb-1">
                                    Kuota Pendaftaran Publik
                                </label>
                                <input
                                    type="number"
                                    id="kuota"
                                    name="kuota"
                                    min="1"
                                    value="{{ old('kuota', $event->kuota) }}"
                                    placeholder="Misal: 150"
                                    class="w-full text-sm rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a
                        href="{{ route('acara.ringkasan', $event->id) }}"
                        class="px-4 py-2 border border-gray-300 rounded-[8px] text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-xs font-semibold shadow-sm transition-colors"
                    >
                        <x-icon name="check" class="w-4 h-4" />
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
