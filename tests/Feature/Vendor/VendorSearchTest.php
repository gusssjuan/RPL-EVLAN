<?php

namespace Tests\Feature\Vendor;

use App\Enums\RoleName;
use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorSearchTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);

        $this->user = User::factory()->create();
    }

    public function test_hanya_vendor_terverifikasi_yang_muncul_di_pencarian(): void
    {
        $terverifikasi = Vendor::factory()->terverifikasi()->create([
            'nama_vendor' => 'Sinar Sound System',
            'kota' => 'Denpasar',
        ]);
        VendorProduct::factory()->create([
            'vendor_id' => $terverifikasi->id,
            'nama_produk' => 'Paket Seminar Sound',
            'harga' => 3500000,
        ]);

        Vendor::factory()->create([
            'nama_vendor' => 'Vendor Belum Verifikasi',
            'kota' => 'Denpasar',
        ]);

        $response = $this->actingAs($this->user)->get('/vendor');

        $response->assertStatus(200);
        $response->assertSee('Sinar Sound System');
        $response->assertSee('Mulai dari');
        $response->assertDontSee('Vendor Belum Verifikasi');
    }

    public function test_filter_kategori_kota_dan_pencarian_produk_bekerja(): void
    {
        $catering = Vendor::factory()->terverifikasi()->create([
            'nama_vendor' => 'Dapur Bu Komang',
            'kategori_vendor' => 'catering',
            'kota' => 'Denpasar',
        ]);

        $dekorasi = Vendor::factory()->terverifikasi()->create([
            'nama_vendor' => 'Rias Dekorasi',
            'kategori_vendor' => 'dekorasi',
            'kota' => 'Bandung',
        ]);
        VendorProduct::factory()->create([
            'vendor_id' => $dekorasi->id,
            'nama_produk' => 'Paket Dekorasi Panggung',
        ]);

        $this->actingAs($this->user)
            ->get('/vendor?kategori=catering')
            ->assertSee('Dapur Bu Komang')
            ->assertDontSee('Rias Dekorasi');

        $this->actingAs($this->user)
            ->get('/vendor?kota=Bandung')
            ->assertSee('Rias Dekorasi')
            ->assertDontSee('Dapur Bu Komang');

        $this->actingAs($this->user)
            ->get('/vendor?cari=Panggung')
            ->assertSee('Rias Dekorasi')
            ->assertDontSee('Dapur Bu Komang');
    }

    public function test_pengguna_dapat_menyimpan_dan_membatalkan_vendor(): void
    {
        $vendor = Vendor::factory()->terverifikasi()->create([
            'nama_vendor' => 'LensArt Studio',
        ]);

        $this->actingAs($this->user)
            ->post('/vendor/'.$vendor->id.'/simpan')
            ->assertRedirect();

        $this->assertDatabaseHas('saved_vendors', [
            'user_id' => $this->user->id,
            'vendor_id' => $vendor->id,
        ]);

        $this->actingAs($this->user)
            ->get('/vendor?tab=tersimpan')
            ->assertSee('LensArt Studio');

        $this->actingAs($this->user)
            ->delete('/vendor/'.$vendor->id.'/simpan')
            ->assertRedirect();

        $this->assertDatabaseMissing('saved_vendors', [
            'user_id' => $this->user->id,
            'vendor_id' => $vendor->id,
        ]);
    }

    public function test_akun_vendor_tidak_dapat_mengakses_pencarian_vendor(): void
    {
        $vendorUser = User::factory()->vendor()->create();

        $this->actingAs($vendorUser)->get('/vendor')->assertStatus(403);
    }
}
