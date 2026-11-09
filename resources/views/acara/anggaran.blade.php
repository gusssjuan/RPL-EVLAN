<x-layouts.app :title="'Estimasi Anggaran - ' . $event->nama_event">
    <div class="space-y-6">
        <x-acara.partials.header :event="$event" activeTab="anggaran" />

        <div class="bg-white p-8 rounded-[16px] border border-gray-200 shadow-sm text-center">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <x-icon name="credit-card" class="w-6 h-6" />
            </div>
            <h2 class="text-lg font-bold text-gray-900">Estimasi Anggaran Acara</h2>
            <p class="text-xs text-gray-500 max-w-md mx-auto mt-1">
                Layar estimasi anggaran (S22) mencakup pencatatan item pengeluaran per kategori, integrasi vendor terpilih, dan kalkulasi total dana otomatis (FR-14).
            </p>
            <div class="mt-6">
                <a href="{{ route('acara.ringkasan', $event->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-[8px] text-xs font-semibold transition-colors">
                    <span>Kembali ke Ringkasan</span>
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
