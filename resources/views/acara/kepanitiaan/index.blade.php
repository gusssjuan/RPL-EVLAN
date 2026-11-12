<x-layouts.app :title="'Kepanitiaan - ' . $event->nama_event">
    <div class="space-y-6">
        <x-acara.partials.header :event="$event" activeTab="kepanitiaan" />

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[12px] text-sm font-medium text-emerald-800 flex items-center gap-2.5">
                <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-[12px] text-xs font-medium text-red-700 space-y-1">
                <div class="flex items-center gap-2 font-bold text-red-800">
                    <x-icon name="alert-circle" class="w-4 h-4" />
                    <span>Terjadi kesalahan:</span>
                </div>
                <ul class="list-disc list-inside pl-2 text-red-600 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Rekomendasi Template (D-10) -->
        @if ($isLeader && $templateDivisions->count() > 0)
            <div class="bg-indigo-50/60 border border-indigo-200 rounded-[12px] p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2.5 text-xs">
                    <x-icon name="lightbulb" class="w-5 h-5 text-indigo-600 shrink-0" />
                    <div>
                        <p class="font-bold text-indigo-900">Rekomendasi template: {{ $rekomendasiDitambahkan }}/{{ $templateDivisions->count() }} divisi ditambahkan</p>
                        <p class="text-indigo-700">Divisi rekomendasi dari template kategori: {{ $templateDivisions->pluck('nama_item')->join(', ') }}</p>
                    </div>
                </div>

                @if ($rekomendasiDitambahkan < $templateDivisions->count())
                    <form action="{{ route('acara.kepanitiaan.rekomendasi', $event->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-xs font-semibold shadow-sm transition-colors whitespace-nowrap">
                            <x-icon name="plus" class="w-3.5 h-3.5" />
                            <span>Tambahkan semua rekomendasi</span>
                        </button>
                    </form>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-100 text-emerald-800 rounded-[8px] text-xs font-bold whitespace-nowrap">
                        <x-icon name="check-circle" class="w-3.5 h-3.5" />
                        <span>Semua rekomendasi ditambahkan</span>
                    </span>
                @endif
            </div>
        @endif

        <!-- Tombol Tambah Divisi & Anggota (Leader Only) -->
        @if ($isLeader)
            <div class="flex flex-wrap gap-2" x-data="{ showDivModal: false, showMemberModal: false }">
                <button @click="showDivModal = true" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-xs font-semibold shadow-sm transition-colors">
                    <x-icon name="plus" class="w-3.5 h-3.5" />
                    <span>+ Divisi</span>
                </button>

                @if ($event->divisions->count() > 0)
                    <button @click="showMemberModal = true" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-[8px] text-xs font-semibold shadow-sm transition-colors">
                        <x-icon name="user-plus" class="w-3.5 h-3.5" />
                        <span>+ Anggota</span>
                    </button>
                @endif

                <!-- Modal Tambah Divisi -->
                <div x-show="showDivModal" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" @click.self="showDivModal = false" @keydown.escape.window="showDivModal = false">
                    <div class="bg-white rounded-[16px] p-6 w-full max-w-md shadow-lg space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-bold text-gray-900">Tambah Divisi Baru</h3>
                            <button @click="showDivModal = false" class="text-gray-400 hover:text-gray-600"><x-icon name="x" class="w-5 h-5" /></button>
                        </div>
                        <form action="{{ route('acara.kepanitiaan.divisi.store', $event->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="nama_divisi" class="block text-xs font-semibold text-gray-700 mb-1">Nama Divisi <span class="text-red-500">*</span></label>
                                <input type="text" id="nama_divisi" name="nama_divisi" required placeholder="Contoh: Acara, Konsumsi, Publikasi" class="w-full text-sm rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="showDivModal = false" class="px-4 py-2 border border-gray-300 rounded-[8px] text-xs font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-xs font-semibold shadow-sm">Simpan Divisi</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal Tambah Anggota -->
                <div x-show="showMemberModal" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" @click.self="showMemberModal = false" @keydown.escape.window="showMemberModal = false">
                    <div class="bg-white rounded-[16px] p-6 w-full max-w-md shadow-lg space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-bold text-gray-900">Tambah Anggota Panitia</h3>
                            <button @click="showMemberModal = false" class="text-gray-400 hover:text-gray-600"><x-icon name="x" class="w-5 h-5" /></button>
                        </div>
                        <form action="{{ route('acara.kepanitiaan.anggota.store', $event->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">Email Akun Pengguna <span class="text-red-500">*</span></label>
                                <input type="email" id="email" name="email" required placeholder="contoh@email.com" class="w-full text-sm rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                                <p class="text-[11px] text-gray-500 mt-0.5">Harus email akun terdaftar dengan peran Pengguna (D-15)</p>
                            </div>
                            <div>
                                <label for="division_id" class="block text-xs font-semibold text-gray-700 mb-1">Divisi <span class="text-red-500">*</span></label>
                                <select id="division_id" name="division_id" required class="w-full text-sm rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Pilih divisi</option>
                                    @foreach ($event->divisions as $div)
                                        <option value="{{ $div->id }}">{{ $div->nama_divisi }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="peran" class="block text-xs font-semibold text-gray-700 mb-1">Peran dalam Divisi <span class="text-red-500">*</span></label>
                                <select id="peran" name="peran" required class="w-full text-sm rounded-[8px] border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="anggota">Anggota</option>
                                    <option value="koordinator">Koordinator</option>
                                </select>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="showMemberModal = false" class="px-4 py-2 border border-gray-300 rounded-[8px] text-xs font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-xs font-semibold shadow-sm">Tambah Anggota</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Daftar Divisi & Anggota per Divisi -->
        @if ($event->divisions->isEmpty())
            <div class="bg-white rounded-[16px] border border-gray-200 p-10 text-center shadow-sm">
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <x-icon name="users" class="w-6 h-6" />
                </div>
                <h3 class="text-base font-bold text-gray-900">Belum Ada Divisi Kepanitiaan</h3>
                <p class="text-xs text-gray-500 max-w-sm mx-auto mt-1">Mulai atur struktur kepanitiaan acara Anda dengan menambahkan divisi dan mengundang anggota panitia.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach ($event->divisions as $division)
                    @php
                        $divMembers = $division->members->load('user');
                        $divTasks = $division->tasks;
                        $divTaskSelesai = $divTasks->where('status.value', 'sudah_diselesaikan')->count();
                        $divProgres = $divTasks->count() > 0 ? round(($divTaskSelesai / $divTasks->count()) * 100) : 0;
                    @endphp
                    <div class="bg-white rounded-[16px] border border-gray-200 shadow-sm overflow-hidden">
                        <!-- Division Header -->
                        <div class="p-5 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-bold text-gray-900">{{ $division->nama_divisi }}</h3>
                                @if ($isLeader)
                                    <form action="{{ route('acara.kepanitiaan.divisi.destroy', [$event->id, $division->id]) }}" method="POST" onsubmit="return confirm('Hapus divisi {{ $division->nama_divisi }}? Anggota akan dilepas dari divisi ini.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors" title="Hapus divisi">
                                            <x-icon name="trash-2" class="w-4 h-4" />
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="text-[11px] font-semibold text-gray-500">{{ $divMembers->count() }} Anggota</span>
                                <span class="text-[11px] font-semibold text-gray-500">{{ $divTasks->count() }} Tugas</span>
                                <span class="text-[11px] font-bold text-indigo-600">{{ $divProgres }}%</span>
                            </div>
                            @if ($divTasks->count() > 0)
                                <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2 overflow-hidden">
                                    <div class="bg-indigo-600 h-1.5 rounded-full transition-all" style="width: {{ $divProgres }}%"></div>
                                </div>
                            @endif
                        </div>

                        <!-- Members List -->
                        <div class="p-4 space-y-2.5">
                            @forelse ($divMembers as $em)
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-[11px] font-bold shrink-0">
                                            {{ strtoupper(substr($em->user->nama, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $em->user->nama }}</p>
                                            <p class="text-[10px] text-gray-500">{{ $em->peran->label() }}</p>
                                        </div>
                                    </div>

                                    @if ($isLeader)
                                        <form action="{{ route('acara.kepanitiaan.anggota.destroy', [$event->id, $em->id]) }}" method="POST" onsubmit="return confirm('Keluarkan {{ $em->user->nama }} dari kepanitiaan?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors" title="Keluarkan anggota">
                                                <x-icon name="x" class="w-3.5 h-3.5" />
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @empty
                                <p class="text-[11px] text-gray-400 text-center py-2">Belum ada anggota di divisi ini.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>

