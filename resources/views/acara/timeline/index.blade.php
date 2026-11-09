<x-layouts.app :title="'Timeline Persiapan - ' . $event->nama_event">
    <div
        x-data="{
            modalOpen: false,
            isEdit: false,
            actionUrl: '{{ route('acara.timeline.store', $event->id) }}',
            kegiatan: '',
            offset_hari: 30,
            openCreate() {
                this.isEdit = false;
                this.actionUrl = '{{ route('acara.timeline.store', $event->id) }}';
                this.kegiatan = '';
                this.offset_hari = 14;
                this.modalOpen = true;
            },
            openEdit(item) {
                this.isEdit = true;
                this.actionUrl = '{{ url('/acara/' . $event->id . '/timeline') }}/' + item.id;
                this.kegiatan = item.kegiatan;
                this.offset_hari = item.offset_hari;
                this.modalOpen = true;
            }
        }"
        class="space-y-6"
    >
        <!-- Shared Event Header -->
        <x-acara.partials.header :event="$event" activeTab="timeline" />

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[12px] text-sm font-medium text-emerald-800 flex items-center gap-2.5">
                <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- Timeline Action Bar -->
        <div class="bg-white p-4 rounded-[12px] border border-gray-200 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-gray-900">Agenda Countdown H-n Persiapan</h2>
                <p class="text-xs text-gray-500">
                    Urutan menurun dari H-terjauh menuju hari pelaksanaan acara.
                </p>
            </div>

            @if ($isLeader)
                <div>
                    <x-button type="button" variant="primary" size="sm" x-on:click="openCreate()">
                        <x-icon name="plus" class="w-3.5 h-3.5 mr-1" />
                        Tambah Agenda Timeline
                    </x-button>
                </div>
            @endif
        </div>

        <!-- Vertical Timeline Card (S13) -->
        <div class="bg-white rounded-[16px] border border-gray-200 shadow-sm p-6 sm:p-8">
            <div class="relative border-l-2 border-indigo-200 ml-4 sm:ml-6 space-y-6">
                @forelse ($timelines as $item)
                    @php
                        $isToday = $item->status_waktu === 'hari_ini';
                        $isPast = $item->status_waktu === 'terlewat';
                        $circleClass = match ($item->status_waktu) {
                            'hari_ini' => 'bg-indigo-600 ring-4 ring-indigo-100',
                            'terlewat' => 'bg-amber-500',
                            default => 'bg-emerald-500',
                        };
                    @endphp

                    <div class="relative pl-6 sm:pl-8 group">
                        <!-- Bullet indicator on line -->
                        <span class="absolute -left-[9px] top-1.5 w-4 h-4 rounded-full {{ $circleClass }} transition-transform"></span>

                        <div class="bg-gray-50 p-4 rounded-[12px] border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-white hover:shadow-sm transition-all">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $item->offset_hari == 0 ? 'bg-indigo-600 text-white' : 'bg-indigo-100 text-indigo-800' }}">
                                        {{ $item->label }}
                                    </span>

                                    <span class="text-xs font-medium text-gray-500">
                                        📅 {{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('dddd, D MMMM Y') }}
                                    </span>

                                    @if ($item->status_waktu === 'hari_ini')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 animate-pulse">
                                            Hari Ini!
                                        </span>
                                    @elseif ($item->status_waktu === 'terlewat')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                            Terlewat
                                        </span>
                                    @endif
                                </div>

                                <p class="text-sm font-bold text-gray-900 mt-1">
                                    {{ $item->kegiatan }}
                                </p>
                            </div>

                            @if ($isLeader)
                                <div class="flex items-center gap-1.5 shrink-0 self-end sm:self-center">
                                    <button
                                        type="button"
                                        x-on:click="openEdit({{ json_encode($item) }})"
                                        class="px-2.5 py-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded transition-colors"
                                    >
                                        Ubah
                                    </button>

                                    @if ($item->offset_hari != 0)
                                        <form
                                            method="POST"
                                            action="{{ route('acara.timeline.destroy', [$event->id, $item->id]) }}"
                                            class="inline-block"
                                            onsubmit="return confirm('Hapus agenda {{ $item->label }}?')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="px-2.5 py-1 text-xs font-semibold text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors"
                                            >
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="pl-6 text-gray-400 py-6">
                        <p class="text-sm">Belum ada agenda timeline.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Modal Tambah / Edit Timeline (Leader only) -->
        @if ($isLeader)
            <div
                x-show="modalOpen"
                x-cloak
                class="fixed inset-0 z-50 overflow-y-auto"
                aria-labelledby="modal-timeline-title"
                role="dialog"
                aria-modal="true"
            >
                <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                    <div
                        x-show="modalOpen"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 bg-gray-900/60 transition-opacity"
                        x-on:click="modalOpen = false"
                    ></div>

                    <div
                        x-show="modalOpen"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                        class="relative bg-white rounded-[16px] text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-md sm:w-full p-6 space-y-4"
                    >
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <h3 class="text-lg font-bold text-gray-900" x-text="isEdit ? 'Ubah Agenda Timeline' : 'Tambah Agenda Timeline'"></h3>
                            <button type="button" x-on:click="modalOpen = false" class="text-gray-400 hover:text-gray-600">
                                <x-icon name="x" class="w-5 h-5" />
                            </button>
                        </div>

                        <form :action="actionUrl" method="POST" class="space-y-4">
                            @csrf
                            <template x-if="isEdit">
                                <input type="hidden" name="_method" value="PUT">
                            </template>

                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                                    Offset Hari (H-n) <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center gap-2">
                                    <input
                                        type="number"
                                        name="offset_hari"
                                        x-model="offset_hari"
                                        min="0"
                                        required
                                        class="w-24 px-3 py-2 bg-white border border-gray-300 rounded-[8px] text-sm focus:outline-none focus:ring-1 focus:ring-indigo-600"
                                    >
                                    <span class="text-xs text-gray-500 font-medium" x-text="offset_hari == 0 ? 'Hari Pelaksanaan (Hari H)' : offset_hari + ' hari sebelum tanggal acara (H-' + offset_hari + ')'"></span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                                    Uraian Kegiatan / Milestone <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    name="kegiatan"
                                    x-model="kegiatan"
                                    rows="3"
                                    required
                                    placeholder="Contoh: Konfirmasi narasumber dan technical meeting"
                                    class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm focus:outline-none focus:ring-1 focus:ring-indigo-600"
                                ></textarea>
                            </div>

                            <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                                <x-button type="button" variant="secondary" x-on:click="modalOpen = false">
                                    Batal
                                </x-button>
                                <x-button type="submit" variant="primary">
                                    Simpan Agenda
                                </x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>

