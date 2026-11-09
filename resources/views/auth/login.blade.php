<x-layouts.guest title="Masuk">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-gray-900">
            Masuk ke Akun Anda
        </h2>
        <p class="mt-2 text-sm text-gray-500">
            Masukkan email dan kata sandi untuk mengakses akun EVLAN Anda.
        </p>
    </div>

    @if (session('status'))
        <div class="mt-4 p-3 bg-emerald-50 border border-emerald-200 rounded-[8px] text-xs font-medium text-emerald-800 flex items-center gap-2">
            <x-icon name="check-circle" class="w-4 h-4 text-emerald-600 shrink-0" />
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf

        <x-input
            name="email"
            type="email"
            label="Alamat Email"
            placeholder="nama@email.com"
            value="{{ old('email') }}"
            required
            autofocus
        />

        <x-input
            name="password"
            type="password"
            label="Kata Sandi"
            placeholder="••••••••"
            required
        />

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2 cursor-pointer select-none">
                <input
                    type="checkbox"
                    name="remember"
                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4"
                >
                <span class="text-gray-600 text-xs sm:text-sm">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                    Lupa kata sandi?
                </a>
            @endif
        </div>

        <div class="pt-2">
            <x-button type="submit" variant="primary" class="w-full py-2.5">
                Masuk
            </x-button>
        </div>
    </form>

    <div class="mt-8 text-center text-sm text-gray-500">
        Belum memiliki akun?
        <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
            Daftar sekarang
        </a>
    </div>
</x-layouts.guest>

