<x-layouts.app title="Kelola Produk/Jasa">
    <x-slot name="breadcrumb">
        <a href="{{ route('usaha.dashboard') }}" class="hover:text-gray-700">Dashboard Usaha</a>
        <span class="mx-1 text-gray-300">/</span>
        Produk/Jasa
    </x-slot>

    <div class="space-y-6" x-data="{ showTambah: false, editId: null, editData: {} }">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Kelola Produk/Jasa</h1>
                <p class="mt-1 text-sm text-gray-500">Katalog produk atau layanan yang Anda tawarkan.</p>
            </div>
            @if($vendor->status_verifikasi === \App\Enums\StatusVerifikasi::Terverifikasi)
                <button
                    type="button"
                    x-on:click="showTambah = true"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-[8px] hover:bg-indigo-700 transition-colors"
                >
                    <x-icon name="plus" class="w-4 h-4" />
                    Tambah Produk/Jasa
                </button>
            @endif
        </div>

        {{-- Banner terkunci --}}
        @if($vendor->status_verifikasi !== \App\Enums\StatusVerifikasi::Terverifikasi)
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-[12px] flex items-start gap-3">
                <x-icon name="lock" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
                <div>
                    <h3 class="text-sm font-semibold text-amber-900">Produk Terkunci</h3>
                    <p class="mt-1 text-sm text-amber-800">Anda hanya dapat menambahkan produk setelah akun vendor diverifikasi oleh Admin.</p>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 rounded-[12px] text-sm text-green-900 flex items-center gap-2">
                <x-icon name="check-circle" class="w-4 h-4 text-green-600 shrink-0" />
                {{ session('success') }}
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-[12px] text-sm text-blue-900 flex items-center gap-2">
                <x-icon name="info" class="w-4 h-4 text-blue-600 shrink-0" />
                {{ session('info') }}
            </div>
        @endif

        {{-- Daftar Produk --}}
        @if($produk->count() > 0)
            <div class="bg-white rounded-[12px] border border-gray-200 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Produk/Jasa</th>
                            <th class="text-right px-4 py-3 font-medium text-gray-600">Harga</th>
                            <th class="text-center px-4 py-3 font-medium text-gray-600">Status</th>
                            @if($vendor->status_verifikasi === \App\Enums\StatusVerifikasi::Terverifikasi)
                                <th class="text-right px-4 py-3 font-medium text-gray-600">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($produk as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900">{{ $item->nama_produk }}</p>
                                    @if($item->deskripsi)
                                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $item->deskripsi }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900">
                                    {{ format_rupiah($item->harga) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($item->is_aktif)
                                        <x-badge color="success" icon="check">Aktif</x-badge>
                                    @else
                                        <x-badge color="neutral" icon="minus">Nonaktif</x-badge>
                                    @endif
                                </td>
                                @if($vendor->status_verifikasi === \App\Enums\StatusVerifikasi::Terverifikasi)
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                type="button"
                                                x-on:click="editId = {{ $item->id }}; editData = {
                                                    nama_produk: {{ json_encode($item->nama_produk) }},
                                                    harga: {{ $item->harga }},
                                                    deskripsi: {{ json_encode($item->deskripsi) }},
                                                    is_aktif: {{ $item->is_aktif ? 'true' : 'false' }}
                                                }"
                                                class="text-indigo-600 hover:text-indigo-800 text-xs font-medium"
                                            >Edit</button>
                                            <form action="{{ route('usaha.produk.destroy', $item) }}" method="POST"
                                                x-on:submit.prevent="if(confirm('Hapus produk ini?')) $el.submit()">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-white rounded-[12px] border border-gray-200 p-10">
                <x-empty-state
                    icon="package"
                    title="Belum ada produk/jasa"
                    description="Tambahkan produk atau layanan pertama Anda."
                />
            </div>
        @endif

        {{-- Modal Tambah Produk --}}
        <div
            x-show="showTambah"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
            style="display:none"
            x-on:keydown.escape.window="showTambah = false"
        >
            <div class="bg-white rounded-[16px] shadow-xl w-full max-w-md p-6 space-y-4" x-on:click.stop>
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Tambah Produk/Jasa</h3>
                    <button type="button" x-on:click="showTambah = false" class="text-gray-400 hover:text-gray-600">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>
                <form action="{{ route('usaha.produk.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <x-input name="nama_produk" label="Nama Produk/Jasa" required placeholder="Contoh: Paket Seminar 300 orang" />
                    <x-input name="harga" label="Harga (Rupiah)" type="number" min="0" required placeholder="Contoh: 1500000" />
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-[8px] focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none" placeholder="Deskripsi singkat…"></textarea>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_aktif" id="is_aktif_new" value="1" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="is_aktif_new" class="text-sm text-gray-700">Aktif (tampil di pencarian)</label>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                        <button type="button" x-on:click="showTambah = false" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-[8px] hover:bg-indigo-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Edit Produk --}}
        <div
            x-show="editId !== null"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
            style="display:none"
            x-on:keydown.escape.window="editId = null"
        >
            <div class="bg-white rounded-[16px] shadow-xl w-full max-w-md p-6 space-y-4" x-on:click.stop>
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Produk/Jasa</h3>
                    <button type="button" x-on:click="editId = null" class="text-gray-400 hover:text-gray-600">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>
                <template x-for="item in [editId]" :key="item">
                    <form :action="`{{ url('/usaha/produk') }}/${editId}`" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk/Jasa <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_produk" :value="editData.nama_produk" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-[8px] focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rupiah) <span class="text-red-500">*</span></label>
                            <input type="number" name="harga" :value="editData.harga" min="0" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-[8px] focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea name="deskripsi" rows="3" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-[8px] focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none" x-text="editData.deskripsi"></textarea>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_aktif" :id="`is_aktif_edit_${editId}`" value="1" :checked="editData.is_aktif" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label :for="`is_aktif_edit_${editId}`" class="text-sm text-gray-700">Aktif (tampil di pencarian)</label>
                        </div>
                        <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                            <button type="button" x-on:click="editId = null" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">Batal</button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-[8px] hover:bg-indigo-700">Perbarui</button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>
</x-layouts.app>
