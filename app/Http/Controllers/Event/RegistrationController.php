<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\TolakPendaftaranRequest;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Services\EventRegistrationService;
use Illuminate\Http\RedirectResponse;

class RegistrationController extends Controller
{
    public function terima(
        Event $event,
        EventRegistration $registration,
        EventRegistrationService $registrationService
    ): RedirectResponse {
        abort_unless($registration->event_id === $event->id, 404);
        $registration->setRelation('event', $event);

        $this->authorize('manage', $registration);

        $nama = $registration->user()->value('nama');
        $registrationService->terima($registration);

        return back()->with('status', 'Pendaftar "'.$nama.'" berhasil diterima.');
    }

    public function tolak(
        TolakPendaftaranRequest $request,
        Event $event,
        EventRegistration $registration,
        EventRegistrationService $registrationService
    ): RedirectResponse {
        abort_unless($registration->event_id === $event->id, 404);
        $registration->setRelation('event', $event);

        $this->authorize('manage', $registration);

        $nama = $registration->user()->value('nama');
        $registrationService->tolak($registration, $request->validated('alasan_penolakan'));

        return back()->with('status', 'Pendaftar "'.$nama.'" ditolak.');
    }
}
