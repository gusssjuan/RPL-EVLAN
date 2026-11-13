@php
    $tipeAwal = old('tipe', request('tipe') === 'vendor' ? 'vendor' : 'user');
@endphp

<x-layouts.auth
    title="Buat akun"
    image="register"
    image-alt="Dua orang menyiapkan catatan acara"
    tagline="Satu akun untuk memimpin acara atau jadi anggota panitia."
    :image-width="1112"
    :image-height="1112"
>
    <div>
        <h1 class="font-display text-3xl font-semibold leading-tight tracking-tight text-neutral-900">Buat akun EVLAN</h1>
        <p class="mt-2 text-base text-neutral-600">Pilih jenis akun terlebih dahulu.</p>
    </div>

    <form
        method="POST"
        action="{{ route('register') }}"
        class="mt-6 space-y-6"
        x-data="{ tipe: @js($tipeAwal), mengirim: false }"
        x-on:submit="mengirim = true"
    >
        @csrf

        <fieldset>
            <legend class="sr-only">Jenis akun</legend>

            <div class="grid grid-cols-2 gap-1 rounded-control border border-neutral-200 bg-neutral-100 p-1">
                <label class="cursor-pointer">
                    <input
                        type="radio"
                        name="tipe"
                        value="user"
                        x-model="tipe"
                        @checked($tipeAwal === 'user')
                        class="peer sr-only"
                    >
                    <span class="flex min-h-11 items-center justify-center rounded-control px-2 text-center text-sm font-medium text-neutral-600 transition-colors hover:text-neutral-900 peer-checked:bg-surface peer-checked:font-semibold peer-checked:text-primary-700 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-600 peer-focus-visible:ring-offset-2">
                        Pengguna (Ketua/Anggota)
                    </span>
                </label>

                <label class="cursor-pointer">
                    <input
                        type="radio"
                        name="tipe"
                        value="vendor"
                        x-model="tipe"
                        @checked($tipeAwal === 'vendor')
                        class="peer sr-only"
                    >
                    <span class="flex min-h-11 items-center justify-center rounded-control px-2 text-center text-sm font-medium text-neutral-600 transition-colors hover:text-neutral-900 peer-checked:bg-surface peer-checked:font-semibold peer-checked:text-primary-700 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-600 peer-focus-visible:ring-offset-2">
                        Vendor
                    </span>
                </label>
            </div>
        </fieldset>

        <div class="grid gap-5 sm:grid-cols-2">
            <x-input
                name="nama"
                type="text"
                label="Nama lengkap"
                placeholder="Nama Anda"
                autocomplete="name"
                :value="old('nama')"
                required
            />

            <x-input
                name="email"
                type="email"
                label="Email"
                placeholder="nama@email.com"
                autocomplete="email"
                inputmode="email"
                :value="old('email')"
                required
            />
        </div>

        <x-input
            name="no_telepon"
            type="text"
            label="Nomor telepon (opsional)"
            placeholder="081234567890"
            autocomplete="tel"
            inputmode="tel"
            :value="old('no_telepon')"
        />

        <div class="grid gap-5 sm:grid-cols-2">
            <x-password-input
                name="password"
                label="Kata sandi"
                hint="Minimal 8 karakter"
                autocomplete="new-password"
                required
            />

            <x-password-input
                name="password_confirmation"
                label="Konfirmasi kata sandi"
                autocomplete="new-password"
                required
            />
        </div>

        <div x-show="tipe === 'vendor'" x-cloak class="border-t border-neutral-200 pt-6">
            <h2 class="font-display text-lg font-semibold text-neutral-900">Data usaha</h2>

            <div class="mt-5 space-y-5">
                <x-input
                    name="nama_vendor"
                    type="text"
                    label="Nama usaha"
                    placeholder="Nama usaha atau bisnis Anda"
                    x-bind:required="tipe === 'vendor'"
                    x-bind:disabled="tipe !== 'vendor'"
                    @if ($tipeAwal !== 'vendor') disabled @endif
                />

                <x-select
                    name="kategori_vendor"
                    label="Kategori vendor"
                    x-bind:required="tipe === 'vendor'"
                    x-bind:disabled="tipe !== 'vendor'"
                    @if ($tipeAwal !== 'vendor') disabled @endif
                >
                    <option value="">Pilih kategori vendor</option>
                    @foreach (\App\Enums\KategoriVendor::cases() as $kat)
                        <option value="{{ $kat->value }}" @selected(old('kategori_vendor') === $kat->value)>
                            {{ $kat->label() }}
                        </option>
                    @endforeach
                </x-select>

                <div>
                    <label for="alamat" class="mb-1.5 block text-sm font-medium text-neutral-900">Alamat</label>
                    <textarea
                        name="alamat"
                        id="alamat"
                        rows="3"
                        placeholder="Alamat usaha Anda"
                        x-bind:required="tipe === 'vendor'"
                        x-bind:disabled="tipe !== 'vendor'"
                        @if ($tipeAwal !== 'vendor') disabled @endif
                        @if ($errors->has('alamat')) aria-invalid="true" aria-describedby="alamat-error" @endif
                        class="block min-h-11 w-full rounded-control border bg-surface px-3.5 py-2.5 text-base text-neutral-900 placeholder:text-neutral-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2 {{ $errors->has('alamat') ? 'border-bahaya-600' : 'border-neutral-200' }}"
                    >{{ old('alamat') }}</textarea>

                    @error('alamat')
                        <p id="alamat-error" class="mt-1.5 flex items-start gap-1.5 text-[13px] leading-[18px] text-bahaya-600">
                            <x-icon name="circle-alert" class="mt-0.5 h-3.5 w-3.5 shrink-0" />
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <x-input
                    name="kontak"
                    type="text"
                    label="Kontak"
                    placeholder="Nomor WhatsApp bisnis"
                    autocomplete="tel"
                    inputmode="tel"
                    :value="old('kontak')"
                    x-bind:required="tipe === 'vendor'"
                    x-bind:disabled="tipe !== 'vendor'"
                    @if ($tipeAwal !== 'vendor') disabled @endif
                />
            </div>

            <p class="mt-4 flex items-start gap-2 text-sm text-neutral-600">
                <x-icon name="info" class="mt-0.5 h-4 w-4 shrink-0 text-info-600" />
                <span>Akun vendor diverifikasi Admin sebelum Anda bisa menambahkan produk atau jasa.</span>
            </p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
            <x-button type="submit" variant="primary" class="w-full sm:w-auto" x-bind:disabled="mengirim">
                <span x-show="! mengirim">Daftar</span>
                <span x-show="mengirim" x-cloak>Memproses...</span>
            </x-button>
        </div>
    </form>

    <p class="mt-8 text-sm text-neutral-600">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-medium text-primary-600 underline-offset-4 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2">
            Masuk
        </a>
    </p>
</x-layouts.auth>
