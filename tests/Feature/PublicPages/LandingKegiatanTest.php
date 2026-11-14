<?php

namespace Tests\Feature\PublicPages;

use App\Enums\StatusPendaftaran;
use App\Models\Category;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingKegiatanTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_menampilkan_kegiatan_publik_terdekat_maksimal_tiga(): void
    {
        $kategori = Category::factory()->create(['nama_kategori' => 'Seminar']);

        $pertama = $this->buatKegiatan('Kegiatan Pertama', today()->addDays(2), $kategori);
        $kedua = $this->buatKegiatan('Kegiatan Kedua', today()->addDays(4), $kategori);
        $ketiga = $this->buatKegiatan('Kegiatan Ketiga', today()->addDays(6), $kategori);
        $keempat = $this->buatKegiatan('Kegiatan Keempat', today()->addDays(8), $kategori);
        $this->buatKegiatan('Kegiatan Privat', today()->addDays(1), $kategori, false);
        $this->buatKegiatan('Kegiatan Lampau', today()->subDay(), $kategori);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeInOrder(['Kegiatan Pertama', 'Kegiatan Kedua', 'Kegiatan Ketiga']);
        $response->assertDontSee('Kegiatan Keempat');
        $response->assertDontSee('Kegiatan Privat');
        $response->assertDontSee('Kegiatan Lampau');
        $response->assertSee(route('cari-acara.show', $pertama), false);
        $response->assertSee(route('cari-acara.show', $kedua), false);
        $response->assertSee(route('cari-acara.show', $ketiga), false);
        $response->assertDontSee(route('cari-acara.show', $keempat), false);
        $response->assertSee(route('cari-acara.index'), false);
    }

    public function test_landing_menampilkan_status_pendaftaran_kegiatan(): void
    {
        $kategori = Category::factory()->create(['nama_kategori' => 'Komunitas']);

        $this->buatKegiatan('Kegiatan Dibuka', today()->addDays(3), $kategori);

        $this->buatKegiatan('Kegiatan Ditutup', today()->addDays(4), $kategori, true, [
            'pendaftaran_dibuka' => false,
        ]);

        $penuh = $this->buatKegiatan('Kegiatan Penuh', today()->addDays(5), $kategori, true, [
            'kuota' => 1,
        ]);

        EventRegistration::factory()->create([
            'event_id' => $penuh->id,
            'user_id' => User::factory()->create()->id,
            'status' => StatusPendaftaran::Diterima,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Pendaftaran dibuka');
        $response->assertSee('Pendaftaran ditutup');
        $response->assertSee('Kuota penuh');
    }

    public function test_landing_menampilkan_keadaan_kosong_bila_belum_ada_kegiatan_terbuka(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Belum ada kegiatan terbuka saat ini.');
    }

    private function buatKegiatan(
        string $nama,
        \DateTimeInterface $tanggal,
        Category $kategori,
        bool $publik = true,
        array $atribut = []
    ): Event {
        return Event::factory()->create(array_merge([
            'nama_event' => $nama,
            'category_id' => $kategori->id,
            'tanggal' => $tanggal->format('Y-m-d'),
            'is_publik' => $publik,
            'pendaftaran_dibuka' => true,
            'batas_pendaftaran' => today()->addDays(2)->toDateString(),
            'kuota' => 50,
        ], $atribut));
    }
}
