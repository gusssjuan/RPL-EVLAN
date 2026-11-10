@php
    use App\Enums\StatusVerifikasi;
@endphp

<x-layouts.vendor title="Profil Usaha">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Profil Usaha</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Data ini ditampilkan pada halaman direktori vendor dan digunakan calon penyelenggara acara.
                </p>
            </div>
            <x-status-badge :status="$vendor->status_verifikasi->value" />
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[12px] text-sm font-medium text-emerald-800 flex items-center gap-2.5">
                <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($vendor->status_verifikasi === StatusVerifikasi::Ditolak && $vendor->alasan_penolakan)
            <div class="p-4 bg-red-50 border border-red-200 rounded-[12px] text-sm text-red-800 flex items-start gap-2.5">
                <x-icon name="alert-circle" class="w-5 h-5 text-red-600 shrink-0 mt-0.5" />
                <div>
                    <p class="font-bold">Pendaftaran ditolak Admin</p>
                    <p class="mt-0.5 text-xs">Alasan: {{ $vendor->alasan_penolakan }}</p>
                </div>
            </div>
        @endif

        <form action="{{ route('usaha.profil.update') }}" method="POST" class="bg-white rounded-[12px] border border-gray-200 shadow-sm p-6 space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-input name="nama_vendor" label="Nama Usaha" :value="$vendor->nama_vendor" required placeholder="Contoh: Sinar Sound System" />

                <x-select name="kategori_vendor" label="Kategori Vendor" required>
                    @foreach ($kategori as $item)
                        <option value="{{ $item->value }}" @selected(old('kategori_vendor', $vendor->kategori_vendor) === $item->value)>
                            {{ $item->label() }}
                        </option>
                    @endforeach
                </x-select>

                <x-input name="kota" label="Kota" :value="$vendor->kota" placeholder="Contoh: Denpasar" />

                <x-input name="kontak" label="Kontak (Telepon/WhatsApp)" :value="$vendor->kontak" required placeholder="Contoh: 081234567890" />
            </div>

            <div>
                <label for="alamat" class="block text-sm font-semibold text-gray-800 mb-1.5">
                    Alamat Usaha <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="alamat"
                    name="alamat"
                    rows="3"
                    required
                    placeholder="Alamat lengkap usaha"
                    class="w-full px-3.5 py-2.5 bg-white border {{ $errors->has('alamat') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-indigo-600 focus:ring-indigo-600' }} rounded-[8px] text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 transition-colors"
                >{{ old('alamat', $vendor->alamat) }}</textarea>
                @error('alamat')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <x-icon name="alert-triangle" class="w-3.5 h-3.5 shrink-0" />
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <div>
                <label for="deskripsi" class="block text-sm font-semibold text-gray-800 mb-1.5">Deskripsi Usaha</label>
                <textarea
                    id="deskripsi"
                    name="deskripsi"
                    rows="4"
                    placeholder="Ceritakan layanan, pengalaman, dan keunggulan usaha Anda"
                    class="w-full px-3.5 py-2.5 bg-white border {{ $errors->has('deskripsi') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-indigo-600 focus:ring-indigo-600' }} rounded-[8px] text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 transition-colors"
                >{{ old('deskripsi', $vendor->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <x-icon name="alert-triangle" class="w-3.5 h-3.5 shrink-0" />
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <div class="flex items-center justify-between gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('usaha.dashboard') }}" class="text-xs font-semibold text-gray-500 hover:text-gray-700">Batal</a>
                <x-button type="submit">
                    <x-icon name="check" class="w-4 h-4" />
                    <span>Simpan Profil Usaha</span>
                </x-button>
            </div>
        </form>
    </div>
</x-layouts.vendor>
