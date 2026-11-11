<?php

namespace Tests\Feature\Notification;

use App\Enums\JenisNotifikasi;
use App\Models\AppNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationPageTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_pengguna_hanya_melihat_notifikasi_miliknya(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        AppNotification::factory()->create([
            'user_id' => $user->id,
            'jenis' => JenisNotifikasi::Penugasan,
            'pesan' => 'Notifikasi milik saya',
        ]);
        AppNotification::factory()->create([
            'user_id' => $other->id,
            'jenis' => JenisNotifikasi::Penugasan,
            'pesan' => 'Notifikasi orang lain',
        ]);

        $response = $this->actingAs($user)->get('/notifikasi');

        $response->assertStatus(200);
        $response->assertSee('Notifikasi milik saya');
        $response->assertDontSee('Notifikasi orang lain');
    }

    public function test_filter_belum_dibaca_dan_jenis_berfungsi(): void
    {
        $user = User::factory()->create();

        AppNotification::factory()->create([
            'user_id' => $user->id,
            'jenis' => JenisNotifikasi::Penugasan,
            'pesan' => 'Tugas belum dibaca',
        ]);
        AppNotification::factory()->create([
            'user_id' => $user->id,
            'jenis' => JenisNotifikasi::Deadline,
            'pesan' => 'Deadline belum dibaca',
        ]);
        AppNotification::factory()->create([
            'user_id' => $user->id,
            'jenis' => JenisNotifikasi::Penugasan,
            'pesan' => 'Tugas sudah dibaca',
            'dibaca_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/notifikasi?status=belum&jenis=penugasan');

        $response->assertStatus(200);
        $response->assertSee('Tugas belum dibaca');
        $response->assertDontSee('Deadline belum dibaca');
        $response->assertDontSee('Tugas sudah dibaca');
    }

    public function test_notifikasi_dikelompokkan_hari_ini_kemarin_dan_minggu_ini(): void
    {
        Carbon::setTestNow('2026-11-11 10:00:00');
        $user = User::factory()->create();

        AppNotification::factory()->create([
            'user_id' => $user->id,
            'pesan' => 'Notifikasi hari ini',
            'waktu_kirim' => now(),
        ]);
        AppNotification::factory()->create([
            'user_id' => $user->id,
            'pesan' => 'Notifikasi kemarin',
            'waktu_kirim' => now()->subDay(),
        ]);
        AppNotification::factory()->create([
            'user_id' => $user->id,
            'pesan' => 'Notifikasi minggu ini',
            'waktu_kirim' => now()->subDays(2),
        ]);

        $response = $this->actingAs($user)->get('/notifikasi');

        $response->assertStatus(200);
        $response->assertSeeInOrder(['Hari ini', 'Notifikasi hari ini', 'Kemarin', 'Notifikasi kemarin', 'Minggu ini', 'Notifikasi minggu ini']);
    }

    public function test_klik_notifikasi_menandai_dibaca_dan_mengarahkan_ke_tautan(): void
    {
        $user = User::factory()->create();
        $notification = AppNotification::factory()->create([
            'user_id' => $user->id,
            'tautan' => '/tugas/123',
            'dibaca_at' => null,
        ]);

        $response = $this->actingAs($user)->post('/notifikasi/'.$notification->id.'/baca');

        $response->assertRedirect('/tugas/123');
        $this->assertNotNull($notification->fresh()->dibaca_at);
    }

    public function test_pengguna_tidak_bisa_membaca_notifikasi_orang_lain(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $notification = AppNotification::factory()->create([
            'user_id' => $other->id,
            'dibaca_at' => null,
        ]);

        $response = $this->actingAs($user)->post('/notifikasi/'.$notification->id.'/baca');

        $response->assertForbidden();
        $this->assertNull($notification->fresh()->dibaca_at);
    }

    public function test_tandai_semua_dibaca_hanya_untuk_pengguna_saat_ini(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        AppNotification::factory()->count(2)->create(['user_id' => $user->id, 'dibaca_at' => null]);
        $otherNotification = AppNotification::factory()->create(['user_id' => $other->id, 'dibaca_at' => null]);

        $response = $this->actingAs($user)->patch('/notifikasi/baca-semua');

        $response->assertRedirect();
        $this->assertSame(0, $user->appNotifications()->belumDibaca()->count());
        $this->assertNull($otherNotification->fresh()->dibaca_at);
    }

    public function test_admin_dan_vendor_dapat_membuka_halaman_notifikasi(): void
    {
        $admin = User::factory()->admin()->create();
        $vendor = User::factory()->vendor()->create();

        AppNotification::factory()->create(['user_id' => $admin->id, 'pesan' => 'Notifikasi admin']);
        AppNotification::factory()->create(['user_id' => $vendor->id, 'pesan' => 'Notifikasi vendor']);

        $this->actingAs($admin)->get('/notifikasi')
            ->assertOk()
            ->assertSee('Notifikasi admin')
            ->assertSee('Notifikasi, 1 belum dibaca');

        $this->actingAs($vendor)->get('/notifikasi')
            ->assertOk()
            ->assertSee('Notifikasi vendor');
    }
}
