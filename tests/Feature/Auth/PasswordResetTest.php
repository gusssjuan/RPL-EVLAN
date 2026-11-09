<?php

namespace Tests\Feature\Auth;

use App\Enums\RoleName;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);
    }

    public function test_halaman_lupa_kata_sandi_dapat_ditampilkan(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Lupa Kata Sandi');
    }

    public function test_tautan_reset_kata_sandi_dapat_diminta(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'user@evlan.test',
        ]);

        $response = $this->post('/forgot-password', [
            'email' => 'user@evlan.test',
        ]);

        $response->assertSessionHasNoErrors();
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_halaman_atur_ulang_kata_sandi_dapat_ditampilkan(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->get('/reset-password/'.$token.'?email='.$user->email);

        $response->assertStatus(200);
        $response->assertSee('Atur Ulang Kata Sandi');
    }

    public function test_kata_sandi_dapat_diatur_ulang_dengan_token_valid(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/login');

        $this->assertTrue(Hash::check('passwordbaru123', $user->fresh()->password));
    }

    public function test_kata_sandi_tidak_dapat_diatur_ulang_dengan_token_tidak_valid(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/reset-password', [
            'token' => 'token-palsu-tidak-valid',
            'email' => $user->email,
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check('password123', $user->fresh()->password));
    }
}

