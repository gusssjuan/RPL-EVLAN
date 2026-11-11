<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin Panel' }} - EVLAN</title>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-gray-900 bg-gray-50">
    <!-- Aksesibilitas: Lewati ke konten utama -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 z-50 px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold shadow-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500">
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

        <!-- Sidebar -->
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 left-0 z-40 w-60 bg-gray-900 text-white flex flex-col justify-between transition-transform duration-300 ease-in-out lg:static lg:translate-x-0"
        >
            <div>
                <!-- Brand / Logo -->
                <div class="h-16 flex items-center px-6 border-b border-gray-800 gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500 flex items-center justify-center text-white font-extrabold text-lg shadow-sm">
                        E
                    </div>
                    <div>
                        <span class="text-lg font-black tracking-tight text-white">EVLAN</span>
                        <span class="block text-[10px] font-medium text-indigo-400 uppercase tracking-wider -mt-1">Administrator</span>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="p-4 space-y-1">
                    <a
                        href="{{ url('/admin') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-[8px] text-sm font-medium transition-colors {{ request()->is('admin') ? 'bg-indigo-600 text-white font-semibold' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    >
                        <x-icon name="circle" class="w-5 h-5" />
                        <span>Dashboard</span>
                    </a>

                    <a
                        href="{{ url('/admin/kategori') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-[8px] text-sm font-medium transition-colors {{ request()->is('admin/kategori*') ? 'bg-indigo-600 text-white font-semibold' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    >
                        <x-icon name="calendar" class="w-5 h-5" />
                        <span>Kelola Kategori</span>
                    </a>

                    <a
                        href="{{ url('/admin/template') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-[8px] text-sm font-medium transition-colors {{ request()->is('admin/template*') ? 'bg-indigo-600 text-white font-semibold' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    >
                        <x-icon name="edit" class="w-5 h-5" />
                        <span>Kelola Template</span>
                    </a>

                    <a
                        href="{{ url('/admin/verifikasi-vendor') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-[8px] text-sm font-medium transition-colors {{ request()->is('admin/verifikasi-vendor*') ? 'bg-indigo-600 text-white font-semibold' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    >
                        <x-icon name="briefcase" class="w-5 h-5" />
                        <span>Verifikasi Vendor</span>
                    </a>

                    <a
                        href="{{ url('/admin/pengguna') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-[8px] text-sm font-medium transition-colors {{ request()->is('admin/pengguna*') ? 'bg-indigo-600 text-white font-semibold' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    >
                        <x-icon name="users" class="w-5 h-5" />
                        <span>Kelola Pengguna</span>
                    </a>

                    <a
                        href="{{ route('notifikasi.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-[8px] text-sm font-medium transition-colors {{ request()->is('notifikasi*') ? 'bg-indigo-600 text-white font-semibold' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    >
                        <x-icon name="bell" class="w-5 h-5" />
                        <span>Notifikasi</span>
                        @if (($jumlahNotifikasiBelumDibaca ?? 0) > 0)
                            <span class="ml-auto flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">
                                {{ $jumlahNotifikasiBelumDibaca > 99 ? '99+' : $jumlahNotifikasiBelumDibaca }}
                            </span>
                        @endif
                    </a>
                </nav>
            </div>

            <!-- Profile & Logout -->
            <div class="p-4 border-t border-gray-800">
                <form method="POST" action="{{ route('logout', [], false) }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-between p-2 rounded-[8px] hover:bg-gray-800 text-gray-300 hover:text-red-400 transition-colors">
                        <span class="text-sm font-medium">Keluar Admin</span>
                        <x-icon name="logout" class="w-4 h-4" />
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 z-20">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        x-on:click="sidebarOpen = true"
                        class="p-2 -ml-2 text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        <span class="sr-only">Buka menu admin</span>
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <div class="text-sm font-semibold text-gray-800">
                        {{ $title ?? 'Admin Area' }}
                    </div>
                </div>

                <div class="flex items-center gap-3 text-sm text-gray-600">
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
                    <x-avatar :name="auth()->user()->nama ?? 'Admin EVLAN'" size="sm" />
                    <span class="font-medium hidden sm:inline">{{ auth()->user()->nama ?? 'Administrator' }}</span>
                </div>
            </header>

            <main id="main-content" class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto focus:outline-none" tabindex="-1">
                {{ $slot }}
            </main>
        </div>
    </div>

    <x-toast />
</body>
</html>
