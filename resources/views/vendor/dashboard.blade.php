<x-layouts.app title="Dashboard Usaha">
    <x-slot name="breadcrumb">Dashboard Usaha</x-slot>

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard Usaha</h1>
                <p class="mt-1 text-sm text-gray-500">Kelola profil dan katalog produk/layanan Anda.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('usaha.profil') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-[8px] hover:bg-gray-50 transition-colors">
                    <x-icon name="settings" class="w-4 h-4" />
                    Profil Usaha
                </a>
                <a href="{{ route('usaha.produk.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-[8px] hover:bg-indigo-700 transition-colors">
                    <x-icon name="package" class="w-4 h-4" />
                    Kelola Produk
                </a>
            </div>
        </div>

        {{-- Banner Status Verifikasi --}}
        @if($vendor->status_verifikasi === \App\Enums\StatusVerifikasi::Menunggu)
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-[12px] flex items-start gap-3">
                <x-icon name="clock" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
                <div>
                    <h3 class="text-sm font-semibold text-amber-900">Akun Menunggu Verifikasi</h3>
                    <p class="mt-1 text-sm text-amber-800">Akun vendor Anda sedang dalam proses verifikasi oleh Admin. Setelah disetujui, Anda dapat menambahkan produk/jasa ke katalog.</p>
                </div>
            </div>
        @elseif($vendor->status_verifikasi === \App\Enums\StatusVerifikasi::Ditolak)
            <div class="p-4 bg-red-50 border border-red-200 rounded-[12px] flex items-start gap-3">
                <x-icon name="x-circle" class="w-5 h-5 text-red-600 shrink-0 mt-0.5" />
                <div>
                    <h3 class="text-sm font-semibold text-red-900">Verifikasi Ditolak</h3>
                    <p class="mt-1 text-sm text-red-800">
                        {{ $vendor->alasan_penolakan ?? 'Akun vendor Anda ditolak oleh Admin. Silakan hubungi administrator untuk informasi lebih lanjut.' }}
                    </p>
                </div>
            </div>
        @elseif($vendor->status_verifikasi === \App\Enums\StatusVerifikasi::Terverifikasi)
            <div class="p-4 bg-green-50 border border-green-200 rounded-[12px] flex items-start gap-3">
                <x-icon name="check-circle" class="w-5 h-5 text-green-600 shrink-0 mt-0.5" />
                <div>
                    <h3 class="text-sm font-semibold text-green-900">Akun Terverifikasi</h3>
                    <p class="mt-1 text-sm text-green-800">Akun Anda sudah terverifikasi. Produk/jasa Anda dapat ditemukan oleh pengguna di halaman pencarian vendor.</p>
                </div>
            </div>
        @endif

        {{-- Statistik --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-stat-card
                label="Status Verifikasi"
                :value="$vendor->status_verifikasi->label()"
                icon="shield-check"
                color="{{ $vendor->status_verifikasi->color() }}"
            />
            <x-stat-card
                label="Total Produk/Jasa"
                :value="$jumlahProduk"
                icon="package"
                color="info"
            />
            <x-stat-card
                label="Produk Aktif"
                :value="$produkAktif"
                icon="check-square"
                color="success"
            />
        </div>

        {{-- Info Usaha --}}
        <div class="bg-white rounded-[12px] border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-gray-900">Informasi Usaha</h2>
                <a href="{{ route('usaha.profil') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                    Edit Profil →
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Nama Usaha</span>
                    <p class="mt-0.5 font-medium text-gray-900">{{ $vendor->nama_vendor }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Kategori</span>
                    <p class="mt-0.5 font-medium text-gray-900">
                        @php
                            $kategoriEnum = \App\Enums\KategoriVendor::tryFrom($vendor->kategori_vendor);
                        @endphp
                        {{ $kategoriEnum?->label() ?? $vendor->kategori_vendor }}
                    </p>
                </div>
                <div>
                    <span class="text-gray-500">Kontak</span>
                    <p class="mt-0.5 font-medium text-gray-900">{{ $vendor->kontak }}</p>
                </div>
                @if($vendor->kota)
                <div>
                    <span class="text-gray-500">Kota</span>
                    <p class="mt-0.5 font-medium text-gray-900">{{ $vendor->kota }}</p>
                </div>
                @endif
                @if($vendor->deskripsi)
                <div class="sm:col-span-2">
                    <span class="text-gray-500">Deskripsi</span>
                    <p class="mt-0.5 text-gray-900">{{ $vendor->deskripsi }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Produk Terbaru --}}
        @if($vendor->products->count() > 0)
            <div class="bg-white rounded-[12px] border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-gray-900">Produk/Jasa Terbaru</h2>
                    <a href="{{ route('usaha.produk.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                        Lihat semua →
                    </a>
                </div>
                <div class="space-y-3">
                    @foreach($vendor->products->take(5) as $produk)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                                    <x-icon name="package" class="w-4 h-4 text-indigo-600" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $produk->nama_produk }}</p>
                                    @if($produk->deskripsi)
                                        <p class="text-xs text-gray-500 truncate max-w-xs">{{ $produk->deskripsi }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-semibold text-gray-900">{{ format_rupiah($produk->harga) }}</span>
                                @if($produk->is_aktif)
                                    <x-badge color="success" icon="check">Aktif</x-badge>
                                @else
                                    <x-badge color="neutral" icon="minus">Nonaktif</x-badge>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-white rounded-[12px] border border-gray-200 p-6">
                <x-empty-state
                    icon="package"
                    title="Belum ada produk/jasa"
                    description="{{ $vendor->status_verifikasi === \App\Enums\StatusVerifikasi::Terverifikasi ? 'Tambahkan produk atau layanan pertama Anda untuk mulai ditampilkan ke pengguna.' : 'Produk dapat ditambahkan setelah akun Anda diverifikasi oleh Admin.' }}"
                >
                    @if($vendor->status_verifikasi === \App\Enums\StatusVerifikasi::Terverifikasi)
                        <a href="{{ route('usaha.produk.index') }}" class="mt-3 inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-[8px] hover:bg-indigo-700">
                            <x-icon name="plus" class="w-4 h-4" />
                            Tambah Produk
                        </a>
                    @endif
                </x-empty-state>
            </div>
        @endif
    </div>
</x-layouts.app>
