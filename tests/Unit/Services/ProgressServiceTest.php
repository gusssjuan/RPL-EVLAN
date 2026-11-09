<?php

namespace Tests\Unit\Services;

use App\Enums\RoleName;
use App\Enums\StatusTugas;
use App\Models\Category;
use App\Models\Checklist;
use App\Models\Division;
use App\Models\Event;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Services\ProgressService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $leader;
    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);

        $this->leader = User::factory()->create();
        $category = Category::factory()->create();

        $this->event = Event::factory()->create([
            'leader_id' => $this->leader->id,
            'category_id' => $category->id,
            'tanggal' => Carbon::parse('2026-12-12'),
        ]);
    }

    public function test_progres_nol_ketika_tidak_ada_tugas(): void
    {
        $data = ProgressService::forEvent($this->event);

        $this->assertEquals(0, $data['persentase']);
        $this->assertEquals(0, $data['total_tugas']);
        $this->assertEquals(0, $data['tugas_selesai']);
    }

    public function test_progres_sepuluh_dari_dua_puluh_empat_adalah_empat_puluh_dua_persen(): void
    {
        // 10 tugas selesai
        for ($i = 1; $i <= 10; $i++) {
            Task::factory()->create([
                'event_id' => $this->event->id,
                'created_by' => $this->leader->id,
                'status' => StatusTugas::SudahDiselesaikan,
                'deadline' => Carbon::tomorrow(),
            ]);
        }

        // 5 tugas sedang dikerjakan
        for ($i = 1; $i <= 5; $i++) {
            Task::factory()->create([
                'event_id' => $this->event->id,
                'created_by' => $this->leader->id,
                'status' => StatusTugas::SedangDikerjakan,
                'deadline' => Carbon::tomorrow(),
            ]);
        }

        // 9 tugas belum dikerjakan
        for ($i = 1; $i <= 9; $i++) {
            Task::factory()->create([
                'event_id' => $this->event->id,
                'created_by' => $this->leader->id,
                'status' => StatusTugas::BelumDikerjakan,
                'deadline' => Carbon::tomorrow(),
            ]);
        }

        // Total 24 tugas, 10 selesai -> 10/24 * 100 = 41.67% -> dibulatkan 42%
        $data = ProgressService::forEvent($this->event);

        $this->assertEquals(24, $data['total_tugas']);
        $this->assertEquals(10, $data['tugas_selesai']);
        $this->assertEquals(5, $data['tugas_sedang']);
        $this->assertEquals(9, $data['tugas_belum']);
        $this->assertEquals(42, $data['persentase']);
    }

    public function test_tugas_terlewat_dihitung_berdasarkan_deadline_d13(): void
    {
        // 2 tugas lewat deadline dan belum selesai
        Task::factory()->create([
            'event_id' => $this->event->id,
            'created_by' => $this->leader->id,
            'status' => StatusTugas::BelumDikerjakan,
            'deadline' => Carbon::yesterday(),
        ]);

        Task::factory()->create([
            'event_id' => $this->event->id,
            'created_by' => $this->leader->id,
            'status' => StatusTugas::SedangDikerjakan,
            'deadline' => Carbon::yesterday()->subDays(2),
        ]);

        // 1 tugas lewat deadline tapi SUDAH SELESAI (tidak boleh dihitung terlewat)
        Task::factory()->create([
            'event_id' => $this->event->id,
            'created_by' => $this->leader->id,
            'status' => StatusTugas::SudahDiselesaikan,
            'deadline' => Carbon::yesterday(),
        ]);

        $data = ProgressService::forEvent($this->event);

        $this->assertEquals(2, $data['tugas_terlewat']);
    }

    public function test_kalkulasi_progres_per_divisi_dan_checklist(): void
    {
        $divAcara = Division::create(['event_id' => $this->event->id, 'nama_divisi' => 'Acara']);
        $divKonsumsi = Division::create(['event_id' => $this->event->id, 'nama_divisi' => 'Konsumsi']);

        // Divisi Acara: 2 tugas, 1 selesai -> 50%
        Task::factory()->create([
            'event_id' => $this->event->id,
            'division_id' => $divAcara->id,
            'created_by' => $this->leader->id,
            'status' => StatusTugas::SudahDiselesaikan,
        ]);
        Task::factory()->create([
            'event_id' => $this->event->id,
            'division_id' => $divAcara->id,
            'created_by' => $this->leader->id,
            'status' => StatusTugas::BelumDikerjakan,
        ]);

        // Checklists: 4 total, 3 terpenuhi -> 75%
        Checklist::factory()->create(['event_id' => $this->event->id, 'status_selesai' => true]);
        Checklist::factory()->create(['event_id' => $this->event->id, 'status_selesai' => true]);
        Checklist::factory()->create(['event_id' => $this->event->id, 'status_selesai' => true]);
        Checklist::factory()->create(['event_id' => $this->event->id, 'status_selesai' => false]);

        $data = ProgressService::forEvent($this->event);

        $divAcaraData = $data['progres_divisi']->firstWhere('id', $divAcara->id);
        $this->assertEquals(50, $divAcaraData['persentase']);

        $this->assertEquals(4, $data['total_checklist']);
        $this->assertEquals(3, $data['checklist_terpenuhi']);
        $this->assertEquals(75, $data['persen_checklist']);
    }
}

