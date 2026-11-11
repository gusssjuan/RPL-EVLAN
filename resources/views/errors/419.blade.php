<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sesi Berakhir (419) - EVLAN</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 antialiased text-gray-900 bg-gray-50">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 z-50 px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium shadow-md">
        Lewati ke konten utama
    </a>

    <main id="main-content" class="max-w-md w-full text-center bg-white p-8 sm:p-10 rounded-2xl shadow-sm border border-gray-100">
        <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-inner">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <span class="inline-block px-3 py-1 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full uppercase tracking-wider mb-3">
            Sesi Kedaluwarsa
        </span>

        <h1 class="text-2xl font-black text-gray-900 tracking-tight mb-2">
            Sesi Anda Telah Berakhir
        </h1>

        <p class="text-sm text-gray-500 leading-relaxed mb-8">
            Halaman ini tidak aktif dalam waktu yang cukup lama demi keamanan. Silakan muat ulang halaman atau masuk kembali ke akun Anda.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <button
                type="button"
                onclick="window.location.reload()"
                class="w-full sm:w-auto inline-flex items-center justify-center min-h-[40px] px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 transition-colors"
            >
                Muat Ulang
            </button>
            <a
                href="{{ route('login') }}"
                class="w-full sm:w-auto inline-flex items-center justify-center min-h-[40px] px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 shadow-sm transition-colors"
            >
                Masuk Kembali
            </a>
        </div>
    </main>
</body>
</html>

