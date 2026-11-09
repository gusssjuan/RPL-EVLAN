<?php

namespace Tests\Feature\Vendor;

use App\Enums\KategoriVendor;
use App\Enums\RoleName;
use App\Enums\StatusVerifikasi;
use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorAreaTest extends TestCase
{
    use RefreshDatabase;

    protected User $vendorUser;

    protected Vendor $vendor;

    protected User $userBiasa;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        $roleVendor = Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        $roleUser = Role::firstOrCreate(['nama' => RoleName::User->value]);

        $this->vendorUser = User::factory()->create([
            'role_id' => $roleVendor->id,
            'nama' => 'Pak Wayan Vendor',
        ]);

        $this->vendor = Vendor::create([
            'user_id' => $this->vendorUser->id,
            'nama_vendor' => 'Sinar Sound System',
            'kategori_vendor' => KategoriVendor::SoundSystem->value,
            'alamat' => 'Jl. Merdeka No. 10',
            'kota' => 'Denpasar',
            'kontak' => '081234567890',
            'status_verifikasi' => StatusVerifikasi::Menunggu,
        ]);

        $this->userBiasa = User::factory()->create([
            'role_id' => $roleUser->id,
        ]);
    }

    public function test_user_biasa_dilarang_mengakses_area_vendor(): void
    {
        $response = $this->actingAs($this->userBiasa)->get(route('usaha.dashboard'));
        $response->assertStatus(403);
    }

    public function test_vendor_dapat_melihat_dashboard_usaha(): void
    {
        $response = $this->actingAs($this->vendorUser)->get(route('usaha.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Sinar Sound System');
        $response->assertSee('Akun Menunggu Verifikasi');
    }

    public function test_vendor_dapat_memperbarui_profil_usaha(): void
    {
        $response = $this->actingAs($this->vendorUser)->put(route('usaha.profil.update'), [
            'nama_vendor' => 'Sinar Sound System Bali Updated',
            'kategori_vendor' => KategoriVendor::SoundSystem->value,
            'alamat' => 'Jl. Sunset Road No. 88',
            'kota' => 'Badung',
            'kontak' => '08987654321',
            'deskripsi' => 'Penyedia sound system konser dan seminar.',
        ]);

        $response->assertRedirect(route('usaha.profil'));
        $this->assertDatabaseHas('vendors', [
            'id' => $this->vendor->id,
            'nama_vendor' => 'Sinar Sound System Bali Updated',
            'kota' => 'Badung',
        ]);
    }

    public function test_vendor_menunggu_verifikasi_dilarang_menambah_produk(): void
    {
        // Status awal: Menunggu
        $response = $this->actingAs($this->vendorUser)->post(route('usaha.produk.store'), [
            'nama_produk' => 'Paket Sound 5000W',
            'harga' => 2000000,
            'deskripsi' => 'Sound system lengkap',
        ]);

        $response->assertStatus(403);
    }

    public function test_vendor_terverifikasi_dapat_menambah_mengubah_dan_menghapus_produk(): void
    {
        // Ubah status ke Terverifikasi
        $this->vendor->update([
            'status_verifikasi' => StatusVerifikasi::Terverifikasi,
            'verified_at' => now(),
        ]);

        // 1. Tambah Produk
        $response = $this->actingAs($this->vendorUser)->post(route('usaha.produk.store'), [
            'nama_produk' => 'Paket Seminar 300 Orang',
            'harga' => 1500000,
            'deskripsi' => '2 mic wireless, 4 speaker, mixer',
            'is_aktif' => true,
        ]);

        $response->assertRedirect(route('usaha.produk.index'));
        $this->assertDatabaseHas('vendor_products', [
            'vendor_id' => $this->vendor->id,
            'nama_produk' => 'Paket Seminar 300 Orang',
            'harga' => 1500000,
        ]);

        $produk = VendorProduct::where('nama_produk', 'Paket Seminar 300 Orang')->first();

        // 2. Update Produk
        $responseUpdate = $this->actingAs($this->vendorUser)->put(route('usaha.produk.update', $produk), [
            'nama_produk' => 'Paket Seminar 300 Orang (Revisi)',
            'harga' => 1750000,
            'deskripsi' => 'Update deskripsi',
            'is_aktif' => true,
        ]);

        $responseUpdate->assertRedirect(route('usaha.produk.index'));
        $this->assertDatabaseHas('vendor_products', [
            'id' => $produk->id,
            'nama_produk' => 'Paket Seminar 300 Orang (Revisi)',
            'harga' => 1750000,
        ]);

        // 3. Hapus Produk
        $responseDelete = $this->actingAs($this->vendorUser)->delete(route('usaha.produk.destroy', $produk));
        $responseDelete->assertRedirect(route('usaha.produk.index'));
        $this->assertDatabaseMissing('vendor_products', [
            'id' => $produk->id,
        ]);
    }
}
