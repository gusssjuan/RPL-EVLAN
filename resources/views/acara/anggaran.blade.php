<x-layouts.app :title="'Anggaran — '.$event->nama_event">
    <x-slot name="breadcrumb">
        <a href="{{ url('/acara') }}" class="hover:text-gray-700">Acara Saya</a>
        <span class="mx-1 text-gray-300">/</span>
        <a href="{{ route('acara.show', $event) }}" class="hover:text-gray-700">{{ $event->nama_event }}</a>
        <span class="mx-1 text-gray-300">/</span>
        Anggaran
    </x-slot>

    <div class="space-y-5" x-data="{ showTambah: false, editId: null, editItem: {} }">
        {{-- Include partial header tab acara --}}
        @include('acara.partials.header', ['event' => $event])

        {{-- Banner estimasi --}}
        <div class="p-3 bg-blue-50 border border-blue-200 rounded-[8px] text-xs text-blue-800 flex items-center gap-2">
            <x-icon name="info" class="w-4 h-4 text-blue-500 shrink-0" />
            Anggaran ini adalah estimasi (jumlah × harga satuan). EVLAN tidak memproses pembayaran apapun.
        </div>

        {{-- Statistik Anggaran --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <x-stat-card label="Total Estimasi" :value="format_rupiah($summary['total_keseluruhan'])" icon="wallet" color="primary" />
            <x-stat-card label="Jumlah Item" :value="$summary['jumlah_item']" icon="list" color="info" />
            <x-stat-card label="Dari Vendor" :value="$summary['dari_vendor']" icon="briefcase" color="success" />
            <x-stat-card label="Manual" :value="$summary['manual']" icon="pencil" color="neutral" />
        </div>

        {{-- Toolbar: Filter + Cari + Tambah --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <form method="GET" class="flex flex-wrap gap-2">
                <div class="relative">
                    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari nama item…"
                        class="pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-[8px] focus:outline-none focus:ring-2 focus:ring-indigo-500 w-48">
                </div>
                <div class="flex gap-1 bg-gray-100 rounded-[8px] p-1">
                    @foreach([['semua','Semua'],['vendor','Dari Vendor'],['manual','Manual']] as [$val,$label])
                        <button type="submit" name="filter" value="{{ $val }}"
                            class="px-3 py-1 rounded-[6px] text-xs font-medium transition-colors {{ $filter === $val ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </form>

            <div class="flex gap-2">
                <a href="{{ route('vendor.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-[8px] hover:bg-gray-50 transition-colors">
                    <x-icon name="briefcase" class="w-4 h-4" />
                    Tambah dari Vendor
                </a>
                <button
                    type="button"
                    x-on:click="showTambah = true"
                    class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-[8px] hover:bg-indigo-700 transition-colors"
                >
                    <x-icon name="plus" class="w-4 h-4" />
                    Tambah Item
                </button>
            </div>
        </div>

        {{-- Tabel Anggaran --}}
        @if($items->isNotEmpty())
            <div class="bg-white rounded-[12px] border border-gray-200 overflow-x-auto">
                <table class="w-full text-sm min-w-[640px]">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Item</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Sumber</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Kebutuhan</th>
                            <th class="text-right px-4 py-3 font-medium text-gray-600">Jml</th>
                            <th class="text-right px-4 py-3 font-medium text-gray-600">Harga Satuan</th>
                            <th class="text-right px-4 py-3 font-medium text-gray-600">Subtotal</th>
                            <th class="text-right px-4 py-3 font-medium text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $item->nama_item }}</td>
                                <td class="px-4 py-3">
                                    @if($item->vendor_product_id)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-indigo-50 text-indigo-700 rounded-full">
                                            <x-icon name="briefcase" class="w-3 h-3" />
                                            {{ $item->vendorProduct?->vendor?->nama_vendor ?? 'Vendor' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                                            <x-icon name="pencil" class="w-3 h-3" />
                                            Manual
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    {{ $item->checklist?->nama_kebutuhan ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ number_format($item->jumlah) }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ format_rupiah($item->harga_satuan) }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ format_rupiah($item->total) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            type="button"
                                            x-on:click="editId = {{ $item->id }}; editItem = {
                                                nama_item: {{ json_encode($item->nama_item) }},
                                                jumlah: {{ $item->jumlah }},
                                                harga_satuan: {{ $item->harga_satuan }},
                                                checklist_id: {{ $item->checklist_id ?? 'null' }}
                                            }"
                                            class="text-indigo-600 hover:text-indigo-800 text-xs font-medium"
                                        >Edit</button>
                                        <form action="{{ route('acara.anggaran.destroy', [$event, $item]) }}" method="POST"
                                            x-on:submit.prevent="if(confirm('Hapus item ini dari anggaran?')) $el.submit()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200">
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-sm font-semibold text-gray-700 text-right">Total Keseluruhan</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">{{ format_rupiah($summary['total_keseluruhan']) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="bg-white rounded-[12px] border border-gray-200 p-10">
                <x-empty-state
                    icon="wallet"
                    title="Belum ada item anggaran"
                    description="Tambahkan item anggaran secara manual atau dari katalog vendor."
                />
            </div>
        @endif

        {{-- Modal Tambah Item Manual --}}
        <div
            x-show="showTambah"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
            style="display:none"
            x-on:keydown.escape.window="showTambah = false"
        >
            <div class="bg-white rounded-[16px] shadow-xl w-full max-w-md p-6 space-y-4" x-on:click.stop>
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">Tambah Item Anggaran</h3>
                    <button type="button" x-on:click="showTambah = false" class="text-gray-400 hover:text-gray-600">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>
                <form action="{{ route('acara.anggaran.store', $event) }}" method="POST" class="space-y-4">
                    @csrf
                    <x-input name="nama_item" label="Nama Item" required placeholder="Contoh: Sewa Sound System" />
                    <div class="grid grid-cols-2 gap-3">
                        <x-input name="jumlah" label="Jumlah" type="number" min="1" value="1" required />
                        <x-input name="harga_satuan" label="Harga Satuan (Rp)" type="number" min="0" value="0" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kaitkan ke Kebutuhan</label>
                        <select name="checklist_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-[8px] focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Tidak dikaitkan —</option>
                            @foreach($checklists as $cl)
                                <option value="{{ $cl->id }}">{{ $cl->nama_kebutuhan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                        <button type="button" x-on:click="showTambah = false" class="px-4 py-2 text-sm text-gray-700">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-[8px] hover:bg-indigo-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Edit Item --}}
        <div
            x-show="editId !== null"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
            style="display:none"
            x-on:keydown.escape.window="editId = null"
        >
            <div class="bg-white rounded-[16px] shadow-xl w-full max-w-md p-6 space-y-4" x-on:click.stop>
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">Edit Item Anggaran</h3>
                    <button type="button" x-on:click="editId = null" class="text-gray-400 hover:text-gray-600">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>
                <form :action="`{{ url('/acara/'.$event->id.'/anggaran') }}/${editId}`" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Item <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_item" :value="editItem.nama_item" required
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-[8px] focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah <span class="text-red-500">*</span></label>
                            <input type="number" name="jumlah" :value="editItem.jumlah" min="1" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-[8px] focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="harga_satuan" :value="editItem.harga_satuan" min="0" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-[8px] focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kaitkan ke Kebutuhan</label>
                        <select name="checklist_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-[8px] focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Tidak dikaitkan —</option>
                            @foreach($checklists as $cl)
                                <option value="{{ $cl->id }}" :selected="editItem.checklist_id == {{ $cl->id }}">{{ $cl->nama_kebutuhan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                        <button type="button" x-on:click="editId = null" class="px-4 py-2 text-sm text-gray-700">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-[8px] hover:bg-indigo-700">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
