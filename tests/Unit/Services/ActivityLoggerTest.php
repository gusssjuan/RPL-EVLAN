<?php

namespace Tests\Unit\Services;

use App\Enums\RoleName;
use App\Models\Category;
use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLoggerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);
    }

    public function test_activity_logger_mencatat_log_dengan_benar(): void
    {
        $user = User::factory()->create(['nama' => 'Rina Astuti']);
        $category = Category::factory()->create();
        $event = Event::factory()->create(['leader_id' => $user->id, 'category_id' => $category->id]);

        $log = ActivityLogger::log(
            $event,
            $user,
            'ubah_status',
            'Rina Astuti mengubah status "Membuat rundown acara" → Sedang dikerjakan',
            ['task_id' => 12, 'status' => 'sedang_dikerjakan']
        );

        $this->assertDatabaseHas('activity_logs', [
            'id' => $log->id,
            'event_id' => $event->id,
            'user_id' => $user->id,
            'aksi' => 'ubah_status',
            'deskripsi' => 'Rina Astuti mengubah status "Membuat rundown acara" → Sedang dikerjakan',
        ]);

        $this->assertEquals(12, $log->meta['task_id']);
        $this->assertEquals('sedang_dikerjakan', $log->meta['status']);
    }
}

