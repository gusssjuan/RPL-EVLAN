<x-layouts.guest title="Daftar Akun">
    <div x-data="{ tipe: '{{ old('tipe', 'user') }}' }">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                Buat Akun Baru
            </h2>
            <p class="mt-2 text-sm text-gray-500">
                Pilih peran Anda dan mulai kelola kepanitiaan atau jasa event Anda.
            </p>
        </div>

        <!-- Role Toggle Pill -->
        <div class="mt-6 p-1 bg-gray-100 rounded-[10px] flex gap-1">
            <button
                type="button"
                x-on:click="tipe = 'user'"
                :class="tipe === 'user' ? 'bg-white text-indigo-700 shadow-sm font-semibold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                class="flex-1 py-2 text-xs sm:text-sm rounded-[8px] transition-all text-center flex items-center justify-center gap-1.5"
            >
                <x-icon name="users" class="w-4 h-4" />
                <span>Pengguna (Panitia)</span>
            </button>
            <button
                type="button"
                x-on:click="tipe = 'vendor'"
                :class="tipe === 'vendor' ? 'bg-white text-indigo-700 shadow-sm font-semibold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                class="flex-1 py-2 text-xs sm:text-sm rounded-[8px] transition-all text-center flex items-center justify-center gap-1.5"
            >
                <x-icon name="briefcase" class="w-4 h-4" />
                <span>Vendor Jasa</span>
            </button>
        </div>

        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
            @csrf
            <input type="hidden" name="tipe" :value="tipe">

            <x-input
                name="nama"
                type="text"
                x-bind:label="tipe === 'vendor' ? 'Nama Penanggung Jawab / Pemilik' : 'Nama Lengkap'"
                placeholder="Contoh: Gus Ega"
                value="{{ old('nama') }}"
                required
                autofocus
            />

            <x-input
                name="email"
                type="email"
                label="Alamat Email"
                placeholder="nama@email.com"
                value="{{ old('email') }}"
                required
            />

            <x-input
                name="no_telepon"
                type="text"
                label="Nomor Telepon / WhatsApp"
                placeholder="081234567890"
                value="{{ old('no_telepon') }}"
            />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input
                    name="password"
                    type="password"
                    label="Kata Sandi"
                    placeholder="Min. 8 karakter"
                    required
                />

                <x-input
                    name="password_confirmation"
                    type="password"
                    label="Konfirmasi Sandi"
                    placeholder="Ulangi sandi"
                    required
                />
            </div>

            <!-- Bagian Data Usaha (Khusus Vendor) -->
            <div x-show="tipe === 'vendor'" x-cloak class="pt-4 mt-4 border-t border-gray-100 space-y-4">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Data Usaha Vendor</h3>
                </div>

                <x-input
                    name="nama_vendor"
                    type="text"
                    label="Nama Usaha / Bisnis"
                    placeholder="Contoh: Sinar Sound System"
                    value="{{ old('nama_vendor') }}"
                    x-bind:required="tipe === 'vendor'"
                />

                <x-select
                    name="kategori_vendor"
                    label="Kategori Vendor"
                    x-bind:required="tipe === 'vendor'"
                >
                    <option value="">Pilih kategori vendor</option>
                    @foreach (\App\Enums\KategoriVendor::cases() as $kat)
                        <option value="{{ $kat->value }}" {{ old('kategori_vendor') === $kat->value ? 'selected' : '' }}>
                            {{ $kat->label() }}
                        </option>
                    @endforeach
                </x-select>

                <div class="w-full">
                    <label for="alamat" class="block text-sm font-semibold text-gray-800 mb-1.5">
                        Alamat Usaha <span class="text-red-500" x-show="tipe === 'vendor'">*</span>
                    </label>
                    <textarea
                        name="alamat"
                        id="alamat"
                        rows="2"
                        placeholder="Alamat kantor / gudang usaha Anda"
                        class="w-full px-3.5 py-2.5 bg-white border {{ $errors->has('alamat') ? 'border-red-500' : 'border-gray-300' }} rounded-[8px] text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-indigo-600 transition-colors"
                        x-bind:required="tipe === 'vendor'"
                    >{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <x-icon name="alert-triangle" class="w-3.5 h-3.5 shrink-0" />
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <x-input
                    name="kontak"
                    type="text"
                    label="Kontak Pemesanan / No. WA Bisnis"
                    placeholder="081299990001"
                    value="{{ old('kontak') }}"
                    x-bind:required="tipe === 'vendor'"
                />

                <div class="p-3 bg-amber-50 border border-amber-200 rounded-[8px] text-xs text-amber-800 flex items-start gap-2">
                    <x-icon name="alert-circle" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
                    <span>Akun vendor akan diverifikasi Admin sebelum produk dapat ditambahkan.</span>
                </div>
            </div>

            <div class="pt-3">
                <x-button type="submit" variant="primary" class="w-full py-2.5">
                    Daftar Sekarang
                </x-button>
            </div>
        </form>

        <div class="mt-8 text-center text-sm text-gray-500">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                Masuk ke akun
            </a>
        </div>
    </div>
</x-layouts.guest>

