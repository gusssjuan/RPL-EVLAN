<?php

namespace Tests\Feature\Vendor;

use App\Enums\RoleName;
use App\Enums\StatusVerifikasi;
use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorAreaTest extends TestCase
{
    use RefreshDatabase;

    protected User $vendorUser;

    protected Vendor $vendor;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);

        $this->vendorUser = User::factory()->vendor()->create(['nama' => 'Sinar Sound System']);
        $this->vendor = Vendor::factory()->create([
            'user_id' => $this->vendorUser->id,
            'nama_vendor' => 'Sinar Sound System',
            'status_verifikasi' => StatusVerifikasi::Menunggu,
        ]);
    }

    public function test_vendor_dapat_melihat_dashboard_dengan_status_menunggu(): void
    {
        $response = $this->actingAs($this->vendorUser)->get('/usaha');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Usaha Vendor');
        $response->assertSee('Pendaftaran usaha sedang ditinjau Admin');
        $response->assertSee('Sinar Sound System');
    }

    public function test_vendor_dapat_memperbarui_profil_usaha(): void
    {
        $response = $this->actingAs($this->vendorUser)->put('/usaha/profil', [
            'nama_vendor' => 'Sinar Sound System Bali',
            'kategori_vendor' => 'sound_system',
            'alamat' => 'Jl. Tukad Badung No. 12',
            'kota' => 'Denpasar',
            'kontak' => '081299990001',
            'deskripsi' => 'Penyewaan sound system profesional.',
        ]);

        $response->assertRedirect('/usaha/profil');

        $this->assertDatabaseHas('vendors', [
            'id' => $this->vendor->id,
            'nama_vendor' => 'Sinar Sound System Bali',
            'kota' => 'Denpasar',
        ]);
    }

    public function test_vendor_menunggu_tidak_dapat_menambah_produk(): void
    {
        $response = $this->actingAs($this->vendorUser)->post('/usaha/produk', [
            'nama_produk' => 'Paket Sound System',
            'harga' => 3500000,
            'deskripsi' => '4 mic wireless',
            'is_aktif' => 1,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('vendor_products', [
            'vendor_id' => $this->vendor->id,
            'nama_produk' => 'Paket Sound System',
        ]);
    }

    public function test_vendor_terverifikasi_dapat_menambah_produk(): void
    {
        $this->vendor->update([
            'status_verifikasi' => StatusVerifikasi::Terverifikasi,
            'verified_at' => now(),
        ]);

        $response = $this->actingAs($this->vendorUser)->post('/usaha/produk', [
            'nama_produk' => 'Paket Sound System',
            'harga' => 3500000,
            'deskripsi' => '4 mic wireless',
            'is_aktif' => 1,
        ]);

        $response->assertRedirect('/usaha/produk');
        $this->assertDatabaseHas('vendor_products', [
            'vendor_id' => $this->vendor->id,
            'nama_produk' => 'Paket Sound System',
            'harga' => 3500000,
            'is_aktif' => true,
        ]);
    }

    public function test_pengguna_non_vendor_tidak_dapat_mengakses_area_vendor(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/usaha');

        $response->assertStatus(403);
    }
}
