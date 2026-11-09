<x-layouts.admin :title="$isEdit ? 'Edit Template Master' : 'Buat Template Master'">
    @php
        $kebutuhanInit = $template->kebutuhan ? $template->kebutuhan->map(fn($i) => ['nama_item' => $i->nama_item, 'kelompok' => $i->kelompok ?? ''])->values()->toArray() : [];
        $timelineInit = $template->timeline ? $template->timeline->map(fn($i) => ['nama_item' => $i->nama_item, 'offset_hari' => $i->offset_hari ?? 0])->values()->toArray() : [];
        $divisiInit = $template->divisi ? $template->divisi->map(fn($i) => ['nama_item' => $i->nama_item])->values()->toArray() : [];
        $jobdeskInit = $template->jobdesk ? $template->jobdesk->map(fn($i) => ['nama_item' => $i->nama_item, 'kelompok' => $i->kelompok ?? ''])->values()->toArray() : [];
    @endphp

    <div
        x-data="{
            activeTab: 'kebutuhan',
            kebutuhan: {{ json_encode($kebutuhanInit) }},
            timeline: {{ json_encode($timelineInit) }},
            divisi: {{ json_encode($divisiInit) }},
            jobdesk: {{ json_encode($jobdeskInit) }},

            addKebutuhan() {
                this.kebutuhan.push({ nama_item: '', kelompok: 'Perlengkapan' });
            },
            removeKebutuhan(index) {
                this.kebutuhan.splice(index, 1);
            },

            addTimeline() {
                this.timeline.push({ nama_item: '', offset_hari: 30 });
            },
            removeTimeline(index) {
                this.timeline.splice(index, 1);
            },

            addDivisi() {
                this.divisi.push({ nama_item: '' });
            },
            removeDivisi(index) {
                this.divisi.splice(index, 1);
            },

            addJobdesk() {
                let defaultDivisi = this.divisi.length > 0 ? this.divisi[0].nama_item : 'Acara';
                this.jobdesk.push({ nama_item: '', kelompok: defaultDivisi });
            },
            removeJobdesk(index) {
                this.jobdesk.splice(index, 1);
            }
        }"
        class="space-y-6 pb-24"
    >
        <form
            method="POST"
            action="{{ $isEdit ? route('admin.template.update', $template->id) : route('admin.template.store') }}"
            class="space-y-6"
        >
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <!-- Header Title -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                        {{ $isEdit ? 'Ubah Template: ' . $template->nama_template : 'Buat Template Master Baru' }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Kelola paket otomatisasi 4 komponen utama template yang akan disalin ke acara baru.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.template.index') }}" class="px-4 py-2 border border-gray-300 rounded-[8px] text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                        Kembali
                    </a>
                    <x-button type="submit" variant="primary">
                        Simpan Template
                    </x-button>
                </div>
            </div>

            <!-- Card Identitas Template -->
            <div class="bg-white p-6 rounded-[12px] border border-gray-200 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-2">Informasi Dasar Template</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                            Nama Template <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="nama_template"
                            value="{{ old('nama_template', $template->nama_template) }}"
                            placeholder="Contoh: Seminar Umum Nasional"
                            required
                            class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-indigo-600"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1.5">
                            Kategori Acara <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="category_id"
                            required
                            class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-[8px] text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-indigo-600"
                        >
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $cat)
                                <option
                                    value="{{ $cat->id }}"
                                    {{ old('category_id', $template->category_id) == $cat->id ? 'selected' : '' }}
                                >
                                    {{ $cat->ikon ?? '📁' }} {{ $cat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pt-2">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            name="is_aktif"
                            value="1"
                            {{ old('is_aktif', $template->is_aktif) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4"
                        >
                        <span class="text-sm font-medium text-gray-700">Jadikan template aktif utama untuk kategori ini (D-07)</span>
                    </label>
                </div>
            </div>

            <!-- Tabbed Component Editor -->
            <div class="bg-white rounded-[12px] border border-gray-200 shadow-sm overflow-hidden">
                <!-- Tab Headers -->
                <div class="flex border-b border-gray-200 bg-gray-50 overflow-x-auto">
                    <button
                        type="button"
                        x-on:click="activeTab = 'kebutuhan'"
                        :class="activeTab === 'kebutuhan' ? 'border-indigo-600 text-indigo-700 bg-white font-bold' : 'border-transparent text-gray-600 hover:text-gray-900 font-medium'"
                        class="px-5 py-3.5 border-b-2 text-sm flex items-center gap-2 transition-colors whitespace-nowrap"
                    >
                        <span>1. Kebutuhan (Checklist)</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700" x-text="kebutuhan.length"></span>
                    </button>

                    <button
                        type="button"
                        x-on:click="activeTab = 'timeline'"
                        :class="activeTab === 'timeline' ? 'border-indigo-600 text-indigo-700 bg-white font-bold' : 'border-transparent text-gray-600 hover:text-gray-900 font-medium'"
                        class="px-5 py-3.5 border-b-2 text-sm flex items-center gap-2 transition-colors whitespace-nowrap"
                    >
                        <span>2. Timeline (H-n)</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700" x-text="timeline.length"></span>
                    </button>

                    <button
                        type="button"
                        x-on:click="activeTab = 'divisi'"
                        :class="activeTab === 'divisi' ? 'border-indigo-600 text-indigo-700 bg-white font-bold' : 'border-transparent text-gray-600 hover:text-gray-900 font-medium'"
                        class="px-5 py-3.5 border-b-2 text-sm flex items-center gap-2 transition-colors whitespace-nowrap"
                    >
                        <span>3. Divisi Kepanitiaan</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700" x-text="divisi.length"></span>
                    </button>

                    <button
                        type="button"
                        x-on:click="activeTab = 'jobdesk'"
                        :class="activeTab === 'jobdesk' ? 'border-indigo-600 text-indigo-700 bg-white font-bold' : 'border-transparent text-gray-600 hover:text-gray-900 font-medium'"
                        class="px-5 py-3.5 border-b-2 text-sm flex items-center gap-2 transition-colors whitespace-nowrap"
                    >
                        <span>4. Rekomendasi Jobdesk</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700" x-text="jobdesk.length"></span>
                    </button>
                </div>

                <div class="p-6">
                    <!-- Tab 1: Kebutuhan -->
                    <div x-show="activeTab === 'kebutuhan'" class="space-y-4">
                        <div class="flex items-center justify-between pb-2">
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Daftar Kebutuhan / Checklist Logistik</h3>
                                <p class="text-xs text-gray-500">Kebutuhan logistik yang akan dibuatkan checklist pada acara baru.</p>
                            </div>
                            <x-button type="button" variant="secondary" size="sm" x-on:click="addKebutuhan()">
                                <x-icon name="plus" class="w-3.5 h-3.5 mr-1" />
                                Tambah Item
                            </x-button>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(item, index) in kebutuhan" :key="index">
                                <div class="flex items-center gap-3 p-2.5 bg-gray-50 rounded-[8px] border border-gray-200">
                                    <span class="text-xs font-bold text-gray-400 w-6 text-center" x-text="index + 1"></span>
                                    <input
                                        type="text"
                                        :name="'kebutuhan[' + index + '][nama_item]'"
                                        x-model="item.nama_item"
                                        placeholder="Nama kebutuhan (misal: Sound system & 4 mikrofon)"
                                        required
                                        class="flex-1 px-3 py-1.5 bg-white border border-gray-300 rounded-[6px] text-sm focus:outline-none focus:ring-1 focus:ring-indigo-600"
                                    >
                                    <input
                                        type="text"
                                        :name="'kebutuhan[' + index + '][kelompok]'"
                                        x-model="item.kelompok"
                                        placeholder="Kelompok (Perlengkapan/Konsumsi/dll)"
                                        class="w-48 px-3 py-1.5 bg-white border border-gray-300 rounded-[6px] text-sm focus:outline-none focus:ring-1 focus:ring-indigo-600"
                                    >
                                    <button
                                        type="button"
                                        x-on:click="removeKebutuhan(index)"
                                        class="p-1.5 text-gray-400 hover:text-red-600 rounded transition-colors"
                                        title="Hapus baris"
                                    >
                                        <x-icon name="x" class="w-4 h-4" />
                                    </button>
                                </div>
                            </template>
                            <template x-if="kebutuhan.length === 0">
                                <div class="py-8 text-center text-gray-400 border border-dashed border-gray-200 rounded-[8px]">
                                    <p class="text-sm">Belum ada item kebutuhan.</p>
                                    <button type="button" x-on:click="addKebutuhan()" class="mt-2 text-xs text-indigo-600 font-semibold hover:underline">
                                        + Tambah baris pertama
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Tab 2: Timeline -->
                    <div x-show="activeTab === 'timeline'" class="space-y-4" style="display: none;">
                        <div class="flex items-center justify-between pb-2">
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Agenda Timeline Countdown (H-n)</h3>
                                <p class="text-xs text-gray-500">Agenda waktu mundur otomatis dihitung dari tanggal acara.</p>
                            </div>
                            <x-button type="button" variant="secondary" size="sm" x-on:click="addTimeline()">
                                <x-icon name="plus" class="w-3.5 h-3.5 mr-1" />
                                Tambah Agenda
                            </x-button>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(item, index) in timeline" :key="index">
                                <div class="flex items-center gap-3 p-2.5 bg-gray-50 rounded-[8px] border border-gray-200">
                                    <span class="text-xs font-bold text-gray-400 w-6 text-center" x-text="index + 1"></span>
                                    <div class="flex items-center gap-1.5 w-36">
                                        <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2 py-1.5 rounded-[6px]">
                                            <span x-text="item.offset_hari == 0 ? 'Hari H' : 'H-' + item.offset_hari"></span>
                                        </span>
                                        <input
                                            type="number"
                                            :name="'timeline[' + index + '][offset_hari]'"
                                            x-model="item.offset_hari"
                                            min="0"
                                            placeholder="Offset"
                                            required
                                            class="w-16 px-2 py-1.5 bg-white border border-gray-300 rounded-[6px] text-xs text-center focus:outline-none focus:ring-1 focus:ring-indigo-600"
                                            title="Jumlah hari sebelum acara (0 = Hari H)"
                                        >
                                    </div>
                                    <input
                                        type="text"
                                        :name="'timeline[' + index + '][nama_item]'"
                                        x-model="item.nama_item"
                                        placeholder="Uraian agenda (misal: Konfirmasi narasumber & moderator)"
                                        required
                                        class="flex-1 px-3 py-1.5 bg-white border border-gray-300 rounded-[6px] text-sm focus:outline-none focus:ring-1 focus:ring-indigo-600"
                                    >
                                    <button
                                        type="button"
                                        x-on:click="removeTimeline(index)"
                                        class="p-1.5 text-gray-400 hover:text-red-600 rounded transition-colors"
                                        title="Hapus agenda"
                                    >
                                        <x-icon name="x" class="w-4 h-4" />
                                    </button>
                                </div>
                            </template>
                            <template x-if="timeline.length === 0">
                                <div class="py-8 text-center text-gray-400 border border-dashed border-gray-200 rounded-[8px]">
                                    <p class="text-sm">Belum ada agenda timeline.</p>
                                    <button type="button" x-on:click="addTimeline()" class="mt-2 text-xs text-indigo-600 font-semibold hover:underline">
                                        + Tambah baris pertama
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Tab 3: Divisi -->
                    <div x-show="activeTab === 'divisi'" class="space-y-4" style="display: none;">
                        <div class="flex items-center justify-between pb-2">
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Struktur Divisi Kepanitiaan</h3>
                                <p class="text-xs text-gray-500">Divisi default yang akan otomatis dibuat pada kepanitiaan acara.</p>
                            </div>
                            <x-button type="button" variant="secondary" size="sm" x-on:click="addDivisi()">
                                <x-icon name="plus" class="w-3.5 h-3.5 mr-1" />
                                Tambah Divisi
                            </x-button>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(item, index) in divisi" :key="index">
                                <div class="flex items-center gap-3 p-2.5 bg-gray-50 rounded-[8px] border border-gray-200">
                                    <span class="text-xs font-bold text-gray-400 w-6 text-center" x-text="index + 1"></span>
                                    <input
                                        type="text"
                                        :name="'divisi[' + index + '][nama_item]'"
                                        x-model="item.nama_item"
                                        placeholder="Nama divisi (misal: Acara, Konsumsi, Publikasi, Perlengkapan, Dokumentasi)"
                                        required
                                        class="flex-1 px-3 py-1.5 bg-white border border-gray-300 rounded-[6px] text-sm focus:outline-none focus:ring-1 focus:ring-indigo-600"
                                    >
                                    <button
                                        type="button"
                                        x-on:click="removeDivisi(index)"
                                        class="p-1.5 text-gray-400 hover:text-red-600 rounded transition-colors"
                                        title="Hapus divisi"
                                    >
                                        <x-icon name="x" class="w-4 h-4" />
                                    </button>
                                </div>
                            </template>
                            <template x-if="divisi.length === 0">
                                <div class="py-8 text-center text-gray-400 border border-dashed border-gray-200 rounded-[8px]">
                                    <p class="text-sm">Belum ada struktur divisi.</p>
                                    <button type="button" x-on:click="addDivisi()" class="mt-2 text-xs text-indigo-600 font-semibold hover:underline">
                                        + Tambah baris pertama
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Tab 4: Rekomendasi Jobdesk -->
                    <div x-show="activeTab === 'jobdesk'" class="space-y-4" style="display: none;">
                        <div class="flex items-center justify-between pb-2">
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Rekomendasi Rincian Jobdesk Tim</h3>
                                <p class="text-xs text-gray-500">Rekomendasi uraian tugas per divisi untuk memandu pembagian kerja.</p>
                            </div>
                            <x-button type="button" variant="secondary" size="sm" x-on:click="addJobdesk()">
                                <x-icon name="plus" class="w-3.5 h-3.5 mr-1" />
                                Tambah Jobdesk
                            </x-button>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(item, index) in jobdesk" :key="index">
                                <div class="flex items-center gap-3 p-2.5 bg-gray-50 rounded-[8px] border border-gray-200">
                                    <span class="text-xs font-bold text-gray-400 w-6 text-center" x-text="index + 1"></span>
                                    <input
                                        type="text"
                                        :name="'jobdesk[' + index + '][nama_item]'"
                                        x-model="item.nama_item"
                                        placeholder="Uraian tugas (misal: Menyusun daftar narasumber & moderator)"
                                        required
                                        class="flex-1 px-3 py-1.5 bg-white border border-gray-300 rounded-[6px] text-sm focus:outline-none focus:ring-1 focus:ring-indigo-600"
                                    >
                                    <input
                                        type="text"
                                        :name="'jobdesk[' + index + '][kelompok]'"
                                        x-model="item.kelompok"
                                        placeholder="Divisi terkait (misal: Acara)"
                                        class="w-48 px-3 py-1.5 bg-white border border-gray-300 rounded-[6px] text-sm focus:outline-none focus:ring-1 focus:ring-indigo-600"
                                    >
                                    <button
                                        type="button"
                                        x-on:click="removeJobdesk(index)"
                                        class="p-1.5 text-gray-400 hover:text-red-600 rounded transition-colors"
                                        title="Hapus jobdesk"
                                    >
                                        <x-icon name="x" class="w-4 h-4" />
                                    </button>
                                </div>
                            </template>
                            <template x-if="jobdesk.length === 0">
                                <div class="py-8 text-center text-gray-400 border border-dashed border-gray-200 rounded-[8px]">
                                    <p class="text-sm">Belum ada rekomendasi jobdesk.</p>
                                    <button type="button" x-on:click="addJobdesk()" class="mt-2 text-xs text-indigo-600 font-semibold hover:underline">
                                        + Tambah baris pertama
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Bottom Action Bar -->
            <div class="fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur border-t border-gray-200 py-3.5 px-6 z-30 shadow-lg">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <p class="text-xs text-gray-500 hidden sm:block">
                        💡 Catatan D-08: Perubahan template tidak mengubah acara lama yang sudah dibuat.
                    </p>
                    <div class="flex items-center gap-3 ml-auto">
                        <a href="{{ route('admin.template.index') }}" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
                            Batal
                        </a>
                        <x-button type="submit" variant="primary">
                            Simpan Seluruh Template
                        </x-button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>

