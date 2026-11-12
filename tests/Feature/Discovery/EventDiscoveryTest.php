<?php

namespace Tests\Feature\Discovery;

use App\Models\Category;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Category $seminar;

    private Category $workshop;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['nama' => 'Peserta Umum']);
        $this->seminar = Category::factory()->create(['nama_kategori' => 'Seminar', 'ikon' => '🎤']);
        $this->workshop = Category::factory()->create(['nama_kategori' => 'Workshop', 'ikon' => '🛠️']);
    }

    private function buatAcara(array $attributes = []): Event
    {
        return Event::factory()->publik()->create(array_merge([
            'leader_id' => User::factory(),
            'category_id' => $this->seminar->id,
            'nama_event' => 'Seminar Teknologi 2026',
            'lokasi' => 'Auditorium Kampus',
            'tanggal' => now()->addMonth()->toDateString(),
            'penyelenggara' => 'Himpunan Mahasiswa',
        ], $attributes));
    }

    public function test_hanya_acara_publik_yang_tampil_di_daftar(): void
    {
        $publik = $this->buatAcara(['nama_event' => 'Acara Publik Terbuka']);
        Event::factory()->create([
            'leader_id' => User::factory(),
            'category_id' => $this->seminar->id,
            'nama_event' => 'Acara Privat Internal',
            'is_publik' => false,
        ]);

        $response = $this->actingAs($this->user)->get(route('cari-acara.index'));

        $response->assertOk();
        $response->assertSee($publik->nama_event);
        $response->assertDontSee('Acara Privat Internal');
    }

    public function test_filter_kategori_lokasi_rentang_tanggal_dan_pencarian_dapat_dikombinasikan(): void
    {
        $cocok = $this->buatAcara([
            'nama_event' => 'Workshop Desain UI',
            'category_id' => $this->workshop->id,
            'lokasi' => 'Gedung Kreatif Makassar',
            'tanggal' => '2026-12-10',
            'penyelenggara' => 'Komunitas Desain',
        ]);
        $this->buatAcara([
            'nama_event' => 'Workshop Fotografi',
            'category_id' => $this->workshop->id,
            'lokasi' => 'Gedung Kreatif Makassar',
            'tanggal' => '2027-02-10',
        ]);
        $this->buatAcara([
            'nama_event' => 'Seminar Desain Produk',
            'category_id' => $this->seminar->id,
            'lokasi' => 'Gedung Kreatif Makassar',
            'tanggal' => '2026-12-10',
        ]);
        $this->buatAcara([
            'nama_event' => 'Workshop Desain di Jakarta',
            'category_id' => $this->workshop->id,
            'lokasi' => 'Jakarta',
            'tanggal' => '2026-12-10',
        ]);

        $response = $this->actingAs($this->user)->get(route('cari-acara.index', [
            'cari' => 'desain',
            'kategori' => $this->workshop->id,
            'lokasi' => 'Makassar',
            'dari' => '2026-12-01',
            'sampai' => '2026-12-31',
        ]));

        $response->assertOk();
        $response->assertSee($cocok->nama_event);
        $response->assertDontSee('Workshop Fotografi');
        $response->assertDontSee('Seminar Desain Produk');
        $response->assertDontSee('Workshop Desain di Jakarta');
    }

    public function test_pencarian_dapat_menemukan_acara_berdasarkan_penyelenggara(): void
    {
        $this->buatAcara([
            'nama_event' => 'Acara Komunitas',
            'penyelenggara' => 'Yayasan Literasi Nusantara',
        ]);

        $response = $this->actingAs($this->user)->get(route('cari-acara.index', ['cari' => 'Literasi']));

        $response->assertOk();
        $response->assertSee('Acara Komunitas');
    }

    public function test_rentang_tanggal_tidak_valid_mengembalikan_galat_validasi(): void
    {
        $response = $this->actingAs($this->user)->get(route('cari-acara.index', [
            'dari' => '2026-12-31',
            'sampai' => '2026-12-01',
        ]));

        $response->assertSessionHasErrors('sampai');
    }

    public function test_tidak_ada_hasil_menampilkan_keadaan_kosong(): void
    {
        $this->buatAcara(['nama_event' => 'Seminar Teknologi 2026']);

        $response = $this->actingAs($this->user)->get(route('cari-acara.index', ['cari' => 'tidak-ada-yang-cocok']));

        $response->assertOk();
        $response->assertSee('Tidak ada acara yang cocok');
    }

    public function test_detail_acara_publik_dapat_dilihat_dan_menampilkan_panel_pendaftaran(): void
    {
        $event = $this->buatAcara(['kuota' => 150]);

        $response = $this->actingAs($this->user)->get(route('cari-acara.show', $event));

        $response->assertOk();
        $response->assertSee($event->nama_event);
        $response->assertSee('Pendaftaran');
        $response->assertSee('Daftar Acara');
        $response->assertSee('0/150');
    }

    public function test_detail_acara_privat_menghasilkan_404(): void
    {
        $event = Event::factory()->create([
            'leader_id' => User::factory(),
            'category_id' => $this->seminar->id,
            'is_publik' => false,
        ]);

        $this->actingAs($this->user)
            ->get(route('cari-acara.show', $event))
            ->assertNotFound();
    }

    public function test_vendor_tidak_dapat_mengakses_penemuan_acara(): void
    {
        $vendor = User::factory()->vendor()->create();

        $this->actingAs($vendor)
            ->get(route('cari-acara.index'))
            ->assertForbidden();
    }
}
