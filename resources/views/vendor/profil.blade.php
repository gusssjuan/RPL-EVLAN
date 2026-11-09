<x-layouts.app title="Profil Usaha">
    <x-slot name="breadcrumb">
        <a href="{{ route('usaha.dashboard') }}" class="hover:text-gray-700">Dashboard Usaha</a>
        <span class="mx-1 text-gray-300">/</span>
        Profil Usaha
    </x-slot>

    <div class="max-w-2xl space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Profil Usaha</h1>
            <p class="mt-1 text-sm text-gray-500">Informasi usaha yang ditampilkan kepada pengguna di halaman pencarian vendor.</p>
        </div>

        <form action="{{ route('usaha.profil.update') }}" method="POST" class="bg-white rounded-[12px] border border-gray-200 p-6 space-y-5">
            @csrf
            @method('PUT')

            <x-input
                name="nama_vendor"
                label="Nama Usaha"
                :value="old('nama_vendor', $vendor->nama_vendor)"
                required
                placeholder="Contoh: Sinar Sound System"
            />

            <div>
                <label for="kategori_vendor" class="block text-sm font-medium text-gray-700 mb-1">Kategori Vendor <span class="text-red-500">*</span></label>
                <select
                    id="kategori_vendor"
                    name="kategori_vendor"
                    class="w-full px-3 py-2 text-sm border rounded-[8px] focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('kategori_vendor') border-red-300 @else border-gray-300 @enderror"
                >
                    @foreach($kategoriOptions as $kat)
                        <option value="{{ $kat->value }}" {{ old('kategori_vendor', $vendor->kategori_vendor) === $kat->value ? 'selected' : '' }}>
                            {{ $kat->label() }}
                        </option>
                    @endforeach
                </select>
                @error('kategori_vendor')
                    <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                        <x-icon name="alert-circle" class="w-3 h-3" /> {{ $message }}
                    </p>
                @enderror
            </div>

            <x-input
                name="alamat"
                label="Alamat"
                :value="old('alamat', $vendor->alamat)"
                required
                placeholder="Jl. Contoh No. 1, Kelurahan, Kecamatan"
            />

            <x-input
                name="kota"
                label="Kota"
                :value="old('kota', $vendor->kota)"
                placeholder="Contoh: Makassar"
            />

            <x-input
                name="kontak"
                label="Kontak (Telepon / WhatsApp)"
                :value="old('kontak', $vendor->kontak)"
                required
                placeholder="Contoh: 081234567890"
            />

            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Usaha</label>
                <textarea
                    id="deskripsi"
                    name="deskripsi"
                    rows="4"
                    maxlength="1000"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-[8px] focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"
                    placeholder="Ceritakan singkat tentang usaha Anda…"
                >{{ old('deskripsi', $vendor->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                        <x-icon name="alert-circle" class="w-3 h-3" /> {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('usaha.dashboard') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">Batal</a>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-[8px] hover:bg-indigo-700 transition-colors">
                    <x-icon name="save" class="w-4 h-4" />
                    Simpan Profil
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
