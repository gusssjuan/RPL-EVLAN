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

class UserDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $leader;
    protected User $memberOnly;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);

        $this->leader = User::factory()->create(['nama' => 'Gus Ega']);
        $this->memberOnly = User::factory()->create(['nama' => 'Rina Astuti']);

        $this->category = Category::factory()->create(['nama_kategori' => 'Seminar']);
    }

    public function test_ketua_melihat_dashboard_ketua_dengan_progres(): void
    {
        $event = Event::factory()->create([
            'leader_id' => $this->leader->id,
            'category_id' => $this->category->id,
            'nama_event' => 'Seminar Teknologi 2026',
            'tanggal' => Carbon::parse('2026-12-12'),
        ]);

        // Buat 24 tugas: 10 selesai, 14 belum/sedang -> 10/24 = 41.67% -> dibulatkan 42%
        for ($i = 1; $i <= 10; $i++) {
            Task::factory()->create([
                'event_id' => $event->id,
                'created_by' => $this->leader->id,
                'status' => StatusTugas::SudahDiselesaikan,
            ]);
        }
        for ($i = 1; $i <= 14; $i++) {
            Task::factory()->create([
                'event_id' => $event->id,
                'created_by' => $this->leader->id,
                'status' => StatusTugas::BelumDikerjakan,
            ]);
        }

        $response = $this->actingAs($this->leader)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Halo, Gus Ega 👋');
        $response->assertSee('Seminar Teknologi 2026');
        $response->assertSee('42%');
        $response->assertSee('Acara Aktif');
        $response->assertSee('Total Tugas Tim');
    }

    public function test_anggota_melihat_dashboard_anggota_dengan_tugas(): void
    {
        $otherLeader = User::factory()->create(['nama' => 'Ketua Lain']);
        $event = Event::factory()->create([
            'leader_id' => $otherLeader->id,
            'category_id' => $this->category->id,
            'nama_event' => 'Event Kepanitiaan Rina',
        ]);

        EventMember::factory()->create([
            'event_id' => $event->id,
            'user_id' => $this->memberOnly->id,
        ]);

        Task::factory()->create([
            'event_id' => $event->id,
            'created_by' => $otherLeader->id,
            'penerima_id' => $this->memberOnly->id,
            'judul' => 'Tugas Prioritas Rina',
            'deadline' => Carbon::tomorrow(),
            'status' => StatusTugas::BelumDikerjakan,
        ]);

        $response = $this->actingAs($this->memberOnly)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Halo, Rina Astuti 👋');
        $response->assertSee('Tugas Prioritas Saya');
        $response->assertSee('Tugas Prioritas Rina');
        $response->assertSee('Event Kepanitiaan Rina');
    }
}

