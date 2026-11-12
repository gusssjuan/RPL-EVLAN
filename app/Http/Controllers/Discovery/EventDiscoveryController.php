<?php

namespace App\Http\Controllers\Discovery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Discovery\CariAcaraRequest;
use App\Http\Requests\Discovery\EventRegistrationRequest;
use App\Models\Category;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Services\EventRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EventDiscoveryController extends Controller
{
    public function index(CariAcaraRequest $request): View
    {
        $filters = $request->validated();
        $search = $filters['cari'] ?? '';
        $kategori = $filters['kategori'] ?? null;
        $lokasi = $filters['lokasi'] ?? '';
        $dari = $filters['dari'] ?? null;
        $sampai = $filters['sampai'] ?? null;

        $query = Event::query()
            ->publik()
            ->with(['category', 'leader'])
            ->withCount([
                'registrations as diterima_count' => fn ($q) => $q->where('status', 'diterima'),
            ]);

        if ($kategori !== null) {
            $query->where('category_id', $kategori);
        }

        if ($lokasi !== '') {
            $query->where('lokasi', 'like', '%'.$lokasi.'%');
        }

        if ($dari !== null) {
            $query->whereDate('tanggal', '>=', $dari);
        }

        if ($sampai !== null) {
            $query->whereDate('tanggal', '<=', $sampai);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_event', 'like', '%'.$search.'%')
                    ->orWhere('tujuan', 'like', '%'.$search.'%')
                    ->orWhere('lokasi', 'like', '%'.$search.'%')
                    ->orWhere('penyelenggara', 'like', '%'.$search.'%')
                    ->orWhereHas('category', fn ($cq) => $cq->where('nama_kategori', 'like', '%'.$search.'%'));
            });
        }

        $events = $query->orderBy('tanggal')->paginate(9)->withQueryString();

        $categories = Category::query()
            ->whereHas('events', fn ($q) => $q->publik())
            ->orderBy('nama_kategori')
            ->get();

        $adaAcara = Event::query()->publik()->exists();
        $adaFilter = filled($search) || $kategori !== null || filled($lokasi) || $dari !== null || $sampai !== null;

        return view('cari-acara.index', compact(
            'events',
            'categories',
            'adaAcara',
            'adaFilter',
            'search',
            'kategori',
            'lokasi',
            'dari',
            'sampai'
        ));
    }

    public function show(Request $request, Event $event, EventRegistrationService $registrationService): View
    {
        abort_unless($event->is_publik, 404);

        $user = $request->user();
        abort_if($user !== null && ! $user->isUser(), 403);

        $event->load(['category', 'leader'])
            ->loadCount([
                'registrations as diterima_count' => fn ($q) => $q->where('status', 'diterima'),
            ]);

        $pendaftaranSaya = null;
        $alasanTidakBisaDaftar = null;

        if ($user !== null) {
            $pendaftaranSaya = $event->registrations()
                ->where('user_id', $user->id)
                ->first();

            $alasanTidakBisaDaftar = $pendaftaranSaya
                ? null
                : $registrationService->alasanTidakBisaDaftar($event, $user);
        }

        return view('cari-acara.show', compact(
            'event',
            'pendaftaranSaya',
            'alasanTidakBisaDaftar'
        ));
    }

    public function daftar(
        EventRegistrationRequest $request,
        Event $event,
        EventRegistrationService $registrationService
    ): RedirectResponse {
        abort_unless($event->is_publik, 404);

        try {
            $registrationService->daftar($event, $request->user(), $request->validated());
        } catch (ValidationException $exception) {
            $pesan = collect($exception->errors())->flatten()->first() ?? 'Pendaftaran belum dapat diproses.';

            return back()
                ->withErrors($exception->errors())
                ->with('peringatan', $pesan)
                ->with('error', $pesan);
        }

        return redirect()
            ->route('cari-acara.show', $event)
            ->with('status', 'Pendaftaran berhasil dikirim. Menunggu konfirmasi ketua acara.')
            ->with('sukses', 'Pendaftaran berhasil dikirim. Menunggu konfirmasi ketua acara.');
    }

    public function batalkan(
        Request $request,
        Event $event,
        EventRegistrationService $registrationService
    ): RedirectResponse {
        abort_unless($event->is_publik, 404);

        $registration = EventRegistration::query()
            ->where('event_id', $event->id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $registration->setRelation('event', $event);
        $this->authorize('cancel', $registration);

        try {
            $registrationService->batalkan($registration);
        } catch (ValidationException $exception) {
            $pesan = collect($exception->errors())->flatten()->first() ?? 'Pendaftaran tidak dapat dibatalkan.';

            return back()
                ->withErrors($exception->errors())
                ->with('peringatan', $pesan)
                ->with('error', $pesan);
        }

        return redirect()
            ->route('cari-acara.show', $event)
            ->with('status', 'Pendaftaran Anda berhasil dibatalkan.')
            ->with('sukses', 'Pendaftaran Anda berhasil dibatalkan.');
    }
}
