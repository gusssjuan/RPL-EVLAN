@php
    use App\Enums\KategoriVendor;

    $kategoriMeta = [
        'catering' => ['emoji' => '🍽️', 'bg' => 'bg-orange-50 text-orange-600'],
        'dekorasi' => ['emoji' => '🎀', 'bg' => 'bg-pink-50 text-pink-600'],
        'dokumentasi' => ['emoji' => '📷', 'bg' => 'bg-sky-50 text-sky-600'],
        'sound_system' => ['emoji' => '🔊', 'bg' => 'bg-indigo-50 text-indigo-600'],
        'perlengkapan_acara' => ['emoji' => '🎪', 'bg' => 'bg-emerald-50 text-emerald-600'],
    ];

    $kategoriChips = ['' => 'Semua'];
    foreach (KategoriVendor::cases() as $case) {
        $kategoriChips[$case->value] = $case->label();
    }
@endphp

<x-layouts.app title="Cari Vendor">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Cari Vendor &amp; Jasa</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Temukan penyedia kebutuhan acara yang sudah diverifikasi Admin.
                </p>
            </div>
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[12px] text-sm font-medium text-emerald-800 flex items-center gap-2.5">
                <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-[12px] flex items-start gap-2.5 text-xs text-indigo-800">
            <x-icon name="alert-circle" class="w-4 h-4 text-indigo-500 shrink-0 mt-0.5" />
            <p>Informasi vendor bersifat referensi. Transaksi dan pembayaran dilakukan di luar EVLAN.</p>
        </div>

        {{-- Tabs --}}
        <div class="flex items-center gap-2 border-b border-gray-200">
            <a
                href="{{ route('vendor.index', array_merge(request()->except('tab', 'page'), ['tab' => 'semua'])) }}"
                class="px-4 py-2.5 text-sm font-semibold -mb-px border-b-2 transition-colors {{ $tab === 'tersimpan' ? 'border-transparent text-gray-500 hover:text-gray-800' : 'border-indigo-600 text-indigo-600' }}"
            >
                Semua Vendor ({{ $counts['semua'] }})
            </a>
            <a
                href="{{ route('vendor.index', array_merge(request()->except('tab', 'page'), ['tab' => 'tersimpan'])) }}"
                class="px-4 py-2.5 text-sm font-semibold -mb-px border-b-2 transition-colors {{ $tab === 'tersimpan' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-800' }}"
            >
                Tersimpan ({{ $counts['tersimpan'] }})
            </a>
        </div>

        {{-- Search & Filter --}}
        <div class="bg-white p-4 rounded-[12px] border border-gray-200 shadow-sm space-y-4">
            <form method="GET" action="{{ route('vendor.index') }}" class="flex flex-col lg:flex-row gap-3">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <x-icon name="search" class="w-4 h-4" />
                    </span>
                    <input
                        type="text"
                        name="cari"
                        value="{{ $search }}"
                        placeholder="Cari nama vendor atau produk/jasa…"
                        class="w-full pl-9 pr-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 transition-colors"
                    >
                </div>

                <div class="flex gap-2">
                    <select name="kota" class="w-44 px-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm text-gray-900 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                        <option value="">Semua kota</option>
                        @foreach ($daftarKota as $namaKota)
                            <option value="{{ $namaKota }}" @selected($kota === $namaKota)>{{ $namaKota }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-xs font-semibold shadow-sm transition-colors">
                        <x-icon name="search" class="w-3.5 h-3.5" />
                        <span>Cari</span>
                    </button>
                </div>
            </form>

            <div class="flex items-center gap-1.5 overflow-x-auto pb-1">
                @foreach ($kategoriChips as $value => $label)
                    @php
                        $isActive = ($value === '' && $kategori === 'semua') || $value === $kategori;
                        $params = array_merge(request()->except('kategori', 'page'), ['kategori' => $value === '' ? 'semua' : $value]);
                    @endphp
                    <a
                        href="{{ route('vendor.index', $params) }}"
                        class="px-3 py-1.5 rounded-[8px] text-xs font-semibold whitespace-nowrap transition-colors {{ $isActive ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        @if ($vendors->isEmpty())
            <x-empty-state
                title="{{ $tab === 'tersimpan' ? 'Belum ada vendor tersimpan' : 'Vendor tidak ditemukan' }}"
                description="{{ $tab === 'tersimpan' ? 'Simpan vendor yang menarik agar mudah ditemukan kembali.' : 'Coba ubah kata kunci, kategori, atau filter kota.' }}"
                icon="briefcase"
            >
                @if ($tab === 'tersimpan')
                    <a href="{{ route('vendor.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-xs font-semibold shadow-sm transition-colors">
                        <span>Jelajahi Semua Vendor</span>
                    </a>
                @endif
            </x-empty-state>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach ($vendors as $vendor)
                    @php
                        $meta = $kategoriMeta[$vendor->kategori_vendor] ?? ['emoji' => '🏷️', 'bg' => 'bg-gray-100 text-gray-600'];
                        $labelKategori = KategoriVendor::tryFrom($vendor->kategori_vendor)?->label() ?? $vendor->kategori_vendor;
                        $disimpan = in_array($vendor->id, $savedIds, true);
                    @endphp

                    <div class="bg-white rounded-[12px] border border-gray-200 shadow-sm overflow-hidden flex flex-col hover:border-indigo-200 hover:shadow-md transition-all">
                        <div class="p-5 space-y-3 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-11 h-11 rounded-[10px] {{ $meta['bg'] }} flex items-center justify-center text-xl shrink-0">
                                        {{ $meta['emoji'] }}
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('vendor.show', $vendor->id) }}" class="block text-sm font-bold text-gray-900 truncate hover:text-indigo-700">
                                            {{ $vendor->nama_vendor }}
                                        </a>
                                        <p class="text-[11px] font-medium text-gray-500">{{ $labelKategori }}</p>
                                    </div>
                                </div>

                                <form action="{{ $disimpan ? route('vendor.batal-simpan', $vendor->id) : route('vendor.simpan', $vendor->id) }}" method="POST" class="shrink-0">
                                    @csrf
                                    @if ($disimpan)
                                        @method('DELETE')
                                    @endif
                                    <button type="submit" class="p-1.5 rounded-md transition-colors {{ $disimpan ? 'text-indigo-600 bg-indigo-50 hover:bg-indigo-100' : 'text-gray-400 hover:text-indigo-600 hover:bg-gray-50' }}" title="{{ $disimpan ? 'Hapus dari tersimpan' : 'Simpan vendor' }}">
                                        <x-icon name="bookmark" class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <x-badge variant="success">
                                    <span class="font-bold text-[11px] leading-none">✓</span>
                                    <span>Terverifikasi</span>
                                </x-badge>
                                @if ($vendor->kota)
                                    <span class="inline-flex items-center gap-1 text-[11px] text-gray-500">
                                        <x-icon name="map-pin" class="w-3.5 h-3.5" />
                                        {{ $vendor->kota }}
                                    </span>
                                @endif
                            </div>

                            <p class="text-xs text-gray-500 line-clamp-2 min-h-[2.5rem]">
                                {{ $vendor->deskripsi ?: 'Vendor kebutuhan acara terverifikasi.' }}
                            </p>
                        </div>

                        <div class="px-5 py-3.5 border-t border-gray-100 flex items-center justify-between gap-3 bg-gray-50/60">
                            <div>
                                <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Mulai dari</p>
                                @if ($vendor->harga_mulai !== null)
                                    <p class="text-sm font-bold text-gray-900">{{ format_rupiah($vendor->harga_mulai) }}</p>
                                @else
                                    <p class="text-xs font-semibold text-gray-400">Harga belum tersedia</p>
                                @endif
                            </div>
                            <a href="{{ route('vendor.show', $vendor->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-[8px] text-[11px] font-semibold shadow-sm transition-colors">
                                <span>Lihat Detail</span>
                                <x-icon name="chevron-right" class="w-3 h-3" />
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div>
                {{ $vendors->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
