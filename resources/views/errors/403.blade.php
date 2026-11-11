<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akses Ditolak (403) - EVLAN</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 antialiased text-gray-900 bg-gray-50">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 z-50 px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium shadow-md">
        Lewati ke konten utama
    </a>

    <main id="main-content" class="max-w-md w-full text-center bg-white p-8 sm:p-10 rounded-2xl shadow-sm border border-gray-100">
        <div class="w-16 h-16 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-inner">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
            </svg>
        </div>

        <span class="inline-block px-3 py-1 bg-red-50 text-red-700 text-xs font-semibold rounded-full uppercase tracking-wider mb-3">
            Galat 403
        </span>

        <h1 class="text-2xl font-black text-gray-900 tracking-tight mb-2">
            Akses Ditolak
        </h1>

        <p class="text-sm text-gray-500 leading-relaxed mb-8">
            {{ $exception?->getMessage() ?: 'Anda tidak memiliki hak akses untuk membuka halaman atau melakukan tindakan ini.' }}
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

