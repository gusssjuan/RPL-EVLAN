<?php

namespace Tests\Feature\Event;

use App\Enums\RoleName;
use App\Enums\StatusEvent;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Checklist;
use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $leader;

    protected User $member;

    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);

        $this->leader = User::factory()->create();
        $this->member = User::factory()->create();

        $category = Category::factory()->create();

        $this->event = Event::factory()->create([
            'leader_id' => $this->leader->id,
            'category_id' => $category->id,
            'nama_event' => 'Seminar Teknologi 2026',
        ]);
    }

    public function test_leader_dapat_melihat_estimasi_anggaran(): void
    {
        Budget::factory()->create([
            'event_id' => $this->event->id,
            'nama_item' => 'Honorarium Narasumber',
            'jumlah' => 2,
            'harga_satuan' => 500000,
        ]);

        $response = $this->actingAs($this->leader)->get('/acara/'.$this->event->id.'/anggaran');

        $response->assertStatus(200);
        $response->assertSee('Total Estimasi');
        $response->assertSee('Honorarium Narasumber');
        $response->assertSee('Rp 1.000.000');
        $response->assertSee('Anggaran ini adalah estimasi');
    }

    public function test_leader_dapat_menambah_item_anggaran_manual(): void
    {
        $checklist = Checklist::factory()->create([
            'event_id' => $this->event->id,
            'nama_kebutuhan' => 'Konsumsi 300 peserta',
        ]);

        $response = $this->actingAs($this->leader)->post('/acara/'.$this->event->id.'/anggaran', [
            'nama_item' => 'Konsumsi Peserta',
            'jumlah' => 300,
            'harga_satuan' => 25000,
            'checklist_id' => $checklist->id,
        ]);

        $response->assertRedirect('/acara/'.$this->event->id.'/anggaran');

        $this->assertDatabaseHas('budgets', [
            'event_id' => $this->event->id,
            'checklist_id' => $checklist->id,
            'nama_item' => 'Konsumsi Peserta',
            'jumlah' => 300,
            'harga_satuan' => 25000,
            'total' => 7500000,
        ]);
    }

    public function test_leader_dapat_mengubah_dan_menghapus_item_anggaran(): void
    {
        $budget = Budget::factory()->create([
            'event_id' => $this->event->id,
            'nama_item' => 'Honorarium MC',
            'jumlah' => 1,
            'harga_satuan' => 300000,
        ]);

        $this->actingAs($this->leader)->put('/acara/'.$this->event->id.'/anggaran/'.$budget->id, [
            'nama_item' => 'Honorarium MC Profesional',
            'jumlah' => 2,
            'harga_satuan' => 350000,
            'checklist_id' => null,
        ])->assertRedirect('/acara/'.$this->event->id.'/anggaran');

        $this->assertDatabaseHas('budgets', [
            'id' => $budget->id,
            'nama_item' => 'Honorarium MC Profesional',
            'jumlah' => 2,
            'harga_satuan' => 350000,
            'total' => 700000,
        ]);

        $this->actingAs($this->leader)
            ->delete('/acara/'.$this->event->id.'/anggaran/'.$budget->id)
            ->assertRedirect('/acara/'.$this->event->id.'/anggaran');

        $this->assertDatabaseMissing('budgets', ['id' => $budget->id]);
    }

    public function test_member_tidak_dapat_mengakses_atau_mengubah_anggaran(): void
    {
        $this->actingAs($this->member)
            ->get('/acara/'.$this->event->id.'/anggaran')
            ->assertStatus(403);

        $this->actingAs($this->member)
            ->post('/acara/'.$this->event->id.'/anggaran', [
                'nama_item' => 'Item Ilegal',
                'jumlah' => 1,
                'harga_satuan' => 100000,
            ])
            ->assertStatus(403);
    }

    public function test_checklist_dari_acara_lain_ditolak(): void
    {
        $otherEvent = Event::factory()->create([
            'leader_id' => $this->leader->id,
            'category_id' => $this->event->category_id,
        ]);
        $checklistLain = Checklist::factory()->create(['event_id' => $otherEvent->id]);

        $response = $this->actingAs($this->leader)->post('/acara/'.$this->event->id.'/anggaran', [
            'nama_item' => 'Item Salah Kebutuhan',
            'jumlah' => 1,
            'harga_satuan' => 100000,
            'checklist_id' => $checklistLain->id,
        ]);

        $response->assertSessionHasErrors('checklist_id');
        $this->assertDatabaseCount('budgets', 0);
    }

    public function test_acara_selesai_tidak_dapat_diubah_anggarannya(): void
    {
        $this->event->update(['status_event' => StatusEvent::Selesai]);

        $this->actingAs($this->leader)
            ->post('/acara/'.$this->event->id.'/anggaran', [
                'nama_item' => 'Item Setelah Tutup',
                'jumlah' => 1,
                'harga_satuan' => 100000,
            ])
            ->assertStatus(403);
    }
}
