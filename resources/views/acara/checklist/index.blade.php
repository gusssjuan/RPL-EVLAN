<x-layouts.app :title="'Checklist Kebutuhan - ' . $event->nama_event">
    <div
        x-data="{
            modalOpen: false,
            isEdit: false,
            actionUrl: '{{ route('acara.checklist.store', $event->id) }}',
            nama: '',
            kelompok: '',
            openCreate() {
                this.isEdit = false;
                this.actionUrl = '{{ route('acara.checklist.store', $event->id) }}';
                this.nama = '';
                this.kelompok = 'Perlengkapan';
                this.modalOpen = true;
            },
            openEdit(item) {
                this.isEdit = true;
                this.actionUrl = '{{ url('/acara/' . $event->id . '/checklist') }}/' + item.id;
                this.nama = item.nama_kebutuhan;
                this.kelompok = item.kelompok || '';
                this.modalOpen = true;
            }
        }"
        class="space-y-6"
    >
        <!-- Shared Event Header -->
        <x-acara.partials.header :event="$event" activeTab="checklist" />

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[12px] text-sm font-medium text-emerald-800 flex items-center gap-2.5">
                <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- Progress Card (S12) -->
        <div class="bg-white p-6 rounded-[16px] border border-gray-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Progres Kebutuhan Acara</h2>
                    <p class="text-xs text-gray-500">
                        Kebutuhan logistik terpenuhi <strong class="text-indigo-600">{{ $terpenuhiCount }}</strong> dari <strong>{{ $totalCount }}</strong> kebutuhan ({{ $progress }}%)
                    </p>
                </div>
                <span class="text-xl font-black text-indigo-600">{{ $progress }}%</span>
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                <div class="bg-indigo-600 h-3 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
            </div>
        </div>

        <!-- Filter & Search Card -->
        <div class="bg-white p-4 rounded-[12px] border border-gray-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Filter Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto">
                <a
                    href="{{ route('acara.checklist.index', [$event->id, 'status' => 'semua', 'cari' => $search]) }}"
                    class="px-3 py-1.5 rounded-[8px] text-xs font-semibold transition-colors {{ $filter === 'semua' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                >
                    Semua ({{ $totalCount }})
                </a>
                <a
                    href="{{ route('acara.checklist.index', [$event->id, 'status' => 'belum', 'cari' => $search]) }}"
                    class="px-3 py-1.5 rounded-[8px] text-xs font-semibold transition-colors {{ $filter === 'belum' ? 'bg-amber-500 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                >
                    Belum ({{ $belumCount }})
                </a>
                <a
                    href="{{ route('acara.checklist.index', [$event->id, 'status' => 'terpenuhi', 'cari' => $search]) }}"
                    class="px-3 py-1.5 rounded-[8px] text-xs font-semibold transition-colors {{ $filter === 'terpenuhi' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                >
                    Terpenuhi ({{ $terpenuhiCount }})
                </a>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <!-- Search Input -->
                <form method="GET" action="{{ route('acara.checklist.index', $event->id) }}" class="flex gap-2 w-full md:w-64">
                    <input type="hidden" name="status" value="{{ $filter }}">
                    <input
                        type="text"
                        name="cari"
                        value="{{ $search }}"
                        placeholder="Cari kebutuhan..."
                        class="flex-1 px-3 py-1.5 bg-white border border-gray-300 rounded-[8px] text-xs focus:outline-none focus:ring-1 focus:ring-indigo-600"
                    >
                    <x-button type="submit" variant="secondary" size="sm">Cari</x-button>
                </form>

                @if ($isLeader)
                    <x-button type="button" variant="primary" size="sm" x-on:click="openCreate()" class="shrink-0">
                        <x-icon name="plus" class="w-3.5 h-3.5 mr-1" />
                        Tambah Kebutuhan
                    </x-button>
                @endif
            </div>
        </div>

        <!-- Table Checklist (S12) -->
        <div class="bg-white rounded-[12px] border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            <th class="py-3 px-4 w-12 text-center">Centang</th>
                            <th class="py-3 px-4">Nama Kebutuhan</th>
                            <th class="py-3 px-4">Kelompok</th>
                            <th class="py-3 px-4 text-center">Sumber</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4">Vendor Terkait</th>
                            @if ($isLeader)
                                <th class="py-3 px-4 text-right">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        @forelse ($checklists as $item)
                            <tr class="hover:bg-gray-50 transition-colors {{ $item->status_selesai ? 'bg-gray-50/50' : '' }}">
                                <td class="py-3 px-4 text-center">
                                    @if ($isLeader)
                                        <form method="POST" action="{{ route('acara.checklist.toggle', [$event->id, $item->id]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input
                                                type="checkbox"
                                                onchange="this.form.submit()"
                                                {{ $item->status_selesai ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer"
                                                title="Tandai terpenuhi / belum"
                                            >
                                        </form>
                                    @else
                                        <input
                                            type="checkbox"
                                            {{ $item->status_selesai ? 'checked' : '' }}
                                            disabled
                                            class="rounded border-gray-200 text-gray-400 w-4 h-4 cursor-not-allowed"
                                        >
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 font-semibold {{ $item->status_selesai ? 'line-through text-gray-400' : 'text-gray-900' }}">
                                    {{ $item->nama_kebutuhan }}
                                </td>

                                <td class="py-3.5 px-4 text-xs text-gray-500">
                                    {{ $item->kelompok ?? 'Umum' }}
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    @if ($item->dari_template)
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-700">
                                            Dari Template
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-600">
                                            Manual
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    @if ($item->status_selesai)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Terpenuhi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                            Belum
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-xs">
                                    <a
                                        href="{{ url('/vendor') }}"
                                        class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 hover:underline font-medium"
                                    >
                                        <x-icon name="search" class="w-3 h-3" />
                                        <span>Cari Vendor</span>
                                    </a>
                                </td>

                                @if ($isLeader)
                                    <td class="py-3.5 px-4 text-right space-x-1.5">
                                        <button
                                            type="button"
                                            x-on:click="openEdit({{ json_encode($item) }})"
                                            class="px-2 py-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded"
                                        >
                                            Ubah
                                        </button>

                                        <form
                                            method="POST"
                                            action="{{ route('acara.checklist.destroy', [$event->id, $item->id]) }}"
                                            class="inline-block"
                                            onsubmit="return confirm('Hapus kebutuhan {{ $item->nama_kebutuhan }}?')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="px-2 py-1 text-xs font-semibold text-red-600 hover:text-red-800 hover:bg-red-50 rounded"
                                            >
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isLeader ? 7 : 6 }}" class="py-8 text-center text-gray-400">
                                    <p class="text-sm font-medium">Tidak ada kebutuhan logistik yang sesuai</p>
                                    <p class="text-xs mt-1">Gunakan filter status lain atau tambahkan kebutuhan baru.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Tambah / Edit Kebutuhan (Leader only) -->
        @if ($isLeader)
            <div
                x-show="modalOpen"
                x-cloak
                class="fixed inset-0 z-50 overflow-y-auto"
                aria-labelledby="modal-checklist-title"
                role="dialog"
                aria-modal="true"
            >
                <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                    <div
                        x-show="modalOpen"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 bg-gray-900/60 transition-opacity"
                        x-on:click="modalOpen = false"
                    ></div>

                    <div
                        x-show="modalOpen"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                        class="relative bg-white rounded-[16px] text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-md sm:w-full p-6 space-y-4"
                    >
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <h3 class="text-lg font-bold text-gray-900" x-text="isEdit ? 'Ubah Kebutuhan Acara' : 'Tambah Kebutuhan Manual'"></h3>
                            <button type="button" x-on:click="modalOpen = false" class="text-gray-400 hover:text-gray-600">
                                <x-icon name="x" class="w-5 h-5" />
                            </button>
                        </div>

                        <form :action="actionUrl" method="POST" class="space-y-4">
                            @csrf
                            <template x-if="isEdit">
                                <input type="hidden" name="_method" value="PUT">
                            </template>

                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                                    Nama Kebutuhan <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="nama_kebutuhan"
                                    x-model="nama"
                                    placeholder="Contoh: Genset cadangan 5000 watt"
                                    required
                                    class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm focus:outline-none focus:ring-1 focus:ring-indigo-600"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                                    Kelompok / Divisi Penanggung Jawab
                                </label>
                                <input
                                    type="text"
                                    name="kelompok"
                                    x-model="kelompok"
                                    placeholder="Contoh: Perlengkapan / Konsumsi"
                                    class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm focus:outline-none focus:ring-1 focus:ring-indigo-600"
                                >
                            </div>

                            <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                                <x-button type="button" variant="secondary" x-on:click="modalOpen = false">
                                    Batal
                                </x-button>
                                <x-button type="submit" variant="primary">
                                    Simpan Kebutuhan
                                </x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>

