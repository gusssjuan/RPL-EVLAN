<?php

namespace Tests\Feature\Event;

use App\Enums\RoleName;
use App\Enums\StatusEvent;
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

class EventEvaluationTest extends TestCase
{
    use RefreshDatabase;

    protected User $leader;

    protected User $member;

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

        $this->category = Category::factory()->create(['nama_kategori' => 'Seminar']);

        $this->event = Event::factory()->create([
            'leader_id' => $this->leader->id,
            'category_id' => $this->category->id,
            'nama_event' => 'Seminar Teknologi 2026',
            'tanggal' => Carbon::parse('2026-12-12'),
            'status_event' => StatusEvent::Perencanaan,
        ]);

        EventMember::create([
            'event_id' => $this->event->id,
            'user_id' => $this->member->id,
        ]);
    }

    public function test_ketua_dapat_melihat_halaman_evaluasi_s19(): void
    {
        $response = $this->actingAs($this->leader)->get('/acara/'.$this->event->id.'/evaluasi');

        $response->assertStatus(200);
        $response->assertSee('Evaluasi & Penutupan Acara', false);
        $response->assertSee('Catatan Evaluasi Pelaksanaan Acara');
    }

    public function test_anggota_dilarang_mengakses_halaman_evaluasi(): void
    {
        $response = $this->actingAs($this->member)->get('/acara/'.$this->event->id.'/evaluasi');

        $response->assertStatus(403);
    }

    public function test_evaluasi_kosong_ditolak(): void
    {
        $response = $this->actingAs($this->leader)->post('/acara/'.$this->event->id.'/tutup', [
            'evaluasi' => '',
        ]);

        $response->assertSessionHasErrors('evaluasi');
        $this->event->refresh();
        $this->assertEquals(StatusEvent::Perencanaan, $this->event->status_event);
    }

    public function test_ketua_dapat_menutup_acara_dan_menyimpan_evaluasi(): void
    {
        $catatan = 'Acara seminar berjalan dengan sangat lancar dan mencapai 95% target peserta.';

        $response = $this->actingAs($this->leader)->post('/acara/'.$this->event->id.'/tutup', [
            'evaluasi' => $catatan,
        ]);

        $response->assertRedirect('/acara/'.$this->event->id);

        $this->event->refresh();
        $this->assertEquals(StatusEvent::Selesai, $this->event->status_event);
        $this->assertEquals($catatan, $this->event->evaluasi);
        $this->assertNotNull($this->event->closed_at);

        // Audit log tercatat
        $this->assertDatabaseHas('activity_logs', [
            'event_id' => $this->event->id,
            'aksi' => 'tutup_acara',
        ]);
    }

    public function test_acara_yang_sudah_selesai_bersifat_read_only(): void
    {
        // Tutup acara terlebih dahulu
        $this->event->update([
            'status_event' => StatusEvent::Selesai,
            'closed_at' => now(),
            'evaluasi' => 'Sudah selesai',
        ]);

        $task = Task::factory()->create([
            'event_id' => $this->event->id,
            'penerima_id' => $this->member->id,
            'created_by' => $this->leader->id,
            'status' => StatusTugas::BelumDikerjakan,
        ]);

        // 1. Leader mencoba mengubah detail acara -> 403
        $resUpdateEvent = $this->actingAs($this->leader)->put('/acara/'.$this->event->id, [
            'nama_event' => 'Nama Baru Ditolak',
            'tujuan' => 'Tujuan',
            'tanggal' => '2026-12-15',
            'lokasi' => 'Lokasi',
            'target_peserta' => 'Umum',
            'jumlah_peserta' => 100,
        ]);
        $resUpdateEvent->assertStatus(403);

        // 2. Leader mencoba membuat tugas baru -> 403
        $resCreateTask = $this->actingAs($this->leader)->post('/acara/'.$this->event->id.'/tugas', [
            'judul' => 'Tugas Ilegal',
            'penerima_id' => $this->member->id,
            'deadline' => '2026-12-10',
        ]);
        $resCreateTask->assertStatus(403);

        // 3. Leader mencoba menambah divisi -> 403
        $resCreateDiv = $this->actingAs($this->leader)->post('/acara/'.$this->event->id.'/kepanitiaan/divisi', [
            'nama_divisi' => 'Divisi Ilegal',
        ]);
        $resCreateDiv->assertStatus(403);

        // 4. Penerima mencoba update status tugas -> 403
        $resUpdateStatus = $this->actingAs($this->member)->patch('/tugas/'.$task->id.'/status', [
            'status' => 'sudah_diselesaikan',
        ]);
        $resUpdateStatus->assertStatus(403);
    }
}
