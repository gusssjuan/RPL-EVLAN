<?php

namespace App\Http\Controllers\Event;

use App\Enums\JenisNotifikasi;
use App\Enums\StatusTugas;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\ActivityLogger;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyTaskController extends Controller
{
    /**
     * Tugas Saya (S16): semua tugas di mana user = penerima lintas acara.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = $user->tasksAssigned()->with(['event.category', 'division']);

        // Filter status
        if ($request->filled('status') && in_array($request->status, ['belum_dikerjakan', 'sedang_dikerjakan', 'sudah_diselesaikan', 'terlewat'])) {
            if ($request->status === 'terlewat') {
                $query->terlewat();
            } else {
                $query->where('status', $request->status);
            }
        }

        // Filter acara
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        $tasks = $query->orderBy('deadline')->get();

        // Hitungan
        $allTasks = $user->tasksAssigned;
        $countBelum = $allTasks->where('status', StatusTugas::BelumDikerjakan)->count();
        $countSedang = $allTasks->where('status', StatusTugas::SedangDikerjakan)->count();
        $countSelesai = $allTasks->where('status', StatusTugas::SudahDiselesaikan)->count();
        $countTerlewat = $allTasks->filter(fn ($t) => $t->is_terlewat)->count();

        // Daftar acara untuk filter dropdown
        $myEvents = $user->tasksAssigned()->with('event')->get()->pluck('event')->unique('id')->sortBy('nama_event');

        return view('tugas.index', compact(
            'tasks',
            'countBelum',
            'countSedang',
            'countSelesai',
            'countTerlewat',
            'myEvents'
        ));
    }

    /**
     * Detail Tugas (S17).
     */
    public function show(Request $request, Task $task): View
    {
        $task->load(['event.leader', 'division', 'penerima', 'creator']);

        $user = $request->user();
        $event = $task->event;

        // Harus peserta acara untuk melihat detail tugas
        abort_if(! $event->isParticipant($user), 403);

        $isLeader = $event->isLeader($user);
        $isPenerima = $task->penerima_id === $user->id;

        // Riwayat aktivitas terkait tugas ini
        $riwayat = $event->activityLogs()
            ->where(function ($q) use ($task) {
                $q->whereJsonContains('meta->task_id', $task->id)
                    ->orWhere('deskripsi', 'like', '%"'.$task->judul.'"%');
            })
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('tugas.show', compact('task', 'event', 'isLeader', 'isPenerima', 'riwayat'));
    }

    /**
     * Update status tugas (Process Spec 4.4 + D-14): hanya penerima.
     */
    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('updateStatus', $task);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:belum_dikerjakan,sedang_dikerjakan,sudah_diselesaikan'],
        ]);

        $oldStatus = $task->status;
        $newStatus = StatusTugas::from($validated['status']);

        $task->update([
            'status' => $newStatus,
            'completed_at' => $newStatus === StatusTugas::SudahDiselesaikan ? now() : null,
        ]);

        $event = $task->event;
        $user = $request->user();

        // Catat aktivitas
        ActivityLogger::log(
            $event,
            $user,
            'ubah_status',
            'mengubah status "'.$task->judul.'" → '.$newStatus->label(),
            ['task_id' => $task->id, 'old_status' => $oldStatus->value, 'new_status' => $newStatus->value]
        );

        // Notifikasi status_tugas ke Leader (in-app)
        if (! $event->isLeader($user)) {
            NotificationService::kirim(
                $event->leader()->firstOrFail(),
                JenisNotifikasi::StatusTugas,
                $user->nama.' mengubah status "'.$task->judul.'" menjadi '.$newStatus->label().'.',
                $event,
                $task,
                '/tugas/'.$task->id,
            );
        }

        return redirect()->route('tugas.show', $task->id)
            ->with('status', 'Status tugas berhasil diubah menjadi "'.$newStatus->label().'".');
    }
}
