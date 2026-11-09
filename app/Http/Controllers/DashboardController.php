<?php

namespace App\Http\Controllers;

use App\Enums\StatusTugas;
use App\Models\ActivityLog;
use App\Models\Task;
use Carbon\Carbon;
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
            $eventsLed = $user->eventsLed()
                ->with(['category', 'tasks', 'timelines', 'budgets'])
                ->latest()
                ->get();

            $eventIds = $eventsLed->pluck('id');

            $statAcaraAktif = $eventsLed->where('status_event.value', 'perencanaan')->count();
            $statTotalTugas = Task::whereIn('event_id', $eventIds)->count();
            $statTugasSelesai = Task::whereIn('event_id', $eventIds)
                ->where('status', StatusTugas::SudahDiselesaikan)
                ->count();

            $statTotalAnggaran = $eventsLed->sum(fn ($e) => $e->budgets->sum('total'));

            $acaraUtama = $eventsLed->first();

            $deadlines = Task::whereIn('event_id', $eventIds)
                ->with(['event', 'penerima'])
                ->where('status', '!=', StatusTugas::SudahDiselesaikan)
                ->orderBy('deadline')
                ->take(5)
                ->get();

            $aktivitasTerbaru = ActivityLog::whereIn('event_id', $eventIds)
                ->with(['user', 'event'])
                ->latest()
                ->take(6)
                ->get();

            return view('dashboard.ketua', compact(
                'user',
                'eventsLed',
                'statAcaraAktif',
                'statTotalTugas',
                'statTugasSelesai',
                'statTotalAnggaran',
                'acaraUtama',
                'deadlines',
                'aktivitasTerbaru'
            ));
        }

        // Varian Anggota (S05-V)
        $statTugasSaya = $user->tasksAssigned()->count();
        $statTugasSelesai = $user->tasksAssigned()
            ->where('status', StatusTugas::SudahDiselesaikan)
            ->count();
        $statTugasTerlewat = $user->tasksAssigned()
            ->where('status', '!=', StatusTugas::SudahDiselesaikan)
            ->where('deadline', '<', Carbon::today())
            ->count();
        $statAcaraDiikuti = $user->eventsJoined()->count();

        $tugasPrioritas = $user->tasksAssigned()
            ->with(['event.category', 'division'])
            ->where('status', '!=', StatusTugas::SudahDiselesaikan)
            ->orderBy('deadline')
            ->take(6)
            ->get();

        $acaraDiikuti = $user->eventsJoined()
            ->with(['category', 'leader'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.anggota', compact(
            'user',
            'statTugasSaya',
            'statTugasSelesai',
            'statTugasTerlewat',
            'statAcaraDiikuti',
            'tugasPrioritas',
            'acaraDiikuti'
        ));
    }
}
