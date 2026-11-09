<?php

namespace Tests\Feature\Auth;

use App\Enums\RoleName;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);
    }

    public function test_halaman_profil_dapat_ditampilkan(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profil');

        $response->assertStatus(200);
        $response->assertSee('Informasi Pribadi');
    }

    public function test_informasi_profil_dapat_diperbarui(): void
    {
        $user = User::factory()->create([
            'nama' => 'Nama Lama',
            'email' => 'lama@evlan.test',
        ]);

        $response = $this->actingAs($user)->patch('/profil', [
            'nama' => 'Nama Baru',
            'email' => 'baru@evlan.test',
            'no_telepon' => '081234567890',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profil');

        $user->refresh();
        $this->assertSame('Nama Baru', $user->nama);
        $this->assertSame('baru@evlan.test', $user->email);
        $this->assertSame('081234567890', $user->no_telepon);
    }

    public function test_email_duplikat_pada_update_profil_ditolak(): void
    {
        $user1 = User::factory()->create(['email' => 'user1@evlan.test']);
        $user2 = User::factory()->create(['email' => 'user2@evlan.test']);

        $response = $this->actingAs($user1)->patch('/profil', [
            'nama' => 'User Satu Ganti Nama',
            'email' => 'user2@evlan.test',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertSame('user1@evlan.test', $user1->fresh()->email);
    }

    public function test_kata_sandi_dapat_diubah_dengan_sandi_lama_yang_benar(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->put('/profil/password', [
            'current_password' => 'password123',
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profil');

        $this->assertTrue(Hash::check('passwordbaru123', $user->fresh()->password));
    }

    public function test_kata_sandi_gagal_diubah_bila_sandi_lama_salah(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->put('/profil/password', [
            'current_password' => 'sandisalah',
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('password123', $user->fresh()->password));
    }

    public function test_preferensi_notifikasi_dapat_disimpan(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/profil/notifikasi', [
            'preferensi' => [
                'tugas_baru' => '1',
                'deadline_h3' => '1',
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profil');

        $user->refresh();
        $this->assertIsArray($user->preferensi_notifikasi);
        $this->assertEquals('1', $user->preferensi_notifikasi['tugas_baru']);
        $this->assertEquals('1', $user->preferensi_notifikasi['deadline_h3']);
    }
}

