<x-layouts.app title="Dashboard Anggota">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                Dashboard Kepanitiaan
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Selamat datang, {{ auth()->user()->nama }}. Pantau tugas dan kepanitiaan acara yang Anda ikuti.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-stat-card
                title="Tugas Saya"
                value="{{ auth()->user()->tasksAssigned()->count() }}"
                icon="check-circle"
                color="indigo"
            />
            <x-stat-card
                title="Kepanitiaan Diikuti"
                value="{{ auth()->user()->eventsJoined()->count() }}"
                icon="users"
                color="emerald"
            />
        </div>
    </div>
</x-layouts.app>

