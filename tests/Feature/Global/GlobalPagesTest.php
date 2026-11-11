<?php

namespace Tests\Feature\Global;

use App\Enums\RoleName;
use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);
    }

    public function test_halaman_beranda_publik_dapat_diakses(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('EVLAN');
        $response->assertSee('Rencanakan Acara Lebih');
        $response->assertSee('Template Otomatis');
        $response->assertSee('Jelajahi Acara Publik');
        $response->assertSee('Lewati ke konten utama');
    }

    public function test_halaman_404_menampilkan_pesan_bahasa_indonesia(): void
    {
        $response = $this->get('/halaman-yang-pasti-tidak-ada-xyz-999');

        $response->assertStatus(404);
        $response->assertSee('Halaman Tidak Ditemukan');
        $response->assertSee('Galat 404');
        $response->assertSee('Ke Beranda');
    }

    public function test_halaman_403_menampilkan_pesan_bahasa_indonesia(): void
    {
        // Buat user biasa dan acara milik orang lain
        $user = User::factory()->create();
        $leader = User::factory()->create();
        $event = Event::factory()->create([
            'leader_id' => $leader->id,
        ]);

        // Akses halaman monitoring yang hanya boleh diakses leader
        $response = $this->actingAs($user)->get("/acara/{$event->id}/monitoring");

        $response->assertStatus(403);
        $response->assertSee('Akses Ditolak');
        $response->assertSee('Galat 403');
    }

    public function test_layout_pengguna_memuat_navigasi_mobile_dan_skip_link(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Lewati ke konten utama');
        // Mobile bottom navigation labels
        $response->assertSee('Navigasi Bawah Ponsel');
        $response->assertSee('Beranda');
        $response->assertSee('Acara');
        $response->assertSee('Tugas');
        $response->assertSee('Vendor');
        $response->assertSee('Lainnya');
    }
}

