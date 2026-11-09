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

class VendorDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Vendor $vendorTerverifikasi;

    protected Vendor $vendorMenunggu;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        $roleVendor = Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        $roleUser = Role::firstOrCreate(['nama' => RoleName::User->value]);

        $this->user = User::factory()->create(['role_id' => $roleUser->id]);

        $userVendor1 = User::factory()->create(['role_id' => $roleVendor->id]);
        $this->vendorTerverifikasi = Vendor::create([
            'user_id' => $userVendor1->id,
            'nama_vendor' => 'Dapur Bu Komang Catering',
            'kategori_vendor' => KategoriVendor::Catering->value,
            'alamat' => 'Jl. Katering Sehat No. 1',
            'kota' => 'Denpasar',
            'kontak' => '081122334455',
            'status_verifikasi' => StatusVerifikasi::Terverifikasi,
            'verified_at' => now(),
        ]);

        VendorProduct::create([
            'vendor_id' => $this->vendorTerverifikasi->id,
            'nama_produk' => 'Nasi Kotak Premium',
            'harga' => 35000,
            'is_aktif' => true,
        ]);

        $userVendor2 = User::factory()->create(['role_id' => $roleVendor->id]);
        $this->vendorMenunggu = Vendor::create([
            'user_id' => $userVendor2->id,
            'nama_vendor' => 'Dekorasi Indah Abadi',
            'kategori_vendor' => KategoriVendor::Dekorasi->value,
            'alamat' => 'Jl. Mawar No. 5',
            'kota' => 'Badung',
            'kontak' => '082233445566',
            'status_verifikasi' => StatusVerifikasi::Menunggu,
        ]);
    }

    public function test_pencarian_hanya_menampilkan_vendor_terverifikasi(): void
    {
        $response = $this->actingAs($this->user)->get(route('vendor.index'));

        $response->assertStatus(200);
        $response->assertSee('Dapur Bu Komang Catering');
        $response->assertDontSee('Dekorasi Indah Abadi');
    }

    public function test_filter_kategori_dan_cari_vendor(): void
    {
        $response = $this->actingAs($this->user)->get(route('vendor.index', [
            'kategori' => KategoriVendor::Catering->value,
            'cari' => 'Bu Komang',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Dapur Bu Komang Catering');

        // Cari kategori lain yang tidak cocok
        $responseKosong = $this->actingAs($this->user)->get(route('vendor.index', [
            'kategori' => KategoriVendor::Dokumentasi->value,
        ]));

        $responseKosong->assertStatus(200);
        $responseKosong->assertDontSee('Dapur Bu Komang Catering');
    }

    public function test_detail_vendor_terverifikasi_dapat_diakses_tetapi_menunggu_404(): void
    {
        $responseOk = $this->actingAs($this->user)->get(route('vendor.show', $this->vendorTerverifikasi));
        $responseOk->assertStatus(200);
        $responseOk->assertSee('Dapur Bu Komang Catering');
        $responseOk->assertSee('Nasi Kotak Premium');

        $response404 = $this->actingAs($this->user)->get(route('vendor.show', $this->vendorMenunggu));
        $response404->assertStatus(404);
    }

    public function test_toggle_simpan_vendor_tersimpan(): void
    {
        // 1. Simpan (bookmark)
        $responseSave = $this->actingAs($this->user)->post(route('vendor.toggle-save', $this->vendorTerverifikasi));
        $responseSave->assertRedirect();
        $this->assertDatabaseHas('saved_vendors', [
            'user_id' => $this->user->id,
            'vendor_id' => $this->vendorTerverifikasi->id,
        ]);

        // Cek tab tersimpan
        $responseTab = $this->actingAs($this->user)->get(route('vendor.index', ['tab' => 'tersimpan']));
        $responseTab->assertSee('Dapur Bu Komang Catering');

        // 2. Batal simpan (unbookmark)
        $responseUnsave = $this->actingAs($this->user)->post(route('vendor.toggle-save', $this->vendorTerverifikasi));
        $responseUnsave->assertRedirect();
        $this->assertDatabaseMissing('saved_vendors', [
            'user_id' => $this->user->id,
            'vendor_id' => $this->vendorTerverifikasi->id,
        ]);
    }
}
