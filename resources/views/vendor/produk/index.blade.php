@php
    use App\Enums\StatusVerifikasi;
@endphp

<x-layouts.vendor title="Kelola Produk/Jasa">
    <div
        x-data="{
            modalOpen: false,
            isEdit: false,
            actionUrl: '{{ route('usaha.produk.store') }}',
            method: 'POST',
            nama_produk: '',
            harga: '',
            deskripsi: '',
            is_aktif: true,
            openCreate() {
                this.isEdit = false;
                this.actionUrl = '{{ route('usaha.produk.store') }}';
                this.method = 'POST';
                this.nama_produk = '';
                this.harga = '';
                this.deskripsi = '';
                this.is_aktif = true;
                this.modalOpen = true;
            },
            openEdit(item) {
                this.isEdit = true;
                this.actionUrl = '{{ url('/usaha/produk') }}/' + item.id;
                this.method = 'PUT';
                this.nama_produk = item.nama_produk;
                this.harga = item.harga;
                this.deskripsi = item.deskripsi || '';
                this.is_aktif = item.is_aktif;
                this.modalOpen = true;
            }
        }"
        class="space-y-6"
    >
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">Kelola Produk/Jasa</h1>
                    <x-status-badge :status="$vendor->status_verifikasi->value" />
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Daftar produk dan jasa yang ditawarkan usaha {{ $vendor->nama_vendor }}.
                </p>
            </div>

            @if ($terverifikasi)
                <button type="button" x-on:click="openCreate()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-xs font-semibold shadow-sm transition-colors">
                    <x-icon name="plus" class="w-3.5 h-3.5" />
                    <span>Tambah Produk/Jasa</span>
                </button>
            @else
                <span class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gray-100 text-gray-500 rounded-[8px] text-xs font-semibold cursor-not-allowed" title="Menunggu verifikasi Admin">
                    <x-icon name="lock" class="w-3.5 h-3.5" />
                    <span>Terkunci sampai terverifikasi</span>
                </span>
            @endif
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[12px] text-sm font-medium text-emerald-800 flex items-center gap-2.5">
                <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-red-50 border border-red-200 rounded-[12px] text-sm font-medium text-red-800 flex items-center gap-2.5">
                <x-icon name="alert-circle" class="w-5 h-5 text-red-600 shrink-0" />
                <span>{{ session('error') }}</span>
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

        @if (! $terverifikasi)
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-[12px] flex items-start gap-2.5">
                <x-icon name="lock" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
                <div class="text-xs text-amber-800">
                    <p class="font-bold">Kelola produk terkunci</p>
                    <p class="mt-0.5">
                        @if ($vendor->status_verifikasi === StatusVerifikasi::Menunggu)
                            Akun usaha Anda masih menunggu verifikasi Admin. Anda belum dapat menambah, mengubah, atau menghapus produk/jasa.
                        @else
                            Akun usaha Anda ditolak Admin. Perbaiki profil usaha terlebih dahulu sebelum menambahkan produk/jasa.
                        @endif
                    </p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-stat-card title="Total Produk/Jasa" :value="$jumlahTotal" subtext="Seluruh entri katalog" icon="tag" />
            <x-stat-card title="Produk Aktif" :value="$jumlahAktif" subtext="Tampil di direktori vendor" icon="check-circle" iconBg="bg-emerald-50 text-emerald-600" />
            <x-stat-card title="Status Katalog" :value="$terverifikasi ? 'Terbuka' : 'Terkunci'" subtext="Bergantung status verifikasi" icon="lock" iconBg="bg-amber-50 text-amber-600" />
        </div>

        <div class="bg-white rounded-[12px] border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-sm font-bold text-gray-900">Daftar Produk/Jasa</h2>
                <p class="text-xs text-gray-500">Harga dalam rupiah. Produk nonaktif tidak tampil di direktori vendor.</p>
            </div>

            @if ($produk->isEmpty())
                <div class="p-5">
                    <x-empty-state
                        title="Belum ada produk/jasa"
                        description="{{ $terverifikasi ? 'Tambahkan produk atau jasa pertama agar usaha Anda mudah ditemukan.' : 'Produk/jasa dapat ditambahkan setelah akun usaha terverifikasi.' }}"
                        icon="tag"
                    >
                        @if ($terverifikasi)
                            <button type="button" x-on:click="openCreate()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-xs font-semibold shadow-sm transition-colors">
                                <x-icon name="plus" class="w-3.5 h-3.5" />
                                <span>Tambah Produk/Jasa</span>
                            </button>
                        @endif
                    </x-empty-state>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left text-[11px] uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Nama Produk/Jasa</th>
                                <th class="px-5 py-3 font-semibold">Harga</th>
                                <th class="px-5 py-3 font-semibold">Status</th>
                                <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($produk as $item)
                                <tr class="hover:bg-gray-50/60">
                                    <td class="px-5 py-3.5">
                                        <p class="font-semibold text-gray-900">{{ $item->nama_produk }}</p>
                                        <p class="text-xs text-gray-500 max-w-md">{{ $item->deskripsi ?: 'Tanpa deskripsi' }}</p>
                                    </td>
                                    <td class="px-5 py-3.5 font-bold text-gray-900 whitespace-nowrap">{{ format_rupiah($item->harga) }}</td>
                                    <td class="px-5 py-3.5">
                                        <x-badge :variant="$item->is_aktif ? 'success' : 'neutral'">
                                            {{ $item->is_aktif ? 'Aktif' : 'Nonaktif' }}
                                        </x-badge>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if ($terverifikasi)
                                                <button type="button" x-on:click='openEdit(@json($item))' class="p-1.5 text-gray-400 hover:text-indigo-600 rounded-md hover:bg-indigo-50 transition-colors" title="Ubah">
                                                    <x-icon name="edit" class="w-4 h-4" />
                                                </button>
                                                <form action="{{ route('usaha.produk.toggle', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-amber-600 rounded-md hover:bg-amber-50 transition-colors" title="{{ $item->is_aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                        <x-icon name="check-circle" class="w-4 h-4" />
                                                    </button>
                                                </form>
                                                <form action="{{ route('usaha.produk.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus produk/jasa ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 rounded-md hover:bg-red-50 transition-colors" title="Hapus">
                                                        <x-icon name="trash" class="w-4 h-4" />
                                                    </button>
                                                </form>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-gray-400">
                                                    <x-icon name="lock" class="w-3.5 h-3.5" />
                                                    <span>Terkunci</span>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $produk->links() }}
                </div>
            @endif
        </div>

        {{-- Modal Tambah/Ubah Produk --}}
        <div x-show="modalOpen" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" x-on:click.self="modalOpen = false" x-on:keydown.escape.window="modalOpen = false">
            <div class="bg-white rounded-[16px] p-6 w-full max-w-lg shadow-lg space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900" x-text="isEdit ? 'Ubah Produk/Jasa' : 'Tambah Produk/Jasa'"></h3>
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
                        <label for="nama_produk" class="block text-sm font-semibold text-gray-800 mb-1.5">
                            Nama Produk/Jasa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_produk" name="nama_produk" x-model="nama_produk" required placeholder="Contoh: Paket Sound System Seminar" class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 transition-colors" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="harga" class="block text-sm font-semibold text-gray-800 mb-1.5">
                                Harga (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="harga" name="harga" x-model="harga" min="0" step="1" required placeholder="3500000" class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 transition-colors" />
                        </div>

                        <div class="flex items-end pb-2.5">
                            <label class="inline-flex items-center gap-2 text-sm font-semibold text-gray-800">
                                <input type="hidden" name="is_aktif" value="0">
                                <input type="checkbox" name="is_aktif" value="1" x-model="is_aktif" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                <span>Tampilkan sebagai produk aktif</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="deskripsi" class="block text-sm font-semibold text-gray-800 mb-1.5">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" rows="3" x-model="deskripsi" placeholder="Detail singkat produk/jasa" class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 transition-colors"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                        <x-button type="button" variant="secondary" x-on:click="modalOpen = false">Batal</x-button>
                        <x-button type="submit">Simpan</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.vendor>
