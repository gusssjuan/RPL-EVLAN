<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EVLAN - Aplikasi Perencanaan dan Manajemen Acara Terpadu</title>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased text-gray-900 bg-white selection:bg-indigo-500 selection:text-white flex flex-col">
    <!-- Aksesibilitas: Lewati ke konten utama -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 z-50 px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold shadow-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500">
        Lewati ke konten utama
    </a>

    <!-- Navbar Top -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 rounded-lg p-1">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-extrabold text-lg shadow-sm">
                        E
                    </div>
                    <span class="text-xl font-black tracking-tight text-gray-900">EVLAN</span>
                </a>

                <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-600" aria-label="Navigasi Publik">
                    <a href="#fitur" class="hover:text-indigo-600 transition-colors">Fitur</a>
                    <a href="#cara-kerja" class="hover:text-indigo-600 transition-colors">Cara Kerja</a>
                    <a href="{{ route('cari-acara.index') }}" class="hover:text-indigo-600 transition-colors">Cari Acara</a>
                    <a href="{{ route('vendor.cari.index') }}" class="hover:text-indigo-600 transition-colors">Katalog Vendor</a>
                </nav>
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <a
                        href="{{ route('dashboard') }}"
                        class="inline-flex items-center justify-center min-h-[40px] px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500 transition-colors"
                    >
                        Masuk ke Dashboard
                    </a>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex items-center justify-center min-h-[40px] px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 transition-colors"
                    >
                        Masuk
                    </a>
                    <a
                        href="{{ route('register') }}"
                        class="inline-flex items-center justify-center min-h-[40px] px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500 transition-colors"
                    >
                        Daftar Akun
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main id="main-content" class="flex-1 focus:outline-none" tabindex="-1">
        <!-- Hero Section (S01, hal. 3) -->
        <section class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-28 bg-gradient-to-b from-indigo-50/50 via-white to-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-3xl mx-auto">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-100/70 text-indigo-700 text-xs font-semibold mb-6">
                        <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                        Platform Manajemen Acara Terstruktur #1 di Indonesia
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-gray-900 tracking-tight leading-tight sm:leading-tight mb-6">
                        Rencanakan Acara Lebih <span class="text-indigo-600">Mudah & Terstruktur</span>
                    </h1>

                    <p class="text-lg sm:text-xl text-gray-600 leading-relaxed mb-8">
                        Aplikasi all-in-one untuk kepanitiaan Anda. Template checklist otomatis, jadwal timeline H-n instan, pembagian tugas, estimasi anggaran, hingga pendaftaran peserta.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a
                            href="{{ auth()->check() ? url('/acara/buat/kategori') : route('register') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center min-h-[48px] px-6 py-3 text-base font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md hover:shadow-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500 transition-all"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Mulai Rencanakan Acara
                        </a>

                        <a
                            href="{{ route('cari-acara.index') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center min-h-[48px] px-6 py-3 text-base font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 transition-all"
                        >
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                            Jelajahi Acara Publik
                        </a>
                    </div>
                </div>

                <!-- Preview Card Mockup -->
                <div class="mt-12 lg:mt-16 max-w-4xl mx-auto rounded-2xl bg-white p-4 sm:p-6 shadow-xl border border-gray-200/80">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-red-400"></span>
                            <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                            <span class="w-3 h-3 rounded-full bg-green-400"></span>
                            <span class="text-xs font-semibold text-gray-400 ml-2">Dashboard Perencanaan Acara EVLAN</span>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 bg-green-50 text-green-700 rounded-full">Status: Perencanaan Aktif</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                            <span class="text-xs font-medium text-gray-500">Timeline Hari H</span>
                            <p class="text-2xl font-black text-indigo-600 mt-1">H-14</p>
                            <p class="text-xs text-gray-500 mt-1">6 dari 8 agenda timeline selesai</p>
                        </div>
                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                            <span class="text-xs font-medium text-gray-500">Checklist Kebutuhan</span>
                            <p class="text-2xl font-black text-gray-900 mt-1">11 / 14</p>
                            <p class="text-xs text-green-600 mt-1 font-medium">78% Kebutuhan Terpenuhi</p>
                        </div>
                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                            <span class="text-xs font-medium text-gray-500">Estimasi Anggaran</span>
                            <p class="text-2xl font-black text-gray-900 mt-1">Rp 18.450.000</p>
                            <p class="text-xs text-gray-500 mt-1">11 item anggaran tercatat</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Fitur Unggulan Section -->
        <section id="fitur" class="py-16 sm:py-24 bg-white border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Solusi Lengkap</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight mt-2">
                        Semua yang Dibutuhkan Panitia Acara
                    </h2>
                    <p class="text-base text-gray-500 mt-3">
                        Dari ide pertama hingga hari pelaksanaan, EVLAN memastikan setiap detail acara tercatat dan terkontrol.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Fitur 1 -->
                    <div class="p-6 rounded-2xl bg-gray-50 hover:bg-white border border-gray-100 hover:border-gray-200 hover:shadow-md transition-all">
                        <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Template Otomatis</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Cukup pilih kategori acara (seminar, konser, workshop), sistem langsung menyusun checklist kebutuhan dan jadwal timeline H-n.
                        </p>
                    </div>

                    <!-- Fitur 2 -->
                    <div class="p-6 rounded-2xl bg-gray-50 hover:bg-white border border-gray-100 hover:border-gray-200 hover:shadow-md transition-all">
                        <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.199l-.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Struktur & Tugas Tim</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Bagi divisi kepanitiaan, tugaskan jobdesk dengan tenggat waktu, dan pantau status pengerjaan secara kanban.
                        </p>
                    </div>

                    <!-- Fitur 3 -->
                    <div class="p-6 rounded-2xl bg-gray-50 hover:bg-white border border-gray-100 hover:border-gray-200 hover:shadow-md transition-all">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Anggaran & Vendor</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Hitung estimasi pengeluaran secara real-time dan hubungkan kebutuhan dengan penyedia vendor terverifikasi.
                        </p>
                    </div>

                    <!-- Fitur 4 -->
                    <div class="p-6 rounded-2xl bg-gray-50 hover:bg-white border border-gray-100 hover:border-gray-200 hover:shadow-md transition-all">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Pendaftaran Terbuka</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Buka acara untuk umum, pantau kuota secara otomatis, dan kelola konfirmasi pendaftar dengan notifikasi langsung.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Cara Kerja Section -->
        <section id="cara-kerja" class="py-16 sm:py-24 bg-gray-50 border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Langkah Sederhana</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight mt-2">
                        Cara Kerja EVLAN
                    </h2>
                    <p class="text-base text-gray-500 mt-3">
                        Tiga langkah praktis menyiapkan acara Anda dari nol sampai sukses terlaksana.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 relative">
                        <span class="text-4xl font-black text-indigo-100 absolute top-6 right-6">01</span>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Pilih Kategori & Template</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Tentukan jenis acara Anda. EVLAN menyediakan template teruji lengkap dengan daftar barang dan timeline mundur.
                        </p>
                    </div>

                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 relative">
                        <span class="text-4xl font-black text-indigo-100 absolute top-6 right-6">02</span>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Bentuk Panitia & Bagi Tugas</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Undang anggota via email, kelompokkan ke divisi kerja, dan bagikan tanggung jawab jobdesk secara spesifik.
                        </p>
                    </div>

                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 relative">
                        <span class="text-4xl font-black text-indigo-100 absolute top-6 right-6">03</span>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Monitoring & Evaluasi</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Pantau kemajuan persiapan lewat dashboard visual, terima reminder tenggat, dan tutup acara dengan evaluasi akhir.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action Banner -->
        <section class="py-16 sm:py-20 bg-indigo-600 text-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight mb-4">
                    Siap Mengatur Acara Anda dengan Lebih Teratur?
                </h2>
                <p class="text-base sm:text-lg text-indigo-100 max-w-2xl mx-auto mb-8">
                    Bergabunglah bersama ribuan panitia lainnya. Buat acara pertama Anda sekarang dalam hitungan menit.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a
                        href="{{ route('register') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center min-h-[48px] px-8 py-3 text-base font-bold text-indigo-700 bg-white hover:bg-indigo-50 rounded-xl shadow-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white transition-all"
                    >
                        Daftar Gratis Sekarang
                    </a>
                    <a
                        href="{{ route('cari-acara.index') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center min-h-[48px] px-8 py-3 text-base font-semibold text-white bg-indigo-700 hover:bg-indigo-800 rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white transition-all"
                    >
                        Lihat Acara Terbuka
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 pb-8 border-b border-gray-800">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500 flex items-center justify-center text-white font-extrabold text-lg">
                        E
                    </div>
                    <span class="text-xl font-black tracking-tight text-white">EVLAN</span>
                    <span class="text-xs text-gray-500 ml-2">Event Planner & Organizer</span>
                </div>

                <div class="flex items-center gap-6 text-sm">
                    <a href="{{ route('cari-acara.index') }}" class="hover:text-white transition-colors">Cari Acara</a>
                    <a href="{{ route('vendor.cari.index') }}" class="hover:text-white transition-colors">Katalog Vendor</a>
                    <a href="{{ route('login') }}" class="hover:text-white transition-colors">Masuk</a>
                    <a href="{{ route('register') }}" class="hover:text-white transition-colors">Daftar</a>
                </div>
            </div>

            <div class="pt-8 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} EVLAN. Seluruh hak cipta dilindungi. Zona Waktu Asia/Makassar (WITA).
            </div>
        </div>
    </footer>
</body>
</html>
