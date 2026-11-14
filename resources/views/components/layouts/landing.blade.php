@props([
    'title' => 'EVLAN - Perencanaan acara',
])

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="{{ config('evlan.brand_color') }}">
    <link rel="icon" href="{{ asset('images/logo/purple.svg') }}" type="image/svg">

    <title>{{ $title }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-svh bg-neutral-50 font-sans text-neutral-900 antialiased [overflow-wrap:anywhere]">
    <a
        href="#konten"
        class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-control focus:bg-primary-600 focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2"
    >
        Lewati ke konten utama
    </a>

    <header class="border-b border-neutral-200 bg-neutral-50 z-80 fixed w-full">
        <div class="mx-auto flex min-h-16 w-full max-w-[72rem] flex-wrap items-center justify-between gap-x-3 gap-y-1 px-4 py-2 sm:px-8">
            <a href="{{ url('/') }}" class="inline-flex shrink-0 items-center rounded-control focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2">
                <x-logo variant="purple" />
            </a>

            <nav aria-label="Navigasi utama" class="hidden items-center gap-7 text-sm font-medium text-neutral-600 lg:flex">
                <a href="#cara-kerja" class="transition-colors hover:text-primary-700">Cara kerja</a>
                <a href="#cari-kegiatan" class="transition-colors hover:text-primary-700">Cari kegiatan</a>
                <a href="#tujuan" class="transition-colors hover:text-primary-700">Tujuan</a>
                <a href="#untuk-siapa" class="transition-colors hover:text-primary-700">Untuk siapa</a>
            </nav>

            <div class="flex shrink-0 items-center gap-1 sm:gap-2">
                <a
                    href="{{ route('login') }}"
                    class="inline-flex min-h-11 items-center justify-center rounded-control px-2 text-sm font-medium text-neutral-600 transition-colors hover:text-neutral-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2 sm:px-3"
                >
                    Masuk
                </a>
                <a
                    href="{{ route('register') }}"
                    class="inline-flex min-h-11 items-center justify-center rounded-control bg-primary-600 px-3 text-sm font-medium text-white transition-colors hover:bg-primary-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2"
                >
                    Daftar
                </a>
            </div>
        </div>
    </header>

    <main id="konten" tabindex="-1" class="focus:outline-none">
        {{ $slot }}
    </main>

    <footer class="bg-sidebar">
        <div class="mx-auto w-full max-w-[72rem] px-5 py-10 sm:px-8">
            <div class="flex flex-col gap-6 border-b border-white/10 pb-8 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-3">
                    <x-logo variant="white" />
                </div>

                <nav aria-label="Navigasi footer" class="flex items-center gap-6 text-sm font-medium text-white/80">
                    <a href="{{ route('login') }}" class="transition-colors hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-sidebar">Masuk</a>
                    <a href="{{ route('register') }}" class="transition-colors hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-sidebar">Daftar</a>
                </nav>
            </div>

            <div class="flex flex-col gap-2 pt-6 text-sm text-white/70 md:flex-row md:items-center md:justify-between">
                <p>Aplikasi perencanaan acara berbasis web.</p>
                <p>&copy; 2026 EVLAN</p>
            </div>
        </div>
    </footer>
</body>
</html>
