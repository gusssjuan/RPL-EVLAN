<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\TolakPendaftaranRequest;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Services\EventRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

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

        try {
            $registrationService->terima($registration);
        } catch (ValidationException $exception) {
            $pesan = collect($exception->errors())->flatten()->first() ?? 'Pendaftaran belum dapat diterima.';

            return back()
                ->withErrors($exception->errors())
                ->with('error', $pesan)
                ->with('peringatan', $pesan);
        }

        $pesan = 'Pendaftar "'.$nama.'" berhasil diterima.';

        return back()->with('status', $pesan)->with('sukses', $pesan);
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

        try {
            $registrationService->tolak($registration, $request->validated('alasan_penolakan'));
        } catch (ValidationException $exception) {
            $pesan = collect($exception->errors())->flatten()->first() ?? 'Pendaftaran belum dapat ditolak.';

            return back()
                ->withErrors($exception->errors())
                ->with('error', $pesan)
                ->with('peringatan', $pesan);
        }

        $pesan = 'Pendaftar "'.$nama.'" ditolak.';

        return back()->with('status', $pesan)->with('sukses', $pesan);
    }
}
