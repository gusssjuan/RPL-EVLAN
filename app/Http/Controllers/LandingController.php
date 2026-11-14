<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(): View
    {
        $kegiatan = Event::query()
            ->publik()
            ->with(['category', 'leader'])
            ->withCount([
                'registrations as diterima_count' => fn ($query) => $query->where('status', 'diterima'),
            ])
            ->whereDate('tanggal', '>=', today())
            ->orderBy('tanggal')
            ->orderBy('id')
            ->limit(3)
            ->get();

        return view('welcome', compact('kegiatan'));
    }
}
