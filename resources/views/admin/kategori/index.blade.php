<x-layouts.admin title="Kelola Kategori">
    <div
        x-data="{
            modalOpen: false,
            isEdit: false,
            actionUrl: '{{ route('admin.kategori.store') }}',
            nama: '',
            ikon: '',
            deskripsi: '',
            openCreate() {
                this.isEdit = false;
                this.actionUrl = '{{ route('admin.kategori.store') }}';
                this.nama = '';
                this.ikon = 'tag';
                this.deskripsi = '';
                this.modalOpen = true;
            },
            openEdit(cat) {
                this.isEdit = true;
                this.actionUrl = '{{ url('/admin/kategori') }}/' + cat.id;
                this.nama = cat.nama_kategori;
                const ikonValid = ['mic', 'flag', 'cake', 'heart', 'users', 'tag'];
                this.ikon = ikonValid.includes(cat.ikon) ? cat.ikon : 'tag';
                this.deskripsi = cat.deskripsi || '';
                this.modalOpen = true;
            }
        }"
        class="space-y-6"
    >
        <!-- Top Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    Kelola Kategori Acara
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kategori utama untuk pengelompokan template master dan perencanaan acara.
                </p>
            </div>
            <div>
                <x-button type="button" variant="primary" x-on:click="openCreate()">
                    <x-icon name="plus" class="w-4 h-4 mr-1.5" />
                    Tambah Kategori
                </x-button>
            </div>
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[12px] text-sm font-medium text-emerald-800 flex items-center gap-2.5">
                <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-red-50 border border-red-200 rounded-[12px] text-sm font-medium text-red-800 flex items-center gap-2.5">
                <x-icon name="alert-triangle" class="w-5 h-5 text-red-600 shrink-0" />
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-[12px] text-sm text-red-800 space-y-1">
                <p class="font-bold flex items-center gap-2">
                    <x-icon name="alert-triangle" class="w-4 h-4 shrink-0" />
                    <span>Terjadi kesalahan pengisian formulir:</span>
                </p>
                <ul class="list-disc list-inside text-xs pl-2 space-y-0.5">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Table Card -->
        <div class="bg-white rounded-[12px] border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            <th class="py-3 px-4 w-16 text-center">Ikon</th>
                            <th class="py-3 px-4">Nama Kategori</th>
                            <th class="py-3 px-4">Deskripsi</th>
                            <th class="py-3 px-4 text-center">Template</th>
                            <th class="py-3 px-4 text-center">Acara</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        @forelse ($categories as $cat)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3.5 px-4 text-center text-xl">
                                    <x-category-icon :kategori="$cat" class="w-5 h-5 mx-auto" />
                                </td>
                                <td class="py-3.5 px-4 font-bold text-gray-900">
                                    {{ $cat->nama_kategori }}
                                </td>
                                <td class="py-3.5 px-4 text-gray-500 text-xs max-w-xs truncate">
                                    {{ $cat->deskripsi ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                                        {{ $cat->templates_count }} template
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                        {{ $cat->events_count }} acara
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right space-x-2">
                                    <button
                                        type="button"
                                        x-on:click="openEdit({{ json_encode($cat) }})"
                                        class="px-2.5 py-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded transition-colors"
                                    >
                                        Ubah
                                    </button>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.kategori.destroy', $cat->id) }}"
                                        class="inline-block"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $cat->nama_kategori }}? Hapus akan ditolak jika masih digunakan.')"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="px-2.5 py-1 text-xs font-semibold text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors"
                                        >
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400">
                                    <p class="text-sm font-medium">Belum ada kategori terdaftar</p>
                                    <p class="text-xs mt-1">Klik tombol 'Tambah Kategori' untuk membuat baru.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Tambah / Ubah Kategori (Alpine.js) -->
        <div
            x-show="modalOpen"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
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
                    class="relative bg-white rounded-[16px] text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full p-6 space-y-4"
                >
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h3 class="text-lg font-bold text-gray-900" x-text="isEdit ? 'Ubah Kategori Acara' : 'Tambah Kategori Baru'"></h3>
                        <button type="button" x-on:click="modalOpen = false" class="text-gray-400 hover:text-gray-600">
                            <x-icon name="x" class="w-5 h-5" />
                        </button>
                    </div>

                    <form :action="actionUrl" method="POST" class="space-y-4">
                        @csrf
                        <template x-if="isEdit">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="grid grid-cols-4 gap-3">
                            <div class="col-span-1">
                                <label class="block text-sm font-semibold text-gray-800 mb-1.5">Ikon</label>
                                <select
                                    name="ikon"
                                    x-model="ikon"
                                    class="w-full py-2.5 px-2 bg-white border border-gray-300 rounded-[8px] text-sm focus:outline-none focus:ring-1 focus:ring-indigo-600"
                                >
                                    @foreach (collect(config('ikon.kategori'))->unique() as $ikon)
                                        <option value="{{ $ikon }}">{{ $ikon }}</option>
                                    @endforeach
                                    <option value="tag">tag</option>
                                </select>
                            </div>
                            <div class="col-span-3">
                                <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                                    Nama Kategori <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="nama_kategori"
                                    x-model="nama"
                                    placeholder="Contoh: Seminar"
                                    required
                                    class="w-full py-2.5 px-3 bg-white border border-gray-300 rounded-[8px] text-sm focus:outline-none focus:ring-1 focus:ring-indigo-600"
                                >
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-1.5">Deskripsi</label>
                            <textarea
                                name="deskripsi"
                                x-model="deskripsi"
                                rows="3"
                                placeholder="Deskripsi singkat mengenai jenis acara ini"
                                class="w-full py-2 px-3 bg-white border border-gray-300 rounded-[8px] text-sm focus:outline-none focus:ring-1 focus:ring-indigo-600"
                            ></textarea>
                        </div>

                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                            <x-button type="button" variant="secondary" x-on:click="modalOpen = false">
                                Batal
                            </x-button>
                            <x-button type="submit" variant="primary">
                                Simpan Kategori
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>

