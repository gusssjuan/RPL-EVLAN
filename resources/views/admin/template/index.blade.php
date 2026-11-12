<x-layouts.admin title="Kelola Template Master">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    Kelola Template Master
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Template master otomatisasi kebutuhan, timeline H-n, divisi, dan jobdesk kepanitiaan.
                </p>
            </div>
            <div>
                <a
                    href="{{ route('admin.template.create') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-sm font-semibold shadow-sm transition-colors"
                >
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Buat Template Baru</span>
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[12px] text-sm font-medium text-emerald-800 flex items-center gap-2.5">
                <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-[12px] border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            <th class="py-3 px-4">Nama Template</th>
                            <th class="py-3 px-4">Kategori</th>
                            <th class="py-3 px-4">Komponen Template</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        @forelse ($templates as $tmpl)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-gray-900">
                                    {{ $tmpl->nama_template }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                        <x-category-icon :kategori="$tmpl->category" class="w-3.5 h-3.5" />
                                        <span>{{ $tmpl->category?->nama_kategori ?? 'Umum' }}</span>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="flex flex-wrap gap-1.5 text-[11px]">
                                        <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded font-medium">
                                            {{ $tmpl->kebutuhan_count }} Kebutuhan
                                        </span>
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded font-medium">
                                            {{ $tmpl->timeline_count }} Timeline
                                        </span>
                                        <span class="px-2 py-0.5 bg-amber-50 text-amber-700 rounded font-medium">
                                            {{ $tmpl->divisi_count }} Divisi
                                        </span>
                                        <span class="px-2 py-0.5 bg-purple-50 text-purple-700 rounded font-medium">
                                            {{ $tmpl->jobdesk_count }} Jobdesk
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if ($tmpl->is_aktif)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif Utama
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                            Draft
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right space-x-1.5">
                                    <a
                                        href="{{ route('admin.template.edit', $tmpl->id) }}"
                                        class="inline-block px-2.5 py-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded transition-colors"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.template.duplicate', $tmpl->id) }}"
                                        class="inline-block"
                                    >
                                        @csrf
                                        <button
                                            type="submit"
                                            class="px-2.5 py-1 text-xs font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded transition-colors"
                                            title="Duplikat template"
                                        >
                                            Duplikat
                                        </button>
                                    </form>

                                    @if (! $tmpl->is_aktif)
                                        <form
                                            method="POST"
                                            action="{{ route('admin.template.activate', $tmpl->id) }}"
                                            class="inline-block"
                                        >
                                            @csrf
                                            <button
                                                type="submit"
                                                class="px-2.5 py-1 text-xs font-semibold text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 rounded transition-colors"
                                                title="Aktifkan untuk kategori ini"
                                            >
                                                Aktifkan
                                            </button>
                                        </form>
                                    @endif

                                    <form
                                        method="POST"
                                        action="{{ route('admin.template.destroy', $tmpl->id) }}"
                                        class="inline-block"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus template ini?')"
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
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400">
                                    <p class="text-sm font-medium">Belum ada template master yang dibuat</p>
                                    <p class="text-xs mt-1">Klik tombol 'Buat Template Baru' untuk memulai.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>

