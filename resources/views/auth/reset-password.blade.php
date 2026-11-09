<x-layouts.guest title="Atur Ulang Kata Sandi">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-gray-900">
            Atur Ulang Kata Sandi
        </h2>
        <p class="mt-2 text-sm text-gray-500">
            Masukkan kata sandi baru untuk akun Anda.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-4">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-input
            name="email"
            type="email"
            label="Alamat Email"
            value="{{ old('email', $request->email) }}"
            required
            readonly
        />

        <x-input
            name="password"
            type="password"
            label="Kata Sandi Baru"
            placeholder="Min. 8 karakter"
            required
            autofocus
        />

        <x-input
            name="password_confirmation"
            type="password"
            label="Konfirmasi Sandi Baru"
            placeholder="Ulangi sandi baru"
            required
        />

        <div class="pt-2">
            <x-button type="submit" variant="primary" class="w-full py-2.5">
                Simpan Kata Sandi Baru
            </x-button>
        </div>
    </form>
</x-layouts.guest>

