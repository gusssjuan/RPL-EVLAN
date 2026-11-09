<?php

namespace Tests\Feature\Event;

use App\Enums\RoleName;
use App\Models\Category;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Role;
use App\Models\Timeline;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimelineManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $leader;

    protected User $member;

    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);

        $this->leader = User::factory()->create();
        $this->member = User::factory()->create();

        $category = Category::factory()->create();

        $this->event = Event::factory()->create([
            'leader_id' => $this->leader->id,
            'category_id' => $category->id,
            'tanggal' => Carbon::now()->addDays(60)->toDateString(),
        ]);

        EventMember::factory()->create([
            'event_id' => $this->event->id,
            'user_id' => $this->member->id,
        ]);
    }

    public function test_timeline_ditampilkan_berurutan_menurun_dari_h_terjauh(): void
    {
        Timeline::create([
            'event_id' => $this->event->id,
            'label' => 'Hari H',
            'offset_hari' => 0,
            'tanggal' => $this->event->tanggal,
            'kegiatan' => 'Hari Acara',
            'urutan' => 2,
        ]);

        Timeline::create([
            'event_id' => $this->event->id,
            'label' => 'H-30',
            'offset_hari' => 30,
            'tanggal' => Carbon::parse($this->event->tanggal)->subDays(30)->toDateString(),
            'kegiatan' => 'Publikasi',
            'urutan' => 1,
        ]);

        Timeline::create([
            'event_id' => $this->event->id,
            'label' => 'H-60',
            'offset_hari' => 60,
            'tanggal' => Carbon::parse($this->event->tanggal)->subDays(60)->toDateString(),
            'kegiatan' => 'Rapat Awal',
            'urutan' => 0,
        ]);

        $response = $this->actingAs($this->leader)->get('/acara/'.$this->event->id.'/timeline');

        $response->assertStatus(200);
        $response->assertSeeInOrder(['H-60', 'H-30', 'Hari H']);
    }

    public function test_leader_dapat_menambah_agenda_timeline(): void
    {
        $response = $this->actingAs($this->leader)->post('/acara/'.$this->event->id.'/timeline', [
            'kegiatan' => 'Gladi resik peralatan',
            'offset_hari' => 2,
        ]);

        $response->assertRedirect('/acara/'.$this->event->id.'/timeline');
        $this->assertDatabaseHas('timelines', [
            'event_id' => $this->event->id,
            'label' => 'H-2',
            'offset_hari' => 2,
            'kegiatan' => 'Gladi resik peralatan',
        ]);
    }

    public function test_member_hanya_bisa_melihat_dan_dilarang_mengubah_timeline(): void
    {
        $timeline = Timeline::create([
            'event_id' => $this->event->id,
            'label' => 'H-10',
            'offset_hari' => 10,
            'tanggal' => Carbon::parse($this->event->tanggal)->subDays(10)->toDateString(),
            'kegiatan' => 'Cetak banner',
            'urutan' => 1,
        ]);

        $viewResponse = $this->actingAs($this->member)->get('/acara/'.$this->event->id.'/timeline');
        $viewResponse->assertStatus(200);

        // Tambah -> 403
        $addResponse = $this->actingAs($this->member)->post('/acara/'.$this->event->id.'/timeline', [
            'kegiatan' => 'Kegiatan Liar',
            'offset_hari' => 5,
        ]);
        $addResponse->assertStatus(403);

        // Ubah -> 403
        $editResponse = $this->actingAs($this->member)->put('/acara/'.$this->event->id.'/timeline/'.$timeline->id, [
            'kegiatan' => 'Ubah Kegiatan',
            'offset_hari' => 10,
        ]);
        $editResponse->assertStatus(403);
    }
}
