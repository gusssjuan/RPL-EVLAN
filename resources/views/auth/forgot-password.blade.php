<x-layouts.guest title="Lupa Kata Sandi">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-gray-900">
            Lupa Kata Sandi
        </h2>
        <p class="mt-2 text-sm text-gray-500">
            Masukkan alamat email yang terdaftar. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi akun Anda.
        </p>
    </div>

    @if (session('status'))
        <div class="mt-4 p-3 bg-emerald-50 border border-emerald-200 rounded-[8px] text-xs font-medium text-emerald-800 flex items-center gap-2">
            <x-icon name="check-circle" class="w-4 h-4 text-emerald-600 shrink-0" />
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
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

        <div class="pt-2">
            <x-button type="submit" variant="primary" class="w-full py-2.5">
                Kirim Tautan Reset
            </x-button>
        </div>
    </form>

    <div class="mt-8 text-center text-sm text-gray-500">
        Ingat kata sandi Anda?
        <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
            Kembali masuk
        </a>
    </div>
</x-layouts.guest>

