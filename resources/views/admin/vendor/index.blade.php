<x-layouts.admin title="Verifikasi Vendor">
    <div
        x-data="{
            rejectModalOpen: false,
            rejectActionUrl: '',
            vendorName: '',
            alasan: '',
            openReject(vendor) {
                this.rejectActionUrl = '{{ url('/admin/verifikasi-vendor') }}/' + vendor.id + '/tolak';
                this.vendorName = vendor.nama_vendor;
                this.alasan = '';
                this.rejectModalOpen = true;
            }
        }"
        class="space-y-6"
    >
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    Verifikasi Pendaftaran Vendor
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Tinjau kelayakan data usaha vendor sebelum mereka dapat mempublikasikan produk/jasa.
                </p>
            </div>
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[12px] text-sm font-medium text-emerald-800 flex items-center gap-2.5">
                <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-[12px] text-sm text-red-800 space-y-1">
                @foreach ($errors->all() as $err)
                    <p class="flex items-center gap-2">
                        <x-icon name="alert-triangle" class="w-4 h-4 shrink-0" />
                        <span>{{ $err }}</span>
                    </p>
                @endforeach
            </div>
        @endif

        <!-- Filter & Search Bar -->
        <div class="bg-white p-4 rounded-[12px] border border-gray-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Filter Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto">
                <a
                    href="{{ route('admin.verifikasi-vendor.index', ['status' => 'semua', 'cari' => $search]) }}"
                    class="px-3 py-1.5 rounded-[8px] text-xs font-semibold transition-colors {{ $status === 'semua' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                >
                    Semua ({{ $counts['semua'] }})
                </a>
                <a
                    href="{{ route('admin.verifikasi-vendor.index', ['status' => 'menunggu', 'cari' => $search]) }}"
                    class="px-3 py-1.5 rounded-[8px] text-xs font-semibold transition-colors flex items-center gap-1.5 {{ $status === 'menunggu' ? 'bg-amber-500 text-white shadow-sm' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}"
                >
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    Menunggu ({{ $counts['menunggu'] }})
                </a>
                <a
                    href="{{ route('admin.verifikasi-vendor.index', ['status' => 'terverifikasi', 'cari' => $search]) }}"
                    class="px-3 py-1.5 rounded-[8px] text-xs font-semibold transition-colors {{ $status === 'terverifikasi' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}"
                >
                    Terverifikasi ({{ $counts['terverifikasi'] }})
                </a>
                <a
                    href="{{ route('admin.verifikasi-vendor.index', ['status' => 'ditolak', 'cari' => $search]) }}"
                    class="px-3 py-1.5 rounded-[8px] text-xs font-semibold transition-colors {{ $status === 'ditolak' ? 'bg-red-600 text-white shadow-sm' : 'bg-red-50 text-red-700 hover:bg-red-100' }}"
                >
                    Ditolak ({{ $counts['ditolak'] }})
                </a>
            </div>

            <!-- Search Field -->
            <form method="GET" action="{{ route('admin.verifikasi-vendor.index') }}" class="w-full md:w-72 flex gap-2">
                <input type="hidden" name="status" value="{{ $status }}">
                <input
                    type="text"
                    name="cari"
                    value="{{ $search }}"
                    placeholder="Cari nama usaha / kontak..."
                    class="flex-1 px-3 py-1.5 bg-white border border-gray-300 rounded-[8px] text-xs focus:outline-none focus:ring-1 focus:ring-indigo-600"
                >
                <x-button type="submit" variant="secondary" size="sm">
                    Cari
                </x-button>
            </form>
        </div>

        <!-- Vendor Table Card -->
        <div class="bg-white rounded-[12px] border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            <th class="py-3 px-4">Nama Usaha / Vendor</th>
                            <th class="py-3 px-4">Kategori & Alamat</th>
                            <th class="py-3 px-4">Penanggung Jawab</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Aksi Keputusan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        @forelse ($vendors as $v)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-gray-900">
                                    {{ $v->nama_vendor }}
                                    <span class="block text-xs font-normal text-gray-500 mt-0.5">
                                        Kontak: {{ $v->kontak }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="inline-block px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded text-xs font-medium">
                                        {{ ucfirst(str_replace('_', ' ', $v->kategori_vendor)) }}
                                    </span>
                                    <span class="block text-xs text-gray-500 mt-1 max-w-xs truncate">
                                        {{ $v->alamat }} {{ $v->kota ? '('.$v->kota.')' : '' }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4">
                                    <p class="font-medium text-gray-900 text-xs">{{ $v->user?->nama ?? '-' }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $v->user?->email ?? '-' }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">Daftar: {{ $v->created_at->format('d M Y') }}</p>
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    @if ($v->status_verifikasi->value === 'menunggu')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Menunggu
                                        </span>
                                    @elseif ($v->status_verifikasi->value === 'terverifikasi')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Terverifikasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800" title="{{ $v->alasan_penolakan }}">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                            Ditolak
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-right space-x-2">
                                    @if ($v->status_verifikasi->value !== 'terverifikasi')
                                        <form
                                            method="POST"
                                            action="{{ route('admin.verifikasi-vendor.approve', $v->id) }}"
                                            class="inline-block"
                                            onsubmit="return confirm('Setujui dan verifikasi vendor {{ $v->nama_vendor }}?')"
                                        >
                                            @csrf
                                            <button
                                                type="submit"
                                                class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-[6px] text-xs font-semibold shadow-sm transition-colors"
                                            >
                                                Setujui
                                            </button>
                                        </form>
                                    @endif

                                    @if ($v->status_verifikasi->value !== 'ditolak')
                                        <button
                                            type="button"
                                            x-on:click="openReject({{ json_encode($v) }})"
                                            class="px-3 py-1 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-[6px] text-xs font-semibold transition-colors"
                                        >
                                            Tolak
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400">
                                    <p class="text-sm font-medium">Tidak ada data pendaftaran vendor</p>
                                    <p class="text-xs mt-1">Gunakan filter status atau kata kunci lain.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($vendors->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $vendors->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Alasan Penolakan (Alpine.js) -->
        <div
            x-show="rejectModalOpen"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-reject-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                <div
                    x-show="rejectModalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-900/60 transition-opacity"
                    x-on:click="rejectModalOpen = false"
                ></div>

                <div
                    x-show="rejectModalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                    class="relative bg-white rounded-[16px] text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-md sm:w-full p-6 space-y-4"
                >
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h3 class="text-lg font-bold text-gray-900">
                            Tolak Pendaftaran Vendor
                        </h3>
                        <button type="button" x-on:click="rejectModalOpen = false" class="text-gray-400 hover:text-gray-600">
                            <x-icon name="x" class="w-5 h-5" />
                        </button>
                    </div>

                    <p class="text-xs text-gray-600">
                        Anda akan menolak pengajuan vendor <strong x-text="vendorName"></strong>. Silakan masukkan alasan penolakan agar pemilik usaha dapat memperbaikinya.
                    </p>

                    <form :action="rejectActionUrl" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                                Alasan Penolakan <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                name="alasan_penolakan"
                                x-model="alasan"
                                rows="3"
                                required
                                placeholder="Contoh: Nomor kontak tidak dapat dihubungi atau alamat tidak lengkap."
                                class="w-full py-2 px-3 bg-white border border-gray-300 rounded-[8px] text-sm focus:outline-none focus:ring-1 focus:ring-red-500"
                            ></textarea>
                        </div>

                        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                            <x-button type="button" variant="secondary" x-on:click="rejectModalOpen = false">
                                Batal
                            </x-button>
                            <x-button type="submit" variant="danger">
                                Konfirmasi Tolak
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>

