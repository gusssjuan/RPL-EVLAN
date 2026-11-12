<?php

namespace App\Http\Controllers\Event;

use App\Enums\JenisNotifikasi;
use App\Enums\StatusTugas;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Task;
use App\Models\TemplateItem;
use App\Services\ActivityLogger;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Halaman Tugas Acara - Kelola Jobdesk (S15).
     */
    public function index(Request $request, Event $event): View
    {
        $this->authorize('view', $event);

        $event->load(['divisions', 'members.user', 'leader']);

        $isLeader = $event->isLeader($request->user());

        $query = $event->tasks()->with(['penerima', 'division']);

        // Filter divisi
        if ($request->filled('divisi')) {
            $query->where('division_id', $request->divisi);
        }

        // Filter penerima
        if ($request->filled('penerima')) {
            $query->where('penerima_id', $request->penerima);
        }

        // Filter status
        if ($request->filled('status') && in_array($request->status, ['belum_dikerjakan', 'sedang_dikerjakan', 'sudah_diselesaikan', 'terlewat'])) {
            if ($request->status === 'terlewat') {
                $query->terlewat();
            } else {
                $query->where('status', $request->status);
            }
        }

        // Pencarian
        if ($request->filled('cari')) {
            $query->where('judul', 'like', '%'.$request->cari.'%');
        }

        $tasks = $query->orderBy('deadline')->paginate(8)->withQueryString();

        // Hitungan status
        $allTasks = $event->tasks;
        $countBelum = $allTasks->where('status', StatusTugas::BelumDikerjakan)->count();
        $countSedang = $allTasks->where('status', StatusTugas::SedangDikerjakan)->count();
        $countSelesai = $allTasks->where('status', StatusTugas::SudahDiselesaikan)->count();
        $countTerlewat = $allTasks->filter(fn ($t) => $t->is_terlewat)->count();

        // Rekomendasi jobdesk dari template untuk modal "Dari rekomendasi"
        $jobdeskRecommendations = collect();
        $activeTemplate = $event->category?->templates()?->where('is_aktif', true)->first();
        if ($activeTemplate) {
            $jobdeskRecommendations = TemplateItem::where('template_id', $activeTemplate->id)
                ->where('jenis', 'jobdesk')
                ->orderBy('kelompok')
                ->orderBy('urutan')
                ->get();
        }

        // Daftar peserta untuk dropdown penerima (Leader + Members)
        $pesertaAcara = collect();
        $pesertaAcara->push($event->leader);
        foreach ($event->members as $m) {
            $pesertaAcara->push($m->user);
        }
        $pesertaAcara = $pesertaAcara->unique('id');

        return view('acara.tugas.index', compact(
            'event',
            'isLeader',
            'tasks',
            'countBelum',
            'countSedang',
            'countSelesai',
            'countTerlewat',
            'jobdeskRecommendations',
            'pesertaAcara'
        ));
    }

    /**
     * Buat tugas baru (Process Spec 4.3).
     */
    public function store(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('manage', $event);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'penerima_id' => ['required', 'exists:users,id'],
            'deadline' => ['required', 'date'],
        ]);

        // Validasi: penerima harus peserta acara (leader atau member)
        if (! $event->isParticipant($validated['penerima_id'])) {
            return back()->withErrors(['penerima_id' => 'Penerima tugas harus merupakan peserta (anggota atau ketua) acara ini.'])->withInput();
        }

        // Validasi: deadline <= tanggal_acara (D-26)
        if (Carbon::parse($validated['deadline'])->greaterThan($event->tanggal)) {
            return back()->withErrors(['deadline' => 'Deadline tugas tidak boleh melewati tanggal pelaksanaan acara ('.$event->tanggal->isoFormat('D MMMM Y').').'])->withInput();
        }

        // Pastikan division milik event
        if (! empty($validated['division_id'])) {
            $divBelongsToEvent = $event->divisions()->where('id', $validated['division_id'])->exists();
            if (! $divBelongsToEvent) {
                return back()->withErrors(['division_id' => 'Divisi tidak valid untuk acara ini.'])->withInput();
            }
        }

        $task = Task::create([
            'event_id' => $event->id,
            'division_id' => $validated['division_id'] ?? null,
            'penerima_id' => $validated['penerima_id'],
            'created_by' => $request->user()->id,
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'deadline' => $validated['deadline'],
            'status' => StatusTugas::BelumDikerjakan,
        ]);

        // Notifikasi penugasan (in-app)
        $penerima = $task->penerima;
        NotificationService::kirim(
            $penerima,
            JenisNotifikasi::Penugasan,
            'Anda ditugaskan "'.$task->judul.'" di acara "'.$event->nama_event.'" dengan batas waktu '.Carbon::parse($task->deadline)->isoFormat('D MMMM Y').'.',
            $event,
            $task,
            '/tugas/'.$task->id,
        );

        // Activity log
        ActivityLogger::log(
            $event,
            $request->user(),
            'buat_tugas',
            'membuat tugas "'.$task->judul.'" dan menugaskan ke '.$penerima->nama,
            ['task_id' => $task->id, 'penerima_id' => $penerima->id]
        );

        return redirect()->route('acara.tugas.index', $event->id)
            ->with('status', 'Tugas "'.$task->judul.'" berhasil dibuat dan ditugaskan ke '.$penerima->nama.'.');
    }

    /**
     * Edit tugas (leader only).
     */
    public function update(Request $request, Event $event, Task $task): RedirectResponse
    {
        $this->authorize('manage', $event);

        abort_if($task->event_id !== $event->id, 404);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'penerima_id' => ['required', 'exists:users,id'],
            'deadline' => ['required', 'date'],
        ]);

        if (! $event->isParticipant($validated['penerima_id'])) {
            return back()->withErrors(['penerima_id' => 'Penerima tugas harus merupakan peserta acara ini.'])->withInput();
        }

        if (Carbon::parse($validated['deadline'])->greaterThan($event->tanggal)) {
            return back()->withErrors(['deadline' => 'Deadline tugas tidak boleh melewati tanggal pelaksanaan acara.'])->withInput();
        }

        $task->update([
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'division_id' => $validated['division_id'] ?? null,
            'penerima_id' => $validated['penerima_id'],
            'deadline' => $validated['deadline'],
        ]);

        ActivityLogger::log($event, $request->user(), 'ubah_tugas', 'mengubah detail tugas "'.$task->judul.'"');

        return redirect()->route('acara.tugas.index', $event->id)
            ->with('status', 'Tugas "'.$task->judul.'" berhasil diperbarui.');
    }

    /**
     * Hapus tugas (leader only).
     */
    public function destroy(Event $event, Task $task): RedirectResponse
    {
        $this->authorize('manage', $event);

        abort_if($task->event_id !== $event->id, 404);

        $judul = $task->judul;
        $task->delete();

        ActivityLogger::log($event, auth()->user(), 'hapus_tugas', 'menghapus tugas "'.$judul.'"');

        return redirect()->route('acara.tugas.index', $event->id)
            ->with('status', 'Tugas "'.$judul.'" berhasil dihapus.');
    }
}
