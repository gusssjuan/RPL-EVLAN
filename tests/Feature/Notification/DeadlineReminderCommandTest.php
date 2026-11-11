<?php

namespace Tests\Feature\Notification;

use App\Enums\JenisNotifikasi;
use App\Enums\StatusEvent;
use App\Enums\StatusTugas;
use App\Models\Category;
use App\Models\Event;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeadlineReminderCommandTest extends TestCase
{
    use RefreshDatabase;

    protected User $leader;

    protected User $member;

    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-11-10 07:00:00');

        $this->leader = User::factory()->create(['nama' => 'Gus Ega']);
        $this->member = User::factory()->create(['nama' => 'Rina Astuti']);
        $category = Category::factory()->create();

        $this->event = Event::factory()->create([
            'leader_id' => $this->leader->id,
            'category_id' => $category->id,
            'nama_event' => 'Seminar Teknologi 2026',
            'tanggal' => '2026-12-12',
            'status_event' => StatusEvent::Perencanaan,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_reminder_mengirim_ke_penerima_dan_leader_untuk_deadline_hari_ini_besok_dan_terlewat(): void
    {
        $hariIni = $this->buatTugas('Tugas Hari Ini', '2026-11-10');
        $besok = $this->buatTugas('Tugas Besok', '2026-11-11');
        $terlewat = $this->buatTugas('Tugas Terlewat', '2026-11-09');

        $this->artisan('evlan:kirim-reminder-deadline')->assertSuccessful();

        foreach ([$hariIni, $besok, $terlewat] as $task) {
            $this->assertDatabaseHas('app_notifications', [
                'user_id' => $this->member->id,
                'task_id' => $task->id,
                'jenis' => JenisNotifikasi::Deadline->value,
            ]);
            $this->assertDatabaseHas('app_notifications', [
                'user_id' => $this->leader->id,
                'task_id' => $task->id,
                'jenis' => JenisNotifikasi::Deadline->value,
            ]);
        }

        $this->assertDatabaseHas('app_notifications', [
            'task_id' => $hariIni->id,
            'dedupe_key' => 'deadline:'.$hariIni->id.':mendekati',
        ]);
        $this->assertDatabaseHas('app_notifications', [
            'task_id' => $terlewat->id,
            'dedupe_key' => 'deadline:'.$terlewat->id.':terlewat',
        ]);
        $this->assertDatabaseCount('app_notifications', 6);
    }

    public function test_reminder_idempoten_saat_dijalankan_dua_kali(): void
    {
        $this->buatTugas('Tugas Hari Ini', '2026-11-10');

        $this->artisan('evlan:kirim-reminder-deadline')->assertSuccessful();
        $this->artisan('evlan:kirim-reminder-deadline')->assertSuccessful();

        $this->assertDatabaseCount('app_notifications', 2);
    }

    public function test_tugas_selesai_dan_acara_selesai_dilewati(): void
    {
        $this->buatTugas('Tugas Selesai', '2026-11-10', StatusTugas::SudahDiselesaikan);

        $acaraSelesai = Event::factory()->selesai()->create([
            'leader_id' => $this->leader->id,
            'category_id' => $this->event->category_id,
            'tanggal' => '2026-12-12',
        ]);
        Task::factory()->create([
            'event_id' => $acaraSelesai->id,
            'division_id' => null,
            'penerima_id' => $this->member->id,
            'created_by' => $this->leader->id,
            'judul' => 'Tugas Acara Selesai',
            'deadline' => '2026-11-10',
        ]);

        $this->artisan('evlan:kirim-reminder-deadline')->assertSuccessful();

        $this->assertDatabaseCount('app_notifications', 0);
    }

    public function test_deadline_lebih_dari_besok_tidak_dikirim(): void
    {
        $this->buatTugas('Tugas Lusa', '2026-11-12');

        $this->artisan('evlan:kirim-reminder-deadline')->assertSuccessful();

        $this->assertDatabaseCount('app_notifications', 0);
    }

    public function test_penerima_yang_juga_leader_hanya_mendapat_satu_notifikasi(): void
    {
        $this->buatTugas('Tugas Untuk Leader', '2026-11-10', StatusTugas::BelumDikerjakan, $this->leader);

        $this->artisan('evlan:kirim-reminder-deadline')->assertSuccessful();

        $this->assertDatabaseCount('app_notifications', 1);
        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $this->leader->id,
            'task_id' => Task::where('judul', 'Tugas Untuk Leader')->first()->id,
        ]);
    }

    private function buatTugas(
        string $judul,
        string $deadline,
        StatusTugas $status = StatusTugas::BelumDikerjakan,
        ?User $penerima = null,
    ): Task {
        return Task::factory()->create([
            'event_id' => $this->event->id,
            'division_id' => null,
            'penerima_id' => ($penerima ?? $this->member)->id,
            'created_by' => $this->leader->id,
            'judul' => $judul,
            'deadline' => $deadline,
            'status' => $status,
            'completed_at' => $status === StatusTugas::SudahDiselesaikan ? now() : null,
        ]);
    }
}
