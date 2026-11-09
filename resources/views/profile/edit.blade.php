<x-layouts.app title="Profil dan Pengaturan">
    <div class="max-w-4xl mx-auto space-y-8">
        <!-- Header Profil -->
        <div class="bg-white p-6 rounded-[12px] border border-gray-200 shadow-sm flex flex-col sm:flex-row items-center sm:items-start gap-5">
            <x-avatar :name="$user->nama" size="lg" class="w-16 h-16 text-xl" />
            <div class="flex-1 text-center sm:text-left">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $user->nama }}</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                        {{ ucfirst($user->role?->nama ?? 'Pengguna') }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-1">{{ $user->email }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $user->no_telepon ?? 'Nomor telepon belum diatur' }}</p>
            </div>
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[12px] text-sm font-medium text-emerald-800 flex items-center gap-2.5">
                <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- 1. Form Informasi Profil -->
        <div class="bg-white p-6 rounded-[12px] border border-gray-200 shadow-sm space-y-4">
            <div class="border-b border-gray-100 pb-3">
                <h2 class="text-lg font-bold text-gray-900">Informasi Pribadi</h2>
                <p class="text-xs text-gray-500">Perbarui informasi kontak dan data akun utama Anda.</p>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <x-input
                    name="nama"
                    label="Nama Lengkap"
                    value="{{ old('nama', $user->nama) }}"
                    required
                />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input
                        name="email"
                        type="email"
                        label="Alamat Email"
                        value="{{ old('email', $user->email) }}"
                        required
                    />

                    <x-input
                        name="no_telepon"
                        type="text"
                        label="Nomor Telepon / WhatsApp"
                        value="{{ old('no_telepon', $user->no_telepon) }}"
                    />
                </div>

                <div class="flex justify-end pt-2">
                    <x-button type="submit" variant="primary">
                        Simpan Perubahan
                    </x-button>
                </div>
            </form>
        </div>

        <!-- 2. Form Ganti Kata Sandi -->
        <div class="bg-white p-6 rounded-[12px] border border-gray-200 shadow-sm space-y-4">
            <div class="border-b border-gray-100 pb-3">
                <h2 class="text-lg font-bold text-gray-900">Ganti Kata Sandi</h2>
                <p class="text-xs text-gray-500">Pastikan akun Anda menggunakan kata sandi yang kuat dan aman.</p>
            </div>

            <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <x-input
                    name="current_password"
                    type="password"
                    label="Kata Sandi Saat Ini"
                    placeholder="••••••••"
                    required
                />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input
                        name="password"
                        type="password"
                        label="Kata Sandi Baru"
                        placeholder="Min. 8 karakter"
                        required
                    />

                    <x-input
                        name="password_confirmation"
                        type="password"
                        label="Konfirmasi Sandi Baru"
                        placeholder="Ulangi sandi baru"
                        required
                    />
                </div>

                <div class="flex justify-end pt-2">
                    <x-button type="submit" variant="primary">
                        Perbarui Kata Sandi
                    </x-button>
                </div>
            </form>
        </div>

        <!-- 3. Form Preferensi Notifikasi -->
        @php
            $prefs = $user->preferensi_notifikasi ?? [];
        @endphp
        <div class="bg-white p-6 rounded-[12px] border border-gray-200 shadow-sm space-y-4">
            <div class="border-b border-gray-100 pb-3">
                <h2 class="text-lg font-bold text-gray-900">Preferensi Notifikasi</h2>
                <p class="text-xs text-gray-500">Pilih jenis pemberitahuan sistem yang ingin Anda terima.</p>
            </div>

            <form method="POST" action="{{ route('profile.notifications') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div class="divide-y divide-gray-100">
                    <label class="py-3 flex items-center justify-between cursor-pointer">
                        <div>
                            <span class="text-sm font-semibold text-gray-900 block">Notifikasi Penugasan Tugas</span>
                            <span class="text-xs text-gray-500">Dapatkan peringatan saat Anda diberi tugas baru dalam kepanitiaan.</span>
                        </div>
                        <input
                            type="checkbox"
                            name="preferensi[tugas_baru]"
                            value="1"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 ml-4"
                            {{ !empty($prefs['tugas_baru']) ? 'checked' : '' }}
                        >
                    </label>

                    <label class="py-3 flex items-center justify-between cursor-pointer">
                        <div>
                            <span class="text-sm font-semibold text-gray-900 block">Pengingat Deadline Tugas (H-3)</span>
                            <span class="text-xs text-gray-500">Pemberitahuan otomatis saat tugas mendekati batas akhir pengerjaan.</span>
                        </div>
                        <input
                            type="checkbox"
                            name="preferensi[deadline_h3]"
                            value="1"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 ml-4"
                            {{ !empty($prefs['deadline_h3']) ? 'checked' : '' }}
                        >
                    </label>

                    <label class="py-3 flex items-center justify-between cursor-pointer">
                        <div>
                            <span class="text-sm font-semibold text-gray-900 block">Undangan Kepanitiaan Baru</span>
                            <span class="text-xs text-gray-500">Pemberitahuan ketika akun Anda ditambahkan ke dalam kepanitiaan acara.</span>
                        </div>
                        <input
                            type="checkbox"
                            name="preferensi[undangan_kepanitiaan]"
                            value="1"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 ml-4"
                            {{ !empty($prefs['undangan_kepanitiaan']) ? 'checked' : '' }}
                        >
                    </label>
                </div>

                <div class="flex justify-end pt-2">
                    <x-button type="submit" variant="secondary">
                        Simpan Preferensi
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>

