<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Models\Checklist;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChecklistController extends Controller
{
    public function index(Request $request, Event $event): View
    {
        $this->authorize('view', $event);

        $filter = $request->query('status', 'semua');
        $search = $request->query('cari');

        $query = $event->checklists()->orderBy('urutan')->orderBy('id');

        if ($filter === 'terpenuhi') {
            $query->where('status_selesai', true);
        } elseif ($filter === 'belum') {
            $query->where('status_selesai', false);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kebutuhan', 'like', "%{$search}%")
                    ->orWhere('kelompok', 'like', "%{$search}%");
            });
        }

        $checklists = $query->get();

        $totalCount = $event->checklists()->count();
        $terpenuhiCount = $event->checklists()->where('status_selesai', true)->count();
        $belumCount = $totalCount - $terpenuhiCount;
        $progress = $totalCount > 0 ? round(($terpenuhiCount / $totalCount) * 100) : 0;

        $isLeader = $event->isLeader($request->user());

        return view('acara.checklist.index', compact(
            'event',
            'checklists',
            'totalCount',
            'terpenuhiCount',
            'belumCount',
            'progress',
            'filter',
            'search',
            'isLeader'
        ));
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('manage', $event);

        $validated = $request->validate([
            'nama_kebutuhan' => ['required', 'string', 'max:255'],
            'kelompok' => ['nullable', 'string', 'max:100'],
        ]);

        $maxUrutan = $event->checklists()->max('urutan') ?? 0;

        $event->checklists()->create([
            'nama_kebutuhan' => $validated['nama_kebutuhan'],
            'kelompok' => $validated['kelompok'] ?? 'Umum',
            'dari_template' => false,
            'status_selesai' => false,
            'urutan' => $maxUrutan + 1,
        ]);

        return redirect()->route('acara.checklist.index', $event->id)
            ->with('status', 'Kebutuhan baru berhasil ditambahkan.');
    }

    public function toggle(Request $request, Event $event, Checklist $checklist): RedirectResponse|JsonResponse
    {
        $this->authorize('manage', $event);

        abort_if($checklist->event_id !== $event->id, 404);

        $checklist->update([
            'status_selesai' => ! $checklist->status_selesai,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status_selesai' => $checklist->status_selesai,
            ]);
        }

        return back()->with('status', 'Status kebutuhan berhasil diperbarui.');
    }

    public function update(Request $request, Event $event, Checklist $checklist): RedirectResponse
    {
        $this->authorize('manage', $event);

        abort_if($checklist->event_id !== $event->id, 404);

        $validated = $request->validate([
            'nama_kebutuhan' => ['required', 'string', 'max:255'],
            'kelompok' => ['nullable', 'string', 'max:100'],
        ]);

        $checklist->update($validated);

        return redirect()->route('acara.checklist.index', $event->id)
            ->with('status', 'Kebutuhan berhasil diperbarui.');
    }

    public function destroy(Event $event, Checklist $checklist): RedirectResponse
    {
        $this->authorize('manage', $event);

        abort_if($checklist->event_id !== $event->id, 404);

        $checklist->delete();

        return redirect()->route('acara.checklist.index', $event->id)
            ->with('status', 'Kebutuhan berhasil dihapus.');
    }
}
