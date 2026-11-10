@php
    use App\Enums\KategoriVendor;
    use App\Enums\StatusVerifikasi;

    $status = $vendor->status_verifikasi;
    $kategoriLabel = KategoriVendor::tryFrom($vendor->kategori_vendor)?->label() ?? $vendor->kategori_vendor;
@endphp

<x-layouts.vendor title="Dashboard Vendor">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    Dashboard Usaha Vendor
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola profil usaha dan katalog produk/jasa yang Anda tawarkan.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('usaha.profil.edit') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-[8px] text-xs font-semibold shadow-sm transition-colors">
                    <x-icon name="edit" class="w-3.5 h-3.5" />
                    <span>Edit Profil Usaha</span>
                </a>
                @if ($status === StatusVerifikasi::Terverifikasi)
                    <a href="{{ route('usaha.produk.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-xs font-semibold shadow-sm transition-colors">
                        <x-icon name="plus" class="w-3.5 h-3.5" />
                        <span>Kelola Produk</span>
                    </a>
                @endif
            </div>
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[12px] text-sm font-medium text-emerald-800 flex items-center gap-2.5">
                <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($status === StatusVerifikasi::Menunggu)
            <div class="p-5 bg-amber-50 border border-amber-200 rounded-[12px] flex items-start gap-3">
                <x-icon name="clock" class="w-6 h-6 text-amber-600 shrink-0 mt-0.5" />
                <div>
                    <h2 class="text-sm font-bold text-amber-900">Pendaftaran usaha sedang ditinjau Admin</h2>
                    <p class="mt-1 text-xs text-amber-800">
                        Anda dapat melengkapi profil usaha terlebih dahulu. Menu kelola produk/jasa akan terbuka otomatis setelah akun vendor diverifikasi.
                    </p>
                </div>
            </div>
        @elseif ($status === StatusVerifikasi::Ditolak)
            <div class="p-5 bg-red-50 border border-red-200 rounded-[12px] flex items-start gap-3">
                <x-icon name="alert-circle" class="w-6 h-6 text-red-600 shrink-0 mt-0.5" />
                <div>
                    <h2 class="text-sm font-bold text-red-900">Pendaftaran usaha ditolak</h2>
                    <p class="mt-1 text-xs text-red-800">
                        Alasan: {{ $vendor->alasan_penolakan ?: 'Tidak disebutkan.' }}
                    </p>
                    <p class="mt-2 text-xs text-red-700">
                        Perbarui data profil usaha Anda, lalu hubungi Admin untuk peninjauan ulang.
                    </p>
                </div>
            </div>
        @else
            <div class="p-5 bg-emerald-50 border border-emerald-200 rounded-[12px] flex items-start gap-3">
                <x-icon name="check-circle" class="w-6 h-6 text-emerald-600 shrink-0 mt-0.5" />
                <div>
                    <h2 class="text-sm font-bold text-emerald-900">Usaha Anda sudah terverifikasi</h2>
                    <p class="mt-1 text-xs text-emerald-800">
                        Produk/jasa aktif akan tampil di direktori vendor dan dapat dipilih pengguna saat menyusun estimasi anggaran.
                    </p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-[12px] border border-gray-200 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status Verifikasi</p>
                <div class="mt-2">
                    <x-status-badge :status="$status->value" />
                </div>
                <p class="mt-2 text-[11px] text-gray-500">
                    @if ($status === StatusVerifikasi::Terverifikasi)
                        Terverifikasi {{ $vendor->verified_at?->isoFormat('D MMM Y') }}
                    @else
                        {!! $status === StatusVerifikasi::Menunggu ? 'Menunggu keputusan Admin.' : 'Silakan perbaiki profil usaha.' !!}
                    @endif
                </p>
            </div>

            <div class="bg-white p-5 rounded-[12px] border border-gray-200 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Produk / Jasa</p>
                <p class="mt-1.5 text-2xl font-bold text-gray-900">{{ $vendor->active_products_count }} <span class="text-sm font-medium text-gray-400">/ {{ $vendor->products_count }} aktif</span></p>
                <p class="mt-2 text-[11px] text-gray-500">Total produk dan jasa yang tercatat.</p>
            </div>

            <div class="bg-white p-5 rounded-[12px] border border-gray-200 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nilai Katalog Aktif</p>
                <p class="mt-1.5 text-2xl font-bold text-gray-900">{{ format_rupiah($totalNilaiProduk) }}</p>
                <p class="mt-2 text-[11px] text-gray-500">Akumulasi harga produk/jasa aktif.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-[12px] border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Produk/Jasa Terbaru</h2>
                        <p class="text-xs text-gray-500">5 entri terakhir pada katalog usaha Anda.</p>
                    </div>
                    <a href="{{ $status === StatusVerifikasi::Terverifikasi ? route('usaha.produk.index') : '#' }}" class="text-xs font-semibold {{ $status === StatusVerifikasi::Terverifikasi ? 'text-indigo-600 hover:underline' : 'text-gray-400 cursor-not-allowed' }}">
                        Lihat semua
                    </a>
                </div>

                @if ($produkTerbaru->isEmpty())
                    <div class="p-5">
                        <x-empty-state
                            title="Belum ada produk/jasa"
                            description="{{ $status === StatusVerifikasi::Terverifikasi ? 'Tambahkan produk atau jasa pertama Anda agar muncul di direktori vendor.' : 'Produk/jasa dapat ditambahkan setelah akun usaha terverifikasi Admin.' }}"
                            icon="tag"
                        >
                            @if ($status === StatusVerifikasi::Terverifikasi)
                                <a href="{{ route('usaha.produk.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-xs font-semibold shadow-sm transition-colors">
                                    <x-icon name="plus" class="w-3.5 h-3.5" />
                                    <span>Tambah Produk/Jasa</span>
                                </a>
                            @endif
                        </x-empty-state>
                    </div>
                @else
                    <ul class="divide-y divide-gray-100">
                        @foreach ($produkTerbaru as $item)
                            <li class="px-5 py-3.5 flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $item->nama_produk }}</p>
                                    <p class="text-xs text-gray-500 line-clamp-1">{{ $item->deskripsi ?: 'Tanpa deskripsi' }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-sm font-bold text-gray-900">{{ format_rupiah($item->harga) }}</p>
                                    <x-badge :variant="$item->is_aktif ? 'success' : 'neutral'">
                                        {{ $item->is_aktif ? 'Aktif' : 'Nonaktif' }}
                                    </x-badge>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="bg-white rounded-[12px] border border-gray-200 shadow-sm p-5 space-y-4">
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Profil Usaha</h2>
                    <p class="text-xs text-gray-500">Ringkasan data yang tampil di direktori vendor.</p>
                </div>

                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Nama Usaha</p>
                        <p class="font-semibold text-gray-900">{{ $vendor->nama_vendor }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Kategori</p>
                        <p class="text-gray-800">{{ $kategoriLabel }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Kontak</p>
                        <p class="text-gray-800">{{ $vendor->kontak }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Alamat</p>
                        <p class="text-gray-800">{{ $vendor->alamat }}{{ $vendor->kota ? ', ' . $vendor->kota : '' }}</p>
                    </div>
                </div>

                <a href="{{ route('usaha.profil.edit') }}" class="inline-flex items-center justify-center gap-1.5 w-full px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-[8px] text-xs font-semibold transition-colors">
                    <x-icon name="edit" class="w-3.5 h-3.5" />
                    <span>Perbarui Profil</span>
                </a>
            </div>
        </div>

        <div class="p-4 bg-gray-100 border border-gray-200 rounded-[12px] text-xs text-gray-600 flex items-start gap-2.5">
            <x-icon name="alert-circle" class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" />
            <p>Transaksi dan pembayaran dilakukan di luar EVLAN. Informasi produk/jasa hanya referensi bagi calon penyelenggara acara.</p>
        </div>
    </div>
</x-layouts.vendor>
