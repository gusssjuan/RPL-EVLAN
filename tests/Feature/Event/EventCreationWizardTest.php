<?php

namespace Tests\Feature\Event;

use App\Enums\RoleName;
use App\Enums\StatusEvent;
use App\Models\Category;
use App\Models\Event;
use App\Models\Role;
use App\Models\Template;
use App\Models\TemplateItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventCreationWizardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;
    protected Template $template;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);

        $this->user = User::factory()->create();

        $this->category = Category::factory()->create([
            'nama_kategori' => 'Seminar Umum',
            'ikon' => '🎤',
        ]);

        $this->template = Template::factory()->create([
            'category_id' => $this->category->id,
            'nama_template' => 'Template Seminar Nasional',
            'is_aktif' => true,
        ]);

        // 3 items kebutuhan
        TemplateItem::factory()->create([
            'template_id' => $this->template->id,
            'jenis' => 'kebutuhan',
            'nama_item' => 'Sound system & 4 mikrofon',
            'kelompok' => 'Perlengkapan',
            'urutan' => 0,
        ]);
        TemplateItem::factory()->create([
            'template_id' => $this->template->id,
            'jenis' => 'kebutuhan',
            'nama_item' => 'Proyektor & layar',
            'kelompok' => 'Perlengkapan',
            'urutan' => 1,
        ]);
        TemplateItem::factory()->create([
            'template_id' => $this->template->id,
            'jenis' => 'kebutuhan',
            'nama_item' => 'Snack box 300 porsi',
            'kelompok' => 'Konsumsi',
            'urutan' => 2,
        ]);

        // 2 items timeline: H-60 dan H-7
        TemplateItem::factory()->create([
            'template_id' => $this->template->id,
            'jenis' => 'timeline',
            'nama_item' => 'Membuat konsep dan proposal',
            'offset_hari' => 60,
            'urutan' => 0,
        ]);
        TemplateItem::factory()->create([
            'template_id' => $this->template->id,
            'jenis' => 'timeline',
            'nama_item' => 'Final checking vendor',
            'offset_hari' => 7,
            'urutan' => 1,
        ]);
    }

    public function test_langkah_1_kategori_dapat_ditampilkan(): void
    {
        $response = $this->actingAs($this->user)->get('/acara/buat/kategori');

        $response->assertStatus(200);
        $response->assertSee('Pilih Kategori Acara');
        $response->assertSee('Seminar Umum');
    }

    public function test_langkah_1_menyimpan_kategori_ke_session_d25(): void
    {
        $response = $this->actingAs($this->user)->post('/acara/buat/kategori', [
            'category_id' => $this->category->id,
        ]);

        $response->assertRedirect('/acara/buat/detail');
        $this->assertEquals($this->category->id, session('event_draft.category_id'));
    }

    public function test_langkah_2_detail_mengalihkan_jika_kategori_belum_dipilih(): void
    {
        $response = $this->actingAs($this->user)->get('/acara/buat/detail');

        $response->assertRedirect('/acara/buat/kategori');
    }

    public function test_langkah_2_validasi_tanggal_tidak_boleh_sebelum_hari_ini_d26(): void
    {
        session(['event_draft' => ['category_id' => $this->category->id]]);

        $kemarin = Carbon::yesterday()->toDateString();

        $response = $this->actingAs($this->user)->post('/acara/buat/detail', [
            'nama_event' => 'Seminar AI 2026',
            'tujuan' => 'Sosialisasi AI',
            'tanggal' => $kemarin,
            'lokasi' => 'Kampus Jimbaran',
            'target_peserta' => 'Mahasiswa',
            'jumlah_peserta' => 100,
        ]);

        $response->assertSessionHasErrors('tanggal');
    }

    public function test_langkah_2_menyimpan_detail_ke_session(): void
    {
        session(['event_draft' => ['category_id' => $this->category->id]]);

        $besok = Carbon::tomorrow()->toDateString();

        $response = $this->actingAs($this->user)->post('/acara/buat/detail', [
            'nama_event' => 'Seminar Teknologi 2026',
            'tujuan' => 'Pengenalan agentic AI',
            'tanggal' => $besok,
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '13:00',
            'lokasi' => 'Auditorium Widya Sabha',
            'target_peserta' => 'Mahasiswa dan Profesional',
            'jumlah_peserta' => 300,
        ]);

        $response->assertRedirect('/acara/buat/rancangan');
        $this->assertEquals('Seminar Teknologi 2026', session('event_draft.nama_event'));
        $this->assertEquals(300, session('event_draft.jumlah_peserta'));
    }

    public function test_langkah_3_rancangan_dapat_ditampilkan(): void
    {
        $besok = Carbon::tomorrow()->toDateString();

        session(['event_draft' => [
            'category_id' => $this->category->id,
            'nama_event' => 'Seminar Teknologi 2026',
            'tujuan' => 'Pengenalan agentic AI',
            'tanggal' => $besok,
            'lokasi' => 'Auditorium Widya Sabha',
            'target_peserta' => 'Mahasiswa',
            'jumlah_peserta' => 300,
        ]]);

        $response = $this->actingAs($this->user)->get('/acara/buat/rancangan');

        $response->assertStatus(200);
        $response->assertSee('Rancangan & Pratinjau Acara');
        $response->assertSee('Seminar Teknologi 2026');
        $response->assertSee('Sound system & 4 mikrofon');
    }

    public function test_pembuatan_acara_dari_template_berhasil_penuh_t43(): void
    {
        // Tetapkan tanggal tetap acara: 2026-12-12
        $tanggalAcara = '2026-12-12';

        session(['event_draft' => [
            'category_id' => $this->category->id,
            'nama_event' => 'Seminar Teknologi 2026',
            'tujuan' => 'Edukasi dan kolaborasi inovasi',
            'tanggal' => $tanggalAcara,
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '15:00',
            'lokasi' => 'Auditorium Widya Sabha',
            'target_peserta' => '300 Peserta',
            'jumlah_peserta' => 300,
        ]]);

        $response = $this->actingAs($this->user)->post('/acara/buat/simpan');

        $event = Event::where('nama_event', 'Seminar Teknologi 2026')->first();
        $this->assertNotNull($event);

        $response->assertRedirect('/acara/'.$event->id);

        // 1. Verifikasi data Event
        $this->assertEquals($this->user->id, $event->leader_id);
        $this->assertEquals(StatusEvent::Perencanaan, $event->status_event);

        // 2. Verifikasi Checklists: 3 item, semua status_selesai = false dan dari_template = true
        $this->assertCount(3, $event->checklists);
        $this->assertTrue($event->checklists->every(fn ($c) => $c->status_selesai === false && $c->dari_template === true));
        $this->assertDatabaseHas('checklists', [
            'event_id' => $event->id,
            'nama_kebutuhan' => 'Sound system & 4 mikrofon',
        ]);

        // 3. Verifikasi Timelines:
        // H-60 dihitung: 2026-12-12 - 60 hari = 2026-10-13
        $this->assertDatabaseHas('timelines', [
            'event_id' => $event->id,
            'label' => 'H-60',
            'offset_hari' => 60,
            'tanggal' => '2026-10-13',
            'kegiatan' => 'Membuat konsep dan proposal',
        ]);

        // H-7 dihitung: 2026-12-12 - 7 hari = 2026-12-05
        $this->assertDatabaseHas('timelines', [
            'event_id' => $event->id,
            'label' => 'H-7',
            'offset_hari' => 7,
            'tanggal' => '2026-12-05',
        ]);

        // D-09: Entri Hari H otomatis dibuat
        $this->assertDatabaseHas('timelines', [
            'event_id' => $event->id,
            'label' => 'Hari H',
            'offset_hari' => 0,
            'tanggal' => '2026-12-12',
        ]);

        // 4. Verifikasi Activity Log tercipta
        $this->assertDatabaseHas('activity_logs', [
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'aksi' => 'Buat Acara',
        ]);

        // 5. Session draf dibersihkan
        $this->assertNull(session('event_draft'));
    }
}

