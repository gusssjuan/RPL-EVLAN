<x-layouts.app title="Vendor & Jasa">
    <x-slot name="breadcrumb">Vendor &amp; Jasa</x-slot>

    <div class="space-y-5">
        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Vendor &amp; Jasa</h1>
            <p class="mt-1 text-sm text-gray-500">Temukan vendor terpercaya untuk mendukung acara Anda.</p>
        </div>

        {{-- Banner transaksi di luar --}}
        <div class="p-3 bg-blue-50 border border-blue-200 rounded-[8px] text-xs text-blue-800 flex items-center gap-2">
            <x-icon name="info" class="w-4 h-4 text-blue-500 shrink-0" />
            Transaksi dilakukan di luar EVLAN. EVLAN hanya memfasilitasi pencarian dan kontak vendor.
        </div>

        {{-- Tab Semua / Tersimpan --}}
        <div class="flex gap-1 bg-gray-100 rounded-[8px] p-1 w-fit">
            <a href="{{ request()->fullUrlWithQuery(['tab' => 'semua']) }}"
               class="px-4 py-1.5 rounded-[6px] text-sm font-medium transition-colors {{ $tab === 'semua' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                Semua
            </a>
            <a href="{{ request()->fullUrlWithQuery(['tab' => 'tersimpan']) }}"
               class="px-4 py-1.5 rounded-[6px] text-sm font-medium transition-colors {{ $tab === 'tersimpan' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                Tersimpan
            </a>
        </div>

        {{-- Filter & Cari --}}
        <form method="GET" action="{{ route('vendor.index') }}" class="flex flex-wrap gap-3">
            <input type="hidden" name="tab" value="{{ $tab }}" />
            <div class="flex-1 min-w-48">
                <div class="relative">
                    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        type="text"
                        name="cari"
                        value="{{ $cari }}"
                        placeholder="Cari nama vendor atau produk…"
                        class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-[8px] focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                </div>
            </div>
            <select name="kategori" class="px-3 py-2 text-sm border border-gray-300 rounded-[8px] focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="semua" {{ !$kategori || $kategori === 'semua' ? 'selected' : '' }}>Semua Kategori</option>
                @foreach($kategoriOptions as $kat)
                    <option value="{{ $kat->value }}" {{ $kategori === $kat->value ? 'selected' : '' }}>{{ $kat->label() }}</option>
                @endforeach
            </select>
            @if($kotaList->isNotEmpty())
                <select name="kota" class="px-3 py-2 text-sm border border-gray-300 rounded-[8px] focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Kota</option>
                    @foreach($kotaList as $k)
                        <option value="{{ $k }}" {{ $kota === $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            @endif
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-[8px] hover:bg-indigo-700 transition-colors">
                Cari
            </button>
            @if($cari || ($kategori && $kategori !== 'semua') || $kota)
                <a href="{{ route('vendor.index', ['tab' => $tab]) }}" class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 rounded-[8px] hover:bg-gray-50 transition-colors">
                    Reset
                </a>
            @endif
        </form>

        {{-- Chip kategori cepat --}}
        <div class="flex flex-wrap gap-2">
            <a href="{{ request()->fullUrlWithQuery(['kategori' => 'semua']) }}"
               class="px-3 py-1 rounded-full text-xs font-medium transition-colors {{ !$kategori || $kategori === 'semua' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Semua
            </a>
            @foreach($kategoriOptions as $kat)
                <a href="{{ request()->fullUrlWithQuery(['kategori' => $kat->value]) }}"
                   class="px-3 py-1 rounded-full text-xs font-medium transition-colors {{ $kategori === $kat->value ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $kat->label() }}
                </a>
            @endforeach
        </div>

        {{-- Grid Vendor --}}
        @if($vendors->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($vendors as $v)
                    @php
                        $isSaved = in_array($v->id, $savedVendorIds);
                        $kat = \App\Enums\KategoriVendor::tryFrom($v->kategori_vendor);
                    @endphp
                    <div class="bg-white rounded-[12px] border border-gray-200 p-5 flex flex-col gap-3 hover:border-indigo-300 hover:shadow-sm transition-all">
                        {{-- Header kartu --}}
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-[8px] bg-indigo-50 flex items-center justify-center shrink-0">
                                    <x-icon name="briefcase" class="w-5 h-5 text-indigo-600" />
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 leading-tight">{{ $v->nama_vendor }}</h3>
                                    <span class="text-xs text-gray-500">{{ $kat?->label() ?? $v->kategori_vendor }}</span>
                                </div>
                            </div>
                            <form action="{{ route('vendor.toggle-save', $v) }}" method="POST">
                                @csrf
                                <button type="submit" title="{{ $isSaved ? 'Hapus dari tersimpan' : 'Simpan vendor' }}"
                                    class="p-1.5 rounded-lg transition-colors {{ $isSaved ? 'text-indigo-600 hover:text-indigo-800' : 'text-gray-400 hover:text-gray-600' }}">
                                    <x-icon name="{{ $isSaved ? 'bookmark' : 'bookmark' }}" class="w-5 h-5 {{ $isSaved ? 'fill-indigo-600' : '' }}" />
                                </button>
                            </form>
                        </div>

                        {{-- Badge Terverifikasi & Kota --}}
                        <div class="flex flex-wrap items-center gap-2">
                            <x-badge color="success" icon="shield-check">Terverifikasi</x-badge>
                            @if($v->kota)
                                <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                    <x-icon name="map-pin" class="w-3 h-3" />{{ $v->kota }}
                                </span>
                            @endif
                        </div>

                        {{-- Harga mulai --}}
                        @php $hargaMulai = $v->harga_mulai; @endphp
                        <div class="text-sm">
                            @if($hargaMulai !== null)
                                <span class="text-gray-500">Mulai </span>
                                <span class="font-semibold text-indigo-700">{{ format_rupiah($hargaMulai) }}</span>
                            @else
                                <span class="text-gray-400 italic text-xs">Belum ada produk</span>
                            @endif
                        </div>

                        <a href="{{ route('vendor.show', $v) }}" class="mt-auto inline-flex items-center justify-center gap-2 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-800 border border-indigo-200 rounded-[8px] hover:bg-indigo-50 transition-colors">
                            Lihat Detail →
                        </a>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div>{{ $vendors->links() }}</div>

        @elseif($tab === 'tersimpan')
            <x-empty-state
                icon="bookmark"
                title="Belum ada vendor tersimpan"
                description="Simpan vendor favorit Anda dengan menekan ikon bookmark di kartu vendor."
            />
        @else
            <x-empty-state
                icon="search"
                title="Vendor tidak ditemukan"
                description="Coba ubah kata kunci atau filter pencarian Anda."
            />
        @endif
    </div>
</x-layouts.app>
