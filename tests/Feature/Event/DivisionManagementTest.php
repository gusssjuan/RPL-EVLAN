<?php

namespace Tests\Feature\Event;

use App\Enums\PeranAnggota;
use App\Enums\RoleName;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Division;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Role;
use App\Models\Task;
use App\Models\Template;
use App\Models\TemplateItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DivisionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $leader;
    protected User $member;
    protected User $userCandidate;
    protected User $otherUser;
    protected Category $category;
    protected Template $template;
    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);

        $this->leader = User::factory()->create(['nama' => 'Gus Ega', 'email' => 'gusega@evlan.test']);
        $this->member = User::factory()->create(['nama' => 'Rina Astuti', 'email' => 'rina@evlan.test']);
        $this->userCandidate = User::factory()->create(['nama' => 'Komang Putra', 'email' => 'komang@evlan.test']);
        $this->otherUser = User::factory()->create(['nama' => 'Orang Lain', 'email' => 'lain@evlan.test']);

        $this->category = Category::factory()->create(['nama_kategori' => 'Seminar']);

        $this->template = Template::factory()->create([
            'category_id' => $this->category->id,
            'nama_template' => 'Template Seminar',
            'is_aktif' => true,
        ]);

        TemplateItem::factory()->create([
            'template_id' => $this->template->id,
            'jenis' => 'divisi',
            'nama_item' => 'Acara',
            'urutan' => 0,
        ]);
        TemplateItem::factory()->create([
            'template_id' => $this->template->id,
            'jenis' => 'divisi',
            'nama_item' => 'Konsumsi',
            'urutan' => 1,
        ]);

        $this->event = Event::factory()->create([
            'leader_id' => $this->leader->id,
            'category_id' => $this->category->id,
            'nama_event' => 'Seminar Nasional 2026',
        ]);
    }

    public function test_ketua_dapat_melihat_halaman_kepanitiaan(): void
    {
        $response = $this->actingAs($this->leader)->get('/acara/' . $this->event->id . '/kepanitiaan');

        $response->assertStatus(200);
        $response->assertSee('Kepanitiaan');
        $response->assertSee('+ Divisi');
    }

    public function test_anggota_dapat_melihat_kepanitiaan_tetapi_tidak_melihat_tombol_kelola(): void
    {
        $div = Division::create(['event_id' => $this->event->id, 'nama_divisi' => 'Acara']);
        EventMember::create([
            'event_id' => $this->event->id,
            'user_id' => $this->member->id,
            'division_id' => $div->id,
            'peran' => PeranAnggota::Anggota,
        ]);

        $response = $this->actingAs($this->member)->get('/acara/' . $this->event->id . '/kepanitiaan');

        $response->assertStatus(200);
        $response->assertSee('Acara');
        $response->assertDontSee('+ Divisi');
    }

    public function test_pengguna_luar_mendapat_403(): void
    {
        $this->event->update(['is_publik' => false]);

        $response = $this->actingAs($this->otherUser)->get('/acara/' . $this->event->id . '/kepanitiaan');
        $response->assertStatus(403);
    }

    public function test_ketua_dapat_menambah_divisi_baru(): void
    {
        $response = $this->actingAs($this->leader)->post('/acara/' . $this->event->id . '/kepanitiaan/divisi', [
            'nama_divisi' => 'Perlengkapan',
        ]);

        $response->assertRedirect('/acara/' . $this->event->id . '/kepanitiaan');
        $this->assertDatabaseHas('divisions', [
            'event_id' => $this->event->id,
            'nama_divisi' => 'Perlengkapan',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'event_id' => $this->event->id,
            'aksi' => 'buat_divisi',
        ]);
    }

    public function test_nama_divisi_duplikat_pada_acara_sama_ditolak(): void
    {
        Division::create(['event_id' => $this->event->id, 'nama_divisi' => 'Acara']);

        $response = $this->actingAs($this->leader)->post('/acara/' . $this->event->id . '/kepanitiaan/divisi', [
            'nama_divisi' => 'acara', // case-insensitive check
        ]);

        $response->assertSessionHasErrors('nama_divisi');
    }

    public function test_ketua_dapat_menambahkan_seluruh_rekomendasi_divisi_d10(): void
    {
        $response = $this->actingAs($this->leader)->post('/acara/' . $this->event->id . '/kepanitiaan/rekomendasi');

        $response->assertRedirect('/acara/' . $this->event->id . '/kepanitiaan');
        $this->assertDatabaseHas('divisions', ['event_id' => $this->event->id, 'nama_divisi' => 'Acara']);
        $this->assertDatabaseHas('divisions', ['event_id' => $this->event->id, 'nama_divisi' => 'Konsumsi']);
    }

    public function test_ketua_dapat_menambahkan_anggota_via_email(): void
    {
        $div = Division::create(['event_id' => $this->event->id, 'nama_divisi' => 'Acara']);

        $response = $this->actingAs($this->leader)->post('/acara/' . $this->event->id . '/kepanitiaan/anggota', [
            'email' => $this->userCandidate->email,
            'division_id' => $div->id,
            'peran' => 'koordinator',
        ]);

        $response->assertRedirect('/acara/' . $this->event->id . '/kepanitiaan');
        $this->assertDatabaseHas('event_members', [
            'event_id' => $this->event->id,
            'user_id' => $this->userCandidate->id,
            'division_id' => $div->id,
            'peran' => PeranAnggota::Koordinator->value,
        ]);

        // Memastikan notifikasi undangan terkirim
        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $this->userCandidate->id,
            'event_id' => $this->event->id,
            'jenis' => 'undangan',
        ]);
    }

    public function test_email_tidak_terdaftar_ditolak(): void
    {
        $div = Division::create(['event_id' => $this->event->id, 'nama_divisi' => 'Acara']);

        $response = $this->actingAs($this->leader)->post('/acara/' . $this->event->id . '/kepanitiaan/anggota', [
            'email' => 'tidakada@email.com',
            'division_id' => $div->id,
            'peran' => 'anggota',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_menambahkan_ketua_sebagai_anggota_ditolak(): void
    {
        $div = Division::create(['event_id' => $this->event->id, 'nama_divisi' => 'Acara']);

        $response = $this->actingAs($this->leader)->post('/acara/' . $this->event->id . '/kepanitiaan/anggota', [
            'email' => $this->leader->email,
            'division_id' => $div->id,
            'peran' => 'anggota',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_hapus_divisi_dengan_tugas_aktif_ditolak(): void
    {
        $div = Division::create(['event_id' => $this->event->id, 'nama_divisi' => 'Acara']);
        Task::factory()->create([
            'event_id' => $this->event->id,
            'division_id' => $div->id,
            'penerima_id' => $this->leader->id,
            'created_by' => $this->leader->id,
        ]);

        $response = $this->actingAs($this->leader)->delete('/acara/' . $this->event->id . '/kepanitiaan/divisi/' . $div->id);

        $response->assertSessionHasErrors('division');
        $this->assertDatabaseHas('divisions', ['id' => $div->id]);
    }

    public function test_ketua_dapat_mengeluarkan_anggota(): void
    {
        $div = Division::create(['event_id' => $this->event->id, 'nama_divisi' => 'Acara']);
        $mem = EventMember::create([
            'event_id' => $this->event->id,
            'user_id' => $this->member->id,
            'division_id' => $div->id,
            'peran' => PeranAnggota::Anggota,
        ]);

        $response = $this->actingAs($this->leader)->delete('/acara/' . $this->event->id . '/kepanitiaan/anggota/' . $mem->id);

        $response->assertRedirect('/acara/' . $this->event->id . '/kepanitiaan');
        $this->assertDatabaseMissing('event_members', ['id' => $mem->id]);
    }
}

