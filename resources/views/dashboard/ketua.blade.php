<x-layouts.app title="Dashboard Ketua">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    Dashboard Ketua Panitia
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Selamat datang kembali, {{ auth()->user()->nama }}. Pantau progres seluruh acara yang Anda pimpin.
                </p>
            </div>
            <div>
                <a
                    href="{{ url('/acara/buat/kategori') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[8px] text-sm font-semibold shadow-sm transition-colors"
                >
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Buat Acara Baru</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-stat-card
                title="Acara Dipimpin"
                value="{{ auth()->user()->eventsLed()->count() }}"
                icon="calendar"
                color="indigo"
            />
            <x-stat-card
                title="Tugas Saya"
                value="{{ auth()->user()->tasksAssigned()->count() }}"
                icon="check-circle"
                color="emerald"
            />
            <x-stat-card
                title="Acara Diikuti"
                value="{{ auth()->user()->eventsJoined()->count() }}"
                icon="users"
                color="amber"
            />
        </div>
    </div>
</x-layouts.app>

