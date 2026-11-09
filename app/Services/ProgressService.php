<?php

namespace App\Services;

use App\Enums\StatusTugas;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ProgressService
{
    /**
     * Hitung ringkasan data progres komprehensif untuk suatu event.
     * Menggunakan query agregat terstruktur (bukan loop per tugas).
     *
     * @return array{
     *     persentase: int,
     *     total_tugas: int,
     *     tugas_selesai: int,
     *     tugas_sedang: int,
     *     tugas_belum: int,
     *     tugas_terlewat: int,
     *     progres_divisi: Collection,
     *     deadline_terdekat: Collection,
     *     deadline_terlewat: Collection,
     *     total_checklist: int,
     *     checklist_terpenuhi: int,
     *     persen_checklist: int,
     *     total_anggaran: int
     * }
     */
    public static function forEvent(Event $event): array
    {
        $today = Carbon::today()->toDateString();

        // 1. Agregasi status tugas dalam 1 query SQL efisien
        $taskCounts = $event->tasks()->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'sudah_diselesaikan' THEN 1 ELSE 0 END) as selesai,
            SUM(CASE WHEN status = 'sedang_dikerjakan' THEN 1 ELSE 0 END) as sedang,
            SUM(CASE WHEN status = 'belum_dikerjakan' THEN 1 ELSE 0 END) as belum,
            SUM(CASE WHEN status != 'sudah_diselesaikan' AND deadline < ? THEN 1 ELSE 0 END) as terlewat
        ", [$today])->first();

        $totalTugas = (int) ($taskCounts->total ?? 0);
        $tugasSelesai = (int) ($taskCounts->selesai ?? 0);
        $tugasSedang = (int) ($taskCounts->sedang ?? 0);
        $tugasBelum = (int) ($taskCounts->belum ?? 0);
        $tugasTerlewat = (int) ($taskCounts->terlewat ?? 0);

        // Rumus (Process Spec 5.0): jumlah_tugas = 0 -> 0, selain itu selesai / total * 100 dibulatkan
        $persentase = $totalTugas === 0 ? 0 : (int) round(($tugasSelesai / $totalTugas) * 100);

        // 2. Progres per divisi via group by agregat
        $divisionStats = $event->tasks()
            ->selectRaw("
                division_id,
                COUNT(*) as total,
                SUM(CASE WHEN status = 'sudah_diselesaikan' THEN 1 ELSE 0 END) as selesai
            ")
            ->whereNotNull('division_id')
            ->groupBy('division_id')
            ->get()
            ->keyBy('division_id');

        $progresDivisi = $event->divisions->map(function ($division) use ($divisionStats) {
            $stat = $divisionStats->get($division->id);
            $divTotal = (int) ($stat->total ?? 0);
            $divSelesai = (int) ($stat->selesai ?? 0);
            $divPersen = $divTotal === 0 ? 0 : (int) round(($divSelesai / $divTotal) * 100);

            return [
                'id' => $division->id,
                'nama_divisi' => $division->nama_divisi,
                'total' => $divTotal,
                'selesai' => $divSelesai,
                'persentase' => $divPersen,
            ];
        });

        // 3. Deadline terdekat & terlewat
        $deadlines = $event->tasks()
            ->with(['penerima', 'division'])
            ->where('status', '!=', StatusTugas::SudahDiselesaikan)
            ->orderBy('deadline')
            ->take(6)
            ->get();

        $deadlinesTerlewat = $event->tasks()
            ->with(['penerima', 'division'])
            ->where('status', '!=', StatusTugas::SudahDiselesaikan)
            ->whereDate('deadline', '<', Carbon::today())
            ->orderBy('deadline')
            ->take(5)
            ->get();

        // 4. Checklist kebutuhan
        $checklistCounts = $event->checklists()->selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN status_selesai = 1 THEN 1 ELSE 0 END) as terpenuhi
        ')->first();

        $totalChecklist = (int) ($checklistCounts->total ?? 0);
        $checklistTerpenuhi = (int) ($checklistCounts->terpenuhi ?? 0);
        $persenChecklist = $totalChecklist === 0 ? 0 : (int) round(($checklistTerpenuhi / $totalChecklist) * 100);

        // 5. Total Anggaran
        $totalAnggaran = (int) $event->budgets()->sum('total');

        return [
            'persentase' => $persentase,
            'total_tugas' => $totalTugas,
            'tugas_selesai' => $tugasSelesai,
            'tugas_sedang' => $tugasSedang,
            'tugas_belum' => $tugasBelum,
            'tugas_terlewat' => $tugasTerlewat,
            'progres_divisi' => $progresDivisi,
            'deadline_terdekat' => $deadlines,
            'deadline_terlewat' => $deadlinesTerlewat,
            'total_checklist' => $totalChecklist,
            'checklist_terpenuhi' => $checklistTerpenuhi,
            'persen_checklist' => $persenChecklist,
            'total_anggaran' => $totalAnggaran,
        ];
    }
}
