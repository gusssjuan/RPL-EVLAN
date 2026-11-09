<x-layouts.app :title="'Detail Tugas - ' . $task->judul">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Back Navigation -->
        <div class="flex items-center justify-between text-xs text-gray-500">
            <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('tugas.saya') }}" class="inline-flex items-center gap-1.5 hover:text-indigo-600 transition-colors font-medium">
                <x-icon name="chevron-left" class="w-4 h-4" />
                <span>Kembali</span>
            </a>
            <span class="text-xs text-gray-400">ID Tugas: #{{ $task->id }}</span>
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[12px] text-sm font-medium text-emerald-800 flex items-center gap-2.5">
                <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-[12px] text-xs font-medium text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @php
            $isOverdue = $task->is_terlewat;
            $dlDate = \Carbon\Carbon::parse($task->deadline);
        @endphp

        <!-- Main Card -->
        <div class="bg-white rounded-[16px] border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('acara.ringkasan', $event->id) }}" class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200">
                            {{ $event->category?->ikon ?? '📁' }} {{ $event->nama_event }}
                        </a>
                        @if ($task->division)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                                Divisi {{ $task->division->nama_divisi }}
                            </span>
                        @endif
                    </div>

                    @if ($isOverdue)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                            <x-icon name="alert-triangle" class="w-3.5 h-3.5" />
                            <span>Tenggat Terlewat</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold
                            {{ $task->status === \App\Enums\StatusTugas::BelumDikerjakan ? 'bg-gray-100 text-gray-700' : '' }}
                            {{ $task->status === \App\Enums\StatusTugas::SedangDikerjakan ? 'bg-amber-100 text-amber-800' : '' }}
                            {{ $task->status === \App\Enums\StatusTugas::SudahDiselesaikan ? 'bg-emerald-100 text-emerald-800' : '' }}
                        ">
                            <span>{{ $task->status->icon() }}</span>
                            <span>{{ $task->status->label() }}</span>
                        </span>
                    @endif
                </div>

                <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
                    {{ $task->judul }}
                </h1>

                @if ($task->deskripsi)
                    <div class="text-sm text-gray-700 leading-relaxed bg-gray-50/70 rounded-[10px] p-4 border border-gray-100">
                        {!! nl2br(e($task->deskripsi)) !!}
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 text-xs">
                    <div>
                        <span class="text-gray-400 block font-medium">Penerima Tugas:</span>
                        <div class="flex items-center gap-1.5 mt-1">
                            <div class="w-5 h-5 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-[10px] font-bold">
                                {{ strtoupper(substr($task->penerima?->nama ?? 'U', 0, 1)) }}
                            </div>
                            <span class="font-bold text-gray-900">{{ $task->penerima?->nama ?? '-' }}</span>
                        </div>
                    </div>

                    <div>
                        <span class="text-gray-400 block font-medium">Batas Waktu (Deadline):</span>
                        <span class="font-bold mt-1 inline-block {{ $isOverdue ? 'text-red-600' : 'text-gray-900' }}">
                            📅 {{ $dlDate->isoFormat('dddd, D MMMM Y') }}
                        </span>
                    </div>

                    <div>
                        <span class="text-gray-400 block font-medium">Ditugaskan Oleh:</span>
                        <span class="font-bold text-gray-900 mt-1 inline-block">
                            👑 {{ $task->creator?->nama ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Bagian Ubah Status (D-14: Hanya Penerima Tugas) -->
            @if ($isPenerima)
                <div class="p-6 bg-indigo-50/30 border-b border-gray-100">
                    <h3 class="text-xs font-bold text-indigo-900 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <x-icon name="check-square" class="w-4 h-4 text-indigo-600" />
                        <span>Perbarui Status Pengerjaan</span>
                    </h3>
                    <p class="text-xs text-gray-500 mb-3">
                        Sebagai penerima tugas, Anda dapat memperbarui status pengerjaan sesuai tahapan terkini:
                    </p>

                    <form action="{{ route('tugas.status', $task->id) }}" method="POST" class="flex flex-wrap items-center gap-2">
                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            name="status"
                            value="belum_dikerjakan"
                            class="px-4 py-2 rounded-[8px] text-xs font-bold transition-colors {{ $task->status === \App\Enums\StatusTugas::BelumDikerjakan ? 'bg-gray-800 text-white shadow-sm' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}"
                        >
                            ○ Belum dikerjakan
                        </button>

                        <button
                            type="submit"
                            name="status"
                            value="sedang_dikerjakan"
                            class="px-4 py-2 rounded-[8px] text-xs font-bold transition-colors {{ $task->status === \App\Enums\StatusTugas::SedangDikerjakan ? 'bg-amber-500 text-white shadow-sm' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}"
                        >
                            ◐ Sedang dikerjakan
                        </button>

                        <button
                            type="submit"
                            name="status"
                            value="sudah_diselesaikan"
                            class="px-4 py-2 rounded-[8px] text-xs font-bold transition-colors {{ $task->status === \App\Enums\StatusTugas::SudahDiselesaikan ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}"
                        >
                            ✓ Sudah diselesaikan
                        </button>
                    </form>
                </div>
            @endif

            <!-- Riwayat Aktivitas Tugas -->
            <div class="p-6">
                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                    <x-icon name="activity" class="w-4 h-4 text-indigo-600" />
                    <span>Riwayat Aktivitas Tugas</span>
                </h3>

                <div class="space-y-3">
                    @forelse ($riwayat as $log)
                        <div class="text-xs flex items-start gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">
                                {{ strtoupper(substr($log->user?->nama ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-gray-800">
                                    <strong class="text-gray-900">{{ $log->user?->nama ?? 'Pengguna' }}</strong>
                                    {{ $log->deskripsi }}
                                </p>
                                <span class="text-[10px] text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400">Belum ada catatan aktivitas untuk tugas ini.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

