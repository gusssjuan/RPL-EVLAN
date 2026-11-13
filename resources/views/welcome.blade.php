<x-layouts.landing title="EVLAN - Perencanaan acara dari H-60 sampai hari H">
    <section class="bg-neutral-50">
        <div class="mx-auto w-full max-w-[72rem] px-5 py-14 sm:px-8 md:py-20">
            <div class="grid items-center gap-10 lg:grid-cols-12 lg:gap-12">
                <div class="lg:col-span-5">
                    <h1 class="judul-h1 text-neutral-900">
                        Dari H-60 sampai hari H, semua panitia tahu tugasnya.
                    </h1>

                    <p class="mt-5 max-w-[62ch] text-base leading-7 text-neutral-600">
                        EVLAN menyusun checklist, timeline, dan pembagian tugas acara dari template kategori. Anda mengisi tanggal dan mengundang panitia; sisanya sudah tertata.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-5">
                        <a
                            href="{{ route('register') }}"
                            class="inline-flex min-h-11 items-center justify-center rounded-control bg-primary-600 px-5 py-2.5 text-base font-medium text-white transition-colors hover:bg-primary-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2"
                        >
                            Buat akun gratis
                        </a>

                        <p class="text-sm text-neutral-600">
                            Sudah punya akun?
                            <a href="{{ route('login') }}" class="font-medium text-primary-600 underline-offset-4 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2">
                                Masuk
                            </a>
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-7">
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
                            class="h-auto w-full rounded-panel border border-neutral-200"
                        >
                    </picture>
                </div>
            </div>
        </div>
    </section>

    <section class="border-t border-neutral-200 bg-surface">
        <div class="mx-auto grid w-full max-w-[72rem] gap-8 px-5 py-14 sm:px-8 md:py-20 lg:grid-cols-12 lg:gap-12">
            <div class="lg:col-span-4">
                <h2 class="judul-h2 text-neutral-900">Persiapan acara sering tercecer di banyak tempat</h2>
            </div>

            <div class="grid gap-8 sm:grid-cols-2 sm:gap-0 sm:divide-x sm:divide-neutral-200 lg:col-span-8">
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

    <section id="cara-kerja" class="scroll-mt-20 border-t border-neutral-200 bg-neutral-50">
        <div class="mx-auto w-full max-w-[72rem] px-5 py-14 sm:px-8 md:py-20">
            <h2 class="judul-h2 text-neutral-900">Cara kerjanya</h2>

            <ol class="mt-10 max-w-[45rem]">
                <li class="flex gap-5 pb-10 last:pb-0 sm:gap-7">
                    <div class="flex flex-col items-center">
                        <span class="font-display text-[2rem] font-semibold leading-[2.25rem] tabular-nums text-neutral-900 sm:text-[2.5rem] sm:leading-[2.75rem]">01</span>
                        <span class="mt-3 w-0.5 flex-1 bg-neutral-200" aria-hidden="true"></span>
                    </div>
                    <div class="pt-1">
                        <h3 class="font-display text-lg font-semibold text-neutral-900">Pilih kategori acara</h3>
                        <p class="mt-2 max-w-[62ch] text-base leading-7 text-neutral-600">
                            Seminar, wedding, 17 Agustus, ulang tahun, atau gathering. Kebutuhan, timeline, dan struktur panitia untuk kategori itu langsung terlihat.
                        </p>
                    </div>
                </li>

                <li class="flex gap-5 pb-10 last:pb-0 sm:gap-7">
                    <div class="flex flex-col items-center">
                        <span class="font-display text-[2rem] font-semibold leading-[2.25rem] tabular-nums text-neutral-900 sm:text-[2.5rem] sm:leading-[2.75rem]">02</span>
                        <span class="mt-3 w-0.5 flex-1 bg-neutral-200" aria-hidden="true"></span>
                    </div>
                    <div class="pt-1">
                        <h3 class="font-display text-lg font-semibold text-neutral-900">Isi detail acara</h3>
                        <p class="mt-2 max-w-[62ch] text-base leading-7 text-neutral-600">
                            Nama, tanggal, lokasi, dan jumlah peserta. Checklist dan timeline H-n dihitung dari tanggal acara.
                        </p>
                    </div>
                </li>

                <li class="flex gap-5 pb-10 last:pb-0 sm:gap-7">
                    <div class="flex flex-col items-center">
                        <span class="font-display text-[2rem] font-semibold leading-[2.25rem] tabular-nums text-neutral-900 sm:text-[2.5rem] sm:leading-[2.75rem]">03</span>
                        <span class="mt-3 w-0.5 flex-1 bg-neutral-200" aria-hidden="true"></span>
                    </div>
                    <div class="pt-1">
                        <h3 class="font-display text-lg font-semibold text-neutral-900">Bentuk kepanitiaan</h3>
                        <p class="mt-2 max-w-[62ch] text-base leading-7 text-neutral-600">
                            Tambah divisi, undang anggota lewat email, lalu bagikan tugas lengkap dengan deadline.
                        </p>
                    </div>
                </li>

                <li class="flex gap-5 pb-10 last:pb-0 sm:gap-7">
                    <div class="flex flex-col items-center">
                        <span class="font-display text-[2rem] font-semibold leading-[2.25rem] tabular-nums text-neutral-900 sm:text-[2.5rem] sm:leading-[2.75rem]">04</span>
                        <span class="mt-3 w-0.5 flex-1 bg-neutral-200" aria-hidden="true"></span>
                    </div>
                    <div class="pt-1">
                        <h3 class="font-display text-lg font-semibold text-neutral-900">Pantau progres</h3>
                        <p class="mt-2 max-w-[62ch] text-base leading-7 text-neutral-600">
                            Anggota memperbarui status tugasnya. Ketua melihat progres tiap divisi dan tugas yang sudah lewat deadline.
                        </p>
                    </div>
                </li>

                <li class="flex gap-5 pb-10 last:pb-0 sm:gap-7">
                    <div class="flex flex-col items-center">
                        <span class="font-display text-[2rem] font-semibold leading-[2.25rem] tabular-nums text-neutral-900 sm:text-[2.5rem] sm:leading-[2.75rem]">05</span>
                    </div>
                    <div class="pt-1">
                        <h3 class="font-display text-lg font-semibold text-neutral-900">Atur anggaran dan cari vendor</h3>
                        <p class="mt-2 max-w-[62ch] text-base leading-7 text-neutral-600">
                            Hitung estimasi biaya dari jumlah dan harga satuan, lalu lihat vendor terverifikasi untuk catering, dekorasi, dokumentasi, dan sound system.
                        </p>
                    </div>
                </li>
            </ol>
        </div>
    </section>

    <section id="contoh-hasil" class="scroll-mt-20 border-t border-neutral-200 bg-surface">
        <div class="mx-auto grid w-full max-w-[72rem] gap-10 px-5 py-14 sm:px-8 md:py-20 lg:grid-cols-12 lg:gap-12">
            <div class="lg:col-span-4">
                <h2 class="judul-h2 text-neutral-900">Contoh timeline untuk seminar pada 12 Desember 2026</h2>
                <p class="mt-4 max-w-[62ch] text-base leading-7 text-neutral-600">Dihitung otomatis dari tanggal acara. Ini data contoh.</p>
            </div>

            <ol class="lg:col-span-8">
                <li class="border-b border-neutral-200 py-4 first:pt-0">
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 sm:grid sm:grid-cols-[5.5rem_9.5rem_1fr] sm:items-center">
                        <span class="inline-flex w-fit items-center rounded-full bg-aksen-500 px-2.5 py-1 text-sm font-semibold text-neutral-900">H-60</span>
                        <time datetime="2026-10-13" class="text-sm tabular-nums text-neutral-600">Sel, 13 Okt 2026</time>
                        <span class="w-full text-base text-neutral-900 sm:w-auto">Membuat konsep dan proposal</span>
                    </div>
                </li>
                <li class="border-b border-neutral-200 py-4">
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 sm:grid sm:grid-cols-[5.5rem_9.5rem_1fr] sm:items-center">
                        <span class="inline-flex w-fit items-center rounded-full bg-aksen-500 px-2.5 py-1 text-sm font-semibold text-neutral-900">H-45</span>
                        <time datetime="2026-10-28" class="text-sm tabular-nums text-neutral-600">Rab, 28 Okt 2026</time>
                        <span class="w-full text-base text-neutral-900 sm:w-auto">Konfirmasi narasumber dan moderator</span>
                    </div>
                </li>
                <li class="border-b border-neutral-200 py-4">
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 sm:grid sm:grid-cols-[5.5rem_9.5rem_1fr] sm:items-center">
                        <span class="inline-flex w-fit items-center rounded-full bg-aksen-500 px-2.5 py-1 text-sm font-semibold text-neutral-900">H-30</span>
                        <time datetime="2026-11-12" class="text-sm tabular-nums text-neutral-600">Kam, 12 Nov 2026</time>
                        <span class="w-full text-base text-neutral-900 sm:w-auto">Publikasi dan persiapan vendor</span>
                    </div>
                </li>
                <li class="border-b border-neutral-200 py-4">
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 sm:grid sm:grid-cols-[5.5rem_9.5rem_1fr] sm:items-center">
                        <span class="inline-flex w-fit items-center rounded-full bg-aksen-500 px-2.5 py-1 text-sm font-semibold text-neutral-900">H-7</span>
                        <time datetime="2026-12-05" class="text-sm tabular-nums text-neutral-600">Sab, 5 Des 2026</time>
                        <span class="w-full text-base text-neutral-900 sm:w-auto">Final checking</span>
                    </div>
                </li>
                <li class="border-b border-neutral-200 py-4">
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 sm:grid sm:grid-cols-[5.5rem_9.5rem_1fr] sm:items-center">
                        <span class="inline-flex w-fit items-center rounded-full bg-aksen-500 px-2.5 py-1 text-sm font-semibold text-neutral-900">H-3</span>
                        <time datetime="2026-12-09" class="text-sm tabular-nums text-neutral-600">Rab, 9 Des 2026</time>
                        <span class="w-full text-base text-neutral-900 sm:w-auto">Gladi bersih</span>
                    </div>
                </li>
                <li class="border-b border-neutral-200 py-4">
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 sm:grid sm:grid-cols-[5.5rem_9.5rem_1fr] sm:items-center">
                        <span class="inline-flex w-fit items-center rounded-full bg-aksen-500 px-2.5 py-1 text-sm font-semibold text-neutral-900">H-1</span>
                        <time datetime="2026-12-11" class="text-sm tabular-nums text-neutral-600">Jum, 11 Des 2026</time>
                        <span class="w-full text-base text-neutral-900 sm:w-auto">Persiapan lokasi</span>
                    </div>
                </li>
                <li class="py-4 last:pb-0">
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 sm:grid sm:grid-cols-[5.5rem_9.5rem_1fr] sm:items-center">
                        <span class="inline-flex w-fit items-center rounded-full bg-aksen-500 px-2.5 py-1 text-sm font-semibold text-neutral-900">Hari H</span>
                        <time datetime="2026-12-12" class="text-sm tabular-nums text-neutral-600">Sab, 12 Des 2026</time>
                        <span class="w-full text-base text-neutral-900 sm:w-auto">Pelaksanaan acara</span>
                    </div>
                </li>
            </ol>
        </div>
    </section>

    <section id="tujuan" class="scroll-mt-20 border-t border-neutral-200 bg-neutral-50">
        <div class="mx-auto w-full max-w-[72rem] px-5 py-14 sm:px-8 md:py-20">
            <h2 class="judul-h2 text-neutral-900">Yang dibantu EVLAN</h2>

            <div class="mt-10 grid gap-10 lg:grid-cols-12 lg:items-start lg:gap-12">
                <div class="lg:col-span-5">
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

                <ol class="space-y-5 lg:col-span-7">
                    <li class="grid grid-cols-[2rem_1fr] gap-3">
                        <span class="font-display text-lg font-semibold tabular-nums text-primary-700">1</span>
                        <p class="text-base leading-7 text-neutral-900">Merancang acara berdasarkan kategori dan kebutuhan yang sesuai.</p>
                    </li>
                    <li class="grid grid-cols-[2rem_1fr] gap-3">
                        <span class="font-display text-lg font-semibold tabular-nums text-primary-700">2</span>
                        <p class="text-base leading-7 text-neutral-900">Memakai template perencanaan, jadi tidak perlu membuat rencana dari awal.</p>
                    </li>
                    <li class="grid grid-cols-[2rem_1fr] gap-3">
                        <span class="font-display text-lg font-semibold tabular-nums text-primary-700">3</span>
                        <p class="text-base leading-7 text-neutral-900">Membagi jobdesk ke tiap divisi atau anggota panitia.</p>
                    </li>
                    <li class="grid grid-cols-[2rem_1fr] gap-3">
                        <span class="font-display text-lg font-semibold tabular-nums text-primary-700">4</span>
                        <p class="text-base leading-7 text-neutral-900">Memantau progres pekerjaan lewat status tugas.</p>
                    </li>
                    <li class="grid grid-cols-[2rem_1fr] gap-3">
                        <span class="font-display text-lg font-semibold tabular-nums text-primary-700">5</span>
                        <p class="text-base leading-7 text-neutral-900">Melihat vendor yang menyediakan kebutuhan acara.</p>
                    </li>
                    <li class="grid grid-cols-[2rem_1fr] gap-3">
                        <span class="font-display text-lg font-semibold tabular-nums text-primary-700">6</span>
                        <p class="text-base leading-7 text-neutral-900">Memperkirakan anggaran dan menyiapkan kebutuhan acara.</p>
                    </li>
                </ol>
            </div>
        </div>
    </section>

    <section id="untuk-siapa" class="scroll-mt-20 border-t border-neutral-200 bg-surface">
        <div class="mx-auto w-full max-w-[72rem] px-5 py-14 sm:px-8 md:py-20">
            <h2 class="judul-h2 text-neutral-900">Untuk siapa</h2>

            <div class="mt-8 grid gap-10 lg:grid-cols-2 lg:gap-12">
                <ul class="list-disc space-y-3 pl-5 text-base leading-7 text-neutral-900 marker:text-neutral-400">
                    <li>Mahasiswa dan organisasi kampus</li>
                    <li>Komunitas</li>
                    <li>Event organizer pemula</li>
                    <li>Perusahaan atau institusi penyelenggara kegiatan</li>
                    <li>Siapa saja yang ingin membuat acara</li>
                </ul>

                <div class="border-t border-neutral-200 pt-8 lg:border-l lg:border-t-0 lg:pl-12 lg:pt-0">
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
        <div class="mx-auto flex w-full max-w-[72rem] flex-col gap-6 px-5 py-14 sm:px-8 md:flex-row md:items-center md:justify-between md:py-20">
            <div>
                <h2 class="judul-h2 text-white">Mulai dari acara berikutnya</h2>
                <p class="mt-3 max-w-[52ch] text-base leading-7 text-white/80">Buat akun, pilih kategori, dan lihat checklist serta timeline acara Anda.</p>
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
