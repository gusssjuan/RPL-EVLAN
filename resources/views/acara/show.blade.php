<x-layouts.app :title="$event->nama_event">
    <div class="space-y-6">
        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[12px] text-sm font-medium text-emerald-800 flex items-center gap-2.5">
                <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- Event Header Card -->
        <div class="bg-white p-6 rounded-[16px] border border-gray-200 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs font-semibold">
                            {{ $event->category?->ikon ?? '📁' }} {{ $event->category?->nama_kategori }}
                        </span>
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-bold">
                            {{ $event->hari_menuju }}
                        </span>
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 mt-2">
                        {{ $event->nama_event }}
                    </h1>
                    <p class="text-xs text-gray-500 mt-1">
                        Ketua Panitia: {{ $event->leader?->nama }} • Lokasi: {{ $event->lokasi }} • Tanggal: {{ $event->tanggal->isoFormat('D MMMM Y') }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a
                        href="{{ url('/acara') }}"
                        class="px-4 py-2 border border-gray-300 rounded-[8px] text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Daftar Acara
                    </a>
                </div>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <x-stat-card
                title="Checklist Kebutuhan"
                value="{{ $event->checklists->count() }}"
                icon="check-circle"
                color="indigo"
                description="{{ $event->checklists->where('status_selesai', true)->count() }} terpenuhi"
            />

            <x-stat-card
                title="Agenda Timeline"
                value="{{ $event->timelines->count() }}"
                icon="calendar"
                color="emerald"
                description="Countdown H-n aktif"
            />

            <x-stat-card
                title="Target Peserta"
                value="{{ number_format($event->jumlah_peserta, 0, ',', '.') }}"
                icon="users"
                color="amber"
                description="{{ $event->target_peserta }}"
            />
        </div>
    </div>
</x-layouts.app>

