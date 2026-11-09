<x-layouts.app :title="'Monitoring - ' . $event->nama_event">
    <div class="space-y-6">
        <x-acara.partials.header :event="$event" activeTab="monitoring" />

        <div class="bg-white p-8 rounded-[16px] border border-gray-200 shadow-sm text-center">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <x-icon name="activity" class="w-6 h-6" />
            </div>
            <h2 class="text-lg font-bold text-gray-900">Dashboard Monitoring Acara</h2>
            <p class="text-xs text-gray-500 max-w-md mx-auto mt-1">
                Layar monitoring terpadu (S18) mencakup cincin progres, progres per divisi, deadline tugas terdekat, dan audit aktivitas anggota (FR-20).
            </p>
            <div class="mt-6">
                <a href="{{ route('acara.ringkasan', $event->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-[8px] text-xs font-semibold transition-colors">
                    <span>Kembali ke Ringkasan</span>
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
