<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventHistoryController extends Controller
{
    /**
     * Tampilkan halaman Riwayat Acara (S24).
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Ambil ID seluruh acara yang dipimpin atau diikuti pengguna
        $ledIds = $user->eventsLed()->pluck('id');
        $joinedIds = $user->eventsJoined()->pluck('events.id');
        $allEventIds = $ledIds->concat($joinedIds)->unique();

        $query = Event::whereIn('id', $allEventIds)
            ->with(['category', 'leader', 'tasks', 'checklists']);

        // Filter status (Semua, Selesai, Perencanaan)
        if ($request->filled('status') && in_array($request->status, ['selesai', 'perencanaan'])) {
            $query->where('status_event', $request->status);
        }

        // Filter tahun
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        // Pencarian
        if ($request->filled('cari')) {
            $query->where('nama_event', 'like', '%'.$request->cari.'%');
        }

        $events = $query->orderByDesc('tanggal')->get();

        // Tandai peran pengguna pada tiap acara
        $events = $events->map(function ($ev) use ($user) {
            $ev->peran_user = $ev->isLeader($user) ? 'ketua' : 'anggota';

            return $ev;
        });

        // Ambil daftar tahun unik dari seluruh acara pengguna
        $allYears = Event::whereIn('id', $allEventIds)
            ->selectRaw('YEAR(tanggal) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        // Hitungan
        $countSemua = $allEventIds->count();
        $countSelesai = Event::whereIn('id', $allEventIds)->where('status_event', 'selesai')->count();
        $countPerencanaan = Event::whereIn('id', $allEventIds)->where('status_event', 'perencanaan')->count();

        return view('riwayat.index', compact(
            'events',
            'allYears',
            'countSemua',
            'countSelesai',
            'countPerencanaan'
        ));
    }
}
