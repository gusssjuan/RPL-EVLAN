<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Halaman Tidak Ditemukan (404) - EVLAN</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 antialiased text-gray-900 bg-gray-50">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 z-50 px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium shadow-md">
        Lewati ke konten utama
    </a>

    <main id="main-content" class="max-w-md w-full text-center bg-white p-8 sm:p-10 rounded-2xl shadow-sm border border-gray-100">
        <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-inner">
            <x-icon name="circle-alert" class="w-8 h-8" />
        </div>

        <span class="inline-block px-3 py-1 bg-amber-50 text-amber-700 text-xs font-semibold rounded-full uppercase tracking-wider mb-3">
            Galat 404
        </span>

        <h1 class="text-2xl font-black text-gray-900 tracking-tight mb-2">
            Halaman Tidak Ditemukan
        </h1>

        <p class="text-sm text-gray-500 leading-relaxed mb-8">
            Halaman yang Anda tuju tidak ditemukan, telah dipindahkan, atau tautan yang Anda gunakan sudah tidak valid.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a
                href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}"
                class="w-full sm:w-auto inline-flex items-center justify-center min-h-[40px] px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 transition-colors"
            >
                Kembali
            </a>
            <a
                href="{{ auth()->check() ? route('dashboard') : url('/') }}"
                class="w-full sm:w-auto inline-flex items-center justify-center min-h-[40px] px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 shadow-sm transition-colors"
            >
                Ke Beranda
            </a>
        </div>
    </main>
</body>
</html>

