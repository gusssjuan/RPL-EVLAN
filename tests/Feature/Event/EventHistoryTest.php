<?php

namespace Tests\Feature\Event;

use App\Enums\RoleName;
use App\Enums\StatusEvent;
use App\Models\Category;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected User $stranger;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);

        $this->user = User::factory()->create(['nama' => 'Gus Ega']);
        $this->stranger = User::factory()->create(['nama' => 'Orang Asing']);

        $this->category = Category::factory()->create(['nama_kategori' => 'Seminar']);
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $response = $this->get('/riwayat');
        $response->assertRedirect('/login');
    }

    public function test_hanya_menampilkan_acara_milik_atau_diikuti_pengguna(): void
    {
        // 1. Acara dipimpin user (selesai)
        $eventLed = Event::factory()->create([
            'leader_id' => $this->user->id,
            'category_id' => $this->category->id,
            'nama_event' => 'Seminar Teknologi 2026',
            'tanggal' => Carbon::parse('2026-12-12'),
            'status_event' => StatusEvent::Selesai,
            'evaluasi' => 'Seminar sukses besar.',
        ]);

        // 2. Acara diikuti user (perencanaan)
        $eventJoined = Event::factory()->create([
            'leader_id' => $this->stranger->id,
            'category_id' => $this->category->id,
            'nama_event' => 'Gathering Komunitas 2026',
            'tanggal' => Carbon::parse('2026-11-20'),
            'status_event' => StatusEvent::Perencanaan,
        ]);
        EventMember::create([
            'event_id' => $eventJoined->id,
            'user_id' => $this->user->id,
        ]);

        // 3. Acara orang asing (tidak boleh muncul)
        $eventStranger = Event::factory()->create([
            'leader_id' => $this->stranger->id,
            'category_id' => $this->category->id,
            'nama_event' => 'Acara Rahasia Orang Lain',
            'tanggal' => Carbon::parse('2026-10-10'),
            'status_event' => StatusEvent::Selesai,
        ]);

        $response = $this->actingAs($this->user)->get('/riwayat');

        $response->assertStatus(200);
        $response->assertSee('Seminar Teknologi 2026');
        $response->assertSee('Gathering Komunitas 2026');
        $response->assertSee('Seminar sukses besar.');
        $response->assertDontSee('Acara Rahasia Orang Lain');
    }

    public function test_filter_status_bekerja_dengan_benar(): void
    {
        // Selesai
        Event::factory()->create([
            'leader_id' => $this->user->id,
            'category_id' => $this->category->id,
            'nama_event' => 'Acara Selesai A',
            'status_event' => StatusEvent::Selesai,
        ]);

        // Perencanaan
        Event::factory()->create([
            'leader_id' => $this->user->id,
            'category_id' => $this->category->id,
            'nama_event' => 'Acara Masih Berjalan B',
            'status_event' => StatusEvent::Perencanaan,
        ]);

        $resSelesai = $this->actingAs($this->user)->get('/riwayat?status=selesai');
        $resSelesai->assertStatus(200);
        $resSelesai->assertSee('Acara Selesai A');
        $resSelesai->assertDontSee('Acara Masih Berjalan B');

        $resPerencanaan = $this->actingAs($this->user)->get('/riwayat?status=perencanaan');
        $resPerencanaan->assertStatus(200);
        $resPerencanaan->assertSee('Acara Masih Berjalan B');
        $resPerencanaan->assertDontSee('Acara Selesai A');
    }
}
