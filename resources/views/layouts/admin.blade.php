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
    <div x-data="{ sidebarOpen: false }" class="min-h-full flex flex-col lg:flex-row">
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
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 z-20">
                <div class="text-sm font-semibold text-gray-800">
                    {{ $title ?? 'Admin Area' }}
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-600">
                    <x-avatar name="Admin EVLAN" size="sm" />
                    <span class="font-medium">Administrator</span>
                </div>
            </header>

            <main class="flex-1 p-6 lg:p-8 max-w-7xl w-full mx-auto">
                {{ $slot }}
            </main>
        </div>
    </div>

    <x-toast />
</body>
</html>

