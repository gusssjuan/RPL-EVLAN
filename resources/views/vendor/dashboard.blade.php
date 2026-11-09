<x-layouts.app title="Dashboard Vendor">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                Dashboard Usaha Vendor
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Kelola profil usaha dan katalog produk/layanan Anda.
            </p>
        </div>

        @if (session('info'))
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-[12px] text-sm text-amber-900 flex items-start gap-3">
                <x-icon name="alert-circle" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
                <div>
                    <h3 class="font-bold">Status Akun Vendor</h3>
                    <p class="mt-1">{{ session('info') }}</p>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>

