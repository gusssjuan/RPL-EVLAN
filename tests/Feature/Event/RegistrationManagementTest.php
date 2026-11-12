<?php

namespace Tests\Feature\Event;

use App\Enums\JenisNotifikasi;
use App\Enums\StatusPendaftaran;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_leader_dapat_melihat_daftar_pendaftar_di_detail_acara(): void
    {
        $leader = User::factory()->create();
        $event = Event::factory()->publik()->create([
            'leader_id' => $leader->id,
        ]);

        $pendaftar = User::factory()->create(['nama' => 'Budi Santoso']);
        EventRegistration::factory()->create([
            'event_id' => $event->id,
            'user_id' => $pendaftar->id,
            'status' => StatusPendaftaran::Menunggu,
        ]);

        $response = $this->actingAs($leader)->get(route('acara.ringkasan', $event));

        $response->assertStatus(200);
        $response->assertSee('Daftar Pendaftar');
        $response->assertSee('Budi Santoso');
    }

    public function test_leader_dapat_menerima_pendaftaran(): void
    {
        $leader = User::factory()->create();
        $event = Event::factory()->publik()->create([
            'leader_id' => $leader->id,
            'kuota' => 10,
        ]);

        $pendaftar = User::factory()->create();
        $reg = EventRegistration::factory()->create([
            'event_id' => $event->id,
            'user_id' => $pendaftar->id,
            'status' => StatusPendaftaran::Menunggu,
        ]);

        $response = $this->actingAs($leader)->post(route('acara.pendaftar.terima', [$event, $reg]));

        $response->assertSessionHas('sukses');

        $this->assertDatabaseHas('event_registrations', [
            'id' => $reg->id,
            'status' => StatusPendaftaran::Diterima->value,
        ]);

        $this->assertNotNull($reg->fresh()->decided_at);

        // Memastikan notifikasi dibuat untuk pendaftar
        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $pendaftar->id,
            'jenis' => JenisNotifikasi::Pendaftaran->value,
        ]);
    }

    public function test_leader_dapat_menolak_pendaftaran_dengan_alasan(): void
    {
        $leader = User::factory()->create();
        $event = Event::factory()->publik()->create([
            'leader_id' => $leader->id,
        ]);

        $pendaftar = User::factory()->create();
        $reg = EventRegistration::factory()->create([
            'event_id' => $event->id,
            'user_id' => $pendaftar->id,
            'status' => StatusPendaftaran::Menunggu,
        ]);

        $response = $this->actingAs($leader)->post(route('acara.pendaftar.tolak', [$event, $reg]), [
            'alasan_penolakan' => 'Kriteria peserta belum sesuai dengan syarat acara.',
        ]);

        $response->assertSessionHas('sukses');

        $this->assertDatabaseHas('event_registrations', [
            'id' => $reg->id,
            'status' => StatusPendaftaran::Ditolak->value,
            'alasan_penolakan' => 'Kriteria peserta belum sesuai dengan syarat acara.',
        ]);

        $this->assertNotNull($reg->fresh()->decided_at);

        // Memastikan notifikasi penolakan dibuat untuk pendaftar
        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $pendaftar->id,
            'jenis' => JenisNotifikasi::Pendaftaran->value,
        ]);
    }

    public function test_validasi_alasan_penolakan_minimal_5_karakter(): void
    {
        $leader = User::factory()->create();
        $event = Event::factory()->publik()->create([
            'leader_id' => $leader->id,
        ]);

        $pendaftar = User::factory()->create();
        $reg = EventRegistration::factory()->create([
            'event_id' => $event->id,
            'user_id' => $pendaftar->id,
            'status' => StatusPendaftaran::Menunggu,
        ]);

        $response = $this->actingAs($leader)->post(route('acara.pendaftar.tolak', [$event, $reg]), [
            'alasan_penolakan' => 'test', // kurang dari 5 karakter
        ]);

        $response->assertSessionHasErrors('alasan_penolakan');
        $this->assertEquals(StatusPendaftaran::Menunggu, $reg->fresh()->status);
    }

    public function test_pengguna_lain_bukan_leader_tidak_dapat_menerima_atau_menolak(): void
    {
        $leader = User::factory()->create();
        $userLain = User::factory()->create();
        $event = Event::factory()->publik()->create([
            'leader_id' => $leader->id,
        ]);

        $pendaftar = User::factory()->create();
        $reg = EventRegistration::factory()->create([
            'event_id' => $event->id,
            'user_id' => $pendaftar->id,
            'status' => StatusPendaftaran::Menunggu,
        ]);

        $responseTerima = $this->actingAs($userLain)->post(route('acara.pendaftar.terima', [$event, $reg]));
        $responseTerima->assertStatus(403);

        $responseTolak = $this->actingAs($userLain)->post(route('acara.pendaftar.tolak', [$event, $reg]), [
            'alasan_penolakan' => 'Alasan penolakan dari orang lain',
        ]);
        $responseTolak->assertStatus(403);
    }

    public function test_leader_tidak_dapat_menerima_jika_kuota_penuh(): void
    {
        $leader = User::factory()->create();
        $event = Event::factory()->publik()->create([
            'leader_id' => $leader->id,
            'kuota' => 1,
        ]);

        // Sudah ada 1 pendaftaran diterima
        EventRegistration::factory()->create([
            'event_id' => $event->id,
            'status' => StatusPendaftaran::Diterima,
        ]);

        // Pendaftaran kedua yang ingin diterima
        $reg2 = EventRegistration::factory()->create([
            'event_id' => $event->id,
            'status' => StatusPendaftaran::Menunggu,
        ]);

        $response = $this->actingAs($leader)->post(route('acara.pendaftar.terima', [$event, $reg2]));

        $response->assertSessionHas('error');
        $this->assertEquals(StatusPendaftaran::Menunggu, $reg2->fresh()->status);
    }
}
