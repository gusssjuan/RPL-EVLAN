<x-layouts.app :title="$vendor->nama_vendor">
    <x-slot name="breadcrumb">
        <a href="{{ route('vendor.index') }}" class="hover:text-gray-700">Vendor &amp; Jasa</a>
        <span class="mx-1 text-gray-300">/</span>
        {{ $vendor->nama_vendor }}
    </x-slot>

    <div class="space-y-6" x-data="{ showTambah: false, acaraId: '', produkId: '', jumlah: 1 }">
        {{-- Header Profil Vendor --}}
        <div class="bg-white rounded-[12px] border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-16 h-16 rounded-[12px] bg-indigo-50 flex items-center justify-center shrink-0">
                        <x-icon name="briefcase" class="w-8 h-8 text-indigo-600" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-xl font-bold text-gray-900">{{ $vendor->nama_vendor }}</h1>
                            <x-badge color="success" icon="shield-check">Terverifikasi</x-badge>
                        </div>
                        @php $kat = \App\Enums\KategoriVendor::tryFrom($vendor->kategori_vendor); @endphp
                        <p class="mt-1 text-sm text-gray-500">{{ $kat?->label() ?? $vendor->kategori_vendor }}</p>
                        @if($vendor->kota)
                            <p class="mt-1 text-sm text-gray-500 flex items-center gap-1">
                                <x-icon name="map-pin" class="w-4 h-4" /> {{ $vendor->kota }}
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Tombol Hubungi & Simpan --}}
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ str_starts_with($vendor->kontak, '08') || str_starts_with($vendor->kontak, '62') ? 'https://wa.me/'.ltrim(preg_replace('/\D/', '', $vendor->kontak), '0') : 'tel:'.$vendor->kontak }}"
                        target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-[8px] hover:bg-green-700 transition-colors">
                        <x-icon name="phone" class="w-4 h-4" />
                        Hubungi
                    </a>
                    <form action="{{ route('vendor.toggle-save', $vendor) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border rounded-[8px] transition-colors {{ $isSaved ? 'text-indigo-700 bg-indigo-50 border-indigo-200 hover:bg-indigo-100' : 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50' }}">
                            <x-icon name="bookmark" class="w-4 h-4 {{ $isSaved ? 'fill-indigo-600' : '' }}" />
                            {{ $isSaved ? 'Tersimpan' : 'Simpan' }}
                        </button>
                    </form>
                </div>
            </div>

            @if($vendor->deskripsi)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-sm text-gray-700">{{ $vendor->deskripsi }}</p>
                </div>
            @endif

            <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap items-center gap-4 text-sm text-gray-600">
                <span class="flex items-center gap-1.5">
                    <x-icon name="phone" class="w-4 h-4 text-gray-400" />
                    {{ $vendor->kontak }}
                </span>
                <span class="flex items-center gap-1.5">
                    <x-icon name="map-pin" class="w-4 h-4 text-gray-400" />
                    {{ $vendor->alamat }}
                </span>
            </div>
        </div>

        {{-- Daftar Produk/Jasa --}}
        <div>
            <h2 class="text-base font-semibold text-gray-900 mb-3">Produk &amp; Layanan</h2>
            @if($vendor->products->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($vendor->products as $produk)
                        <div class="bg-white rounded-[12px] border border-gray-200 p-4 flex flex-col gap-3">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900">{{ $produk->nama_produk }}</h3>
                                    @if($produk->deskripsi)
                                        <p class="mt-0.5 text-xs text-gray-500">{{ $produk->deskripsi }}</p>
                                    @endif
                                </div>
                                <span class="text-sm font-bold text-indigo-700 whitespace-nowrap">{{ format_rupiah($produk->harga) }}</span>
                            </div>

                            {{-- Tambahkan ke Anggaran — Leader saja --}}
                            @auth
                                @if(auth()->user()->isUser() && $acaraAktif->isNotEmpty())
                                    <button
                                        type="button"
                                        x-on:click="showTambah = true; produkId = {{ $produk->id }}"
                                        class="mt-auto inline-flex items-center justify-center gap-1.5 py-1.5 text-xs font-medium text-indigo-600 border border-indigo-200 rounded-[8px] hover:bg-indigo-50 transition-colors"
                                    >
                                        <x-icon name="plus-circle" class="w-4 h-4" />
                                        Tambahkan ke Anggaran
                                    </button>
                                @endif
                            @endauth
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-[12px] border border-gray-200 p-8">
                    <x-empty-state icon="package" title="Belum ada produk" description="Vendor ini belum menambahkan produk atau layanan." />
                </div>
            @endif
        </div>

        {{-- Modal Tambahkan ke Anggaran --}}
        <div
            x-show="showTambah"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
            style="display:none"
            x-on:keydown.escape.window="showTambah = false"
        >
            <div class="bg-white rounded-[16px] shadow-xl w-full max-w-sm p-6 space-y-4" x-on:click.stop>
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">Tambahkan ke Anggaran</h3>
                    <button type="button" x-on:click="showTambah = false" class="text-gray-400 hover:text-gray-600">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>
                <form :action="`{{ url('/acara') }}/${acaraId}/anggaran/dari-vendor/${produkId}`" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Acara <span class="text-red-500">*</span></label>
                        <select x-model="acaraId" name="acara_id" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-[8px] focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Pilih acara —</option>
                            @foreach($acaraAktif as $acara)
                                <option value="{{ $acara->id }}">{{ $acara->nama_event }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah <span class="text-red-500">*</span></label>
                        <input type="number" name="jumlah" x-model="jumlah" min="1" value="1" required
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-[8px] focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                        <button type="button" x-on:click="showTambah = false" class="px-4 py-2 text-sm text-gray-700">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-[8px] hover:bg-indigo-700">Tambahkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
