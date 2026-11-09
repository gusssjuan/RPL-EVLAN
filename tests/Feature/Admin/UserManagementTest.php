<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleName;
use App\Enums\StatusAkun;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);

        $this->admin = User::factory()->admin()->create([
            'email' => 'admin.utama@evlan.test',
        ]);
    }

    public function test_admin_dapat_melihat_daftar_pengguna(): void
    {
        $targetUser = User::factory()->create([
            'nama' => 'I Made Panitia',
            'email' => 'panitia@evlan.test',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/pengguna');

        $response->assertStatus(200);
        $response->assertSee('I Made Panitia');
        $response->assertSee('panitia@evlan.test');
    }

    public function test_admin_dapat_menonaktifkan_pengguna(): void
    {
        $user = User::factory()->create(['status_akun' => StatusAkun::Aktif]);

        $response = $this->actingAs($this->admin)->post('/admin/pengguna/'.$user->id.'/toggle');

        $response->assertSessionHas('status');
        $this->assertEquals(StatusAkun::Nonaktif, $user->fresh()->status_akun);
    }

    public function test_admin_dilarang_menonaktifkan_dirinya_sendiri(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/pengguna/'.$this->admin->id.'/toggle');

        $response->assertSessionHas('error');
        $this->assertEquals(StatusAkun::Aktif, $this->admin->fresh()->status_akun);
    }

    public function test_admin_dilarang_menonaktifkan_admin_terakhir(): void
    {
        // Admin kedua dibuat
        $admin2 = User::factory()->admin()->create(['status_akun' => StatusAkun::Aktif]);

        // Nonaktifkan admin utama
        $this->actingAs($admin2)->post('/admin/pengguna/'.$this->admin->id.'/toggle');
        $this->assertEquals(StatusAkun::Nonaktif, $this->admin->fresh()->status_akun);

        // Sekarang coba nonaktifkan admin2 (yang merupakan admin aktif terakhir)
        // Login sebagai admin lain atau super user -> dilarang
        // Tambahkan user panitia untuk mencoba panggil toggle admin2
        $panitia = User::factory()->create();
        // Buat admin sementara untuk mencoba menonaktifkan admin2
        $adminTemp = User::factory()->admin()->create(['status_akun' => StatusAkun::Nonaktif]);

        // Ketika admin2 mencoba toggle akun dirinya sendiri, kena aturan 1
        // Ketika admin2 adalah satu-satunya admin aktif:
        $activeAdminCount = User::whereHas('role', fn ($q) => $q->where('nama', 'admin'))
            ->where('status_akun', StatusAkun::Aktif)
            ->count();
        $this->assertEquals(1, $activeAdminCount);

        // Aktifkan admin ketiga lalu coba nonaktifkan hingga sisa 1
        $admin3 = User::factory()->admin()->create(['status_akun' => StatusAkun::Aktif]);

        // admin2 menonaktifkan admin3 sukses
        $this->actingAs($admin2)->post('/admin/pengguna/'.$admin3->id.'/toggle');
        $this->assertEquals(StatusAkun::Nonaktif, $admin3->fresh()->status_akun);

        // Sekarang admin3 mencoba menonaktifkan admin2 (admin aktif terakhir)
        $this->actingAs($admin3)->post('/admin/pengguna/'.$admin2->id.'/toggle');
        $this->assertEquals(StatusAkun::Aktif, $admin2->fresh()->status_akun);
    }

    public function test_pengguna_yang_dinonaktifkan_tidak_bisa_login(): void
    {
        $user = User::factory()->create([
            'email' => 'dinonaktifkan@evlan.test',
            'password' => bcrypt('password123'),
            'status_akun' => StatusAkun::Nonaktif,
        ]);

        $response = $this->post('/login', [
            'email' => 'dinonaktifkan@evlan.test',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }
}

