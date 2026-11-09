<?php

namespace App\Http\Controllers\Event;

use App\Enums\StatusEvent;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\ActivityLogger;
use App\Services\ProgressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EvaluationController extends Controller
{
    /**
     * Tampilkan form Evaluasi & Penutupan Acara (S19).
     */
    public function show(Request $request, Event $event): View
    {
        $this->authorize('close', $event);

        $event->load(['category', 'leader', 'divisions']);

        $monitoring = ProgressService::forEvent($event);

        return view('acara.evaluasi', compact('event', 'monitoring'));
    }

    /**
     * Eksekusi penutupan acara dan simpan evaluasi (Process Spec 5.0, FR-21).
     */
    public function close(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('close', $event);

        $validated = $request->validate([
            'evaluasi' => ['required', 'string', 'min:5'],
        ], [
            'evaluasi.required' => 'Catatan evaluasi wajib diisi sebelum menutup acara.',
            'evaluasi.min' => 'Catatan evaluasi minimal terdiri dari 5 karakter.',
        ]);

        $event->update([
            'status_event' => StatusEvent::Selesai,
            'evaluasi' => $validated['evaluasi'],
            'closed_at' => now(),
        ]);

        ActivityLogger::log(
            $event,
            $request->user(),
            'tutup_acara',
            'menutup acara dan mencatat evaluasi akhir'
        );

        return redirect()->route('acara.ringkasan', $event->id)
            ->with('status', 'Acara berhasil ditutup dan dievaluasi. Data acara kini dikunci (read-only).');
    }
}

