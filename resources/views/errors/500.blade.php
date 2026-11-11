<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terjadi Kesalahan Server (500) - EVLAN</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 antialiased text-gray-900 bg-gray-50">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 z-50 px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium shadow-md">
        Lewati ke konten utama
    </a>

    <main id="main-content" class="max-w-md w-full text-center bg-white p-8 sm:p-10 rounded-2xl shadow-sm border border-gray-100">
        <div class="w-16 h-16 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-inner">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
        </div>

        <span class="inline-block px-3 py-1 bg-red-50 text-red-700 text-xs font-semibold rounded-full uppercase tracking-wider mb-3">
            Galat 500
        </span>

        <h1 class="text-2xl font-black text-gray-900 tracking-tight mb-2">
            Terjadi Kesalahan Server
        </h1>

        <p class="text-sm text-gray-500 leading-relaxed mb-8">
            Mohon maaf, sistem kami sedang mengalami gangguan internal sementara. Tim kami telah mencatat masalah ini untuk segera diperbaiki.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <button
                type="button"
                onclick="window.location.reload()"
                class="w-full sm:w-auto inline-flex items-center justify-center min-h-[40px] px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 transition-colors"
            >
                Coba Lagi
            </button>
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

