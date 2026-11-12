@php
    $chips = [
        ['label' => 'Semua', 'status' => 'semua', 'jenis' => null],
        ['label' => 'Belum dibaca', 'status' => 'belum', 'jenis' => null],
        ['label' => 'Penugasan', 'status' => 'semua', 'jenis' => \App\Enums\JenisNotifikasi::Penugasan->value],
        ['label' => 'Deadline', 'status' => 'semua', 'jenis' => \App\Enums\JenisNotifikasi::Deadline->value],
        ['label' => 'Status tugas', 'status' => 'semua', 'jenis' => \App\Enums\JenisNotifikasi::StatusTugas->value],
        ['label' => 'Undangan', 'status' => 'semua', 'jenis' => \App\Enums\JenisNotifikasi::Undangan->value],
        ['label' => 'Pendaftaran', 'status' => 'semua', 'jenis' => \App\Enums\JenisNotifikasi::Pendaftaran->value],
    ];

    $ikon = fn (\App\Enums\JenisNotifikasi $jenis): array => [
        'name' => $jenis->icon(),
        'class' => match ($jenis) {
            \App\Enums\JenisNotifikasi::Penugasan => 'bg-indigo-100 text-indigo-600',
            \App\Enums\JenisNotifikasi::Deadline => 'bg-amber-100 text-amber-600',
            \App\Enums\JenisNotifikasi::StatusTugas => 'bg-sky-100 text-sky-600',
            \App\Enums\JenisNotifikasi::Undangan => 'bg-emerald-100 text-emerald-600',
            \App\Enums\JenisNotifikasi::Pendaftaran => 'bg-purple-100 text-purple-600',
            \App\Enums\JenisNotifikasi::VerifikasiVendor => 'bg-rose-100 text-rose-600',
        },
    ];
@endphp

<x-dynamic-component :component="$layout" title="Notifikasi">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Notifikasi</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Pantau penugasan, deadline, undangan, dan perubahan status acara Anda.
                </p>
            </div>

            @if ($jumlahBelumDibaca > 0)
                <form method="POST" action="{{ route('notifikasi.baca-semua') }}">
                    @csrf
                    @method('PATCH')
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-[8px] border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition-colors hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        <x-icon name="check" class="h-4 w-4" />
                        Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>

        <div class="-mx-1 flex gap-2 overflow-x-auto px-1 pb-1">
            @foreach ($chips as $chip)
                @php
                    $params = [];
                    if ($chip['status'] === 'belum') {
                        $params['status'] = 'belum';
                    }
                    if ($chip['jenis'] !== null) {
                        $params['jenis'] = $chip['jenis'];
                    }
                    $aktif = $status === $chip['status'] && ($jenis?->value === $chip['jenis']);
                @endphp
                <a
                    href="{{ route('notifikasi.index', $params) }}"
                    class="whitespace-nowrap rounded-full border px-3.5 py-1.5 text-sm font-medium transition-colors {{ $aktif ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-gray-300 bg-white text-gray-600 hover:border-indigo-300 hover:text-indigo-600' }}"
                >
                    {{ $chip['label'] }}
                </a>
            @endforeach
        </div>

        @if ($notifications->isEmpty())
            <div class="rounded-2xl border border-gray-200 bg-white px-6 py-14 text-center shadow-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                    <x-icon name="inbox" class="h-7 w-7" />
                </div>
                <h2 class="mt-4 text-base font-semibold text-gray-900">Belum ada notifikasi</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Notifikasi baru akan muncul di sini saat ada aktivitas pada acara atau tugas Anda.
                </p>
            </div>
        @else
            <div class="space-y-7">
                @foreach ($grouped as $label => $items)
                    <section>
                        <h2 class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-400">{{ $label }}</h2>

                        <div class="space-y-2">
                            @foreach ($items as $notification)
                                @php
                                    $belumDibaca = $notification->dibaca_at === null;
                                    $visual = $ikon($notification->jenis);
                                @endphp

                                <form method="POST" action="{{ route('notifikasi.baca', $notification) }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="w-full rounded-2xl border p-4 text-left shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 {{ $belumDibaca ? 'border-indigo-200 bg-indigo-50/70 hover:bg-indigo-50' : 'border-gray-200 bg-white hover:bg-gray-50' }}"
                                    >
                                        <div class="flex items-start gap-3">
                                            <span class="flex h-10 w-10 flex-none items-center justify-center rounded-full {{ $visual['class'] }}">
                                                <x-icon :name="$visual['name']" class="h-5 w-5" />
                                            </span>

                                            <span class="min-w-0 flex-1">
                                                <span class="flex flex-wrap items-center gap-2">
                                                    <span class="text-sm font-semibold text-gray-900">{{ $notification->jenis->label() }}</span>
                                                    @if ($belumDibaca)
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-indigo-600 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white">
                                                            <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                                                            Baru
                                                        </span>
                                                    @endif
                                                    <span class="ml-auto text-xs text-gray-400">{{ $notification->waktu_kirim?->diffForHumans() }}</span>
                                                </span>

                                                <span class="mt-1 block text-sm leading-6 text-gray-700">{{ $notification->pesan }}</span>

                                                @if ($notification->event)
                                                    <span class="mt-1.5 block text-xs font-medium text-gray-400">
                                                        {{ $notification->event->nama_event }}
                                                    </span>
                                                @endif
                                            </span>

                                            <x-icon name="chevron-right" class="mt-1 h-4 w-4 flex-none text-gray-300" />
                                        </div>
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

            <div>
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</x-dynamic-component>
