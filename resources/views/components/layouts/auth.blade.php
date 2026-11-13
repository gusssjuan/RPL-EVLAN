@props([
    'title',
    'image',
    'imageAlt',
    'tagline',
    'imageWidth',
    'imageHeight',
])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="{{ config('evlan.brand_color') }}">

    <title>{{ $title }} - EVLAN</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-neutral-50 font-sans text-neutral-900 antialiased">
    <a
        href="#konten"
        class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-control focus:bg-primary-600 focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2"
    >
        Lewati ke konten utama
    </a>

    <div class="min-h-svh lg:grid lg:grid-cols-2">
        <aside class="hidden bg-sidebar p-10 lg:sticky lg:top-0 lg:flex lg:h-svh lg:flex-col">
            <a href="{{ url('/') }}" class="inline-flex shrink-0 items-center self-start rounded-control focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-sidebar">
                <x-logo variant="white" size="lg" />
            </a>

            <div class="flex min-h-0 flex-1 items-center justify-center py-8">
                <picture>
                    <source
                        type="image/webp"
                        srcset="{{ asset('images/'.$image.'-640.webp') }} 640w, {{ asset('images/'.$image.'-1280.webp') }} 1280w"
                        sizes="(min-width: 1024px) 50vw, 0px"
                    >
                    <img
                        src="{{ asset('images/'.$image.'.png') }}"
                        width="{{ $imageWidth }}"
                        height="{{ $imageHeight }}"
                        alt="{{ $imageAlt }}"
                        loading="lazy"
                        decoding="async"
                        class="max-h-[46vh] w-auto max-w-full object-contain"
                    >
                </picture>
            </div>

            <p class="max-w-[34ch] text-base leading-6 text-white">{{ $tagline }}</p>
        </aside>

        <main id="konten" tabindex="-1" class="flex min-h-svh flex-col bg-neutral-50 px-5 py-10 focus:outline-none sm:px-8 lg:justify-center lg:px-12">
            <div class="mx-auto w-full max-w-96">
                <div class="mb-10 lg:hidden">
                    <a href="{{ url('/') }}" class="inline-flex items-center rounded-control focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2">
                        <x-logo variant="purple" size="lg" />
                    </a>
                </div>

                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
