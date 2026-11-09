<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Timeline;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TimelineController extends Controller
{
    public function index(Request $request, Event $event): View
    {
        $this->authorize('view', $event);

        // Urutan menurun dari H-terjauh ke Hari H (offset_hari DESC)
        $timelines = $event->timelines()
            ->orderBy('offset_hari', 'desc')
            ->get();

        $today = Carbon::today();
        $eventDate = Carbon::parse($event->tanggal);

        // Hitung status terlewat/hari ini/akan datang untuk setiap item
        $timelines = $timelines->map(function ($item) {
            $itemDate = Carbon::parse($item->tanggal);
            if ($itemDate->isPast() && ! $itemDate->isToday()) {
                $item->status_waktu = 'terlewat';
            } elseif ($itemDate->isToday()) {
                $item->status_waktu = 'hari_ini';
            } else {
                $item->status_waktu = 'akan_datang';
            }

            return $item;
        });

        $isLeader = $event->isLeader($request->user());

        return view('acara.timeline.index', compact('event', 'timelines', 'isLeader'));
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('manage', $event);

        $validated = $request->validate([
            'kegiatan' => ['required', 'string', 'max:255'],
            'offset_hari' => ['required', 'integer', 'min:0'],
        ]);

        $offset = (int) $validated['offset_hari'];
        $eventDate = Carbon::parse($event->tanggal);
        $tgl = $eventDate->copy()->subDays($offset);

        $label = $offset === 0 ? 'Hari H' : 'H-'.$offset;

        $maxUrutan = $event->timelines()->max('urutan') ?? 0;

        $event->timelines()->create([
            'label' => $label,
            'offset_hari' => $offset,
            'tanggal' => $tgl->toDateString(),
            'kegiatan' => $validated['kegiatan'],
            'urutan' => $maxUrutan + 1,
        ]);

        return redirect()->route('acara.timeline.index', $event->id)
            ->with('status', 'Agenda timeline baru berhasil ditambahkan.');
    }

    public function update(Request $request, Event $event, Timeline $timeline): RedirectResponse
    {
        $this->authorize('manage', $event);

        abort_if($timeline->event_id !== $event->id, 404);

        $validated = $request->validate([
            'kegiatan' => ['required', 'string', 'max:255'],
            'offset_hari' => ['required', 'integer', 'min:0'],
        ]);

        $offset = (int) $validated['offset_hari'];
        $eventDate = Carbon::parse($event->tanggal);
        $tgl = $eventDate->copy()->subDays($offset);
        $label = $offset === 0 ? 'Hari H' : 'H-'.$offset;

        $timeline->update([
            'kegiatan' => $validated['kegiatan'],
            'offset_hari' => $offset,
            'label' => $label,
            'tanggal' => $tgl->toDateString(),
        ]);

        return redirect()->route('acara.timeline.index', $event->id)
            ->with('status', 'Agenda timeline berhasil diperbarui.');
    }

    public function destroy(Event $event, Timeline $timeline): RedirectResponse
    {
        $this->authorize('manage', $event);

        abort_if($timeline->event_id !== $event->id, 404);

        $timeline->delete();

        return redirect()->route('acara.timeline.index', $event->id)
            ->with('status', 'Agenda timeline berhasil dihapus.');
    }
}
