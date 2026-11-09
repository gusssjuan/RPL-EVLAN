<?php

namespace Tests\Feature\Admin;

use App\Enums\JenisNotifikasi;
use App\Enums\RoleName;
use App\Enums\StatusVerifikasi;
use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $vendorUser;

    protected Vendor $vendor;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);

        $this->admin = User::factory()->admin()->create();

        $this->vendorUser = User::factory()->vendor()->create();
        $this->vendor = Vendor::factory()->create([
            'user_id' => $this->vendorUser->id,
            'nama_vendor' => 'Sinar Sound System',
            'status_verifikasi' => StatusVerifikasi::Menunggu,
        ]);
    }

    public function test_admin_dapat_melihat_daftar_vendor_dengan_filter(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/verifikasi-vendor?status=menunggu');

        $response->assertStatus(200);
        $response->assertSee('Sinar Sound System');
    }

    public function test_admin_dapat_menyetujui_vendor_dan_mengirim_notifikasi(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/verifikasi-vendor/'.$this->vendor->id.'/setujui');

        $response->assertRedirect('/admin/verifikasi-vendor');

        $this->vendor->refresh();
        $this->assertEquals(StatusVerifikasi::Terverifikasi, $this->vendor->status_verifikasi);
        $this->assertNotNull($this->vendor->verified_at);

        // Process Spec 6.0: Notifikasi verifikasi terkirim
        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $this->vendorUser->id,
            'jenis' => JenisNotifikasi::VerifikasiVendor->value,
        ]);
    }

    public function test_admin_dapat_menolak_vendor_dengan_alasan_wajib(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/verifikasi-vendor/'.$this->vendor->id.'/tolak', [
            'alasan_penolakan' => 'Nomor kontak tidak valid.',
        ]);

        $response->assertRedirect('/admin/verifikasi-vendor');

        $this->vendor->refresh();
        $this->assertEquals(StatusVerifikasi::Ditolak, $this->vendor->status_verifikasi);
        $this->assertEquals('Nomor kontak tidak valid.', $this->vendor->alasan_penolakan);

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $this->vendorUser->id,
            'jenis' => JenisNotifikasi::VerifikasiVendor->value,
        ]);
    }

    public function test_penolakan_vendor_tanpa_alasan_ditolak(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/verifikasi-vendor/'.$this->vendor->id.'/tolak', [
            'alasan_penolakan' => '',
        ]);

        $response->assertSessionHasErrors('alasan_penolakan');
        $this->vendor->refresh();
        $this->assertEquals(StatusVerifikasi::Menunggu, $this->vendor->status_verifikasi);
    }
}
