<?php

namespace Tests\Feature\Event;

use App\Enums\RoleName;
use App\Models\Category;
use App\Models\Checklist;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChecklistManagementTest extends TestCase
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
        ]);

        EventMember::factory()->create([
            'event_id' => $this->event->id,
            'user_id' => $this->member->id,
        ]);
    }

    public function test_leader_dapat_melihat_halaman_checklist(): void
    {
        Checklist::factory()->create([
            'event_id' => $this->event->id,
            'nama_kebutuhan' => 'Sound system 4 mic',
            'status_selesai' => false,
        ]);

        $response = $this->actingAs($this->leader)->get('/acara/'.$this->event->id.'/checklist');

        $response->assertStatus(200);
        $response->assertSee('Sound system 4 mic');
        $response->assertSee('Progres Kebutuhan Acara');
    }

    public function test_leader_dapat_menambah_kebutuhan_manual(): void
    {
        $response = $this->actingAs($this->leader)->post('/acara/'.$this->event->id.'/checklist', [
            'nama_kebutuhan' => 'Genset 5000 watt',
            'kelompok' => 'Perlengkapan',
        ]);

        $response->assertRedirect('/acara/'.$this->event->id.'/checklist');
        $this->assertDatabaseHas('checklists', [
            'event_id' => $this->event->id,
            'nama_kebutuhan' => 'Genset 5000 watt',
            'dari_template' => false,
            'status_selesai' => false,
        ]);
    }

    public function test_leader_dapat_toggle_centang_kebutuhan(): void
    {
        $item = Checklist::factory()->create([
            'event_id' => $this->event->id,
            'status_selesai' => false,
        ]);

        $response = $this->actingAs($this->leader)->patch('/acara/'.$this->event->id.'/checklist/'.$item->id.'/toggle');

        $this->assertTrue($item->fresh()->status_selesai);

        // Toggle kembali
        $this->actingAs($this->leader)->patch('/acara/'.$this->event->id.'/checklist/'.$item->id.'/toggle');
        $this->assertFalse($item->fresh()->status_selesai);
    }

    public function test_member_hanya_bisa_melihat_dan_dilarang_mengubah_checklist(): void
    {
        $item = Checklist::factory()->create([
            'event_id' => $this->event->id,
            'nama_kebutuhan' => 'Mic Wireless',
        ]);

        // Member view OK
        $viewResponse = $this->actingAs($this->member)->get('/acara/'.$this->event->id.'/checklist');
        $viewResponse->assertStatus(200);

        // Member tambah -> 403
        $addResponse = $this->actingAs($this->member)->post('/acara/'.$this->event->id.'/checklist', [
            'nama_kebutuhan' => 'Bunga Meja',
        ]);
        $addResponse->assertStatus(403);

        // Member toggle -> 403
        $toggleResponse = $this->actingAs($this->member)->patch('/acara/'.$this->event->id.'/checklist/'.$item->id.'/toggle');
        $toggleResponse->assertStatus(403);

        // Member hapus -> 403
        $deleteResponse = $this->actingAs($this->member)->delete('/acara/'.$this->event->id.'/checklist/'.$item->id);
        $deleteResponse->assertStatus(403);
    }
}
