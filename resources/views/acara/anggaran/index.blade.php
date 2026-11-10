<x-layouts.app :title="'Estimasi Anggaran - ' . $event->nama_event">
    <div
        x-data="{
            modalOpen: false,
            isEdit: false,
            actionUrl: '{{ route('acara.anggaran.store', $event->id) }}',
            method: 'POST',
            nama_item: '',
            jumlah: 1,
            harga_satuan: '',
            checklist_id: '',
            openCreate() {
                this.isEdit = false;
                this.actionUrl = '{{ route('acara.anggaran.store', $event->id) }}';
                this.method = 'POST';
                this.nama_item = '';
                this.jumlah = 1;
                this.harga_satuan = '';
                this.checklist_id = '';
                this.modalOpen = true;
            },
            openEdit(item) {
                this.isEdit = true;
                this.actionUrl = '{{ url('/acara/' . $event->id . '/anggaran') }}/' + item.id;
                this.method = 'PUT';
                this.nama_item = item.nama_item;
                this.jumlah = item.jumlah;
                this.harga_satuan = item.harga_satuan;
                this.checklist_id = item.checklist_id || '';
                this.modalOpen = true;
            }
        }"
        class="space-y-6"
    >
        <x-acara.partials.header :event="$event" activeTab="anggaran" />

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

        <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-[12px] flex items-start gap-2.5 text-xs text-indigo-800">
            <x-icon name="alert-circle" class="w-4 h-4 text-indigo-500 shrink-0 mt-0.5" />
            <p>Anggaran ini adalah estimasi (jumlah × harga satuan). EVLAN tidak memproses pembayaran.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <x-stat-card title="Total Estimasi" :value="format_rupiah($ringkasan['total'])" subtext="Akumulasi seluruh item" icon="credit-card" />
            <x-stat-card title="Jumlah Item" :value="$ringkasan['jumlah_item']" subtext="Baris anggaran tercatat" icon="tag" iconBg="bg-sky-50 text-sky-600" />
            <x-stat-card title="Dari Produk Vendor" :value="$ringkasan['dari_vendor']" subtext="Item terhubung katalog vendor" icon="briefcase" iconBg="bg-emerald-50 text-emerald-600" />
            <x-stat-card title="Item Manual" :value="$ringkasan['manual']" subtext="Diisi langsung oleh Ketua" icon="edit" iconBg="bg-amber-50 text-amber-600" />
        </div>

        <div class="bg-white p-4 rounded-[12px] border border-gray-200 shadow-sm flex flex-col lg:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-1.5 overflow-x-auto w-full lg:w-auto">
                <a
                    href="{{ route('acara.anggaran', [$event->id, 'sumber' => 'semua', 'cari' => $search]) }}"
                    class="px-3 py-1.5 rounded-[8px] text-xs font-semibold transition-colors {{ $sumber === 'semua' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                >
                    Semua ({{ $ringkasan['jumlah_item'] }})
                </a>
                <a
                    href="{{ route('acara.anggaran', [$event->id, 'sumber' => 'vendor', 'cari' => $search]) }}"
                    class="px-3 py-1.5 rounded-[8px] text-xs font-semibold transition-colors {{ $sumber === 'vendor' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}"
                >
                    Dari Vendor ({{ $ringkasan['dari_vendor'] }})
                </a>
                <a
                    href="{{ route('acara.anggaran', [$event->id, 'sumber' => 'manual', 'cari' => $search]) }}"
                    class="px-3 py-1.5 rounded-[8px] text-xs font-semibold transition-colors {{ $sumber === 'manual' ? 'bg-amber-500 text-white shadow-sm' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}"
                >
                    Manual ({{ $ringkasan['manual'] }})
                </a>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full lg:w-auto">
                <form method="GET" action="{{ route('acara.anggaran', $event->id) }}" class="flex gap-2">
                    <input type="hidden" name="sumber" value="{{ $sumber }}">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <x-icon name="search" class="w-4 h-4" />
                        </span>
                        <input type="text" name="cari" value="{{ $search }}" placeholder="Cari item atau kebutuhan…" class="w-full sm:w-60 pl-9 pr-3.5 py-2 bg-white border border-gray-300 rounded-[8px] text-xs text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                    </div>
                    <button type="submit" class="px-3 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-[8px] text-xs font-semibold">Cari</button>
                </form>

                <div class="flex gap-2">
                    <a href="{{ route('vendor.index') }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-[8px] text-xs font-semibold shadow-sm transition-colors">
                        <x-icon name="briefcase" class="w-3.5 h-3.5" />
                        <span>Tambah dari Vendor</span>
                    </a>
                    <button type="button" x-on:click="openCreate()" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-xs font-semibold shadow-sm transition-colors">
                        <x-icon name="plus" class="w-3.5 h-3.5" />
                        <span>Tambah Item</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[12px] border border-gray-200 shadow-sm overflow-hidden">
            @if ($budgets->isEmpty())
                <div class="p-5">
                    <x-empty-state
                        title="Belum ada item anggaran"
                        description="Tambahkan item manual atau pilih produk/jasa vendor untuk menyusun estimasi biaya acara."
                        icon="credit-card"
                    >
                        <button type="button" x-on:click="openCreate()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-xs font-semibold shadow-sm transition-colors">
                            <x-icon name="plus" class="w-3.5 h-3.5" />
                            <span>Tambah Item Anggaran</span>
                        </button>
                    </x-empty-state>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left text-[11px] uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Item &amp; Kebutuhan</th>
                                <th class="px-5 py-3 font-semibold">Sumber</th>
                                <th class="px-5 py-3 font-semibold text-right">Jumlah</th>
                                <th class="px-5 py-3 font-semibold text-right">Harga Satuan</th>
                                <th class="px-5 py-3 font-semibold text-right">Subtotal</th>
                                <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($budgets as $item)
                                <tr class="hover:bg-gray-50/60">
                                    <td class="px-5 py-3.5">
                                        <p class="font-semibold text-gray-900">{{ $item->nama_item }}</p>
                                        <p class="text-[11px] text-gray-500">
                                            {{ $item->checklist?->nama_kebutuhan ? 'Kebutuhan: ' . $item->checklist->nama_kebutuhan : 'Tanpa kebutuhan terkait' }}
                                        </p>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if ($item->vendor_product_id)
                                            <x-badge variant="success">
                                                <x-icon name="briefcase" class="w-3 h-3" />
                                                <span>{{ $item->vendorProduct?->vendor?->nama_vendor ?? 'Vendor' }}</span>
                                            </x-badge>
                                        @else
                                            <x-badge variant="warning">Manual</x-badge>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-medium text-gray-700 whitespace-nowrap">{{ number_format($item->jumlah, 0, ',', '.') }}×</td>
                                    <td class="px-5 py-3.5 text-right text-gray-700 whitespace-nowrap">{{ format_rupiah($item->harga_satuan) }}</td>
                                    <td class="px-5 py-3.5 text-right font-bold text-gray-900 whitespace-nowrap">{{ format_rupiah($item->total) }}</td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button
                                                type="button"
                                                x-on:click="openEdit(@json($item))"
                                                class="p-1.5 text-gray-400 hover:text-indigo-600 rounded-md hover:bg-indigo-50 transition-colors"
                                                title="Ubah item"
                                            >
                                                <x-icon name="edit" class="w-4 h-4" />
                                            </button>
                                            <form action="{{ route('acara.anggaran.destroy', [$event->id, $item->id]) }}" method="POST" onsubmit="return confirm('Hapus item anggaran ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 rounded-md hover:bg-red-50 transition-colors" title="Hapus item">
                                                    <x-icon name="trash" class="w-4 h-4" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-indigo-50/60 border-t-2 border-indigo-100">
                                <td colspan="4" class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wider text-indigo-800">Total Keseluruhan</td>
                                <td class="px-5 py-4 text-right text-base font-black text-indigo-700 whitespace-nowrap">{{ format_rupiah($ringkasan['total']) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>

        {{-- Modal Tambah/Ubah Item --}}
        <div x-show="modalOpen" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" x-on:click.self="modalOpen = false" x-on:keydown.escape.window="modalOpen = false">
            <div class="bg-white rounded-[16px] p-6 w-full max-w-lg shadow-lg space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900" x-text="isEdit ? 'Ubah Item Anggaran' : 'Tambah Item Anggaran'"></h3>
                    <button type="button" x-on:click="modalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <form :action="actionUrl" method="POST" class="space-y-4">
                    @csrf
                    <template x-if="method === 'PUT'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label for="nama_item" class="block text-sm font-semibold text-gray-800 mb-1.5">
                            Nama Item <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_item" name="nama_item" x-model="nama_item" required placeholder="Contoh: Honorarium Narasumber" class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="jumlah" class="block text-sm font-semibold text-gray-800 mb-1.5">
                                Jumlah <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="jumlah" name="jumlah" x-model="jumlah" min="1" step="1" required class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm text-gray-900 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                        </div>
                        <div>
                            <label for="harga_satuan" class="block text-sm font-semibold text-gray-800 mb-1.5">
                                Harga Satuan (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="harga_satuan" name="harga_satuan" x-model="harga_satuan" min="0" step="1" required placeholder="500000" class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                        </div>
                    </div>

                    <div>
                        <label for="checklist_id" class="block text-sm font-semibold text-gray-800 mb-1.5">Kebutuhan Terkait (opsional)</label>
                        <select id="checklist_id" name="checklist_id" x-model="checklist_id" class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm text-gray-900 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                            <option value="">Tidak terkait kebutuhan tertentu</option>
                            @foreach ($checklists as $checklist)
                                <option value="{{ $checklist->id }}">{{ $checklist->nama_kebutuhan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                        <x-button type="button" variant="secondary" x-on:click="modalOpen = false">Batal</x-button>
                        <x-button type="submit">Simpan Item</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
