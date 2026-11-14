<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/logo/purple.svg') }}" type="image/svg">
    <title>{{ $title ?? 'Masuk' }} - EVLAN</title>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-gray-900 bg-white">
    <div class="min-h-full flex flex-col md:flex-row">
        <!-- Left Purple Branding Panel (Desktop) -->
        <div class="hidden md:flex md:w-1/2 lg:w-5/12 bg-gradient-to-br from-indigo-700 via-indigo-600 to-indigo-800 p-12 text-white flex-col justify-between relative overflow-hidden">
            <!-- Decorative circle blur -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-indigo-500/30 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white text-indigo-600 flex items-center justify-center font-black text-xl shadow-md">
                        E
                    </div>
                    <span class="text-2xl font-black tracking-tight text-white">EVLAN</span>
                </a>
            </div>

            <div class="relative z-10 my-auto py-12 max-w-md">
                <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight text-white leading-tight">
                    Rencanakan acara lebih terstruktur, dari kategori hingga evaluasi.
                </h1>
                <p class="mt-4 text-base text-indigo-100 leading-relaxed">
                    Template otomatis, manajemen checklist H-n, pembagian tugas kepanitiaan, hingga monitoring progres dan estimasi vendor dalam satu tempat.
                </p>

                <div class="mt-8 flex items-center gap-6 text-sm text-indigo-200">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-indigo-500/40 flex items-center justify-center text-white"><x-icon name="check" class="w-3 h-3" /></span>
                        <span>Otomatis H-n</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-indigo-500/40 flex items-center justify-center text-white"><x-icon name="check" class="w-3 h-3" /></span>
                        <span>Jobdesk Tim</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-indigo-500/40 flex items-center justify-center text-white"><x-icon name="check" class="w-3 h-3" /></span>
                        <span>Vendor Lokal</span>
                    </div>
                </div>
            </div>

            <div class="relative z-10 text-xs text-indigo-200">
                &copy; {{ date('Y') }} EVLAN. Seluruh hak cipta dilindungi undang-undang.
            </div>
        </div>

        <!-- Right Content / Form Panel -->
        <div class="flex-1 flex flex-col justify-center px-6 py-12 sm:px-12 lg:px-16 bg-white overflow-y-auto">
            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <!-- Mobile Logo Header -->
                <div class="md:hidden flex items-center justify-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black text-xl shadow-sm">
                        E
                    </div>
                    <span class="text-2xl font-black tracking-tight text-gray-900">EVLAN</span>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>

    <x-toast />
</body>
</html>

