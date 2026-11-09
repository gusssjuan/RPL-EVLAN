<?php

namespace Tests\Feature\Auth;

use App\Enums\RoleName;
use App\Models\Category;
use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRoutingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);
    }

    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_admin_mengunjungi_dashboard_diarahkan_ke_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertRedirect('/admin');
    }

    public function test_vendor_mengunjungi_dashboard_diarahkan_ke_usaha(): void
    {
        $vendor = User::factory()->vendor()->create();

        $response = $this->actingAs($vendor)->get('/dashboard');

        $response->assertRedirect('/usaha');
    }

    public function test_user_ketua_melihat_dashboard_ketua(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        // Create an event led by this user
        Event::factory()->create([
            'leader_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Ketua Panitia');
    }

    public function test_user_anggota_melihat_dashboard_anggota(): void
    {
        $user = User::factory()->create();

        // User has not led any event
        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Kepanitiaan');
    }
}

