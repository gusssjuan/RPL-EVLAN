<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect('/admin');
        }

        if ($user->isVendor()) {
            return redirect('/usaha');
        }

        // Pengguna: cek apakah memimpin minimal 1 acara (Varian Ketua vs Anggota)
        $isLeader = $user->eventsLed()->exists();

        if ($isLeader) {
            return view('dashboard.ketua', [
                'user' => $user,
            ]);
        }

        return view('dashboard.anggota', [
            'user' => $user,
        ]);
    }
}

