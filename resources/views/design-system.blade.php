<x-app-layout>
    <x-slot:breadcrumb>
        Design System & Pustaka Komponen (S00, S00B)
    </x-slot:breadcrumb>

    <div class="space-y-10 pb-16">
        <!-- Header -->
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Design System & Pustaka Komponen EVLAN</h1>
            <p class="mt-2 text-sm text-gray-500">
                Pustaka komponen antarmuka terstandarisasi berdasarkan wireframe halaman 1–2 (S00/S00B) dengan token Tailwind kustom.
            </p>
        </div>

        <!-- 1. Status Badges -->
        <section class="space-y-4">
            <h2 class="text-lg font-bold text-gray-900 border-b pb-2">1. Badge Status (Ikon + Label + Warna)</h2>
            <div class="flex flex-wrap gap-3 items-center">
                <x-status-badge status="belum_dikerjakan" />
                <x-status-badge status="sedang_dikerjakan" />
                <x-status-badge status="sudah_diselesaikan" />
                <x-status-badge status="terlewat" />
                <x-status-badge status="menunggu" />
                <x-status-badge status="terverifikasi" />
                <x-status-badge status="ditolak" />
                <x-status-badge status="aktif" />
                <x-status-badge status="nonaktif" />
                <x-status-badge status="perencanaan" />
                <x-status-badge status="selesai" />
            </div>
        </section>

        <!-- 2. Tombol (Buttons) -->
        <section class="space-y-4">
            <h2 class="text-lg font-bold text-gray-900 border-b pb-2">2. Tombol (Buttons)</h2>
            <div class="flex flex-wrap items-center gap-4">
                <x-button variant="primary">
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Tombol Primary</span>
                </x-button>

                <x-button variant="secondary">
                    <x-icon name="edit" class="w-4 h-4" />
                    <span>Tombol Secondary</span>
                </x-button>

                <x-button variant="danger">
                    <x-icon name="trash" class="w-4 h-4" />
                    <span>Tombol Bahaya</span>
                </x-button>

                <x-button variant="link">
                    <span>Tombol Tautan</span>
                </x-button>

                <x-button variant="primary" :disabled="true">
                    <span>Tombol Dinonaktifkan</span>
                </x-button>

                <x-button variant="primary" size="sm">
                    <span>Kecil (sm)</span>
                </x-button>

                <x-button variant="primary" size="lg">
                    <span>Besar (lg)</span>
                </x-button>
            </div>
        </section>

        <!-- 3. Form Input & Select -->
        <section class="space-y-4">
            <h2 class="text-lg font-bold text-gray-900 border-b pb-2">3. Form Input & Select</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl">
                <x-input
                    name="nama_event"
                    label="Nama Acara"
                    placeholder="Contoh: Seminar Teknologi 2026"
                    :required="true"
                    value="Seminar Teknologi 2026"
                />

                <x-input
                    name="tanggal_acara"
                    type="date"
                    label="Tanggal Acara"
                    :required="true"
                    error="Tanggal tidak boleh sebelum hari ini."
                />

                <x-select name="kategori_id" label="Kategori Acara" :required="true">
                    <option value="">Pilih Kategori...</option>
                    <option value="1" selected>Seminar</option>
                    <option value="2">17 Agustus</option>
                    <option value="3">Ulang Tahun</option>
                </x-select>

                <x-input
                    name="jumlah_peserta"
                    type="number"
                    label="Target Jumlah Peserta"
                    value="300"
                />
            </div>
        </section>

        <!-- 4. Stat Cards & Kartu -->
        <section class="space-y-4">
            <h2 class="text-lg font-bold text-gray-900 border-b pb-2">4. Stat Card & Kartu</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <x-stat-card
                    title="Total Acara Aktif"
                    value="3 Acara"
                    subtext="2 dipimpin, 1 diikuti"
                    icon="calendar"
                    iconBg="bg-indigo-50 text-indigo-600"
                />

                <x-stat-card
                    title="Tugas Saya"
                    value="12 Tugas"
                    subtext="5 selesai, 2 terlewat"
                    icon="check-circle"
                    iconBg="bg-amber-50 text-amber-600"
                />

                <x-stat-card
                    title="Progres Acara"
                    value="42%"
                    subtext="10 dari 24 tugas selesai"
                    icon="clock"
                    iconBg="bg-green-50 text-green-600"
                />

                <x-stat-card
                    title="Estimasi Anggaran"
                    value="Rp 18.450.000"
                    subtext="11 item tercatat"
                    icon="briefcase"
                    iconBg="bg-blue-50 text-blue-600"
                />
            </div>
        </section>

        <!-- 5. Progres & Avatar -->
        <section class="space-y-4">
            <h2 class="text-lg font-bold text-gray-900 border-b pb-2">5. Progress Bar & Avatar</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div class="space-y-4">
                    <x-progress :value="42" :max="100" :showLabel="true" color="indigo" />
                    <x-progress :value="80" :max="100" :showLabel="true" color="green" />
                    <x-progress :value="25" :max="100" :showLabel="true" color="amber" />
                </div>
                <div class="flex items-center gap-4">
                    <x-avatar name="Gus Ega" size="lg" />
                    <x-avatar name="Rina Astuti" size="md" />
                    <x-avatar name="Komang Putra" size="md" />
                    <x-avatar name="Ketut Sari" size="sm" />
                </div>
            </div>
        </section>

        <!-- 6. Tabel & Tabs -->
        <section class="space-y-4">
            <h2 class="text-lg font-bold text-gray-900 border-b pb-2">6. Tabs & Tabel</h2>

            <x-tabs
                :tabs="[
                    'ringkasan' => ['label' => 'Ringkasan', 'count' => null],
                    'checklist' => ['label' => 'Checklist Kebutuhan', 'count' => 14],
                    'timeline' => ['label' => 'Timeline', 'count' => 6],
                    'tugas' => ['label' => 'Tugas', 'count' => 24],
                ]"
                active="checklist"
            />

            <x-table>
                <x-slot:header>
                    <tr>
                        <th class="px-6 py-3">Nama Kebutuhan</th>
                        <th class="px-6 py-3">Kelompok</th>
                        <th class="px-6 py-3">Sumber</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </x-slot:header>

                <tr>
                    <td class="px-6 py-4 font-medium text-gray-900">Sound system & 4 mikrofon</td>
                    <td class="px-6 py-4 text-gray-500">Perlengkapan</td>
                    <td class="px-6 py-4"><x-badge variant="primary">Dari template</x-badge></td>
                    <td class="px-6 py-4"><x-status-badge status="sudah_diselesaikan" /></td>
                </tr>
                <tr>
                    <td class="px-6 py-4 font-medium text-gray-900">Plakat akrilik narasumber</td>
                    <td class="px-6 py-4 text-gray-500">Acara</td>
                    <td class="px-6 py-4"><x-badge variant="neutral">Manual</x-badge></td>
                    <td class="px-6 py-4"><x-status-badge status="belum_dikerjakan" /></td>
                </tr>
            </x-table>
        </section>

        <!-- 7. Empty State -->
        <section class="space-y-4">
            <h2 class="text-lg font-bold text-gray-900 border-b pb-2">7. Empty State</h2>
            <x-empty-state
                title="Belum ada pendaftar"
                description="Peserta yang mendaftar ke acara publik Anda akan muncul di bagian ini untuk dikonfirmasi."
                icon="users"
            >
                <x-button variant="secondary" size="sm">
                    <span>Bagikan Tautan Pendaftaran</span>
                </x-button>
            </x-empty-state>
        </section>

        <!-- 8. Modal & Toast Triggers -->
        <section class="space-y-4">
            <h2 class="text-lg font-bold text-gray-900 border-b pb-2">8. Modal & Notifikasi Toast Interaktif</h2>
            <div class="flex items-center gap-4">
                <x-button
                    variant="primary"
                    x-data
                    x-on:click="$dispatch('open-modal', 'demo-modal')"
                >
                    Buka Modal Demo
                </x-button>

                <x-button
                    variant="secondary"
                    x-data
                    x-on:click="$dispatch('toast', { type: 'success', message: 'Perubahan berhasil disimpan!' })"
                >
                    Picu Toast Sukses
                </x-button>

                <x-button
                    variant="danger"
                    x-data
                    x-on:click="$dispatch('toast', { type: 'error', message: 'Gagal memperbarui status tugas.' })"
                >
                    Picu Toast Galat
                </x-button>
            </div>

            <x-modal name="demo-modal" title="Modal Konfirmasi Tindakan">
                <p class="text-sm text-gray-600 mb-6">
                    Ini adalah contoh komponen modal EVLAN dengan radius 16px, tombol escape untuk menutup, dan trap focus otomatis.
                </p>
                <div class="flex justify-end gap-3">
                    <x-button variant="secondary" x-data x-on:click="$dispatch('close-modal', 'demo-modal')">
                        Batal
                    </x-button>
                    <x-button variant="primary" x-data x-on:click="$dispatch('close-modal', 'demo-modal'); $dispatch('toast', { type: 'success', message: 'Tindakan berhasil dikonfirmasi!' })">
                        Konfirmasi
                    </x-button>
                </div>
            </x-modal>
        </section>
    </div>
</x-app-layout>

