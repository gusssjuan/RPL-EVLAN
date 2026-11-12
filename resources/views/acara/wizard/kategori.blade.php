<x-layouts.app title="Pilih Kategori - Buat Acara">
    <div
        x-data="{
            selectedId: {{ $selectedCategoryId ?? 'null' }},
            categories: {{ json_encode($categories->map(function ($c) {
                $tmpl = $c->activeTemplate;
                return [
                    'id' => $c->id,
                    'nama' => $c->nama_kategori,
                    'ikon' => config('ikon.kategori')[$c->nama_kategori] ?? (is_string($c->ikon) && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $c->ikon) ? $c->ikon : config('ikon.kategori_default', 'tag')),
                    'deskripsi' => $c->deskripsi,
                    'has_template' => (bool)$tmpl,
                    'template_nama' => $tmpl?->nama_template,
                    'kebutuhan_count' => $tmpl ? $tmpl->kebutuhan->count() : 0,
                    'timeline_count' => $tmpl ? $tmpl->timeline->count() : 0,
                    'divisi_count' => $tmpl ? $tmpl->divisi->count() : 0,
                    'jobdesk_count' => $tmpl ? $tmpl->jobdesk->count() : 0,
                ];
            })) }},
            get current() {
                return this.categories.find(c => c.id == this.selectedId) || this.categories[0];
            }
        }"
        class="max-w-6xl mx-auto space-y-8"
    >
        <!-- Wizard Header & Stepper -->
        <div class="space-y-4">
            <div>
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Langkah 1 dari 3</span>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 mt-1">
                    Pilih Kategori Acara
                </h1>
                <p class="text-sm text-gray-500">
                    Setiap kategori dilengkapi template otomatis untuk checklist kebutuhan dan timeline H-n persiapan.
                </p>
            </div>

            <!-- Stepper Bar -->
            <div class="grid grid-cols-3 gap-2 sm:gap-4 pt-2">
                <div class="p-3 bg-indigo-50 border-2 border-indigo-600 rounded-[10px] flex items-center gap-3">
                    <span class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0">1</span>
                    <div class="min-w-0 hidden sm:block">
                        <p class="text-xs font-bold text-indigo-900 truncate">Kategori</p>
                        <p class="text-[10px] text-indigo-600 truncate">Pilih jenis acara</p>
                    </div>
                </div>

                <div class="p-3 bg-white border border-gray-200 rounded-[10px] flex items-center gap-3 opacity-60">
                    <span class="w-7 h-7 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-xs shrink-0">2</span>
                    <div class="min-w-0 hidden sm:block">
                        <p class="text-xs font-bold text-gray-700 truncate">Detail Acara</p>
                        <p class="text-[10px] text-gray-400 truncate">Jadwal & lokasi</p>
                    </div>
                </div>

                <div class="p-3 bg-white border border-gray-200 rounded-[10px] flex items-center gap-3 opacity-60">
                    <span class="w-7 h-7 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-xs shrink-0">3</span>
                    <div class="min-w-0 hidden sm:block">
                        <p class="text-xs font-bold text-gray-700 truncate">Rancangan</p>
                        <p class="text-[10px] text-gray-400 truncate">Pratinjau otomatisasi</p>
                    </div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-[12px] text-sm text-red-800">
                <p class="font-bold flex items-center gap-2">
                    <x-icon name="alert-triangle" class="w-4 h-4 shrink-0" />
                    <span>Silakan pilih salah satu kategori untuk melanjutkan.</span>
                </p>
            </div>
        @endif

        <form method="POST" action="{{ route('acara.buat.kategori.store') }}">
            @csrf
            <input type="hidden" name="category_id" :value="selectedId">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Kolom Kiri: Pilihan Kategori -->
                <div class="lg:col-span-7 space-y-4">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Kategori Tersedia</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($categories as $cat)
                            @php
                                $hasTmpl = (bool)$cat->activeTemplate;
                            @endphp
                            <div
                                x-on:click="selectedId = {{ $cat->id }}"
                                :class="selectedId == {{ $cat->id }} ? 'border-indigo-600 ring-2 ring-indigo-600/20 bg-indigo-50/20' : 'border-gray-200 hover:border-gray-300 bg-white'"
                                class="p-5 rounded-[12px] border cursor-pointer transition-all flex flex-col justify-between h-44 shadow-sm"
                            >
                                <div>
                                    <div class="flex items-center justify-between">
                                        <x-category-icon :kategori="$cat" class="w-8 h-8" />
                                        <template x-if="selectedId == {{ $cat->id }}">
                                            <span class="w-5 h-5 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-bold"><x-icon name="check" class="w-3 h-3" /></span>
                                        </template>
                                    </div>
                                    <h3 class="text-base font-bold text-gray-900 mt-3">{{ $cat->nama_kategori }}</h3>
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $cat->deskripsi ?? 'Perencanaan komprehensif untuk acara ' . $cat->nama_kategori }}</p>
                                </div>

                                <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-[11px]">
                                    @if ($hasTmpl)
                                        <span class="text-emerald-700 font-semibold flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Template Siap
                                        </span>
                                    @else
                                        <span class="text-amber-700 font-medium">Template Dasar</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Kolom Kanan: Pratinjau Template Aktif (S10 Panel Kanan) -->
                <div class="lg:col-span-5 bg-white p-6 rounded-[16px] border border-gray-200 shadow-sm space-y-5 sticky top-6">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                        <template x-if="current?.ikon === 'mic'"><x-icon name="mic" class="w-8 h-8" /></template>
                        <template x-if="current?.ikon === 'flag'"><x-icon name="flag" class="w-8 h-8" /></template>
                        <template x-if="current?.ikon === 'cake'"><x-icon name="cake" class="w-8 h-8" /></template>
                        <template x-if="current?.ikon === 'heart'"><x-icon name="heart" class="w-8 h-8" /></template>
                        <template x-if="current?.ikon === 'users'"><x-icon name="users" class="w-8 h-8" /></template>
                        <template x-if="!['mic', 'flag', 'cake', 'heart', 'users'].includes(current?.ikon)"><x-icon name="tag" class="w-8 h-8" /></template>
                        <div>
                            <span class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">Pratinjau Template</span>
                            <h3 class="text-lg font-bold text-gray-900" x-text="current?.template_nama || current?.nama"></h3>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <p class="text-xs text-gray-500">
                            Template ini otomatis mengonfigurasi komponen acara Anda:
                        </p>

                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="p-3 bg-indigo-50/50 border border-indigo-100 rounded-[8px]">
                                <p class="text-gray-500 text-[11px]">Kebutuhan Logistik</p>
                                <p class="text-base font-bold text-indigo-700 mt-0.5">
                                    <span x-text="current?.kebutuhan_count || 0"></span> Item
                                </p>
                            </div>

                            <div class="p-3 bg-emerald-50/50 border border-emerald-100 rounded-[8px]">
                                <p class="text-gray-500 text-[11px]">Timeline Persiapan</p>
                                <p class="text-base font-bold text-emerald-700 mt-0.5">
                                    <span x-text="current?.timeline_count || 0"></span> Agenda
                                </p>
                            </div>

                            <div class="p-3 bg-amber-50/50 border border-amber-100 rounded-[8px]">
                                <p class="text-gray-500 text-[11px]">Rekomendasi Divisi</p>
                                <p class="text-base font-bold text-amber-700 mt-0.5">
                                    <span x-text="current?.divisi_count || 0"></span> Divisi
                                </p>
                            </div>

                            <div class="p-3 bg-purple-50/50 border border-purple-100 rounded-[8px]">
                                <p class="text-gray-500 text-[11px]">Rekomendasi Jobdesk</p>
                                <p class="text-base font-bold text-purple-700 mt-0.5">
                                    <span x-text="current?.jobdesk_count || 0"></span> Tugas
                                </p>
                            </div>
                        </div>

                        <div class="p-3 bg-gray-50 rounded-[8px] text-xs text-gray-600 flex items-start gap-2 mt-4">
                            <x-icon name="check-circle" class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                            <span>Timeline H-n akan otomatis disinkronkan dengan tanggal acara pada langkah berikutnya.</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end">
                        <x-button type="submit" variant="primary" class="w-full py-2.5">
                            Lanjut ke Detail Acara
                            <x-icon name="arrow-right" class="w-4 h-4" />
                        </x-button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-layouts.app>

