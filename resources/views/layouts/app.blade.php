<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'EVLAN' }} - Event Planner Organizer</title>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-gray-900 bg-gray-50">
    <!-- Aksesibilitas: Lewati ke konten utama -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 z-50 px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold shadow-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500">
        Lewati ke konten utama
    </a>

    <div x-data="{ sidebarOpen: false, moreDrawerOpen: false }" class="min-h-full flex flex-col lg:flex-row">
        <!-- Mobile Sidebar Backdrop -->
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"
            x-on:click="sidebarOpen = false"
            style="display: none;"
        ></div>

        <!-- Sidebar (Desktop & Mobile Drawer) -->
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-gray-200 flex flex-col justify-between transition-transform duration-300 ease-in-out lg:static lg:translate-x-0"
        >
            <div>
                <!-- Brand / Logo -->
                <div class="h-16 flex items-center justify-between px-6 border-b border-gray-100">
                    <a href="{{ route('dashboard', [], false) }}" class="flex items-center gap-3 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 rounded-lg p-1">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-extrabold text-lg shadow-sm">
                            E
                        </div>
                        <div>
                            <span class="text-lg font-black tracking-tight text-gray-900">EVLAN</span>
                            <span class="block text-[10px] font-medium text-gray-400 uppercase tracking-wider -mt-1">Event Planner</span>
                        </div>
                    </a>
                    <!-- Tutup tombol mobile sidebar -->
                    <button
                        type="button"
                        x-on:click="sidebarOpen = false"
                        class="p-2 -mr-2 text-gray-400 hover:text-gray-600 lg:hidden rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 min-h-[40px] min-w-[40px] flex items-center justify-center"
                        aria-label="Tutup navigasi"
                    >
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="p-4 space-y-1" aria-label="Navigasi Utama">
                    <a
                        href="{{ route('dashboard', [], false) }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors min-h-[40px] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 {{ request()->is('dashboard*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    >
                        <x-icon name="circle" class="w-5 h-5 {{ request()->is('dashboard*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                        <span>Dashboard</span>
                    </a>

                    <a
                        href="{{ url('/acara') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors min-h-[40px] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 {{ request()->is('acara*') && !request()->is('acara/buat*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    >
                        <x-icon name="calendar" class="w-5 h-5 {{ request()->is('acara*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                        <span>Acara Saya</span>
                    </a>

                    <a
                        href="{{ url('/tugas-saya') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors min-h-[40px] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 {{ request()->is('tugas*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    >
                        <x-icon name="check-circle" class="w-5 h-5 {{ request()->is('tugas*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                        <span>Tugas Saya</span>
                    </a>

                    <a
                        href="{{ url('/vendor') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors min-h-[40px] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 {{ request()->is('vendor*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    >
                        <x-icon name="briefcase" class="w-5 h-5 {{ request()->is('vendor*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                        <span>Vendor & Jasa</span>
                    </a>

                    <a
                        href="{{ url('/cari-acara') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors min-h-[40px] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 {{ request()->is('cari-acara*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    >
                        <x-icon name="search" class="w-5 h-5 {{ request()->is('cari-acara*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                        <span>Cari Acara</span>
                    </a>

                    <a
                        href="{{ url('/riwayat') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors min-h-[40px] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 {{ request()->is('riwayat*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    >
                        <x-icon name="archive" class="w-5 h-5 {{ request()->is('riwayat*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                        <span>Riwayat Acara</span>
                    </a>

                    <div class="pt-4 mt-4 border-t border-gray-100">
                        <a
                            href="{{ url('/acara/buat/kategori') }}"
                            class="flex items-center justify-center gap-2 w-full px-3 py-2.5 min-h-[40px] bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500 transition-colors"
                        >
                            <x-icon name="plus" class="w-4 h-4" />
                            <span>Buat Acara</span>
                        </a>
                    </div>
                </nav>
            </div>

            <!-- Profile & Account Card at Bottom of Sidebar -->
            <div class="p-4 border-t border-gray-100">
                @auth
                    <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition-colors">
                        <a href="{{ url('/profil') }}" class="flex items-center gap-3 min-w-0 flex-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 rounded-lg p-1">
                            <x-avatar :name="auth()->user()->nama ?? 'Pengguna'" size="sm" />
                            <div class="truncate">
                                <p class="text-xs font-semibold text-gray-900 truncate">{{ auth()->user()->nama ?? 'Pengguna' }}</p>
                                <p class="text-[11px] text-gray-400 truncate">{{ auth()->user()->email ?? '' }}</p>
                            </div>
                        </a>
                        <form method="POST" action="{{ route('logout', [], false) }}">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-red-600 p-2 min-h-[40px] min-w-[40px] flex items-center justify-center rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 transition-colors" title="Keluar" aria-label="Keluar">
                                <x-icon name="log-out" class="w-4 h-4" />
                            </button>
                        </form>
                    </div>
                @else
                    <div class="text-center">
                        <a href="{{ route('login', [], false) }}" class="inline-flex items-center justify-center min-h-[40px] text-sm font-semibold text-indigo-600 hover:underline">Masuk</a>
                    </div>
                @endauth
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden">
            <!-- Topbar Header -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 z-20">
                <div class="flex items-center gap-3">
                    <!-- Mobile Hamburger (untuk membuka sidebar penuh di mobile jika dibutuhkan) -->
                    <button
                        type="button"
                        x-on:click="sidebarOpen = true"
                        class="p-2 -ml-2 text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 min-h-[40px] min-w-[40px] flex items-center justify-center"
                        aria-label="Buka menu navigasi samping"
                    >
                        <x-icon name="menu" class="w-6 h-6" />
                    </button>

                    <!-- Breadcrumbs / Page Title -->
                    <div class="text-sm font-medium text-gray-500 truncate">
                        {{ $breadcrumb ?? 'Beranda' }}
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Notification Bell -->
                    <a href="{{ url('/notifikasi') }}" class="relative p-2 min-h-[40px] min-w-[40px] flex items-center justify-center text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 transition-colors" aria-label="Notifikasi">
                        <x-icon name="bell" class="w-5 h-5" />
                        <span class="absolute top-2 right-2 flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                    </a>

                    <!-- Avatar Profile Menu -->
                    @auth
                        <a href="{{ url('/profil') }}" class="flex items-center gap-2 p-1 min-h-[40px] min-w-[40px] rounded-full focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500" aria-label="Profil Pengguna">
                            <x-avatar :name="auth()->user()->nama ?? 'User'" size="sm" />
                        </a>
                    @endauth
                </div>
            </header>

            <!-- Page Content (pb-24 di mobile agar tidak terpotong oleh bottom bar) -->
            <main id="main-content" class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto pb-24 lg:pb-8 focus:outline-none" tabindex="-1">
                {{ $slot }}
            </main>
        </div>

        <!-- ========================================== -->
        <!-- M-NAV: Mobile Bottom Navigation Bar (T10.1) -->
        <!-- ========================================== -->
        <nav
            class="lg:hidden fixed bottom-0 left-0 right-0 z-30 bg-white border-t border-gray-200 flex items-center justify-around h-16 px-1 shadow-[0_-2px_10px_rgba(0,0,0,0.05)]"
            aria-label="Navigasi Bawah Ponsel"
        >
            <!-- 1. Beranda -->
            <a
                href="{{ route('dashboard', [], false) }}"
                class="flex flex-col items-center justify-center flex-1 h-full min-h-[44px] text-xs font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 {{ request()->is('dashboard*') ? 'text-indigo-600 font-semibold' : 'text-gray-500 hover:text-gray-900' }}"
            >
                <x-icon name="circle" class="w-5 h-5 mb-0.5 {{ request()->is('dashboard*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                <span>Beranda</span>
            </a>

            <!-- 2. Acara -->
            <a
                href="{{ url('/acara') }}"
                class="flex flex-col items-center justify-center flex-1 h-full min-h-[44px] text-xs font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 {{ request()->is('acara*') && !request()->is('acara/buat*') ? 'text-indigo-600 font-semibold' : 'text-gray-500 hover:text-gray-900' }}"
            >
                <x-icon name="calendar" class="w-5 h-5 mb-0.5 {{ request()->is('acara*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                <span>Acara</span>
            </a>

            <!-- 3. Tugas -->
            <a
                href="{{ url('/tugas-saya') }}"
                class="flex flex-col items-center justify-center flex-1 h-full min-h-[44px] text-xs font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 {{ request()->is('tugas*') ? 'text-indigo-600 font-semibold' : 'text-gray-500 hover:text-gray-900' }}"
            >
                <x-icon name="check-circle" class="w-5 h-5 mb-0.5 {{ request()->is('tugas*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                <span>Tugas</span>
            </a>

            <!-- 4. Vendor -->
            <a
                href="{{ url('/vendor') }}"
                class="flex flex-col items-center justify-center flex-1 h-full min-h-[44px] text-xs font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 {{ request()->is('vendor*') ? 'text-indigo-600 font-semibold' : 'text-gray-500 hover:text-gray-900' }}"
            >
                <x-icon name="briefcase" class="w-5 h-5 mb-0.5 {{ request()->is('vendor*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                <span>Vendor</span>
            </a>

            <!-- 5. Lainnya (Drawer Toggle) -->
            <button
                type="button"
                x-on:click="moreDrawerOpen = !moreDrawerOpen"
                class="flex flex-col items-center justify-center flex-1 h-full min-h-[44px] text-xs font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 text-gray-500 hover:text-gray-900"
                aria-label="Menu lainnya"
                :class="{ 'text-indigo-600 font-semibold': moreDrawerOpen }"
            >
                <x-icon name="menu" class="w-5 h-5 mb-0.5 text-gray-400" :class="{ 'text-indigo-600': moreDrawerOpen }" />
                <span>Lainnya</span>
            </button>
        </nav>

        <!-- Drawer Menu 'Lainnya' untuk Mobile -->
        <div
            x-show="moreDrawerOpen"
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"
            x-on:click="moreDrawerOpen = false"
            style="display: none;"
        ></div>

        <div
            x-show="moreDrawerOpen"
            x-transition:enter="transition ease-out duration-250 transform"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="fixed inset-x-0 bottom-16 z-40 bg-white rounded-t-2xl shadow-2xl p-5 border-t border-gray-100 lg:hidden max-h-[80vh] overflow-y-auto"
            style="display: none;"
            @keydown.escape.window="moreDrawerOpen = false"
        >
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-3">
                <span class="text-sm font-bold text-gray-900">Menu Tambahan</span>
                <button
                    type="button"
                    x-on:click="moreDrawerOpen = false"
                    class="text-gray-400 hover:text-gray-600 p-1 min-h-[40px] min-w-[40px] flex items-center justify-center rounded-md"
                    aria-label="Tutup menu"
                >
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            <div class="grid grid-cols-2 gap-2 mb-4">
                <a
                    href="{{ url('/acara/buat/kategori') }}"
                    class="flex items-center gap-2.5 p-3 rounded-xl bg-indigo-50 text-indigo-700 font-semibold text-sm hover:bg-indigo-100 min-h-[44px]"
                >
                    <x-icon name="plus" class="w-5 h-5 text-indigo-600 shrink-0" />
                    <span>Buat Acara</span>
                </a>

                <a
                    href="{{ url('/cari-acara') }}"
                    class="flex items-center gap-2.5 p-3 rounded-xl bg-gray-50 text-gray-700 font-medium text-sm hover:bg-gray-100 min-h-[44px]"
                >
                    <x-icon name="search" class="w-5 h-5 text-gray-500 shrink-0" />
                    <span>Cari Acara</span>
                </a>

                <a
                    href="{{ url('/riwayat') }}"
                    class="flex items-center gap-2.5 p-3 rounded-xl bg-gray-50 text-gray-700 font-medium text-sm hover:bg-gray-100 min-h-[44px]"
                >
                    <x-icon name="archive" class="w-5 h-5 text-gray-500 shrink-0" />
                    <span>Riwayat Acara</span>
                </a>

                <a
                    href="{{ url('/notifikasi') }}"
                    class="flex items-center gap-2.5 p-3 rounded-xl bg-gray-50 text-gray-700 font-medium text-sm hover:bg-gray-100 min-h-[44px]"
                >
                    <x-icon name="bell" class="w-5 h-5 text-gray-500 shrink-0" />
                    <span>Notifikasi</span>
                </a>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                @auth
                    <a href="{{ url('/profil') }}" class="flex items-center gap-3 py-1">
                        <x-avatar :name="auth()->user()->nama ?? 'Pengguna'" size="sm" />
                        <div class="text-left">
                            <p class="text-xs font-bold text-gray-900">{{ auth()->user()->nama ?? 'Pengguna' }}</p>
                            <span class="text-[11px] text-indigo-600">Lihat Profil</span>
                        </div>
                    </a>
                    <form method="POST" action="{{ route('logout', [], false) }}">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 rounded-lg min-h-[40px] flex items-center">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="w-full py-2.5 text-center text-sm font-semibold text-indigo-600 bg-indigo-50 rounded-xl min-h-[44px] flex items-center justify-center">
                        Masuk ke Akun
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <x-toast />
</body>
</html>
