<x-layouts.landing title="EVLAN - Perencanaan acara dari H-60 sampai hari H">
    <section class="bg-neutral-50 pt-8">
        <div class="mx-auto w-full max-w-[72rem] px-5 py-14 sm:px-8 md:py-20">
            <div class="grid items-center gap-10 lg:grid-cols-12 lg:gap-12">
                <div class="min-w-0 lg:col-span-5">
                    <h1 class="judul-h1 text-neutral-900">
                        Dari H-60 sampai hari H, semua panitia tahu tugasnya.
                    </h1>

                    <p class="mt-5 max-w-[62ch] text-base leading-7 text-neutral-600">
                        EVLAN menyusun checklist, timeline, dan pembagian tugas acara dari template kategori. Anda mengisi tanggal dan mengundang panitia; sisanya sudah tertata.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-5">
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex min-h-11 items-center justify-center rounded-control bg-amber-500 px-5 py-2.5 text-base font-medium text-white transition-colors hover:bg-amber-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2"
                        >
                            Mulai Kegiatan
                        </a>

                        <a
                            href="#cari-kegiatan"
                            class="inline-flex min-h-11 items-center justify-center rounded-control bg-primary-600 px-5 py-2.5 text-base font-medium text-white transition-colors hover:bg-primary-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2"
                        >
                            Cari Kegiatan
                        </a>
                    </div>
                </div>

                <div class="min-w-0 lg:col-span-7">
                    <picture>
                        <source
                            type="image/webp"
                            srcset="{{ asset('images/hero-640.webp') }} 640w, {{ asset('images/hero-1280.webp') }} 1280w"
                            sizes="(min-width: 1024px) 58vw, 100vw"
                        >
                        <img
                            src="{{ asset('images/hero.png') }}"
                            width="1200"
                            height="800"
                            alt="Dua pasangan panitia berdiskusi di depan papan nama EVLAN"
                            fetchpriority="high"
                            decoding="async"
                            class="h-auto w-full rounded-panel"
                        >
                    </picture>
                </div>
            </div>
        </div>
    </section>

    <section class="border-t border-neutral-200 bg-surface">
        <div class="animasi-scroll mx-auto grid w-full max-w-[72rem] gap-8 px-5 py-14 sm:px-8 md:py-20 lg:grid-cols-12 lg:gap-12">
            <div class="min-w-0 lg:col-span-4">
                <h2 class="judul-h2 text-neutral-900">Persiapan acara sering tercecer di banyak tempat</h2>
            </div>

            <div class="grid min-w-0 gap-8 sm:grid-cols-2 sm:gap-0 sm:divide-x sm:divide-neutral-200 lg:col-span-8">
                <div class="sm:pr-8">
                    <h3 class="text-sm font-semibold text-neutral-900">Sekarang</h3>
                    <ul class="mt-4 list-disc space-y-3 pl-5 text-base leading-7 text-neutral-600 marker:text-neutral-400">
                        <li>Tugas dibagikan lewat grup chat dan cepat tenggelam.</li>
                        <li>Progres baru diketahui kalau ditanya satu per satu.</li>
                        <li>Setiap acara disusun dari nol, tanpa standar kebutuhan.</li>
                    </ul>
                </div>

                <div class="border-t border-neutral-200 pt-8 sm:border-t-0 sm:pt-0 sm:pl-8">
                    <h3 class="text-sm font-semibold text-neutral-900">Dengan EVLAN</h3>
                    <ul class="mt-4 list-disc space-y-3 pl-5 text-base leading-7 text-neutral-600 marker:text-neutral-400">
                        <li>Setiap tugas punya penerima, divisi, dan deadline.</li>
                        <li>Ketua melihat persentase progres dan tugas yang terlewat.</li>
                        <li>Template tiap kategori sudah berisi kebutuhan, timeline, dan struktur panitia.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section id="cara-kerja" class="scroll-mt-20 border-t border-neutral-200 bg-primary-600">
        <div class="animasi-scroll mx-auto w-full max-w-[72rem] px-5 py-14 sm:px-8 md:py-20">
            <h2 class="judul-h2 text-neutral-200">Cara kerjanya</h2>

            <ol class="mt-10 max-w-[45rem]">
                <li class="flex gap-5 pb-10 last:pb-0 sm:gap-7">
                    <div class="flex shrink-0 flex-col items-center">
                        <span class="font-display text-[2rem] font-semibold leading-[2.25rem] tabular-nums text-neutral-200 sm:text-[2.5rem] sm:leading-[2.75rem]">01</span>
                        <span class="mt-3 w-0.5 flex-1 bg-neutral-200" aria-hidden="true"></span>
                    </div>
                    <div class="min-w-0 flex-1 pt-1">
                        <h3 class="font-display text-lg font-semibold text-neutral-200">Pilih kategori acara</h3>
                        <p class="mt-2 max-w-[62ch] text-base leading-7 text-neutral-200">
                            Seminar, wedding, 17 Agustus, ulang tahun, atau gathering. Kebutuhan, timeline, dan struktur panitia untuk kategori itu langsung terlihat.
                        </p>
                    </div>
                </li>

                <li class="flex gap-5 pb-10 last:pb-0 sm:gap-7">
                    <div class="flex shrink-0 flex-col items-center">
                        <span class="font-display text-[2rem] font-semibold leading-[2.25rem] tabular-nums text-neutral-200 sm:text-[2.5rem] sm:leading-[2.75rem]">02</span>
                        <span class="mt-3 w-0.5 flex-1 bg-neutral-200" aria-hidden="true"></span>
                    </div>
                    <div class="min-w-0 flex-1 pt-1">
                        <h3 class="font-display text-lg font-semibold text-neutral-200">Isi detail acara</h3>
                        <p class="mt-2 max-w-[62ch] text-base leading-7 text-neutral-200">
                            Nama, tanggal, lokasi, dan jumlah peserta. Checklist dan timeline H-n dihitung dari tanggal acara.
                        </p>
                    </div>
                </li>

                <li class="flex gap-5 pb-10 last:pb-0 sm:gap-7">
                    <div class="flex shrink-0 flex-col items-center">
                        <span class="font-display text-[2rem] font-semibold leading-[2.25rem] tabular-nums text-neutral-200 sm:text-[2.5rem] sm:leading-[2.75rem]">03</span>
                        <span class="mt-3 w-0.5 flex-1 bg-neutral-200" aria-hidden="true"></span>
                    </div>
                    <div class="min-w-0 flex-1 pt-1">
                        <h3 class="font-display text-lg font-semibold text-neutral-200">Bentuk kepanitiaan</h3>
                        <p class="mt-2 max-w-[62ch] text-base leading-7 text-neutral-200">
                            Tambah divisi, undang anggota lewat email, lalu bagikan tugas lengkap dengan deadline.
                        </p>
                    </div>
                </li>

                <li class="flex gap-5 pb-10 last:pb-0 sm:gap-7">
                    <div class="flex shrink-0 flex-col items-center">
                        <span class="font-display text-[2rem] font-semibold leading-[2.25rem] tabular-nums text-neutral-200 sm:text-[2.5rem] sm:leading-[2.75rem]">04</span>
                        <span class="mt-3 w-0.5 flex-1 bg-neutral-200" aria-hidden="true"></span>
                    </div>
                    <div class="min-w-0 flex-1 pt-1">
                        <h3 class="font-display text-lg font-semibold text-neutral-200">Pantau progres</h3>
                        <p class="mt-2 max-w-[62ch] text-base leading-7 text-neutral-200">
                            Anggota memperbarui status tugasnya. Ketua melihat progres tiap divisi dan tugas yang sudah lewat deadline.
                        </p>
                    </div>
                </li>

                <li class="flex gap-5 pb-10 last:pb-0 sm:gap-7">
                    <div class="flex shrink-0 flex-col items-center">
                        <span class="font-display text-[2rem] font-semibold leading-[2.25rem] tabular-nums text-neutral-200 sm:text-[2.5rem] sm:leading-[2.75rem]">05</span>
                    </div>
                    <div class="min-w-0 flex-1 pt-1">
                        <h3 class="font-display text-lg font-semibold text-neutral-200">Atur anggaran dan cari vendor</h3>
                        <p class="mt-2 max-w-[62ch] text-base leading-7 text-neutral-200">
                            Hitung estimasi biaya dari jumlah dan harga satuan, lalu lihat vendor terverifikasi untuk catering, dekorasi, dokumentasi, dan sound system.
                        </p>
                    </div>
                </li>
            </ol>
        </div>
    </section>

    <section id="cari-kegiatan" class="scroll-mt-20 border-t border-neutral-200 bg-surface">
        <div class="animasi-scroll mx-auto w-full max-w-[72rem] px-5 py-14 sm:px-8 md:py-20">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="min-w-0">
                    <h2 class="judul-h2 text-neutral-900">Kegiatan terbuka yang bisa Anda ikuti</h2>
                    <p class="mt-3 max-w-[62ch] text-base leading-7 text-neutral-600">
                        Acara publik terdekat dari penyelenggara lain. Buka detail untuk melihat jadwal, lokasi, dan pendaftaran.
                    </p>
                </div>

                @if ($kegiatan->isNotEmpty())
                    <a
                        href="{{ route('cari-acara.index') }}"
                        class="inline-flex min-h-11 items-center rounded-control text-sm font-medium text-primary-700 underline-offset-4 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2"
                    >
                        Lihat semua kegiatan
                    </a>
                @endif
            </div>

            @if ($kegiatan->isEmpty())
                <div class="mt-10 border-t border-neutral-200 py-10">
                    <p class="max-w-[62ch] text-base leading-7 text-neutral-600">
                        Belum ada kegiatan terbuka saat ini. Anda bisa mulai menyusun acara sendiri.
                    </p>
                    <a
                        href="{{ route('login') }}"
                        class="mt-4 inline-flex min-h-11 items-center justify-center rounded-control bg-primary-600 px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-primary-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2"
                    >
                        Mulai kegiatan
                    </a>
                </div>
            @else
                <ol class="mt-10 border-t border-neutral-200">
                    @foreach ($kegiatan as $event)
                        @php
                            $kuotaPenuh = $event->kuota !== null && $event->diterima_count >= $event->kuota;
                            $batasLewat = $event->batas_pendaftaran && $event->batas_pendaftaran->lt(today());
                            $pendaftaranTerbuka = $event->pendaftaran_dibuka && ! $kuotaPenuh && ! $batasLewat;

                            $status = $pendaftaranTerbuka
                                ? ['label' => 'Pendaftaran dibuka', 'icon' => 'circle-check', 'class' => 'text-sukses-600']
                                : ($kuotaPenuh
                                    ? ['label' => 'Kuota penuh', 'icon' => 'users', 'class' => 'text-bahaya-600']
                                    : ['label' => 'Pendaftaran ditutup', 'icon' => 'clock', 'class' => 'text-neutral-600']);
                        @endphp

                        <li class="border-b border-neutral-200">
                            <a
                                href="{{ route('cari-acara.show', $event) }}"
                                class="group -mx-3 grid gap-4 rounded-control px-3 py-6 transition-colors hover:bg-neutral-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2 sm:grid-cols-[6rem_1fr_auto] sm:items-center sm:gap-6"
                            >
                                <div class="flex items-center gap-3 sm:block">
                                    <span class="inline-flex items-center rounded-full bg-aksen-500 px-2.5 py-1 text-sm font-semibold tabular-nums text-neutral-900">
                                        {{ $event->hari_menuju }}
                                    </span>
                                    <time datetime="{{ $event->tanggal->toDateString() }}" class="text-sm tabular-nums text-neutral-600 sm:mt-2 sm:block">
                                        {{ $event->tanggal->locale('id')->isoFormat('D MMM Y') }}
                                    </time>
                                </div>

                                <div class="min-w-0">
                                    <p class="flex items-center gap-1.5 text-sm text-neutral-600">
                                        <x-category-icon :kategori="$event->category" class="h-4 w-4 shrink-0" />
                                        <span>{{ $event->category?->nama_kategori }}</span>
                                    </p>
                                    <h3 class="mt-1 font-display text-lg font-semibold leading-6 text-neutral-900 transition-colors group-hover:text-primary-700">
                                        {{ $event->nama_event }}
                                    </h3>
                                    <p class="mt-2 flex min-w-0 items-center gap-1.5 text-sm text-neutral-600">
                                        <x-icon name="map-pin" class="h-4 w-4 shrink-0" />
                                        <span class="truncate">{{ $event->lokasi }}</span>
                                    </p>
                                    @if ($event->penyelenggara)
                                        <p class="mt-1 flex min-w-0 items-center gap-1.5 text-sm text-neutral-600">
                                            <x-icon name="building-2" class="h-4 w-4 shrink-0" />
                                            <span class="truncate">{{ $event->penyelenggara }}</span>
                                        </p>
                                    @endif
                                </div>

                                <div class="flex flex-wrap items-center justify-between gap-3 sm:flex-col sm:items-end sm:justify-center">
                                    <span class="inline-flex items-center gap-1.5 text-sm font-medium {{ $status['class'] }}">
                                        <x-icon :name="$status['icon']" class="h-4 w-4 shrink-0" />
                                        <span>{{ $status['label'] }}</span>
                                    </span>
                                    <span class="text-sm font-medium text-primary-700 group-hover:underline">Lihat kegiatan</span>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    </section>

    <section id="tujuan" class="scroll-mt-20 border-t border-neutral-200 bg-neutral-50">
        <div class="animasi-scroll mx-auto w-full max-w-[72rem] px-5 py-14 sm:px-8 md:py-20">
            <h2 class="judul-h2 text-neutral-900">Yang dibantu EVLAN</h2>

            <div class="mt-10 grid gap-10 lg:grid-cols-12 lg:items-start lg:gap-12">
                <div class="min-w-0 lg:col-span-5">
                    <picture>
                        <source
                            type="image/webp"
                            srcset="{{ asset('images/tujuan-640.webp') }} 640w, {{ asset('images/tujuan-1280.webp') }} 1280w"
                            sizes="(min-width: 1024px) 40vw, 100vw"
                        >
                        <img
                            src="{{ asset('images/tujuan.png') }}"
                            width="2400"
                            height="1800"
                            alt="Dua panitia memberi tos di depan papan bertuliskan Evlan"
                            loading="lazy"
                            decoding="async"
                            class="h-auto w-full rounded-panel border border-neutral-200"
                        >
                    </picture>
                </div>

                <ol class="min-w-0 space-y-5 lg:col-span-7">
                    <li class="grid grid-cols-[auto_1fr] gap-2">
                        <span class="font-display text-lg font-semibold tabular-nums text-primary-700">1</span>
                        <p class="text-base leading-7 text-neutral-900">Merancang acara berdasarkan kategori dan kebutuhan yang sesuai.</p>
                    </li>
                    <li class="grid grid-cols-[auto_1fr] gap-2">
                        <span class="font-display text-lg font-semibold tabular-nums text-primary-700">2</span>
                        <p class="text-base leading-7 text-neutral-900">Memakai template perencanaan, jadi tidak perlu membuat rencana dari awal.</p>
                    </li>
                    <li class="grid grid-cols-[auto_1fr] gap-2">
                        <span class="font-display text-lg font-semibold tabular-nums text-primary-700">3</span>
                        <p class="text-base leading-7 text-neutral-900">Membagi jobdesk ke tiap divisi atau anggota panitia.</p>
                    </li>
                    <li class="grid grid-cols-[auto_1fr] gap-2">
                        <span class="font-display text-lg font-semibold tabular-nums text-primary-700">4</span>
                        <p class="text-base leading-7 text-neutral-900">Memantau progres pekerjaan lewat status tugas.</p>
                    </li>
                    <li class="grid grid-cols-[auto_1fr] gap-2">
                        <span class="font-display text-lg font-semibold tabular-nums text-primary-700">5</span>
                        <p class="text-base leading-7 text-neutral-900">Melihat vendor yang menyediakan kebutuhan acara.</p>
                    </li>
                    <li class="grid grid-cols-[auto_1fr] gap-2">
                        <span class="font-display text-lg font-semibold tabular-nums text-primary-700">6</span>
                        <p class="text-base leading-7 text-neutral-900">Memperkirakan anggaran dan menyiapkan kebutuhan acara.</p>
                    </li>
                </ol>
            </div>
        </div>
    </section>

    <section id="untuk-siapa" class="scroll-mt-20 border-t border-neutral-200 bg-surface">
        <div class="animasi-scroll mx-auto w-full max-w-[72rem] px-5 py-14 sm:px-8 md:py-20">
            <h2 class="judul-h2 text-neutral-900">Untuk siapa</h2>

            <div class="mt-8 grid gap-10 lg:grid-cols-2 lg:gap-12">
                <ul class="min-w-0 list-disc space-y-3 pl-5 text-base leading-7 text-neutral-900 marker:text-neutral-400">
                    <li>Mahasiswa dan organisasi kampus</li>
                    <li>Komunitas</li>
                    <li>Event organizer pemula</li>
                    <li>Perusahaan atau institusi penyelenggara kegiatan</li>
                    <li>Siapa saja yang ingin membuat acara</li>
                </ul>

                <div class="min-w-0 border-t border-neutral-200 pt-8 lg:border-l lg:border-t-0 lg:pl-12 lg:pt-0">
                    <h3 class="font-display text-lg font-semibold text-neutral-900">Punya usaha penyedia kebutuhan acara?</h3>
                    <p class="mt-3 max-w-[62ch] text-base leading-7 text-neutral-600">
                        Daftar sebagai vendor. Setelah diverifikasi Admin, usaha Anda tampil di pencarian vendor. Transaksi dilakukan langsung dengan penyelenggara, di luar EVLAN.
                    </p>
                    <a
                        href="{{ route('register', ['tipe' => 'vendor']) }}"
                        class="mt-4 inline-flex min-h-11 items-center rounded-control text-sm font-medium text-primary-600 underline-offset-4 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2"
                    >
                        Daftar sebagai vendor
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-sidebar">
        <div class="animasi-scroll mx-auto flex w-full max-w-[72rem] flex-col gap-6 px-5 py-14 sm:px-8 md:flex-row md:items-center md:justify-between md:py-20">
            <div class="min-w-0">
                <h2 class="judul-h2 text-white">Mulai dari acara berikutnya</h2>
                <p class="mt-3 max-w-[52ch] text-base leading-7 text-white">Buat akun, pilih kategori, dan lihat checklist serta timeline acara Anda.</p>
            </div>

            <a
                href="{{ route('register') }}"
                class="inline-flex min-h-11 w-full items-center justify-center rounded-control bg-surface px-5 py-2.5 text-base font-medium text-primary-700 transition-colors hover:bg-neutral-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-sidebar md:w-auto"
            >
                Buat akun gratis
            </a>
        </div>
    </section>
</x-layouts.landing>
