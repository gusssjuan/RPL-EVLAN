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

class EventOverviewTest extends TestCase
{
    use RefreshDatabase;

    protected User $leader;
    protected User $member;
    protected User $otherUser;
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
        $this->otherUser = User::factory()->create(['nama' => 'Orang Asing']);

        $this->category = Category::factory()->create(['nama_kategori' => 'Seminar']);

        $this->event = Event::factory()->create([
            'leader_id' => $this->leader->id,
            'category_id' => $this->category->id,
            'nama_event' => 'Seminar Teknologi 2026',
            'tanggal' => Carbon::parse('2026-12-12'),
            'is_publik' => false,
        ]);

        EventMember::factory()->create([
            'event_id' => $this->event->id,
            'user_id' => $this->member->id,
        ]);

        // Create timeline entries with offset_hari
        Timeline::create([
            'event_id' => $this->event->id,
            'label' => 'H-60',
            'offset_hari' => 60,
            'tanggal' => Carbon::parse('2026-12-12')->subDays(60)->toDateString(),
            'kegiatan' => 'Konsep dan proposal',
            'urutan' => 1,
        ]);

        Timeline::create([
            'event_id' => $this->event->id,
            'label' => 'Hari H',
            'offset_hari' => 0,
            'tanggal' => '2026-12-12',
            'kegiatan' => 'Pelaksanaan Acara',
            'urutan' => 2,
        ]);
    }

    public function test_ketua_dapat_melihat_ringkasan_acara(): void
    {
        $response = $this->actingAs($this->leader)->get('/acara/' . $this->event->id);

        $response->assertStatus(200);
        $response->assertSee('Seminar Teknologi 2026');
        $response->assertSee('Anda Ketua Acara');
        $response->assertSee('Kebutuhan Belum Terpenuhi');
    }

    public function test_anggota_dapat_melihat_ringkasan_varian_anggota(): void
    {
        $response = $this->actingAs($this->member)->get('/acara/' . $this->event->id);

        $response->assertStatus(200);
        $response->assertSee('Seminar Teknologi 2026');
        $response->assertSee('Anda Anggota Panitia');
        $response->assertSee('Tugas Saya di Acara Ini');
    }

    public function test_pengguna_luar_tidak_dapat_melihat_acara_privat(): void
    {
        $response = $this->actingAs($this->otherUser)->get('/acara/' . $this->event->id);

        $response->assertStatus(403);
    }

    public function test_anggota_dilarang_mengakses_tab_monitoring_d16(): void
    {
        $response = $this->actingAs($this->member)->get('/acara/' . $this->event->id . '/monitoring');

        $response->assertStatus(403);
    }

    public function test_anggota_dilarang_mengakses_tab_anggaran_d16(): void
    {
        $response = $this->actingAs($this->member)->get('/acara/' . $this->event->id . '/anggaran');

        $response->assertStatus(403);
    }

    public function test_ketua_dapat_mengakses_tab_monitoring_dan_anggaran(): void
    {
        $resMonitoring = $this->actingAs($this->leader)->get('/acara/' . $this->event->id . '/monitoring');
        $resMonitoring->assertStatus(200);

        $resAnggaran = $this->actingAs($this->leader)->get('/acara/' . $this->event->id . '/anggaran');
        $resAnggaran->assertStatus(200);
    }

    public function test_mengubah_tanggal_acara_menghitung_ulang_timeline_d11(): void
    {
        $newDate = Carbon::tomorrow()->addDays(90)->toDateString(); // e.g. 90 days from tomorrow

        $response = $this->actingAs($this->leader)->put('/acara/' . $this->event->id, [
            'nama_event' => 'Seminar Teknologi 2026 Update',
            'tujuan' => 'Tujuan seminar yang diperbarui',
            'tanggal' => $newDate,
            'lokasi' => 'Auditorium Utama',
            'target_peserta' => 'Mahasiswa',
            'jumlah_peserta' => 350,
        ]);

        $response->assertRedirect('/acara/' . $this->event->id);

        $this->event->refresh();
        $this->assertEquals($newDate, $this->event->tanggal->toDateString());

        // Check timeline recalculated
        $timelineH60 = Timeline::where('event_id', $this->event->id)->where('offset_hari', 60)->first();
        $expectedH60Date = Carbon::parse($newDate)->subDays(60)->toDateString();
        $this->assertEquals($expectedH60Date, $timelineH60->tanggal->toDateString());

        $timelineHariH = Timeline::where('event_id', $this->event->id)->where('offset_hari', 0)->first();
        $this->assertEquals($newDate, $timelineHariH->tanggal->toDateString());
    }
}

