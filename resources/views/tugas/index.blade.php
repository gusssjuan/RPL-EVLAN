<x-layouts.app title="Tugas Saya">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    Tugas Saya
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Seluruh tugas kepanitiaan yang didelegasikan kepada Anda lintas acara.
                </p>
            </div>
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[12px] text-sm font-medium text-emerald-800 flex items-center gap-2.5">
                <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- Status Filter Chips -->
        <div class="flex flex-wrap items-center gap-2">
            <a
                href="{{ route('tugas.saya', request()->only('event_id')) }}"
                class="px-3 py-1.5 rounded-full text-xs font-semibold {{ !request('status') ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors"
            >
                Semua ({{ $countBelum + $countSedang + $countSelesai }})
            </a>
            <a
                href="{{ route('tugas.saya', array_merge(request()->only('event_id'), ['status' => 'belum_dikerjakan'])) }}"
                class="px-3 py-1.5 rounded-full text-xs font-semibold {{ request('status') === 'belum_dikerjakan' ? 'bg-gray-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors"
            >
                ○ Belum ({{ $countBelum }})
            </a>
            <a
                href="{{ route('tugas.saya', array_merge(request()->only('event_id'), ['status' => 'sedang_dikerjakan'])) }}"
                class="px-3 py-1.5 rounded-full text-xs font-semibold {{ request('status') === 'sedang_dikerjakan' ? 'bg-amber-500 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }} transition-colors"
            >
                ◐ Sedang ({{ $countSedang }})
            </a>
            <a
                href="{{ route('tugas.saya', array_merge(request()->only('event_id'), ['status' => 'sudah_diselesaikan'])) }}"
                class="px-3 py-1.5 rounded-full text-xs font-semibold {{ request('status') === 'sudah_diselesaikan' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }} transition-colors"
            >
                ✓ Selesai ({{ $countSelesai }})
            </a>
            @if ($countTerlewat > 0)
                <a
                    href="{{ route('tugas.saya', array_merge(request()->only('event_id'), ['status' => 'terlewat'])) }}"
                    class="px-3 py-1.5 rounded-full text-xs font-semibold {{ request('status') === 'terlewat' ? 'bg-red-600 text-white' : 'bg-red-50 text-red-700 hover:bg-red-100' }} transition-colors"
                >
                    ▲ Terlewat ({{ $countTerlewat }})
                </a>
            @endif
        </div>

        <!-- Filter Acara Dropdown -->
        @if ($myEvents->count() > 1)
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500 font-medium">Filter Acara:</span>
                <form action="{{ route('tugas.saya') }}" method="GET" class="inline">
                    @if (request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}" />
                    @endif
                    <select
                        name="event_id"
                        onchange="this.form.submit()"
                        class="text-xs rounded-[8px] border-gray-300 py-1.5 pr-8 focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">Semua Acara</option>
                        @foreach ($myEvents as $ev)
                            <option value="{{ $ev->id }}" {{ request('event_id') == $ev->id ? 'selected' : '' }}>
                                {{ $ev->nama_event }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        @endif

        <!-- List Tugas -->
        <div class="bg-white rounded-[16px] border border-gray-200 shadow-sm overflow-hidden">
            @if ($tasks->isEmpty())
                <div class="p-12 text-center">
                    <x-icon name="check-circle" class="w-10 h-10 text-emerald-500 mx-auto mb-2" />
                    <h3 class="text-sm font-bold text-gray-700">Tidak ada tugas</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ request('status') ? 'Tidak ada tugas dengan filter status ini.' : 'Semua tugas kepanitiaan Anda telah diselesaikan.' }}
                    </p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach ($tasks as $task)
                        @php
                            $isOverdue = $task->is_terlewat;
                            $dlDate = \Carbon\Carbon::parse($task->deadline);
                            $isToday = $dlDate->isToday();
                        @endphp
                        <div class="p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 hover:bg-gray-50/60 transition-colors {{ $isOverdue ? 'bg-red-50/20' : '' }}">
                            <div class="space-y-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700">
                                        {{ $task->event?->category?->ikon ?? '📁' }} {{ $task->event?->nama_event }}
                                    </span>
                                    @if ($task->division)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-700">
                                            Divisi {{ $task->division->nama_divisi }}
                                        </span>
                                    @endif
                                    @if ($isOverdue)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800">
                                            ▲ Terlewat
                                        </span>
                                    @endif
                                </div>

                                <h2 class="text-base font-bold text-gray-900 truncate">
                                    <a href="{{ route('tugas.show', $task->id) }}" class="hover:text-indigo-600 transition-colors">
                                        {{ $task->judul }}
                                    </a>
                                </h2>

                                @if ($task->deskripsi)
                                    <p class="text-xs text-gray-500 line-clamp-1">
                                        {{ $task->deskripsi }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex items-center gap-4 shrink-0">
                                <div class="text-right text-xs">
                                    <p class="font-medium {{ $isOverdue ? 'text-red-600 font-bold' : ($isToday ? 'text-amber-600 font-bold' : 'text-gray-500') }}">
                                        📅 {{ $dlDate->isoFormat('D MMM Y') }}
                                    </p>
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold mt-1
                                        {{ $task->status === \App\Enums\StatusTugas::BelumDikerjakan ? 'bg-gray-100 text-gray-700' : '' }}
                                        {{ $task->status === \App\Enums\StatusTugas::SedangDikerjakan ? 'bg-amber-100 text-amber-800' : '' }}
                                        {{ $task->status === \App\Enums\StatusTugas::SudahDiselesaikan ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    ">
                                        {{ $task->status->icon() }} {{ $task->status->label() }}
                                    </span>
                                </div>

                                <a
                                    href="{{ route('tugas.show', $task->id) }}"
                                    class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-[8px] text-xs font-bold transition-colors"
                                >
                                    Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>

