<?php

namespace App\Console\Commands;

use App\Enums\JenisNotifikasi;
use App\Enums\StatusEvent;
use App\Enums\StatusTugas;
use App\Models\Task;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class KirimReminderDeadline extends Command
{
    protected $signature = 'evlan:kirim-reminder-deadline';

    protected $description = 'Kirim notifikasi in-app untuk tugas yang mendekati atau melewati deadline';

    public function handle(): int
    {
        $today = Carbon::today();
        $batas = $today->copy()->addDay()->toDateString();

        Task::query()
            ->with(['event.leader', 'penerima'])
            ->where('status', '!=', StatusTugas::SudahDiselesaikan->value)
            ->whereHas('event', fn (Builder $query) => $query->where('status_event', '!=', StatusEvent::Selesai->value))
            ->whereDate('deadline', '<=', $batas)
            ->chunkById(100, function (Collection $tasks) use ($today) {
                foreach ($tasks as $task) {
                    $selisihHari = (int) $today->diffInDays($task->deadline, false);

                    if ($selisihHari > 1) {
                        continue;
                    }

                    $tanggal = $task->deadline->isoFormat('D MMMM Y');
                    $kondisi = $selisihHari < 0 ? 'terlewat' : 'mendekati';

                    $pesan = match (true) {
                        $selisihHari < 0 => 'Deadline tugas "'.$task->judul.'" di acara "'.$task->event->nama_event.'" telah terlewat pada '.$tanggal.'.',
                        $selisihHari === 0 => 'Deadline tugas "'.$task->judul.'" di acara "'.$task->event->nama_event.'" jatuh tempo hari ini ('.$tanggal.').',
                        default => 'Deadline tugas "'.$task->judul.'" di acara "'.$task->event->nama_event.'" jatuh tempo besok ('.$tanggal.').',
                    };

                    $penerima = collect([$task->penerima, $task->event->leader])
                        ->filter()
                        ->unique('id');

                    foreach ($penerima as $user) {
                        NotificationService::kirim(
                            $user,
                            JenisNotifikasi::Deadline,
                            $pesan,
                            $task->event,
                            $task,
                            '/tugas/'.$task->id,
                            'deadline:'.$task->id.':'.$kondisi,
                        );
                    }
                }
            });

        $this->info('Reminder deadline selesai diproses.');

        return self::SUCCESS;
    }
}
