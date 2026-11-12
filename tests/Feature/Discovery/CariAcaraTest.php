<?php

namespace Tests\Feature\Discovery;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CariAcaraTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengunjung_dapat_melihat_halaman_cari_acara(): void
    {
        $response = $this->get(route('cari-acara.index'));

        $response->assertStatus(200);
        $response->assertSee('Temukan Acara Menarik');
    }

    public function test_hanya_acara_publik_yang_ditampilkan(): void
    {
        $acaraPublik = Event::factory()->publik()->create([
            'nama_event' => 'Seminar Teknologi Masa Depan',
        ]);

        $acaraPrivat = Event::factory()->create([
            'nama_event' => 'Rapat Rahasia Organisasi',
            'is_publik' => false,
        ]);

        $response = $this->get(route('cari-acara.index'));

        $response->assertStatus(200);
        $response->assertSee('Seminar Teknologi Masa Depan');
        $response->assertDontSee('Rapat Rahasia Organisasi');
    }

    public function test_pencarian_berdasarkan_kata_kunci(): void
    {
        Event::factory()->publik()->create([
            'nama_event' => 'Festival Musik Kampus',
            'penyelenggara' => 'BEM Universitas',
        ]);

        Event::factory()->publik()->create([
            'nama_event' => 'Workshop Desain Grafis',
            'penyelenggara' => 'Komunitas Kreatif',
        ]);

        $response = $this->get(route('cari-acara.index', ['q' => 'Musik']));
        $response->assertStatus(200);
        $response->assertSee('Festival Musik Kampus');
        $response->assertDontSee('Workshop Desain Grafis');

        $responsePenyelenggara = $this->get(route('cari-acara.index', ['q' => 'Komunitas']));
        $responsePenyelenggara->assertStatus(200);
        $responsePenyelenggara->assertSee('Workshop Desain Grafis');
        $responsePenyelenggara->assertDontSee('Festival Musik Kampus');
    }

    public function test_filter_berdasarkan_kategori(): void
    {
        $kategoriA = Category::factory()->create(['nama_kategori' => 'Pendidikan']);
        $kategoriB = Category::factory()->create(['nama_kategori' => 'Hiburan']);

        Event::factory()->publik()->create([
            'nama_event' => 'Seminar Pendidikan Nasional',
            'category_id' => $kategoriA->id,
        ]);

        Event::factory()->publik()->create([
            'nama_event' => 'Konser Hiburan Akhir Tahun',
            'category_id' => $kategoriB->id,
        ]);

        $response = $this->get(route('cari-acara.index', ['kategori' => $kategoriA->id]));

        $response->assertStatus(200);
        $response->assertSee('Seminar Pendidikan Nasional');
        $response->assertDontSee('Konser Hiburan Akhir Tahun');
    }

    public function test_filter_berdasarkan_lokasi(): void
    {
        Event::factory()->publik()->create([
            'nama_event' => 'Acara di Makassar',
            'lokasi' => 'Gedung Balai Praja Makassar',
        ]);

        Event::factory()->publik()->create([
            'nama_event' => 'Acara di Jakarta',
            'lokasi' => 'Stadion Utama Senayan Jakarta',
        ]);

        $response = $this->get(route('cari-acara.index', ['lokasi' => 'Makassar']));

        $response->assertStatus(200);
        $response->assertSee('Acara di Makassar');
        $response->assertDontSee('Acara di Jakarta');
    }

    public function test_filter_berdasarkan_rentang_tanggal(): void
    {
        Event::factory()->publik()->create([
            'nama_event' => 'Acara Awal Bulan',
            'tanggal' => '2026-05-10',
        ]);

        Event::factory()->publik()->create([
            'nama_event' => 'Acara Akhir Bulan',
            'tanggal' => '2026-05-28',
        ]);

        $response = $this->get(route('cari-acara.index', [
            'dari' => '2026-05-01',
            'sampai' => '2026-05-15',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Acara Awal Bulan');
        $response->assertDontSee('Acara Akhir Bulan');
    }

    public function test_tampilan_keadaan_kosong_atau_tidak_ada_hasil(): void
    {
        $response = $this->get(route('cari-acara.index', ['q' => 'KataKunciYangPastiTidakAda123']));

        $response->assertStatus(200);
        $response->assertSee('Tidak ada acara yang cocok');
    }
}
