<x-layouts.admin title="Dashboard Administrator">
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    Dashboard Administrator
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Pantau metrik utama, data master, dan verifikasi vendor baru di EVLAN.
                </p>
            </div>
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Sistem Aktif
                </span>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <x-stat-card
                title="Total Pengguna"
                value="{{ $totalPengguna }}"
                icon="users"
                color="indigo"
                description="Terdaftar di sistem"
            />

            <x-stat-card
                title="Acara Aktif"
                value="{{ $acaraAktif }}"
                icon="calendar"
                color="emerald"
                description="Dalam perencanaan"
            />

            <x-stat-card
                title="Vendor Menunggu"
                value="{{ $vendorMenungguCount }}"
                icon="briefcase"
                color="amber"
                description="Perlu ditinjau"
            />

            <x-stat-card
                title="Kategori / Template"
                value="{{ $totalKategori }} / {{ $totalTemplate }}"
                icon="check-circle"
                color="indigo"
                description="Katalog aktif"
            />
        </div>

        <!-- Two Columns: Vendor Menunggu Verifikasi & Aktivitas Terbaru -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Kolom Kiri: Vendor Menunggu Verifikasi -->
            <div class="bg-white rounded-[12px] border border-gray-200 shadow-sm overflow-hidden flex flex-col justify-between">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <x-icon name="briefcase" class="w-5 h-5 text-amber-500" />
                            <span>Vendor Menunggu Verifikasi</span>
                        </h2>
                        <p class="text-xs text-gray-500 mt-0.5">Pendaftaran usaha baru yang menunggu keputusan</p>
                    </div>
                    <a href="{{ url('/admin/verifikasi-vendor') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                        Lihat Semua
                    </a>
                </div>

                <div class="p-6 divide-y divide-gray-100 flex-1">
                    @forelse ($vendorMenunggu as $vendor)
                        <div class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-gray-900 truncate">
                                    {{ $vendor->nama_vendor }}
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5 flex items-center gap-2">
                                    <span class="inline-block px-2 py-0.5 bg-gray-100 rounded text-[11px] font-medium text-gray-700">
                                        {{ ucfirst(str_replace('_', ' ', $vendor->kategori_vendor)) }}
                                    </span>
                                    <span>•</span>
                                    <span>{{ $vendor->kontak }}</span>
                                </p>
                                <p class="text-[11px] text-gray-400 mt-0.5">
                                    Pemilik: {{ $vendor->user?->nama ?? '-' }} ({{ $vendor->created_at->diffForHumans() }})
                                </p>
                            </div>
                            <div class="shrink-0">
                                <a
                                    href="{{ url('/admin/verifikasi-vendor') }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-[8px] text-xs font-semibold shadow-sm transition-colors"
                                >
                                    Tinjau
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-gray-400">
                            <x-icon name="check-circle" class="w-8 h-8 mx-auto text-emerald-500 mb-2" />
                            <p class="text-sm font-medium text-gray-600">Tidak ada vendor menunggu verifikasi</p>
                            <p class="text-xs text-gray-400 mt-0.5">Semua permohonan vendor telah diproses.</p>
                        </div>
                    @endforelse
                </div>

                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 text-xs text-gray-500 text-right">
                    Menampilkan maksimal 5 permohonan terbaru
                </div>
            </div>

            <!-- Kolom Kanan: Aktivitas Sistem Terbaru -->
            <div class="bg-white rounded-[12px] border border-gray-200 shadow-sm overflow-hidden flex flex-col justify-between">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <x-icon name="circle" class="w-5 h-5 text-indigo-500" />
                        <span>Aktivitas Sistem Terbaru</span>
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">Audit log operasional pengguna dan acara</p>
                </div>

                <div class="p-6 divide-y divide-gray-100 flex-1">
                    @forelse ($aktivitasTerbaru as $log)
                        <div class="py-3 first:pt-0 last:pb-0 flex items-start gap-3">
                            <div class="w-2 h-2 rounded-full bg-indigo-500 mt-1.5 shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-gray-800">
                                    {{ $log->aksi }}
                                </p>
                                <p class="text-xs text-gray-600 mt-0.5">
                                    {{ $log->deskripsi }}
                                </p>
                                <p class="text-[11px] text-gray-400 mt-0.5 flex items-center gap-1.5">
                                    <span>{{ $log->user?->nama ?? 'Sistem' }}</span>
                                    <span>•</span>
                                    <span>{{ $log->created_at->diffForHumans() }}</span>
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-gray-400">
                            <x-icon name="circle" class="w-8 h-8 mx-auto text-gray-300 mb-2" />
                            <p class="text-sm font-medium text-gray-600">Belum ada aktivitas tercatat</p>
                            <p class="text-xs text-gray-400 mt-0.5">Log akan terisi saat ada interaksi pada acara.</p>
                        </div>
                    @endforelse
                </div>

                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 text-xs text-gray-500 text-right">
                    Pembaruan otomatis real-time
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
