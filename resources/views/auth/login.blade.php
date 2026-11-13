<x-layouts.auth
    title="Masuk"
    image="login"
    image-alt="Dua orang membahas rencana acara di meja"
    tagline="Rencanakan Dan Jalankan Kegiatan Bersama Evlan"
    :image-width="1264"
    :image-height="1264"
>
    <div>
        <h1 class="font-display text-3xl font-semibold leading-tight tracking-tight text-neutral-900">Masuk ke EVLAN</h1>
        <p class="mt-2 text-base text-neutral-600">Lanjutkan menyiapkan acara Anda.</p>
    </div>

    @if (session('status'))
        <x-alert variant="success" class="mt-6">{{ session('status') }}</x-alert>
    @endif

    @if ($errors->any())
        <x-alert variant="error" class="mt-6">{{ $errors->first() }}</x-alert>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5" x-data="{ mengirim: false }" x-on:submit="mengirim = true">
        @csrf

        <x-input
            name="email"
            type="email"
            label="Email"
            placeholder="nama@email.com"
            autocomplete="email"
            inputmode="email"
            :value="old('email')"
            required
            :show-error="false"
        />

        <x-password-input
            name="password"
            label="Kata sandi"
            placeholder="Kata sandi Anda"
            autocomplete="current-password"
            required
        >
            <x-slot:labelRight>
                @if (Route::has('password.request'))
                    <a
                        href="{{ route('password.request') }}"
                        class="text-sm font-medium text-primary-600 underline-offset-4 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2"
                    >
                        Lupa kata sandi?
                    </a>
                @endif
            </x-slot:labelRight>
        </x-password-input>

        <label class="flex min-h-11 cursor-pointer select-none items-center gap-2.5">
            <input
                type="checkbox"
                name="remember"
                class="h-4 w-4 rounded-sm border-neutral-300 text-primary-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2"
            >
            <span class="text-sm text-neutral-600">Ingat saya</span>
        </label>

        <x-button type="submit" variant="primary" class="w-full" x-bind:disabled="mengirim">
            <span x-show="! mengirim">Masuk</span>
            <span x-show="mengirim" x-cloak>Memproses...</span>
        </x-button>
    </form>

    <p class="mt-8 text-sm text-neutral-600">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-medium text-primary-600 underline-offset-4 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2">
            Daftar
        </a>
    </p>
</x-layouts.auth>
