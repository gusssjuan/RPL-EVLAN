<x-layouts.app title="Riwayat Acara">
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    Riwayat Acara
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Arsip seluruh acara yang pernah Anda pimpin atau ikuti beserta evaluasi akhirnya.
                </p>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-200 pb-4">
            <!-- Status Chips -->
            <div class="flex flex-wrap items-center gap-2">
                <a
                    href="{{ route('riwayat.index', request()->except('status')) }}"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold {{ !request('status') ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors"
                >
                    Semua Acara ({{ $countSemua }})
                </a>
                <a
                    href="{{ route('riwayat.index', array_merge(request()->except('status'), ['status' => 'selesai'])) }}"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold {{ request('status') === 'selesai' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors"
                >
                    <x-icon name="check" class="w-3.5 h-3.5" />
                    Selesai ({{ $countSelesai }})
                </a>
                <a
                    href="{{ route('riwayat.index', array_merge(request()->except('status'), ['status' => 'perencanaan'])) }}"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold {{ request('status') === 'perencanaan' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors"
                >
                    <x-icon name="circle" class="w-3.5 h-3.5" />
                    Sedang Berjalan ({{ $countPerencanaan }})
                </a>
            </div>

            <!-- Search & Tahun Filter -->
            <form action="{{ route('riwayat.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}" />
                @endif

                @if ($allYears->count() > 1)
                    <select
                        name="tahun"
                        onchange="this.form.submit()"
                        class="text-xs rounded-[8px] border-gray-300 py-1.5 focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">Semua Tahun</option>
                        @foreach ($allYears as $th)
                            <option value="{{ $th }}" {{ request('tahun') == $th ? 'selected' : '' }}>
                                Tahun {{ $th }}
                            </option>
                        @endforeach
                    </select>
                @endif

                <div class="relative">
                    <input
                        type="text"
                        name="cari"
                        value="{{ request('cari') }}"
                        placeholder="Cari arsip acara..."
                        class="text-xs rounded-[8px] border-gray-300 pl-8 pr-3 py-1.5 focus:border-indigo-500 focus:ring-indigo-500 sm:w-56"
                    />
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-gray-400">
                        <x-icon name="search" class="w-3.5 h-3.5" />
                    </div>
                </div>

                <button
                    type="submit"
                    class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-[8px] text-xs font-semibold transition-colors"
                >
                    Filter
                </button>
            </form>
        </div>

        <!-- Cards List -->
        @if ($events->isEmpty())
            <div class="bg-white rounded-[16px] border border-gray-200 p-12 text-center shadow-sm">
                <div class="w-12 h-12 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-3">
                    <x-icon name="archive" class="w-6 h-6" />
                </div>
                <h3 class="text-base font-bold text-gray-900">Belum Ada Riwayat Acara</h3>
                <p class="text-xs text-gray-500 max-w-sm mx-auto mt-1">
                    {{ request('status') || request('cari') || request('tahun') ? 'Tidak ada acara yang cocok dengan kriteria filter.' : 'Acara yang telah selesai ditutup akan otomatis diarsipkan di halaman ini.' }}
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($events as $item)
                    @php
                        $isClosed = $item->status_event === \App\Enums\StatusEvent::Selesai;
                        $roleText = $item->peran_user === 'ketua' ? 'Ketua Panitia' : 'Anggota Panitia';
                    @endphp
                    <div class="bg-white rounded-[16px] border border-gray-200 hover:border-indigo-300 p-5 shadow-sm hover:shadow transition flex flex-col justify-between">
                        <div>
                            <!-- Header Badges -->
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700">
                                    <x-category-icon :kategori="$item->category" class="w-3.5 h-3.5" />
                                    {{ $item->category?->nama_kategori }}
                                </span>

                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $item->peran_user === 'ketua' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $roleText }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $isClosed ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $item->status_event->label() }}
                                    </span>
                                </div>
                            </div>

                            <!-- Title -->
                            <h2 class="text-base font-bold text-gray-900 mt-3 line-clamp-1">
                                <a href="{{ route('acara.ringkasan', $item->id) }}" class="hover:text-indigo-600 transition-colors">
                                    {{ $item->nama_event }}
                                </a>
                            </h2>

                            <!-- Metadata -->
                            <div class="mt-2 space-y-1 text-xs text-gray-500">
                                <div class="flex items-center gap-1.5">
                                    <x-icon name="calendar" class="w-3.5 h-3.5 text-gray-400 shrink-0" />
                                    <span>{{ $item->tanggal->isoFormat('D MMMM Y') }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <x-icon name="map-pin" class="w-3.5 h-3.5 text-gray-400 shrink-0" />
                                    <span class="truncate">{{ $item->lokasi }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <x-icon name="users" class="w-3.5 h-3.5 text-gray-400 shrink-0" />
                                    <span>{{ number_format($item->jumlah_peserta, 0, ',', '.') }} Peserta</span>
                                </div>
                            </div>

                            <!-- Evaluasi Snippet (jika ada) -->
                            @if ($item->evaluasi)
                                <div class="mt-3 p-3 bg-gray-50 rounded-[8px] border border-gray-100 text-[11px] text-gray-600">
                                    <strong class="text-gray-800 block mb-0.5">Catatan Evaluasi:</strong>
                                    <p class="line-clamp-2 italic">"{{ $item->evaluasi }}"</p>
                                </div>
                            @endif
                        </div>

                        <!-- Footer -->
                        <div class="mt-5 pt-3 border-t border-gray-100">
                            <a
                                href="{{ route('acara.ringkasan', $item->id) }}"
                                class="inline-flex items-center justify-center w-full py-2 bg-gray-100 hover:bg-indigo-50 hover:text-indigo-700 text-gray-700 rounded-[8px] text-xs font-bold transition-colors"
                            >
                                <span>Buka Arsip Acara</span>
                                <x-icon name="chevron-right" class="w-3.5 h-3.5 ml-1" />
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>

