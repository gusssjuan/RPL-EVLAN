<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEvent;
use App\Enums\StatusVerifikasi;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Event;
use App\Models\Template;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $totalPengguna = User::count();
        $acaraAktif = Event::where('status_event', StatusEvent::Perencanaan)->count();
        $vendorMenungguCount = Vendor::where('status_verifikasi', StatusVerifikasi::Menunggu)->count();
        $totalKategori = Category::count();
        $totalTemplate = Template::count();

        // Vendor menunggu verifikasi dengan data relasi user (tanpa N+1)
        $vendorMenunggu = Vendor::with('user')
            ->where('status_verifikasi', StatusVerifikasi::Menunggu)
            ->latest()
            ->take(5)
            ->get();

        // Aktivitas terbaru dengan user dan event (tanpa N+1)
        $aktivitasTerbaru = ActivityLog::with(['user', 'event'])
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'totalPengguna',
            'acaraAktif',
            'vendorMenungguCount',
            'totalKategori',
            'totalTemplate',
            'vendorMenunggu',
            'aktivitasTerbaru'
        ));
    }
}
