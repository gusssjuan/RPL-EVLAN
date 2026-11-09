<x-layouts.app title="Acara Saya">
    <div class="space-y-6" x-data="{ tab: 'semua', search: '' }">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    Acara Saya
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola seluruh acara yang Anda pimpin atau ikuti sebagai panitia.
                </p>
            </div>
            <div>
                <a
                    href="{{ route('acara.buat.kategori') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-sm font-semibold shadow-sm transition-colors"
                >
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Buat Acara Baru</span>
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[12px] text-sm font-medium text-emerald-800 flex items-center gap-2.5">
                <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @php
            $allEvents = $eventsLed->map(function($e) {
                $e->user_role = 'ketua';
                return $e;
            })->concat($eventsJoined->map(function($e) {
                $e->user_role = 'anggota';
                return $e;
            }))->sortByDesc('tanggal');
        @endphp

        <!-- Tabs Filter & Search -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-200 pb-3">
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    @click="tab = 'semua'"
                    :class="tab === 'semua' ? 'bg-indigo-600 text-white font-bold' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 font-medium'"
                    class="px-3.5 py-1.5 rounded-full text-xs transition-colors"
                >
                    Semua Acara ({{ $allEvents->count() }})
                </button>
                <button
                    type="button"
                    @click="tab = 'ketua'"
                    :class="tab === 'ketua' ? 'bg-indigo-600 text-white font-bold' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 font-medium'"
                    class="px-3.5 py-1.5 rounded-full text-xs transition-colors"
                >
                    Saya Ketua ({{ $eventsLed->count() }})
                </button>
                <button
                    type="button"
                    @click="tab = 'anggota'"
                    :class="tab === 'anggota' ? 'bg-indigo-600 text-white font-bold' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 font-medium'"
                    class="px-3.5 py-1.5 rounded-full text-xs transition-colors"
                >
                    Saya Panitia ({{ $eventsJoined->count() }})
                </button>
            </div>

            <div class="relative sm:w-64">
                <input
                    type="text"
                    x-model="search"
                    placeholder="Cari nama acara..."
                    class="w-full text-xs rounded-[8px] border-gray-300 pl-8 pr-3 py-1.5 focus:border-indigo-500 focus:ring-indigo-500"
                />
                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-gray-400">
                    <x-icon name="search" class="w-3.5 h-3.5" />
                </div>
            </div>
        </div>

        <!-- Event Cards Grid -->
        @if ($allEvents->isEmpty())
            <div class="bg-white rounded-[16px] border border-gray-200 p-12 text-center shadow-sm">
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <x-icon name="calendar" class="w-6 h-6" />
                </div>
                <h3 class="text-base font-bold text-gray-900">Belum Ada Acara</h3>
                <p class="text-xs text-gray-500 max-w-sm mx-auto mt-1">
                    Anda belum memimpin atau bergabung dalam acara apapun. Mulai rencanakan acara pertama Anda sekarang.
                </p>
                <div class="mt-5">
                    <a
                        href="{{ route('acara.buat.kategori') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-xs font-semibold shadow-sm transition-colors"
                    >
                        <x-icon name="plus" class="w-4 h-4" />
                        <span>Mulai Buat Acara</span>
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($allEvents as $item)
                    @php
                        $roleText = $item->user_role === 'ketua' ? 'Ketua Panitia' : 'Anggota Panitia';
                        $progres = $item->progres_persen;
                    @endphp
                    <div
                        x-show="(tab === 'semua' || tab === '{{ $item->user_role }}') && ('{{ strtolower(addslashes($item->nama_event)) }}'.includes(search.toLowerCase()))"
                        x-transition
                        class="bg-white rounded-[16px] border border-gray-200 hover:border-indigo-300 p-5 shadow-sm hover:shadow transition flex flex-col justify-between"
                    >
                        <div>
                            <!-- Header Badges -->
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700">
                                    {{ $item->category?->ikon ?? '📁' }} {{ $item->category?->nama_kategori }}
                                </span>

                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">
                                        {{ $item->hari_menuju }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $item->user_role === 'ketua' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $roleText }}
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
                        </div>

                        <!-- Footer: Progress & Button -->
                        <div class="mt-5 pt-3 border-t border-gray-100">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="font-medium text-gray-600">Progres Persiapan</span>
                                <span class="font-bold text-indigo-600">{{ $progres }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden mb-3">
                                <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progres }}%"></div>
                            </div>

                            <a
                                href="{{ route('acara.ringkasan', $item->id) }}"
                                class="inline-flex items-center justify-center w-full py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-[8px] text-xs font-bold transition-colors"
                            >
                                <span>Buka Manajemen Acara</span>
                                <x-icon name="chevron-right" class="w-3.5 h-3.5 ml-1" />
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
