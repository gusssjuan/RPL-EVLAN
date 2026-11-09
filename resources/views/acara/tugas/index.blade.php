<x-layouts.app :title="'Tugas Tim - ' . $event->nama_event">
    <div class="space-y-6" x-data="{ showCreateModal: false }">
        <x-acara.partials.header :event="$event" activeTab="tugas" />

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

        <!-- Chip Ringkasan Status -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('acara.tugas.index', $event->id) }}" class="px-3 py-1.5 rounded-full text-xs font-semibold {{ !request('status') ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">
                Semua ({{ $countBelum + $countSedang + $countSelesai }})
            </a>
            <a href="{{ route('acara.tugas.index', array_merge(request()->except('status', 'page'), ['event' => $event->id, 'status' => 'belum_dikerjakan'])) }}" class="px-3 py-1.5 rounded-full text-xs font-semibold {{ request('status') === 'belum_dikerjakan' ? 'bg-gray-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">
                ○ Belum ({{ $countBelum }})
            </a>
            <a href="{{ route('acara.tugas.index', array_merge(request()->except('status', 'page'), ['event' => $event->id, 'status' => 'sedang_dikerjakan'])) }}" class="px-3 py-1.5 rounded-full text-xs font-semibold {{ request('status') === 'sedang_dikerjakan' ? 'bg-amber-500 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }} transition-colors">
                ◐ Sedang ({{ $countSedang }})
            </a>
            <a href="{{ route('acara.tugas.index', array_merge(request()->except('status', 'page'), ['event' => $event->id, 'status' => 'sudah_diselesaikan'])) }}" class="px-3 py-1.5 rounded-full text-xs font-semibold {{ request('status') === 'sudah_diselesaikan' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }} transition-colors">
                ✓ Selesai ({{ $countSelesai }})
            </a>
            @if ($countTerlewat > 0)
                <a href="{{ route('acara.tugas.index', array_merge(request()->except('status', 'page'), ['event' => $event->id, 'status' => 'terlewat'])) }}" class="px-3 py-1.5 rounded-full text-xs font-semibold {{ request('status') === 'terlewat' ? 'bg-red-600 text-white' : 'bg-red-50 text-red-700 hover:bg-red-100' }} transition-colors">
                    ▲ Terlewat ({{ $countTerlewat }})
                </a>
            @endif
        </div>

        <!-- Filter Bar & Buat Tugas -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <form action="{{ route('acara.tugas.index', $event->id) }}" method="GET" class="flex flex-wrap items-center gap-2">
                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}" />
                @endif
                <select name="divisi" class="text-xs rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5">
                    <option value="">Semua Divisi</option>
                    @foreach ($event->divisions as $div)
                        <option value="{{ $div->id }}" {{ request('divisi') == $div->id ? 'selected' : '' }}>{{ $div->nama_divisi }}</option>
                    @endforeach
                </select>
                <select name="penerima" class="text-xs rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5">
                    <option value="">Semua Penerima</option>
                    @foreach ($pesertaAcara as $p)
                        <option value="{{ $p->id }}" {{ request('penerima') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                    @endforeach
                </select>
                <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari tugas…" class="text-xs rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5 w-40" />
                <button type="submit" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-[8px] text-xs font-semibold transition-colors">Filter</button>
            </form>

            @if ($isLeader)
                <button @click="showCreateModal = true" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-xs font-semibold shadow-sm transition-colors whitespace-nowrap">
                    <x-icon name="plus" class="w-3.5 h-3.5" />
                    <span>Buat Tugas</span>
                </button>
            @endif
        </div>

        <!-- Tabel Tugas -->
        <div class="bg-white rounded-[16px] border border-gray-200 shadow-sm overflow-hidden">
            @if ($tasks->isEmpty())
                <div class="p-10 text-center">
                    <x-icon name="clipboard-list" class="w-10 h-10 text-gray-300 mx-auto mb-2" />
                    <h3 class="text-sm font-bold text-gray-700">Belum ada tugas</h3>
                    <p class="text-xs text-gray-500 mt-1">Buat tugas pertama untuk mulai membagi pekerjaan kepada anggota panitia.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3 text-left font-semibold">Judul</th>
                                <th class="px-5 py-3 text-left font-semibold">Divisi</th>
                                <th class="px-5 py-3 text-left font-semibold">Penerima</th>
                                <th class="px-5 py-3 text-left font-semibold">Deadline</th>
                                <th class="px-5 py-3 text-center font-semibold">Status</th>
                                <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($tasks as $task)
                                @php
                                    $isOverdue = $task->is_terlewat;
                                @endphp
                                <tr class="hover:bg-gray-50/60 transition-colors {{ $isOverdue ? 'bg-red-50/30' : '' }}">
                                    <td class="px-5 py-3.5 font-semibold text-gray-900 max-w-[200px] truncate">
                                        <a href="{{ route('tugas.show', $task->id) }}" class="hover:text-indigo-600 transition-colors">{{ $task->judul }}</a>
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-600">{{ $task->division?->nama_divisi ?? '—' }}</td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-1.5">
                                            <div class="w-5 h-5 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-[9px] font-bold shrink-0">
                                                {{ strtoupper(substr($task->penerima?->nama ?? 'U', 0, 1)) }}
                                            </div>
                                            <span class="text-gray-700">{{ $task->penerima?->nama ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap {{ $isOverdue ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                        @if ($isOverdue)<x-icon name="alert-triangle" class="w-3.5 h-3.5 inline text-red-500 mr-0.5" />@endif
                                        {{ $task->deadline->isoFormat('D MMM Y') }}
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        @if ($isOverdue)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800">▲ Terlewat</span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold
                                                {{ $task->status === \App\Enums\StatusTugas::BelumDikerjakan ? 'bg-gray-100 text-gray-700' : '' }}
                                                {{ $task->status === \App\Enums\StatusTugas::SedangDikerjakan ? 'bg-amber-100 text-amber-800' : '' }}
                                                {{ $task->status === \App\Enums\StatusTugas::SudahDiselesaikan ? 'bg-emerald-100 text-emerald-800' : '' }}
                                            ">{{ $task->status->icon() }} {{ $task->status->label() }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <a href="{{ route('tugas.show', $task->id) }}" class="text-indigo-600 hover:underline font-semibold">Detail</a>
                                        @if ($isLeader)
                                            <form action="{{ route('acara.tugas.destroy', [$event->id, $task->id]) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Hapus tugas ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:underline font-semibold">Hapus</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($tasks->hasPages())
                    <div class="px-5 py-3 border-t border-gray-100">
                        {{ $tasks->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- Modal Buat Tugas (S15-V) -->
        @if ($isLeader)
            <div x-show="showCreateModal" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" @click.self="showCreateModal = false" @keydown.escape.window="showCreateModal = false">
                <div class="bg-white rounded-[16px] p-6 w-full max-w-lg shadow-lg space-y-4 max-h-[90vh] overflow-y-auto" x-data="{ fromRec: false }">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-gray-900">Buat Tugas Baru</h3>
                        <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600"><x-icon name="x" class="w-5 h-5" /></button>
                    </div>

                    <form action="{{ route('acara.tugas.store', $event->id) }}" method="POST" class="space-y-4">
                        @csrf

                        @if ($jobdeskRecommendations->count() > 0)
                            <div class="p-3 bg-indigo-50/60 rounded-[8px] border border-indigo-200">
                                <label class="flex items-center gap-2 text-xs font-semibold text-indigo-800 cursor-pointer">
                                    <input type="checkbox" x-model="fromRec" class="rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500" />
                                    <span>Dari rekomendasi jobdesk template</span>
                                </label>
                                <div x-show="fromRec" x-transition class="mt-2">
                                    <select x-on:change="if($event.target.value) { let opt = $event.target.selectedOptions[0]; document.getElementById('judul').value = opt.dataset.judul; document.getElementById('deskripsi').value = opt.dataset.deskripsi || ''; }" class="w-full text-xs rounded-[8px] border-indigo-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5">
                                        <option value="">— Pilih rekomendasi —</option>
                                        @foreach ($jobdeskRecommendations as $jd)
                                            <option value="{{ $jd->id }}" data-judul="{{ $jd->nama_item }}" data-deskripsi="{{ $jd->deskripsi ?? '' }}">
                                                [{{ $jd->kelompok }}] {{ $jd->nama_item }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                        <div>
                            <label for="judul" class="block text-xs font-semibold text-gray-700 mb-1">Judul Tugas <span class="text-red-500">*</span></label>
                            <input type="text" id="judul" name="judul" required class="w-full text-sm rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>

                        <div>
                            <label for="deskripsi" class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" rows="2" class="w-full text-sm rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="task_division_id" class="block text-xs font-semibold text-gray-700 mb-1">Divisi</label>
                                <select id="task_division_id" name="division_id" class="w-full text-sm rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">— Tanpa divisi —</option>
                                    @foreach ($event->divisions as $div)
                                        <option value="{{ $div->id }}">{{ $div->nama_divisi }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="penerima_id" class="block text-xs font-semibold text-gray-700 mb-1">Penerima <span class="text-red-500">*</span></label>
                                <select id="penerima_id" name="penerima_id" required class="w-full text-sm rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">— Pilih —</option>
                                    @foreach ($pesertaAcara as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="deadline" class="block text-xs font-semibold text-gray-700 mb-1">Deadline <span class="text-red-500">*</span></label>
                            <input type="date" id="deadline" name="deadline" required max="{{ $event->tanggal->toDateString() }}" class="w-full text-sm rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                            <p class="text-[11px] text-gray-500 mt-0.5">Maksimal tanggal acara: {{ $event->tanggal->isoFormat('D MMMM Y') }}</p>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="showCreateModal = false" class="px-4 py-2 border border-gray-300 rounded-[8px] text-xs font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-xs font-semibold shadow-sm">Buat Tugas</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>

