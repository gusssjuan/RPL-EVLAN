<?php

namespace Tests\Feature\Auth;

use App\Enums\RoleName;
use App\Enums\StatusAkun;
use App\Enums\StatusVerifikasi;
use App\Models\Category;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RbacPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);

        // Dummy test routes to test middleware
        Route::get('/test-role-user', function () {
            return 'OK User';
        })->middleware(['web', 'role:user']);

        Route::get('/test-role-admin', function () {
            return 'OK Admin';
        })->middleware(['web', 'role:admin']);

        Route::get('/test-role-vendor', function () {
            return 'OK Vendor';
        })->middleware(['web', 'role:vendor']);
    }

    public function test_member_tidak_bisa_mengakses_anggaran_atau_monitoring_d16(): void
    {
        $category = Category::factory()->create();
        $leader = User::factory()->create();
        $member = User::factory()->create();

        $event = Event::factory()->create([
            'leader_id' => $leader->id,
            'category_id' => $category->id,
            'is_publik' => false,
        ]);

        EventMember::factory()->create([
            'event_id' => $event->id,
            'user_id' => $member->id,
        ]);

        // Leader can view budget & monitoring
        $this->assertTrue(Gate::forUser($leader)->allows('viewBudget', $event));
        $this->assertTrue(Gate::forUser($leader)->allows('viewMonitoring', $event));

        // Member is forbidden (403)
        $this->assertFalse(Gate::forUser($member)->allows('viewBudget', $event));
        $this->assertFalse(Gate::forUser($member)->allows('viewMonitoring', $event));
    }

    public function test_user_non_peserta_tidak_bisa_melihat_acara_privat(): void
    {
        $category = Category::factory()->create();
        $leader = User::factory()->create();
        $stranger = User::factory()->create();

        $event = Event::factory()->create([
            'leader_id' => $leader->id,
            'category_id' => $category->id,
            'is_publik' => false,
        ]);

        $this->assertTrue(Gate::forUser($leader)->allows('view', $event));
        $this->assertFalse(Gate::forUser($stranger)->allows('view', $event));
    }

    public function test_vendor_dilarang_mengakses_halaman_khusus_user(): void
    {
        $vendor = User::factory()->vendor()->create();

        $response = $this->actingAs($vendor)->get('/test-role-user');

        $response->assertStatus(403);
    }

    public function test_user_dilarang_mengakses_halaman_khusus_admin(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/test-role-admin');

        $response->assertStatus(403);
    }

    public function test_hanya_penerima_tugas_yang_dapat_update_status_tugas(): void
    {
        $category = Category::factory()->create();
        $leader = User::factory()->create();
        $assignee = User::factory()->create();
        $otherMember = User::factory()->create();

        $event = Event::factory()->create([
            'leader_id' => $leader->id,
            'category_id' => $category->id,
        ]);

        $task = Task::factory()->create([
            'event_id' => $event->id,
            'penerima_id' => $assignee->id,
            'created_by' => $leader->id,
        ]);

        $this->assertTrue(Gate::forUser($assignee)->allows('updateStatus', $task));
        $this->assertFalse(Gate::forUser($otherMember)->allows('updateStatus', $task));
        $this->assertFalse(Gate::forUser($leader)->allows('updateStatus', $task));
    }

    public function test_vendor_belum_terverifikasi_tidak_bisa_kelola_produk(): void
    {
        $vendorUser = User::factory()->vendor()->create();
        $vendor = Vendor::factory()->create([
            'user_id' => $vendorUser->id,
            'status_verifikasi' => StatusVerifikasi::Menunggu,
        ]);

        $this->assertFalse(Gate::forUser($vendorUser)->allows('manageProducts', $vendor));

        // When verified
        $vendor->update(['status_verifikasi' => StatusVerifikasi::Terverifikasi]);
        $this->assertTrue(Gate::forUser($vendorUser)->allows('manageProducts', $vendor));
    }

    public function test_akun_nonaktif_dipaksa_logout_oleh_middleware(): void
    {
        $user = User::factory()->create([
            'status_akun' => StatusAkun::Aktif,
        ]);

        // Login as active user
        $this->actingAs($user);

        // Later user is deactivated by admin
        $user->status_akun = StatusAkun::Nonaktif;
        $user->save();

        $response = $this->get('/test-role-user');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}
