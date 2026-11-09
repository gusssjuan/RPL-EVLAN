<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleName;
use App\Enums\StatusEvent;
use App\Enums\StatusVerifikasi;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Event;
use App\Models\Role;
use App\Models\Template;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);
    }

    public function test_user_biasa_dilarang_mengakses_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_admin_dapat_mengakses_dashboard_dengan_data_metrik(): void
    {
        $admin = User::factory()->admin()->create();

        $category = Category::factory()->create();
        Template::factory()->create(['category_id' => $category->id]);

        $event = Event::factory()->create([
            'category_id' => $category->id,
            'status_event' => StatusEvent::Perencanaan,
        ]);

        $vendorUser = User::factory()->vendor()->create();
        $vendor = Vendor::factory()->create([
            'user_id' => $vendorUser->id,
            'nama_vendor' => 'Vendor Sound Baliku',
            'status_verifikasi' => StatusVerifikasi::Menunggu,
        ]);

        ActivityLog::create([
            'event_id' => $event->id,
            'user_id' => $admin->id,
            'aksi' => 'Buat Template',
            'deskripsi' => 'Admin membuat template acara baru',
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Administrator');
        $response->assertSee('Vendor Sound Baliku');
        $response->assertSee('Admin membuat template acara baru');
    }
}
