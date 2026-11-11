<?php

namespace Tests\Unit\Services;

use App\Enums\JenisNotifikasi;
use App\Models\AppNotification;
use App\Models\Category;
use App\Models\Event;
use App\Models\Task;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_kirim_menyimpan_notifikasi_in_app_lengkap(): void
    {
        $leader = User::factory()->create();
        $penerima = User::factory()->create();
        $category = Category::factory()->create();
        $event = Event::factory()->create([
            'leader_id' => $leader->id,
            'category_id' => $category->id,
        ]);
        $task = Task::factory()->create([
            'event_id' => $event->id,
            'division_id' => null,
            'penerima_id' => $penerima->id,
            'created_by' => $leader->id,
        ]);

        $notification = NotificationService::kirim(
            $penerima,
            JenisNotifikasi::Penugasan,
            'Anda mendapat tugas baru.',
            $event,
            $task,
            '/tugas/'.$task->id,
            'tugas:'.$task->id
        );

        $this->assertInstanceOf(AppNotification::class, $notification);
        $this->assertDatabaseHas('app_notifications', [
            'id' => $notification->id,
            'user_id' => $penerima->id,
            'event_id' => $event->id,
            'task_id' => $task->id,
            'jenis' => JenisNotifikasi::Penugasan->value,
            'pesan' => 'Anda mendapat tugas baru.',
            'tautan' => '/tugas/'.$task->id,
            'dedupe_key' => 'tugas:'.$task->id,
            'dibaca_at' => null,
        ]);
    }

    public function test_dedupe_key_mencegah_duplikat_untuk_pengguna_yang_sama(): void
    {
        $user = User::factory()->create();

        NotificationService::kirim($user, JenisNotifikasi::Deadline, 'Pertama', null, null, null, 'deadline:1:mendekati');
        NotificationService::kirim($user, JenisNotifikasi::Deadline, 'Kedua', null, null, null, 'deadline:1:mendekati');

        $this->assertDatabaseCount('app_notifications', 1);
        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $user->id,
            'pesan' => 'Pertama',
        ]);
    }

    public function test_dedupe_key_yang_sama_boleh_dipakai_untuk_pengguna_berbeda(): void
    {
        $penerima = User::factory()->create();
        $leader = User::factory()->create();

        NotificationService::kirim($penerima, JenisNotifikasi::Deadline, 'Untuk penerima', null, null, null, 'deadline:1:mendekati');
        NotificationService::kirim($leader, JenisNotifikasi::Deadline, 'Untuk leader', null, null, null, 'deadline:1:mendekati');

        $this->assertDatabaseCount('app_notifications', 2);
        $this->assertDatabaseHas('app_notifications', ['user_id' => $penerima->id, 'dedupe_key' => 'deadline:1:mendekati']);
        $this->assertDatabaseHas('app_notifications', ['user_id' => $leader->id, 'dedupe_key' => 'deadline:1:mendekati']);
    }

    public function test_jumlah_belum_dibaca_hanya_menghitung_notifikasi_pengguna(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        AppNotification::factory()->count(2)->create(['user_id' => $user->id]);
        AppNotification::factory()->create(['user_id' => $user->id, 'dibaca_at' => now()]);
        AppNotification::factory()->create(['user_id' => $other->id]);

        $this->assertSame(2, NotificationService::jumlahBelumDibaca($user));
    }
}
