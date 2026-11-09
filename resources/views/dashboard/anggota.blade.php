<x-layouts.app title="Dashboard Anggota Kepanitiaan">
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                Halo, {{ $user->nama }} 👋
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Pantau tugas prioritas Anda dan koordinasi acara yang sedang Anda ikuti.
            </p>
        </div>

        <!-- 4 Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card
                title="Tugas Saya"
                value="{{ $statTugasSaya }}"
                icon="check-circle"
                color="indigo"
            />
            <x-stat-card
                title="Tugas Selesai"
                value="{{ $statTugasSelesai }}"
                icon="award"
                color="emerald"
            />
            <x-stat-card
                title="Tugas Terlewat"
                value="{{ $statTugasTerlewat }}"
                icon="alert-triangle"
                color="danger"
            />
            <x-stat-card
                title="Acara Diikuti"
                value="{{ $statAcaraDiikuti }}"
                icon="users"
                color="amber"
            />
        </div>

        <!-- Two Columns: Priority Tasks & Joined Events -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Kolom Kiri: Tugas Prioritas Saya (S05-V) -->
            <div class="lg:col-span-7 bg-white rounded-[16px] border border-gray-200 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2">
                        <x-icon name="list-checks" class="w-4 h-4 text-indigo-600" />
                        <h2 class="text-sm font-bold text-gray-900">Tugas Prioritas Saya</h2>
                    </div>
                    <span class="text-xs text-gray-400">Diurutkan Berdasarkan Tenggat</span>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($tugasPrioritas as $tgs)
                        @php
                            $dlDate = \Carbon\Carbon::parse($tgs->deadline);
                            $isOverdue = $dlDate->isPast() && ! $dlDate->isToday();
                            $isToday = $dlDate->isToday();
                        @endphp
                        <div class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0">
                                <p class="font-bold text-gray-900 truncate">{{ $tgs->judul }}</p>
                                <p class="text-[11px] text-gray-500 mt-0.5">
                                    <span>{{ $tgs->event?->nama_event }}</span>
                                    @if ($tgs->division)
                                        <span> • Divisi {{ $tgs->division->nama_divisi }}</span>
                                    @endif
                                </p>
                            </div>

                            <div class="shrink-0 text-right space-y-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $isOverdue ? 'bg-red-100 text-red-800' : ($isToday ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700') }}">
                                    {{ $dlDate->isoFormat('D MMM Y') }}
                                </span>
                                <div>
                                    <span class="text-[10px] font-medium text-indigo-600">
                                        {{ ucfirst(str_replace('_', ' ', $tgs->status->value)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center text-gray-400 text-xs">
                            <x-icon name="check-circle" class="w-8 h-8 mx-auto text-emerald-500 mb-2" />
                            <p class="font-medium text-gray-700">Tidak ada tugas tertunda!</p>
                            <p class="text-gray-400 mt-0.5">Semua tugas kepanitiaan Anda telah diselesaikan.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Kolom Kanan: Acara yang Saya Ikuti -->
            <div class="lg:col-span-5 bg-white rounded-[16px] border border-gray-200 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2">
                        <x-icon name="calendar" class="w-4 h-4 text-emerald-600" />
                        <h2 class="text-sm font-bold text-gray-900">Kepanitiaan Acara</h2>
                    </div>
                    <a href="{{ route('acara.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                        Lihat Semua
                    </a>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($acaraDiikuti as $ev)
                        <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0">
                                <h3 class="font-bold text-gray-900 truncate">
                                    <a href="{{ route('acara.ringkasan', $ev->id) }}" class="hover:text-indigo-600">
                                        {{ $ev->nama_event }}
                                    </a>
                                </h3>
                                <p class="text-[11px] text-gray-500 mt-0.5">
                                    <span>👑 Ketua: {{ $ev->leader?->nama ?? '-' }}</span>
                                    <span> • </span>
                                    <span>📅 {{ $ev->tanggal->isoFormat('D MMM Y') }}</span>
                                </p>
                            </div>

                            <a
                                href="{{ route('acara.ringkasan', $ev->id) }}"
                                class="shrink-0 px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold rounded text-[11px] transition-colors"
                            >
                                Buka
                            </a>
                        </div>
                    @empty
                        <div class="py-10 text-center text-gray-400 text-xs">
                            <p>Anda belum terdaftar dalam kepanitiaan acara manapun.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>