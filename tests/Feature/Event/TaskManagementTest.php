<?php

namespace Tests\Feature\Event;

use App\Enums\RoleName;
use App\Enums\StatusTugas;
use App\Models\Category;
use App\Models\Division;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $leader;
    protected User $member;
    protected User $outsider;
    protected Category $category;
    protected Event $event;
    protected Division $division;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);

        $this->leader = User::factory()->create(['nama' => 'Gus Ega']);
        $this->member = User::factory()->create(['nama' => 'Rina Astuti']);
        $this->outsider = User::factory()->create(['nama' => 'Orang Luar']);

        $this->category = Category::factory()->create(['nama_kategori' => 'Seminar']);

        $this->event = Event::factory()->create([
            'leader_id' => $this->leader->id,
            'category_id' => $this->category->id,
            'nama_event' => 'Seminar Teknologi 2026',
            'tanggal' => Carbon::parse('2026-12-12'),
        ]);

        $this->division = Division::create([
            'event_id' => $this->event->id,
            'nama_divisi' => 'Acara',
        ]);

        EventMember::create([
            'event_id' => $this->event->id,
            'user_id' => $this->member->id,
            'division_id' => $this->division->id,
        ]);
    }

    public function test_ketua_dapat_melihat_daftar_tugas(): void
    {
        Task::factory()->create([
            'event_id' => $this->event->id,
            'division_id' => $this->division->id,
            'penerima_id' => $this->member->id,
            'created_by' => $this->leader->id,
            'judul' => 'Menyusun Rundown Acara',
        ]);

        $response = $this->actingAs($this->leader)->get('/acara/' . $this->event->id . '/tugas');

        $response->assertStatus(200);
        $response->assertSee('Menyusun Rundown Acara');
        $response->assertSee('Buat Tugas');
    }

    public function test_ketua_dapat_membuat_tugas_baru(): void
    {
        $response = $this->actingAs($this->leader)->post('/acara/' . $this->event->id . '/tugas', [
            'judul' => 'Konfirmasi Narasumber Utama',
            'deskripsi' => 'Hubungi narasumber via email dan telepon.',
            'division_id' => $this->division->id,
            'penerima_id' => $this->member->id,
            'deadline' => '2026-11-20',
        ]);

        $response->assertRedirect('/acara/' . $this->event->id . '/tugas');

        $this->assertDatabaseHas('tasks', [
            'event_id' => $this->event->id,
            'judul' => 'Konfirmasi Narasumber Utama',
            'penerima_id' => $this->member->id,
            'status' => StatusTugas::BelumDikerjakan->value,
        ]);

        // Notifikasi penugasan
        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $this->member->id,
            'event_id' => $this->event->id,
            'jenis' => 'penugasan',
        ]);

        // Log aktivitas
        $this->assertDatabaseHas('activity_logs', [
            'event_id' => $this->event->id,
            'aksi' => 'buat_tugas',
        ]);
    }

    public function test_penerima_bukan_peserta_acara_ditolak(): void
    {
        $response = $this->actingAs($this->leader)->post('/acara/' . $this->event->id . '/tugas', [
            'judul' => 'Tugas Ilegal',
            'penerima_id' => $this->outsider->id,
            'deadline' => '2026-11-20',
        ]);

        $response->assertSessionHasErrors('penerima_id');
    }

    public function test_deadline_melewati_tanggal_acara_ditolak(): void
    {
        $response = $this->actingAs($this->leader)->post('/acara/' . $this->event->id . '/tugas', [
            'judul' => 'Tugas Terlambat',
            'penerima_id' => $this->member->id,
            'deadline' => '2026-12-15', // Acara 12 Des 2026
        ]);

        $response->assertSessionHasErrors('deadline');
    }

    public function test_ketua_dapat_mengubah_detail_tugas(): void
    {
        $task = Task::factory()->create([
            'event_id' => $this->event->id,
            'division_id' => $this->division->id,
            'penerima_id' => $this->member->id,
            'created_by' => $this->leader->id,
            'judul' => 'Judul Lama',
            'deadline' => '2026-11-20',
        ]);

        $response = $this->actingAs($this->leader)->put('/acara/' . $this->event->id . '/tugas/' . $task->id, [
            'judul' => 'Judul Baru yang Diperbarui',
            'penerima_id' => $this->member->id,
            'deadline' => '2026-11-25',
        ]);

        $response->assertRedirect('/acara/' . $this->event->id . '/tugas');
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'judul' => 'Judul Baru yang Diperbarui',
        ]);
    }

    public function test_ketua_dapat_menghapus_tugas(): void
    {
        $task = Task::factory()->create([
            'event_id' => $this->event->id,
            'division_id' => $this->division->id,
            'penerima_id' => $this->member->id,
            'created_by' => $this->leader->id,
            'judul' => 'Tugas Akan Dihapus',
            'deadline' => '2026-11-20',
        ]);

        $response = $this->actingAs($this->leader)->delete('/acara/' . $this->event->id . '/tugas/' . $task->id);

        $response->assertRedirect('/acara/' . $this->event->id . '/tugas');
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}

