@php
    use App\Enums\KategoriVendor;

    $meta = match ($vendor->kategori_vendor) {
        'catering' => ['emoji' => '🍽️', 'bg' => 'bg-orange-50 text-orange-600'],
        'dekorasi' => ['emoji' => '🎀', 'bg' => 'bg-pink-50 text-pink-600'],
        'dokumentasi' => ['emoji' => '📷', 'bg' => 'bg-sky-50 text-sky-600'],
        'sound_system' => ['emoji' => '🔊', 'bg' => 'bg-indigo-50 text-indigo-600'],
        'perlengkapan_acara' => ['emoji' => '🎪', 'bg' => 'bg-emerald-50 text-emerald-600'],
        default => ['emoji' => '🏷️', 'bg' => 'bg-gray-100 text-gray-600'],
    };

    $bisaTambahAnggaran = $acaraLeader->isNotEmpty() && $vendor->products->isNotEmpty();
@endphp

<x-layouts.app :title="'Vendor - ' . $vendor->nama_vendor">
    <div class="space-y-6" x-data="{ modalAnggaran: false }">
        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('vendor.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 hover:text-indigo-600 transition-colors">
                <x-icon name="chevron-right" class="w-4 h-4 rotate-180" />
                <span>Kembali ke Direktori Vendor</span>
            </a>
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[12px] text-sm font-medium text-emerald-800 flex items-center gap-2.5">
                <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-[12px] text-xs font-medium text-red-700 space-y-1">
                <div class="flex items-center gap-2 font-bold text-red-800">
                    <x-icon name="alert-circle" class="w-4 h-4" />
                    <span>Terjadi kesalahan:</span>
                </div>
                <ul class="list-disc list-inside pl-2 text-red-600 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Profil Vendor --}}
        <div class="bg-white rounded-[16px] border border-gray-200 shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-5">
                <div class="flex items-start gap-4 min-w-0">
                    <div class="w-16 h-16 rounded-[14px] {{ $meta['bg'] }} flex items-center justify-center text-3xl shrink-0">
                        {{ $meta['emoji'] }}
                    </div>
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-2xl font-extrabold tracking-tight text-gray-900">{{ $vendor->nama_vendor }}</h1>
                            <x-badge variant="success">
                                <span class="font-bold text-[11px] leading-none">✓</span>
                                <span>Terverifikasi</span>
                            </x-badge>
                        </div>
                        <p class="mt-1 text-sm font-medium text-indigo-600">{{ $kategoriLabel }}</p>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500 mt-2">
                            @if ($vendor->kota)
                                <span class="inline-flex items-center gap-1">
                                    <x-icon name="map-pin" class="w-3.5 h-3.5" />
                                    {{ $vendor->kota }}
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-1">
                                <x-icon name="phone" class="w-3.5 h-3.5" />
                                {{ $vendor->kontak }}
                            </span>
                            @if ($vendor->user?->email)
                                <span class="inline-flex items-center gap-1">
                                    <x-icon name="mail" class="w-3.5 h-3.5" />
                                    {{ $vendor->user->email }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $vendor->kontak) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-[8px] text-xs font-semibold shadow-sm transition-colors">
                        <x-icon name="phone" class="w-3.5 h-3.5" />
                        <span>Telepon</span>
                    </a>
                    @if ($waNumber)
                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-[8px] text-xs font-semibold shadow-sm transition-colors">
                            <x-icon name="phone" class="w-3.5 h-3.5" />
                            <span>WhatsApp</span>
                        </a>
                    @endif

                    <form action="{{ $tersimpan ? route('vendor.batal-simpan', $vendor->id) : route('vendor.simpan', $vendor->id) }}" method="POST">
                        @csrf
                        @if ($tersimpan)
                            @method('DELETE')
                        @endif
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-[8px] text-xs font-semibold shadow-sm transition-colors {{ $tersimpan ? 'bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                            <x-icon name="bookmark" class="w-3.5 h-3.5" />
                            <span>{{ $tersimpan ? 'Tersimpan' : 'Simpan' }}</span>
                        </button>
                    </form>
                </div>
            </div>

            @if ($vendor->deskripsi)
                <div class="mt-5 pt-5 border-t border-gray-100">
                    <h2 class="text-sm font-bold text-gray-900">Tentang Usaha</h2>
                    <p class="mt-1.5 text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $vendor->deskripsi }}</p>
                </div>
            @endif

            <div class="mt-4 pt-4 border-t border-gray-100">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-400">Alamat</h2>
                <p class="mt-1 text-sm text-gray-700">{{ $vendor->alamat }}{{ $vendor->kota ? ', ' . $vendor->kota : '' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Produk/Jasa --}}
            <div class="lg:col-span-2 bg-white rounded-[12px] border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Produk &amp; Jasa</h2>
                        <p class="text-xs text-gray-500">Daftar penawaran aktif dari vendor ini.</p>
                    </div>
                    <x-badge variant="primary">{{ $vendor->products->count() }} item</x-badge>
                </div>

                @if ($vendor->products->isEmpty())
                    <div class="p-5">
                        <x-empty-state title="Belum ada produk/jasa" description="Vendor ini belum menambahkan produk atau jasa aktif." icon="tag" />
                    </div>
                @else
                    <ul class="divide-y divide-gray-100">
                        @foreach ($vendor->products as $produk)
                            <li class="px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900">{{ $produk->nama_produk }}</p>
                                    <p class="text-xs text-gray-500">{{ $produk->deskripsi ?: 'Tanpa deskripsi' }}</p>
                                </div>
                                <p class="text-sm font-bold text-gray-900 whitespace-nowrap">{{ format_rupiah($produk->harga) }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Panel Aksi --}}
            <div class="space-y-4">
                <div class="bg-indigo-50 border border-indigo-200 rounded-[12px] p-5 space-y-3">
                    <div class="flex items-center gap-2">
                        <x-icon name="credit-card" class="w-5 h-5 text-indigo-600" />
                        <h2 class="text-sm font-bold text-indigo-900">Estimasi Anggaran</h2>
                    </div>

                    @if ($acaraLeader->isEmpty())
                        <p class="text-xs text-indigo-700">
                            Tambahkan vendor ini ke anggaran acara yang Anda pimpin. Saat ini Anda belum memimpin acara berstatus perencanaan.
                        </p>
                        <a href="{{ route('acara.buat.kategori') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-indigo-200 text-indigo-700 rounded-[8px] text-xs font-semibold hover:bg-indigo-50 transition-colors">
                            <x-icon name="plus" class="w-3.5 h-3.5" />
                            <span>Buat Acara</span>
                        </a>
                    @else
                        <p class="text-xs text-indigo-700">
                            Pilih acara, produk/jasa, dan jumlah. Harga akan masuk otomatis ke estimasi anggaran acara.
                        </p>
                        <button
                            type="button"
                            x-on:click="modalAnggaran = true"
                            @disabled($vendor->products->isEmpty())
                            class="inline-flex items-center justify-center gap-1.5 w-full px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-[8px] text-xs font-semibold shadow-sm transition-colors"
                        >
                            <x-icon name="plus" class="w-3.5 h-3.5" />
                            <span>Tambahkan ke Anggaran</span>
                        </button>
                    @endif
                </div>

                <div class="bg-gray-100 border border-gray-200 rounded-[12px] p-4 text-[11px] text-gray-600 flex items-start gap-2">
                    <x-icon name="alert-circle" class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" />
                    <p>EVLAN tidak memproses pembayaran. Hubungi vendor langsung untuk kesepakatan dan transaksi.</p>
                </div>
            </div>
        </div>

        {{-- Modal Tambah ke Anggaran --}}
        @if ($bisaTambahAnggaran)
            <div x-show="modalAnggaran" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" x-on:click.self="modalAnggaran = false" x-on:keydown.escape.window="modalAnggaran = false">
                <div class="bg-white rounded-[16px] p-6 w-full max-w-md shadow-lg space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-gray-900">Tambahkan ke Anggaran</h3>
                        <button type="button" x-on:click="modalAnggaran = false" class="text-gray-400 hover:text-gray-600">
                            <x-icon name="x" class="w-5 h-5" />
                        </button>
                    </div>

                    <form action="{{ route('vendor.anggaran.store', $vendor->id) }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label for="event_id" class="block text-sm font-semibold text-gray-800 mb-1.5">Acara <span class="text-red-500">*</span></label>
                            <select id="event_id" name="event_id" required class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm text-gray-900 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                                @foreach ($acaraLeader as $acara)
                                    <option value="{{ $acara->id }}">{{ $acara->nama_event }} — {{ $acara->tanggal->isoFormat('D MMM Y') }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="vendor_product_id" class="block text-sm font-semibold text-gray-800 mb-1.5">Produk/Jasa <span class="text-red-500">*</span></label>
                            <select id="vendor_product_id" name="vendor_product_id" required class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm text-gray-900 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                                @foreach ($vendor->products as $produk)
                                    <option value="{{ $produk->id }}">{{ $produk->nama_produk }} — {{ format_rupiah($produk->harga) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="jumlah" class="block text-sm font-semibold text-gray-800 mb-1.5">Jumlah <span class="text-red-500">*</span></label>
                            <input type="number" id="jumlah" name="jumlah" value="{{ old('jumlah', 1) }}" min="1" step="1" required class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm text-gray-900 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                        </div>

                        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                            <x-button type="button" variant="secondary" x-on:click="modalAnggaran = false">Batal</x-button>
                            <x-button type="submit">
                                <x-icon name="plus" class="w-4 h-4" />
                                <span>Tambahkan</span>
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
