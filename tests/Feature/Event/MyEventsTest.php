<?php

namespace Tests\Feature\Event;

use App\Enums\RoleName;
use App\Models\Category;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyEventsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected User $otherUser;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);

        $this->user = User::factory()->create(['nama' => 'Gus Ega']);
        $this->otherUser = User::factory()->create(['nama' => 'User Lain']);

        $this->category = Category::factory()->create(['nama_kategori' => 'Seminar']);
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $response = $this->get('/acara');
        $response->assertRedirect('/login');
    }

    public function test_hanya_menampilkan_acara_yang_dipimpin_atau_diikuti(): void
    {
        // 1. Acara yang dipimpin user
        $eventLed = Event::factory()->create([
            'leader_id' => $this->user->id,
            'category_id' => $this->category->id,
            'nama_event' => 'Acara Saya Pimpin',
            'tanggal' => Carbon::parse('2026-12-10'),
        ]);

        // 2. Acara yang diikuti user sebagai anggota
        $eventJoined = Event::factory()->create([
            'leader_id' => $this->otherUser->id,
            'category_id' => $this->category->id,
            'nama_event' => 'Acara Saya Ikuti',
            'tanggal' => Carbon::parse('2026-12-15'),
        ]);
        EventMember::factory()->create([
            'event_id' => $eventJoined->id,
            'user_id' => $this->user->id,
        ]);

        // 3. Acara orang lain yang tidak diikuti
        $eventOther = Event::factory()->create([
            'leader_id' => $this->otherUser->id,
            'category_id' => $this->category->id,
            'nama_event' => 'Acara Rahasia Orang Lain',
            'tanggal' => Carbon::parse('2026-12-20'),
        ]);

        $response = $this->actingAs($this->user)->get('/acara');

        $response->assertStatus(200);
        $response->assertSee('Acara Saya Pimpin');
        $response->assertSee('Acara Saya Ikuti');
        $response->assertDontSee('Acara Rahasia Orang Lain');
    }
}
