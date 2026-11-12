<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'EVLAN' }} - Event Planner Organizer</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-gray-900 bg-gray-50">
    <!-- Aksesibilitas: Lewati ke konten utama -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 z-50 px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold shadow-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500">
        Lewati ke konten utama
    </a>

    <div x-data="{ sidebarOpen: false }" class="min-h-full flex flex-col lg:flex-row">
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

        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 left-0 z-40 w-60 bg-white border-r border-gray-200 flex flex-col justify-between transition-transform duration-300 ease-in-out lg:static lg:translate-x-0"
        >
            <div>
                <div class="h-16 flex items-center px-6 border-b border-gray-100 gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-extrabold text-lg shadow-sm">
                        E
                    </div>
                    <div>
                        <span class="text-lg font-black tracking-tight text-gray-900">EVLAN</span>
                        <span class="block text-[10px] font-medium text-gray-400 uppercase tracking-wider -mt-1">Vendor Panel</span>
                    </div>
                </div>

                <nav class="p-4 space-y-1">
                    <a
                        href="{{ route('usaha.dashboard') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-[8px] text-sm font-medium transition-colors {{ request()->is('usaha') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    >
                        <x-icon name="circle" class="w-5 h-5 {{ request()->is('usaha') ? 'text-indigo-600' : 'text-gray-400' }}" />
                        <span>Dashboard</span>
                    </a>

                    <a
                        href="{{ route('usaha.profil.edit') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-[8px] text-sm font-medium transition-colors {{ request()->is('usaha/profil*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    >
                        <x-icon name="store" class="w-5 h-5 {{ request()->is('usaha/profil*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                        <span>Profil Usaha</span>
                    </a>

                    <a
                        href="{{ route('usaha.produk.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-[8px] text-sm font-medium transition-colors {{ request()->is('usaha/produk*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    >
                        <x-icon name="tag" class="w-5 h-5 {{ request()->is('usaha/produk*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                        <span>Produk &amp; Jasa</span>
                    </a>

                    <div class="pt-4 mt-4 border-t border-gray-100">
                        <a
                            href="{{ url('/vendor') }}"
                            class="flex items-center justify-center gap-2 w-full px-3 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-[8px] text-xs font-semibold shadow-sm transition-colors"
                        >
                            <x-icon name="search" class="w-4 h-4" />
                            <span>Lihat Direktori Vendor</span>
                        </a>
                    </div>
                </nav>
            </div>

            <div class="p-4 border-t border-gray-100">
                @auth
                    <div class="p-2 rounded-[8px] bg-gray-50">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-3 min-w-0">
                                <x-avatar :name="auth()->user()->nama ?? 'Vendor'" size="sm" />
                                <div class="truncate">
                                    <p class="text-xs font-semibold text-gray-900 truncate">{{ auth()->user()->nama ?? 'Vendor' }}</p>
                                    <p class="text-[11px] text-gray-400 truncate">Akun Vendor</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('logout', [], false) }}">
                                @csrf
                                <button type="submit" class="text-gray-400 hover:text-red-600 p-1 rounded-md transition-colors" title="Keluar">
                                    <x-icon name="log-out" class="w-4 h-4" />
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 z-20">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        x-on:click="sidebarOpen = true"
                        class="p-2 -ml-2 text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        <span class="sr-only">Buka menu</span>
                        <x-icon name="menu" class="w-6 h-6" />
                    </button>

                    <div class="text-sm font-medium text-gray-500">
                        {{ $breadcrumb ?? 'Area Vendor' }}
                    </div>
                </div>

                <div class="flex items-center gap-4">
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

                    <a href="{{ route('usaha.profil.edit') }}" class="flex items-center gap-2">
                        <x-avatar :name="auth()->user()->nama ?? 'Vendor'" size="sm" />
                    </a>
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
