<?php

namespace App\Http\Controllers;

use App\Enums\StatusPendaftaran;
use App\Http\Requests\Event\EventDetailRequest;
use App\Models\Event;
use App\Services\ProgressService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $eventsLed = $user->eventsLed()->with(['category', 'tasks', 'checklists'])->latest()->get();
        $eventsJoined = $user->eventsJoined()->with(['category', 'tasks', 'checklists'])->latest()->get();

        return view('acara.index', compact('eventsLed', 'eventsJoined'));
    }

    public function show(Request $request, Event $event): View
    {
        $this->authorize('view', $event);

        $user = $request->user();
        $isLeader = $event->isLeader($user);

        $event->load([
            'category',
            'leader',
            'checklists',
            'timelines' => fn ($q) => $q->orderBy('offset_hari', 'desc'),
            'divisions.members.user',
            'tasks.penerima',
            'budgets',
        ]);

        // Hitung progres
        $totalChecklist = $event->checklists->count();
        $selesaiChecklist = $event->checklists->where('status_selesai', true)->count();

        $totalTugas = $event->tasks->count();
        $selesaiTugas = $event->tasks->where('status.value', 'sudah_diselesaikan')->count();

        $totalItem = $totalChecklist + $totalTugas;
        $totalSelesai = $selesaiChecklist + $selesaiTugas;
        $persenProgres = $totalItem > 0 ? round(($totalSelesai / $totalItem) * 100) : 0;

        // Agenda terdekat berikutnya
        $agendaTerdekat = $event->timelines
            ->filter(fn ($t) => Carbon::parse($t->tanggal)->isFuture() || Carbon::parse($t->tanggal)->isToday())
            ->sortBy('offset_hari')
            ->first();

        // Tugas saya di acara ini (khusus panitia/anggota)
        $tugasSaya = $event->tasks->where('penerima_id', $user->id);

        // Pendaftar publik (T9.3): hanya dimuat untuk Leader
        $pendaftar = collect();
        $jumlahPendaftarDiterima = 0;
        if ($isLeader && $event->is_publik) {
            $event->load(['registrations' => fn ($q) => $q->with('user')->latest()]);
            $pendaftar = $event->registrations;
            $jumlahPendaftarDiterima = $pendaftar
                ->filter(fn ($p) => $p->status === StatusPendaftaran::Diterima)
                ->count();
        }

        return view('acara.show', compact(
            'event',
            'isLeader',
            'totalChecklist',
            'selesaiChecklist',
            'totalTugas',
            'selesaiTugas',
            'persenProgres',
            'agendaTerdekat',
            'tugasSaya',
            'pendaftar',
            'jumlahPendaftarDiterima'
        ));
    }

    public function edit(Event $event): View
    {
        $this->authorize('manage', $event);

        $event->load('category');

        return view('acara.edit', compact('event'));
    }

    public function update(EventDetailRequest $request, Event $event): RedirectResponse
    {
        $this->authorize('manage', $event);

        $oldDate = $event->tanggal->toDateString();
        $newDate = $request->tanggal;

        $validated = $request->validated();
        $validated['is_publik'] = $request->boolean('is_publik');
        $validated['pendaftaran_dibuka'] = $request->boolean('pendaftaran_dibuka');
        $validated['batas_pendaftaran'] = $request->batas_pendaftaran ?: null;
        $validated['kuota'] = $request->kuota ? (int) $request->kuota : null;
        $validated['penyelenggara'] = $request->penyelenggara ?: null;

        $event->update($validated);

        // Jika tanggal acara berubah, hitung ulang timelines.tanggal dari offset_hari (D-11 / Aturan)
        if ($oldDate !== $newDate) {
            $eventDate = Carbon::parse($newDate);
            foreach ($event->timelines as $timeline) {
                $tgl = $eventDate->copy()->subDays($timeline->offset_hari);
                $timeline->update([
                    'tanggal' => $tgl->toDateString(),
                ]);
            }
        }

        return redirect()->route('acara.ringkasan', $event->id)
            ->with('status', 'Detail acara dan timeline persiapan berhasil diperbarui.');
    }

    public function monitoring(Event $event): View
    {
        $this->authorize('viewMonitoring', $event); // D-16: Leader only

        $event->load(['category', 'divisions']);

        $monitoring = ProgressService::forEvent($event);

        $aktivitasAnggota = $event->activityLogs()
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('acara.monitoring', compact('event', 'monitoring', 'aktivitasAnggota'));
    }
}
