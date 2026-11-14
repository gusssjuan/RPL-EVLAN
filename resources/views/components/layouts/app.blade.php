<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/logo/purple.svg') }}" type="image/svg">
    <title>{{ $title ?? 'EVLAN' }} - Event Planner Organizer</title>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-gray-900 bg-gray-50">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 focus:px-4 focus:py-2 focus:bg-white focus:text-indigo-700 focus:rounded-[8px] focus:shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
        Lewati ke konten utama
    </a>
    <div x-data="{ sidebarOpen: false }" class="min-h-full flex flex-col lg:flex-row">
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

        <!-- Sidebar (Desktop & Mobile) -->
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 left-0 z-40 w-60 bg-white border-r border-gray-200 flex flex-col justify-between transition-transform duration-300 ease-in-out lg:static lg:translate-x-0"
        >
            <div>
                <!-- Brand / Logo -->
                <div class="h-16 flex items-center px-6 border-b border-gray-100 gap-3">
                   <a href="{{ url('/') }}" class="inline-flex shrink-0 items-center rounded-control focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2">
                        <x-logo variant="purple" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="p-4 space-y-1">
                    <a
                        href="{{ route('dashboard', [], false) }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-[8px] text-sm font-medium transition-colors {{ request()->is('dashboard*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    >
                        <x-icon name="circle" class="w-5 h-5 {{ request()->is('dashboard*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                        <span>Dashboard</span>
                    </a>

                    <a
                        href="{{ url('/acara') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-[8px] text-sm font-medium transition-colors {{ request()->is('acara*') && !request()->is('acara/buat*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    >
                        <x-icon name="calendar" class="w-5 h-5 {{ request()->is('acara*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                        <span>Acara Saya</span>
                    </a>

                    <a
                        href="{{ url('/tugas-saya') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-[8px] text-sm font-medium transition-colors {{ request()->is('tugas*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    >
                        <x-icon name="check-circle" class="w-5 h-5 {{ request()->is('tugas*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                        <span>Tugas Saya</span>
                    </a>

                    <a
                        href="{{ url('/vendor') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-[8px] text-sm font-medium transition-colors {{ request()->is('vendor*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    >
                        <x-icon name="briefcase" class="w-5 h-5 {{ request()->is('vendor*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                        <span>Vendor & Jasa</span>
                    </a>

                    <a
                        href="{{ url('/cari-acara') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-[8px] text-sm font-medium transition-colors {{ request()->is('cari-acara*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    >
                        <x-icon name="search" class="w-5 h-5 {{ request()->is('cari-acara*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                        <span>Cari Acara</span>
                    </a>

                    <a
                        href="{{ url('/riwayat') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-[8px] text-sm font-medium transition-colors {{ request()->is('riwayat*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    >
                        <x-icon name="archive" class="w-5 h-5 {{ request()->is('riwayat*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                        <span>Riwayat Acara</span>
                    </a>

                    <div class="pt-4 mt-4 border-t border-gray-100">
                        <a
                            href="{{ url('/acara/buat/kategori') }}"
                            class="flex items-center justify-center gap-2 w-full px-3 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-sm font-semibold shadow-sm transition-colors"
                        >
                            <x-icon name="plus" class="w-4 h-4" />
                            <span>Buat Acara</span>
                        </a>
                    </div>
                </nav>
            </div>

            <!-- Profile & Account Card at Bottom -->
            <div class="p-4 border-t border-gray-100">
                @auth
                    <div class="flex items-center justify-between p-2 rounded-[8px] hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <x-avatar :name="auth()->user()->nama ?? 'Pengguna'" size="sm" />
                            <div class="truncate">
                                <p class="text-xs font-semibold text-gray-900 truncate">{{ auth()->user()->nama ?? 'Pengguna' }}</p>
                                <p class="text-[11px] text-gray-400 truncate">{{ auth()->user()->email ?? '' }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout', [], false) }}">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-red-600 p-1 rounded-md transition-colors" title="Keluar">
                                <x-icon name="log-out" class="w-4 h-4" />
                            </button>
                        </form>
                    </div>
                @else
                    <div class="text-center">
                        <a href="{{ route('login', [], false) }}" class="text-sm font-semibold text-indigo-600 hover:underline">Masuk</a>
                    </div>
                @endauth
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Topbar Header -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 z-20">
                <div class="flex items-center gap-3">
                    <!-- Mobile Hamburger -->
                    <button
                        type="button"
                        x-on:click="sidebarOpen = true"
                        class="p-2 -ml-2 text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        <span class="sr-only">Buka menu</span>
                        <x-icon name="menu" class="w-6 h-6" />
                    </button>

                    <!-- Breadcrumbs / Page Title -->
                    <div class="text-sm font-medium text-gray-500">
                        {{ $breadcrumb ?? 'Beranda' }}
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Notification Bell -->
                    @php($jumlahNotif = $jumlahNotifikasiBelumDibaca ?? 0)
                    <a
                        href="{{ route('notifikasi.index') }}"
                        aria-label="Notifikasi, {{ $jumlahNotif }} belum dibaca"
                        class="relative p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 transition-colors"
                    >
                        <x-icon name="bell" class="w-5 h-5" />
                        @if ($jumlahNotif > 0)
                            <span class="absolute -top-1 -right-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">
                                {{ $jumlahNotif > 99 ? '99+' : $jumlahNotif }}
                            </span>
                        @endif
                    </a>

                    <!-- Avatar Profile Menu -->
                    @auth
                        <a href="{{ url('/profil') }}" class="flex items-center gap-2">
                            <x-avatar :name="auth()->user()->nama ?? 'User'" size="sm" />
                        </a>
                    @endauth
                </div>
            </header>

            <!-- Page Content -->
            <main id="main-content" class="flex-1 p-4 sm:p-6 lg:p-8 pb-24 lg:pb-8 max-w-7xl w-full mx-auto">
                {{ $slot }}
            </main>
        </div>
    </div>

    @auth
        <nav class="fixed bottom-0 inset-x-0 z-30 bg-white border-t border-gray-200 lg:hidden" aria-label="Navigasi utama ponsel">
            <p class="sr-only">Navigasi Bawah Ponsel</p>
            <div class="grid grid-cols-5">
                <a href="{{ route('dashboard', [], false) }}" class="flex flex-col items-center justify-center gap-0.5 py-2.5 text-[10px] font-medium {{ request()->is('dashboard*') ? 'text-indigo-700' : 'text-gray-500' }}">
                    <x-icon name="circle" class="w-5 h-5" />
                    <span>Beranda</span>
                </a>
                <a href="{{ url('/acara') }}" class="flex flex-col items-center justify-center gap-0.5 py-2.5 text-[10px] font-medium {{ request()->is('acara*') && ! request()->is('acara/buat*') ? 'text-indigo-700' : 'text-gray-500' }}">
                    <x-icon name="calendar" class="w-5 h-5" />
                    <span>Acara</span>
                </a>
                <a href="{{ url('/tugas-saya') }}" class="flex flex-col items-center justify-center gap-0.5 py-2.5 text-[10px] font-medium {{ request()->is('tugas*') ? 'text-indigo-700' : 'text-gray-500' }}">
                    <x-icon name="check-circle" class="w-5 h-5" />
                    <span>Tugas</span>
                </a>
                <a href="{{ url('/vendor') }}" class="flex flex-col items-center justify-center gap-0.5 py-2.5 text-[10px] font-medium {{ request()->is('vendor*') ? 'text-indigo-700' : 'text-gray-500' }}">
                    <x-icon name="briefcase" class="w-5 h-5" />
                    <span>Vendor</span>
                </a>
                <a href="{{ url('/profil') }}" class="flex flex-col items-center justify-center gap-0.5 py-2.5 text-[10px] font-medium {{ request()->is('profil*') ? 'text-indigo-700' : 'text-gray-500' }}">
                    <x-icon name="settings" class="w-5 h-5" />
                    <span>Lainnya</span>
                </a>
            </div>
        </nav>
    @endauth

    <x-toast />
</body>
</html>

