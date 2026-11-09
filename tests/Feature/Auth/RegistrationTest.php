<?php

namespace Tests\Feature\Auth;

use App\Enums\RoleName;
use App\Enums\StatusAkun;
use App\Enums\StatusVerifikasi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);
    }

    public function test_halaman_registrasi_dapat_ditampilkan(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Buat Akun Baru');
    }

    public function test_registrasi_pengguna_sukses(): void
    {
        $response = $this->post('/register', [
            'tipe' => 'user',
            'nama' => 'Gus Ega',
            'email' => 'gusega@evlan.test',
            'no_telepon' => '081234567891',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', [
            'nama' => 'Gus Ega',
            'email' => 'gusega@evlan.test',
            'status_akun' => StatusAkun::Aktif->value,
        ]);

        $user = User::where('email', 'gusega@evlan.test')->first();
        $this->assertTrue($user->isUser());
    }

    public function test_registrasi_vendor_membuat_dua_baris_dengan_status_menunggu(): void
    {
        $response = $this->post('/register', [
            'tipe' => 'vendor',
            'nama' => 'Pemilik Sinar Sound',
            'email' => 'sinarsound@evlan.test',
            'no_telepon' => '081299990001',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nama_vendor' => 'Sinar Sound System',
            'kategori_vendor' => 'sound_system',
            'alamat' => 'Jl. Tukad Badung No. 12, Denpasar',
            'kontak' => '081299990001',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/usaha');

        $user = User::where('email', 'sinarsound@evlan.test')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isVendor());

        $this->assertDatabaseHas('vendors', [
            'user_id' => $user->id,
            'nama_vendor' => 'Sinar Sound System',
            'kategori_vendor' => 'sound_system',
            'status_verifikasi' => StatusVerifikasi::Menunggu->value,
        ]);
    }

    public function test_registrasi_dengan_email_duplikat_ditolak(): void
    {
        User::factory()->create([
            'email' => 'terdaftar@evlan.test',
        ]);

        $response = $this->post('/register', [
            'tipe' => 'user',
            'nama' => 'User Baru',
            'email' => 'terdaftar@evlan.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_registrasi_dengan_kata_sandi_kurang_dari_delapan_karakter_ditolak(): void
    {
        $response = $this->post('/register', [
            'tipe' => 'user',
            'nama' => 'User Baru',
            'email' => 'pendek@evlan.test',
            'password' => 'pendek',
            'password_confirmation' => 'pendek',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_registrasi_vendor_membutuhkan_data_usaha(): void
    {
        $response = $this->post('/register', [
            'tipe' => 'vendor',
            'nama' => 'Vendor Kurang Data',
            'email' => 'kurang@evlan.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            // data usaha dikosongkan
        ]);

        $response->assertSessionHasErrors(['nama_vendor', 'kategori_vendor', 'alamat', 'kontak']);
        $this->assertGuest();
    }
}
