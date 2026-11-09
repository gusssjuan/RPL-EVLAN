<x-layouts.admin title="Kelola Pengguna">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    Kelola Pengguna Sistem
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola status akses seluruh akun panitia, vendor, dan administrator.
                </p>
            </div>
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[12px] text-sm font-medium text-emerald-800 flex items-center gap-2.5">
                <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-red-50 border border-red-200 rounded-[12px] text-sm font-medium text-red-800 flex items-center gap-2.5">
                <x-icon name="alert-triangle" class="w-5 h-5 text-red-600 shrink-0" />
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Filter & Search Card -->
        <div class="bg-white p-4 rounded-[12px] border border-gray-200 shadow-sm">
            <form method="GET" action="{{ route('admin.pengguna.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Filter Peran</label>
                    <select
                        name="peran"
                        class="w-full px-3 py-2 bg-white border border-gray-300 rounded-[8px] text-xs focus:outline-none focus:ring-1 focus:ring-indigo-600"
                        onchange="this.form.submit()"
                    >
                        <option value="semua" {{ $roleFilter === 'semua' ? 'selected' : '' }}>Semua Peran</option>
                        @foreach ($roles as $r)
                            <option value="{{ $r->nama }}" {{ $roleFilter === $r->nama ? 'selected' : '' }}>
                                {{ ucfirst($r->nama) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Filter Status</label>
                    <select
                        name="status"
                        class="w-full px-3 py-2 bg-white border border-gray-300 rounded-[8px] text-xs focus:outline-none focus:ring-1 focus:ring-indigo-600"
                        onchange="this.form.submit()"
                    >
                        <option value="semua" {{ $statusFilter === 'semua' ? 'selected' : '' }}>Semua Status</option>
                        <option value="aktif" {{ $statusFilter === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $statusFilter === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Cari Pengguna</label>
                    <div class="flex gap-2">
                        <input
                            type="text"
                            name="cari"
                            value="{{ $search }}"
                            placeholder="Cari nama, email, atau no telepon..."
                            class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded-[8px] text-xs focus:outline-none focus:ring-1 focus:ring-indigo-600"
                        >
                        <x-button type="submit" variant="secondary" size="sm">
                            Cari
                        </x-button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Users Table Card -->
        <div class="bg-white rounded-[12px] border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            <th class="py-3 px-4">Pengguna</th>
                            <th class="py-3 px-4">Peran Akun</th>
                            <th class="py-3 px-4">No. Telepon</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4">Terdaftar</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        @forelse ($users as $u)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <x-avatar :name="$u->nama" size="sm" />
                                        <div>
                                            <p class="font-bold text-gray-900 text-sm">{{ $u->nama }}</p>
                                            <p class="text-xs text-gray-400">{{ $u->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4">
                                    @php
                                        $roleName = $u->role?->nama;
                                        $badgeColor = match ($roleName) {
                                            'admin' => 'bg-indigo-100 text-indigo-800',
                                            'vendor' => 'bg-amber-100 text-amber-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeColor }}">
                                        {{ ucfirst($roleName ?? 'User') }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 text-xs text-gray-600">
                                    {{ $u->no_telepon ?? '-' }}
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    @if ($u->status_akun->value === 'aktif')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-xs text-gray-500">
                                    {{ $u->created_at->format('d M Y') }}
                                </td>

                                <td class="py-3.5 px-4 text-right">
                                    @if ($u->id === auth()->id())
                                        <span class="text-xs text-gray-400 italic">Akun Anda</span>
                                    @else
                                        <form
                                            method="POST"
                                            action="{{ route('admin.pengguna.toggle', $u->id) }}"
                                            class="inline-block"
                                            onsubmit="return confirm('Apakah Anda yakin ingin {{ $u->status_akun->value === 'aktif' ? 'menonaktifkan' : 'mengaktifkan kembali' }} akun {{ $u->nama }}?')"
                                        >
                                            @csrf
                                            @if ($u->status_akun->value === 'aktif')
                                                <button
                                                    type="submit"
                                                    class="px-2.5 py-1 text-xs font-semibold text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors"
                                                >
                                                    Nonaktifkan
                                                </button>
                                            @else
                                                <button
                                                    type="submit"
                                                    class="px-2.5 py-1 text-xs font-semibold text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 rounded transition-colors"
                                                >
                                                    Aktifkan
                                                </button>
                                            @endif
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400">
                                    <p class="text-sm font-medium">Tidak ada data pengguna</p>
                                    <p class="text-xs mt-1">Coba sesuaikan kata kunci pencarian atau filter Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>

