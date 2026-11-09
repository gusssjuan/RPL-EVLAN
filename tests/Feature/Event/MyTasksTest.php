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

class MyTasksTest extends TestCase
{
    use RefreshDatabase;

    protected User $leader;

    protected User $memberA;

    protected User $memberB;

    protected User $stranger;

    protected Event $event;

    protected Task $taskA;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);

        $this->leader = User::factory()->create(['nama' => 'Gus Ega']);
        $this->memberA = User::factory()->create(['nama' => 'Rina Astuti']);
        $this->memberB = User::factory()->create(['nama' => 'Komang Putra']);
        $this->stranger = User::factory()->create(['nama' => 'Orang Asing']);

        $category = Category::factory()->create();

        $this->event = Event::factory()->create([
            'leader_id' => $this->leader->id,
            'category_id' => $category->id,
            'nama_event' => 'Seminar EVLAN 2026',
            'is_publik' => false,
        ]);

        EventMember::create([
            'event_id' => $this->event->id,
            'user_id' => $this->memberA->id,
        ]);

        EventMember::create([
            'event_id' => $this->event->id,
            'user_id' => $this->memberB->id,
        ]);

        $this->taskA = Task::factory()->create([
            'event_id' => $this->event->id,
            'created_by' => $this->leader->id,
            'penerima_id' => $this->memberA->id,
            'judul' => 'Tugas Khusus Rina',
            'deadline' => Carbon::tomorrow(),
            'status' => StatusTugas::BelumDikerjakan,
        ]);
    }

    public function test_anggota_dapat_melihat_daftar_tugas_saya(): void
    {
        $response = $this->actingAs($this->memberA)->get('/tugas-saya');

        $response->assertStatus(200);
        $response->assertSee('Tugas Saya');
        $response->assertSee('Tugas Khusus Rina');
    }

    public function test_anggota_dapat_melihat_detail_tugas(): void
    {
        $response = $this->actingAs($this->memberA)->get('/tugas/'.$this->taskA->id);

        $response->assertStatus(200);
        $response->assertSee('Tugas Khusus Rina');
        $response->assertSee('Perbarui Status Pengerjaan');
    }

    public function test_penerima_dapat_mengubah_status_tugas(): void
    {
        $response = $this->actingAs($this->memberA)->patch('/tugas/'.$this->taskA->id.'/status', [
            'status' => 'sedang_dikerjakan',
        ]);

        $response->assertRedirect('/tugas/'.$this->taskA->id);

        $this->taskA->refresh();
        $this->assertEquals(StatusTugas::SedangDikerjakan, $this->taskA->status);
        $this->assertNull($this->taskA->completed_at);

        // Notifikasi ke ketua acara
        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $this->leader->id,
            'event_id' => $this->event->id,
            'jenis' => 'status_tugas',
        ]);

        // Ubah ke selesai
        $this->actingAs($this->memberA)->patch('/tugas/'.$this->taskA->id.'/status', [
            'status' => 'sudah_diselesaikan',
        ]);

        $this->taskA->refresh();
        $this->assertEquals(StatusTugas::SudahDiselesaikan, $this->taskA->status);
        $this->assertNotNull($this->taskA->completed_at);
    }

    public function test_anggota_lain_tidak_bisa_mengubah_status_tugas_d14(): void
    {
        // Member B mencoba mengubah tugas milik Member A
        $response = $this->actingAs($this->memberB)->patch('/tugas/'.$this->taskA->id.'/status', [
            'status' => 'sudah_diselesaikan',
        ]);

        $response->assertStatus(403);
    }

    public function test_bukan_peserta_tidak_bisa_melihat_detail_tugas(): void
    {
        $response = $this->actingAs($this->stranger)->get('/tugas/'.$this->taskA->id);

        $response->assertStatus(403);
    }
}
