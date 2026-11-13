<?php

namespace Tests\Feature\Auth;

use App\Enums\RoleName;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);
    }

    public function test_halaman_login_dapat_ditampilkan(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk ke EVLAN');
    }

    public function test_login_sukses_pengguna_diarahkan_ke_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'user@evlan.test',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'user@evlan.test',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_login_sukses_admin_diarahkan_ke_admin(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@evlan.test',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@evlan.test',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/admin');
    }

    public function test_login_sukses_vendor_diarahkan_ke_usaha(): void
    {
        $vendor = User::factory()->vendor()->create([
            'email' => 'vendor@evlan.test',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'vendor@evlan.test',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/usaha');
    }

    public function test_pengguna_nonaktif_ditolak_dengan_pesan_generik(): void
    {
        $userNonaktif = User::factory()->nonaktif()->create([
            'email' => 'nonaktif@evlan.test',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'nonaktif@evlan.test',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_halaman_login_menampilkan_banner_generik_dan_mempertahankan_email(): void
    {
        app()->setLocale('id');

        $page = $this->followingRedirects()->from('/login')->post('/login', [
            'email' => 'tidak-terdaftar@evlan.test',
            'password' => 'salah123',
        ]);

        $page->assertOk();
        $page->assertSee('Email atau kata sandi yang Anda masukkan salah.');
        $page->assertSee('value="tidak-terdaftar@evlan.test"', false);
    }

    public function test_kata_sandi_salah_ditolak(): void
    {
        $user = User::factory()->create([
            'email' => 'user@evlan.test',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'user@evlan.test',
            'password' => 'salah123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_rate_limiting_bekerja_setelah_lima_kali_percobaan(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'throttle@evlan.test',
                'password' => 'salah123',
            ]);
        }

        $response = $this->post('/login', [
            'email' => 'throttle@evlan.test',
            'password' => 'salah123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_pengguna_dapat_logout_melalui_post(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
