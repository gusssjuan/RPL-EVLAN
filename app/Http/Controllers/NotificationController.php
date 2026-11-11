<?php

namespace App\Http\Controllers;

use App\Enums\JenisNotifikasi;
use App\Models\AppNotification;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $status = $request->query('status') === 'belum' ? 'belum' : 'semua';
        $jenis = JenisNotifikasi::tryFrom((string) $request->query('jenis'));

        $query = $user->appNotifications()
            ->with('event')
            ->orderByDesc('waktu_kirim')
            ->orderByDesc('id');

        if ($status === 'belum') {
            $query->belumDibaca();
        }

        if ($jenis !== null) {
            $query->where('jenis', $jenis->value);
        }

        $notifications = $query->paginate(20)->withQueryString();
        $grouped = $notifications->getCollection()->groupBy(
            fn (AppNotification $notification) => $this->kelompok($notification->waktu_kirim)
        );

        return view('notifikasi.index', [
            'notifications' => $notifications,
            'grouped' => $grouped,
            'status' => $status,
            'jenis' => $jenis,
            'jumlahBelumDibaca' => NotificationService::jumlahBelumDibaca($user),
            'layout' => match (true) {
                $user->isAdmin() => 'layouts.admin',
                $user->isVendor() => 'layouts.vendor',
                default => 'layouts.app',
            },
        ]);
    }

    public function baca(Request $request, AppNotification $notification): RedirectResponse
    {
        abort_if($notification->user_id !== $request->user()->id, 403);

        if ($notification->dibaca_at === null) {
            $notification->update(['dibaca_at' => now()]);
        }

        $tautan = $notification->tautan;
        if ($tautan !== null && str_starts_with($tautan, '/') && ! str_starts_with($tautan, '//')) {
            return redirect()->to($tautan);
        }

        return redirect()->route('notifikasi.index');
    }

    public function bacaSemua(Request $request): RedirectResponse
    {
        $request->user()->appNotifications()
            ->belumDibaca()
            ->update(['dibaca_at' => now()]);

        return back()->with('status', 'Semua notifikasi berhasil ditandai sudah dibaca.');
    }

    private function kelompok(?Carbon $waktu): string
    {
        $waktu ??= now();

        if ($waktu->isToday()) {
            return 'Hari ini';
        }

        if ($waktu->isYesterday()) {
            return 'Kemarin';
        }

        if ($waktu->greaterThanOrEqualTo(now()->startOfWeek())) {
            return 'Minggu ini';
        }

        return 'Lebih lama';
    }
}
