@php
    use App\Enums\StatusPendaftaran;

    $palette = [
        ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'accent' => 'bg-indigo-600', 'ring' => 'ring-indigo-200'],
        ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'accent' => 'bg-rose-600', 'ring' => 'ring-rose-200'],
        ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'accent' => 'bg-amber-500', 'ring' => 'ring-amber-200'],
        ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'accent' => 'bg-emerald-600', 'ring' => 'ring-emerald-200'],
        ['bg' => 'bg-sky-50', 'text' => 'text-sky-700', 'accent' => 'bg-sky-600', 'ring' => 'ring-sky-200'],
        ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'accent' => 'bg-violet-600', 'ring' => 'ring-violet-200'],
    ];
    $color = $palette[$event->category_id % count($palette)];
    $kuotaPenuh = $event->kuota !== null && $event->diterima_count >= $event->kuota;
    $persenKuota = $event->kuota
        ? min(100, (int) round(($event->diterima_count / $event->kuota) * 100))
        : 0;
@endphp

<x-layouts.app :title="'Detail Acara - ' . $event->nama_event">
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('cari-acara.index') }}" class="inline-flex items-center gap-1.5 text-xs text-gray-500 hover:text-indigo-600 transition-colors font-medium">
                <x-icon name="chevron-left" class="w-4 h-4" />
                <span>Kembali ke Cari Acara</span>
            </a>
            <x-badge variant="primary">
                <x-icon name="globe" class="w-3.5 h-3.5" />
                <span>Acara Publik</span>
            </x-badge>
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-[12px] text-sm font-medium text-emerald-800 flex items-center gap-2.5">
                <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-[12px] text-sm text-red-700">
                <div class="flex items-center gap-2 font-semibold">
                    <x-icon name="alert-circle" class="w-4 h-4 shrink-0" />
                    <span>Pendaftaran belum dapat diproses.</span>
                </div>
                <ul class="mt-1.5 list-disc list-inside space-y-0.5 pl-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <section class="bg-white rounded-[16px] border border-gray-200 shadow-sm overflow-hidden">
                    <div class="h-2 {{ $color['accent'] }}"></div>
                    <div class="p-6 sm:p-7 space-y-5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ $color['bg'] }} {{ $color['text'] }} text-xs font-semibold">
                                <x-category-icon :kategori="$event->category" class="w-4 h-4" />
                                <span>{{ $event->category?->nama_kategori }}</span>
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold">
                                {{ $event->hari_menuju }}
                            </span>
                            @if ($kuotaPenuh)
                                <x-badge variant="danger">Kuota penuh</x-badge>
                            @elseif ($event->pendaftaran_dibuka)
                                <x-badge variant="success">Pendaftaran dibuka</x-badge>
                            @else
                                <x-badge variant="neutral">Pendaftaran ditutup</x-badge>
                            @endif
                        </div>

                        <div>
                            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900">
                                {{ $event->nama_event }}
                            </h1>
                            <p class="mt-2 text-sm leading-relaxed text-gray-600">
                                {{ $event->tujuan }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                            <div class="flex items-start gap-2.5 p-3 bg-gray-50 rounded-[10px] border border-gray-100">
                                <x-icon name="calendar" class="w-4 h-4 text-indigo-600 mt-0.5 shrink-0" />
                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Tanggal</p>
                                    <p class="font-semibold text-gray-800">{{ $event->tanggal->isoFormat('dddd, D MMMM Y') }}</p>
                                    @if ($event->waktu_mulai || $event->waktu_selesai)
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            {{ $event->waktu_mulai ? \Carbon\Carbon::parse($event->waktu_mulai)->format('H.i') : '-' }}
                                            @if ($event->waktu_selesai)
                                                – {{ \Carbon\Carbon::parse($event->waktu_selesai)->format('H.i') }}
                                            @endif
                                            WITA
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5 p-3 bg-gray-50 rounded-[10px] border border-gray-100">
                                <x-icon name="map-pin" class="w-4 h-4 text-indigo-600 mt-0.5 shrink-0" />
                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Lokasi</p>
                                    <p class="font-semibold text-gray-800">{{ $event->lokasi }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5 p-3 bg-gray-50 rounded-[10px] border border-gray-100">
                                <x-icon name="users" class="w-4 h-4 text-indigo-600 mt-0.5 shrink-0" />
                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Target Peserta</p>
                                    <p class="font-semibold text-gray-800">{{ $event->target_peserta }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Estimasi {{ number_format($event->jumlah_peserta, 0, ',', '.') }} peserta</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5 p-3 bg-gray-50 rounded-[10px] border border-gray-100">
                                <x-icon name="store" class="w-4 h-4 text-indigo-600 mt-0.5 shrink-0" />
                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Penyelenggara</p>
                                    <p class="font-semibold text-gray-800">{{ $event->penyelenggara ?: $event->leader?->nama }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Ketua: {{ $event->leader?->nama }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bg-white p-6 rounded-[16px] border border-gray-200 shadow-sm space-y-3">
                    <h2 class="text-base font-bold text-gray-900">Tentang Acara</h2>
                    <p class="text-sm leading-relaxed text-gray-600 whitespace-pre-line">{{ $event->tujuan }}</p>
                    <div class="pt-3 border-t border-gray-100 text-xs text-gray-500">
                        <p>Pendaftaran ini hanya untuk konfirmasi peserta. EVLAN tidak menerbitkan tiket atau memproses pembayaran.</p>
                    </div>
                </section>
            </div>

            <aside class="space-y-4 lg:sticky lg:top-6 self-start">
                <section class="bg-white rounded-[16px] border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-100">
                        <h2 class="text-base font-bold text-gray-900">Pendaftaran</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Konfirmasi keikutsertaan Anda pada acara ini.</p>
                    </div>

                    <div class="p-5 space-y-4">
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="font-medium text-gray-500">Kuota terisi</span>
                                <span class="font-bold text-gray-900">
                                    {{ $event->diterima_count }}/{{ $event->kuota ?? '∞' }}
                                </span>
                            </div>
                            <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-2.5 rounded-full {{ $kuotaPenuh ? 'bg-red-500' : $color['accent'] }}" style="width: {{ $event->kuota ? $persenKuota : 0 }}%"></div>
                            </div>
                            @if ($event->batas_pendaftaran)
                                <p class="mt-2 text-[11px] text-gray-500 flex items-center gap-1">
                                    <x-icon name="clock" class="w-3.5 h-3.5" />
                                    <span>Batas pendaftaran {{ $event->batas_pendaftaran->isoFormat('D MMMM Y') }}</span>
                                </p>
                            @endif
                        </div>

                        @guest
                            <div class="p-4 rounded-[10px] bg-indigo-50 border border-indigo-200 text-xs text-indigo-800 space-y-2">
                                <p class="font-semibold">Masuk untuk mendaftar</p>
                                <p>Anda dapat melihat detail acara sebagai tamu. Masuk sebagai pengguna untuk mengirim pendaftaran.</p>
                            </div>
                            <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-sm font-semibold shadow-sm transition-colors">
                                <x-icon name="user-plus" class="w-4 h-4" />
                                <span>Daftar Sekarang</span>
                            </a>
                        @endguest

                        @auth
                            @if ($pendaftaranSaya)
                                <div class="p-4 rounded-[10px] border {{ $pendaftaranSaya->status === StatusPendaftaran::Diterima ? 'bg-emerald-50 border-emerald-200' : ($pendaftaranSaya->status === StatusPendaftaran::Ditolak ? 'bg-red-50 border-red-200' : 'bg-amber-50 border-amber-200') }} space-y-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-xs font-semibold text-gray-600">Status pendaftaran</span>
                                        <x-status-badge :status="$pendaftaranSaya->status->value" />
                                    </div>

                                    @if ($pendaftaranSaya->status === StatusPendaftaran::Menunggu)
                                        <p class="text-xs text-amber-800">Pendaftaran Anda sedang menunggu konfirmasi ketua acara.</p>
                                        <form action="{{ route('cari-acara.daftar.batal', $event->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-white border border-amber-300 hover:bg-amber-100 text-amber-800 rounded-[8px] text-xs font-semibold transition-colors">
                                                <x-icon name="x" class="w-3.5 h-3.5" />
                                                <span>Batalkan Pendaftaran</span>
                                            </button>
                                        </form>
                                    @elseif ($pendaftaranSaya->status === StatusPendaftaran::Diterima)
                                        <p class="text-xs text-emerald-800">Selamat! Pendaftaran Anda telah diterima. Sampai jumpa di acara.</p>
                                    @else
                                        <p class="text-xs text-red-800">
                                            Pendaftaran Anda ditolak.
                                            @if ($pendaftaranSaya->alasan_penolakan)
                                                Alasan: {{ $pendaftaranSaya->alasan_penolakan }}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            @elseif ($event->isLeader(auth()->user()))
                                <div class="p-4 rounded-[10px] bg-indigo-50 border border-indigo-200 text-xs text-indigo-800">
                                    Anda adalah ketua acara ini. Pengelolaan pendaftar tersedia di halaman ringkasan acara.
                                </div>
                            @elseif ($alasanTidakBisaDaftar)
                                <div class="p-4 rounded-[10px] bg-gray-50 border border-gray-200 text-xs text-gray-600 space-y-2">
                                    <p class="font-semibold text-gray-800">Pendaftaran belum tersedia</p>
                                    <p>{{ $alasanTidakBisaDaftar }}</p>
                                </div>
                                <x-button variant="secondary" disabled class="w-full">Daftar Acara</x-button>
                            @else
                                <button
                                    type="button"
                                    x-on:click="$dispatch('open-modal', 'daftar-acara')"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-sm font-semibold shadow-sm transition-colors"
                                >
                                    <x-icon name="user-plus" class="w-4 h-4" />
                                    <span>Daftar Acara</span>
                                </button>
                            @endif
                        @endauth
                    </div>
                </section>

                <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-[12px] text-xs text-indigo-800 flex items-start gap-2">
                    <x-icon name="lightbulb" class="w-4 h-4 shrink-0 mt-0.5" />
                    <p>Pastikan data profil Anda sudah benar sebelum mendaftar. Perubahan data dapat dilakukan di halaman profil.</p>
                </div>
            </aside>
        </div>
    </div>

    @auth
        @if (! $pendaftaranSaya && ! $event->isLeader(auth()->user()) && ! $alasanTidakBisaDaftar)
            <x-modal name="daftar-acara" title="Daftar Acara" maxWidth="lg">
            <form action="{{ route('cari-acara.daftar', $event->id) }}" method="POST" class="space-y-4">
                @csrf

                <div class="p-3 bg-gray-50 rounded-[10px] border border-gray-100">
                    <p class="text-xs font-semibold text-gray-500">Acara</p>
                    <p class="text-sm font-bold text-gray-900">{{ $event->nama_event }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $event->tanggal->isoFormat('D MMMM Y') }}, {{ $event->lokasi }}</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1.5">Nama</label>
                        <input type="text" value="{{ auth()->user()->nama }}" disabled class="w-full px-3.5 py-2.5 bg-gray-100 border border-gray-200 rounded-[8px] text-sm text-gray-600">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1.5">Email</label>
                        <input type="email" value="{{ auth()->user()->email }}" disabled class="w-full px-3.5 py-2.5 bg-gray-100 border border-gray-200 rounded-[8px] text-sm text-gray-600">
                    </div>
                </div>

                <x-input
                    name="no_telepon"
                    label="Nomor Telepon (opsional)"
                    :value="auth()->user()->no_telepon"
                    placeholder="Contoh: 081234567890"
                />
                <p class="-mt-2 text-[11px] text-gray-500">Minimal 10 digit bila diisi.</p>

                <div>
                    <label for="catatan" class="block text-sm font-semibold text-gray-800 mb-1.5">Catatan (opsional)</label>
                    <textarea
                        id="catatan"
                        name="catatan"
                        rows="3"
                        placeholder="Contoh: Saya ingin mengikuti sesi workshop."
                        class="w-full px-3.5 py-2.5 bg-white border {{ $errors->has('catatan') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-indigo-600 focus:ring-indigo-600' }} rounded-[8px] text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 transition-colors"
                    >{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <x-icon name="alert-triangle" class="w-3.5 h-3.5 shrink-0" />
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <label class="flex items-start gap-2.5 p-3 bg-indigo-50/60 border border-indigo-200 rounded-[10px] cursor-pointer">
                    <input
                        type="checkbox"
                        name="persetujuan_data"
                        value="1"
                        @checked(old('persetujuan_data'))
                        class="mt-0.5 h-4 w-4 rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500"
                    >
                    <span class="text-xs text-indigo-900">
                        Saya menyetujui data saya digunakan untuk keperluan pendaftaran dan konfirmasi acara ini.
                    </span>
                </label>
                @error('persetujuan_data')
                    <p class="text-xs text-red-600 flex items-center gap-1">
                        <x-icon name="alert-triangle" class="w-3.5 h-3.5 shrink-0" />
                        <span>{{ $message }}</span>
                    </p>
                @enderror

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" x-on:click="$dispatch('close-modal', 'daftar-acara')" class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-[8px] text-sm font-semibold transition-colors">
                        Batal
                    </button>
                    <x-button type="submit" variant="primary">
                        <x-icon name="user-plus" class="w-4 h-4" />
                        <span>Kirim Pendaftaran</span>
                    </x-button>
                </div>
            </form>
            </x-modal>
        @endif
    @endauth
</x-layouts.app>
