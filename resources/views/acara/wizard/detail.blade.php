<x-layouts.app title="Detail Acara - Buat Acara">
    <div class="max-w-4xl mx-auto space-y-8">
        <!-- Wizard Header & Stepper -->
        <div class="space-y-4">
            <div>
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Langkah 2 dari 3</span>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 mt-1">
                    Detail Informasi Acara
                </h1>
                <p class="text-sm text-gray-500">
                    Masukkan detail informasi, lokasi, dan tanggal pelaksanaan acara Anda.
                </p>
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

                <div class="p-3 bg-indigo-50 border-2 border-indigo-600 rounded-[10px] flex items-center gap-3">
                    <span class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0">2</span>
                    <div class="min-w-0 hidden sm:block">
                        <p class="text-xs font-bold text-indigo-900 truncate">Detail Acara</p>
                        <p class="text-[10px] text-indigo-600 truncate">Jadwal & lokasi</p>
                    </div>
                </div>

                <div class="p-3 bg-white border border-gray-200 rounded-[10px] flex items-center gap-3 opacity-60">
                    <span class="w-7 h-7 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-xs shrink-0">3</span>
                    <div class="min-w-0 hidden sm:block">
                        <p class="text-xs font-bold text-gray-700 truncate">Rancangan</p>
                        <p class="text-[10px] text-gray-400 truncate">Pratinjau otomatisasi</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Kategori Terpilih -->
        <div class="bg-indigo-50/60 p-4 rounded-[12px] border border-indigo-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <x-category-icon :kategori="$category" class="w-8 h-8" />
                <div>
                    <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider">Kategori Terpilih</span>
                    <p class="text-sm font-bold text-gray-900">{{ $category->nama_kategori }}</p>
                </div>
            </div>
            <a
                href="{{ route('acara.buat.kategori') }}"
                class="text-xs font-semibold text-indigo-700 hover:text-indigo-900 hover:underline"
            >
                Ganti Kategori
            </a>
        </div>

        <!-- Form Detail Acara -->
        <div class="bg-white p-6 sm:p-8 rounded-[16px] border border-gray-200 shadow-sm">
            <form method="POST" action="{{ route('acara.buat.detail.store') }}" class="space-y-6">
                @csrf

                <!-- Nama Acara -->
                <x-input
                    name="nama_event"
                    label="Nama Acara"
                    placeholder="Contoh: Seminar Teknologi Nasional 2026"
                    value="{{ old('nama_event', $draft['nama_event'] ?? '') }}"
                    required
                    autofocus
                />

                <!-- Tujuan Acara -->
                <div class="w-full">
                    <label for="tujuan" class="block text-sm font-semibold text-gray-800 mb-1.5">
                        Tujuan Acara <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        name="tujuan"
                        id="tujuan"
                        rows="3"
                        required
                        placeholder="Jelaskan secara singkat latar belakang dan tujuan diadakannya acara ini..."
                        class="w-full px-3.5 py-2.5 bg-white border {{ $errors->has('tujuan') ? 'border-red-500' : 'border-gray-300' }} rounded-[8px] text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-indigo-600 transition-colors"
                    >{{ old('tujuan', $draft['tujuan'] ?? '') }}</textarea>
                    @error('tujuan')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <x-icon name="alert-triangle" class="w-3.5 h-3.5 shrink-0" />
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Tanggal & Waktu Pelaksanaan -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label for="tanggal" class="block text-sm font-semibold text-gray-800 mb-1.5">
                            Tanggal Acara <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="date"
                            name="tanggal"
                            id="tanggal"
                            required
                            min="{{ date('Y-m-d') }}"
                            value="{{ old('tanggal', $draft['tanggal'] ?? '') }}"
                            class="w-full px-3.5 py-2.5 bg-white border {{ $errors->has('tanggal') ? 'border-red-500' : 'border-gray-300' }} rounded-[8px] text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-indigo-600 transition-colors"
                        >
                        @error('tanggal')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <x-icon name="alert-triangle" class="w-3.5 h-3.5 shrink-0" />
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="waktu_mulai" class="block text-sm font-semibold text-gray-800 mb-1.5">
                            Waktu Mulai
                        </label>
                        <input
                            type="time"
                            name="waktu_mulai"
                            id="waktu_mulai"
                            value="{{ old('waktu_mulai', $draft['waktu_mulai'] ?? '') }}"
                            class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-indigo-600 transition-colors"
                        >
                    </div>

                    <div>
                        <label for="waktu_selesai" class="block text-sm font-semibold text-gray-800 mb-1.5">
                            Waktu Selesai
                        </label>
                        <input
                            type="time"
                            name="waktu_selesai"
                            id="waktu_selesai"
                            value="{{ old('waktu_selesai', $draft['waktu_selesai'] ?? '') }}"
                            class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-indigo-600 transition-colors"
                        >
                    </div>
                </div>

                <!-- Lokasi Acara -->
                <x-input
                    name="lokasi"
                    label="Lokasi Acara"
                    placeholder="Contoh: Auditorium Widya Sabha, Kampus Jimbaran"
                    value="{{ old('lokasi', $draft['lokasi'] ?? '') }}"
                    required
                />

                <!-- Target & Jumlah Peserta -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-input
                        name="target_peserta"
                        label="Target Peserta"
                        placeholder="Contoh: Mahasiswa, Dosen, dan Umum"
                        value="{{ old('target_peserta', $draft['target_peserta'] ?? '') }}"
                        required
                    />

                    <x-input
                        name="jumlah_peserta"
                        type="number"
                        label="Target Jumlah Peserta (Orang)"
                        placeholder="300"
                        min="1"
                        value="{{ old('jumlah_peserta', $draft['jumlah_peserta'] ?? '') }}"
                        required
                    />
                </div>

                <!-- Navigation Action Buttons -->
                <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                    <a
                        href="{{ route('acara.buat.kategori') }}"
                        class="px-4 py-2 border border-gray-300 rounded-[8px] text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        <x-icon name="arrow-left" class="w-4 h-4" />
                        Kembali ke Kategori
                    </a>

                    <x-button type="submit" variant="primary">
                        Lanjut ke Rancangan
                        <x-icon name="arrow-right" class="w-4 h-4" />
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>

