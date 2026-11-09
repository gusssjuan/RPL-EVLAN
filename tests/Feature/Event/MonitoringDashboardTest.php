<?php

namespace Tests\Feature\Event;

use App\Enums\RoleName;
use App\Enums\StatusTugas;
use App\Models\Category;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitoringDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $leader;

    protected User $member;

    protected User $outsider;

    protected Category $category;

    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);

        $this->leader = User::factory()->create(['nama' => 'Gus Ega']);
        $this->member = User::factory()->create(['nama' => 'Rina Astuti']);
        $this->outsider = User::factory()->create(['nama' => 'Orang Lain']);

        $this->category = Category::factory()->create(['nama_kategori' => 'Seminar']);

        $this->event = Event::factory()->create([
            'leader_id' => $this->leader->id,
            'category_id' => $this->category->id,
            'nama_event' => 'Seminar Nasional EVLAN 2026',
            'tanggal' => Carbon::parse('2026-12-12'),
        ]);

        EventMember::create([
            'event_id' => $this->event->id,
            'user_id' => $this->member->id,
        ]);
    }

    public function test_ketua_dapat_mengakses_dashboard_monitoring_s18(): void
    {
        // 10 tugas selesai, 14 tugas belum -> 42%
        for ($i = 1; $i <= 10; $i++) {
            Task::factory()->create([
                'event_id' => $this->event->id,
                'created_by' => $this->leader->id,
                'status' => StatusTugas::SudahDiselesaikan,
            ]);
        }
        for ($i = 1; $i <= 14; $i++) {
            Task::factory()->create([
                'event_id' => $this->event->id,
                'created_by' => $this->leader->id,
                'status' => StatusTugas::BelumDikerjakan,
            ]);
        }

        $response = $this->actingAs($this->leader)->get('/acara/'.$this->event->id.'/monitoring');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Monitoring Terpadu');
        $response->assertSee('42%');
        $response->assertSee('Evaluasi &amp; Tutup Acara', false);
    }

    public function test_anggota_dilarang_mengakses_monitoring_d16(): void
    {
        $response = $this->actingAs($this->member)->get('/acara/'.$this->event->id.'/monitoring');

        $response->assertStatus(403);
    }

    public function test_pengguna_luar_dilarang_mengakses_monitoring(): void
    {
        $response = $this->actingAs($this->outsider)->get('/acara/'.$this->event->id.'/monitoring');

        $response->assertStatus(403);
    }
}
