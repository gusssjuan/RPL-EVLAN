<?php

namespace Tests\Feature\Discovery;

use App\Enums\StatusPendaftaran;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengguna_dapat_melihat_detail_acara_publik(): void
    {
        $event = Event::factory()->publik()->create([
            'nama_event' => 'Seminar Terbuka AI',
        ]);

        $response = $this->get(route('cari-acara.show', $event));

        $response->assertStatus(200);
        $response->assertSee('Seminar Terbuka AI');
        $response->assertSee('Daftar Sekarang');
    }

    public function test_detail_acara_non_publik_mengembalikan_404(): void
    {
        $event = Event::factory()->create([
            'is_publik' => false,
        ]);

        $response = $this->get(route('cari-acara.show', $event));

        $response->assertStatus(404);
    }

    public function test_tamu_diarahkan_ke_login_saat_mendaftar(): void
    {
        $event = Event::factory()->publik()->create();

        $response = $this->post(route('cari-acara.daftar', $event), [
            'no_telepon' => '081234567890',
            'persetujuan_data' => '1',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_pengguna_dapat_mendaftar_ke_acara_publik(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->publik()->create([
            'kuota' => 10,
            'pendaftaran_dibuka' => true,
            'batas_pendaftaran' => now()->addDays(5)->format('Y-m-d'),
        ]);

        $response = $this->actingAs($user)->post(route('cari-acara.daftar', $event), [
            'no_telepon' => '081234567890',
            'catatan' => 'Tertarik dengan topik materi',
            'persetujuan_data' => '1',
        ]);

        $response->assertRedirect(route('cari-acara.show', $event));
        $response->assertSessionHas('sukses');

        $this->assertDatabaseHas('event_registrations', [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'no_telepon' => '081234567890',
            'catatan' => 'Tertarik dengan topik materi',
            'status' => StatusPendaftaran::Menunggu->value,
        ]);
    }

    public function test_validasi_nomor_telepon_minimal_10_karakter_jika_diisi(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->publik()->create();

        $response = $this->actingAs($user)->post(route('cari-acara.daftar', $event), [
            'no_telepon' => '08123',
            'persetujuan_data' => '1',
        ]);

        $response->assertSessionHasErrors('no_telepon');
    }

    public function test_validasi_persetujuan_data_wajib_dicentang(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->publik()->create();

        $response = $this->actingAs($user)->post(route('cari-acara.daftar', $event), [
            'no_telepon' => '081234567890',
            // persetujuan_data tidak dicentang
        ]);

        $response->assertSessionHasErrors('persetujuan_data');
    }

    public function test_pengguna_tidak_dapat_mendaftar_dua_kali_pada_acara_yang_sama(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->publik()->create();

        EventRegistration::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => StatusPendaftaran::Menunggu,
        ]);

        $response = $this->actingAs($user)->post(route('cari-acara.daftar', $event), [
            'no_telepon' => '081234567890',
            'persetujuan_data' => '1',
        ]);

        $response->assertSessionHas('peringatan');
        $this->assertEquals(1, EventRegistration::where('event_id', $event->id)->where('user_id', $user->id)->count());
    }

    public function test_leader_tidak_dapat_mendaftar_pada_acaranya_sendiri(): void
    {
        $leader = User::factory()->create();
        $event = Event::factory()->publik()->create([
            'leader_id' => $leader->id,
        ]);

        $response = $this->actingAs($leader)->post(route('cari-acara.daftar', $event), [
            'no_telepon' => '081234567890',
            'persetujuan_data' => '1',
        ]);

        $response->assertSessionHas('peringatan');
        $this->assertDatabaseMissing('event_registrations', [
            'event_id' => $event->id,
            'user_id' => $leader->id,
        ]);
    }

    public function test_pendaftaran_ditolak_jika_kuota_penuh(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->publik()->create([
            'kuota' => 1,
            'pendaftaran_dibuka' => true,
        ]);

        // Buat 1 pendaftaran yang sudah diterima agar kuota penuh
        EventRegistration::factory()->create([
            'event_id' => $event->id,
            'status' => StatusPendaftaran::Diterima,
        ]);

        $response = $this->actingAs($user)->post(route('cari-acara.daftar', $event), [
            'no_telepon' => '081234567890',
            'persetujuan_data' => '1',
        ]);

        $response->assertSessionHas('peringatan');
        $this->assertDatabaseMissing('event_registrations', [
            'event_id' => $event->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_pendaftaran_ditolak_jika_batas_pendaftaran_telah_lewat(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->publik()->create([
            'batas_pendaftaran' => now()->subDay()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($user)->post(route('cari-acara.daftar', $event), [
            'no_telepon' => '081234567890',
            'persetujuan_data' => '1',
        ]);

        $response->assertSessionHas('peringatan');
        $this->assertDatabaseMissing('event_registrations', [
            'event_id' => $event->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_pengguna_dapat_membatalkan_pendaftaran_yang_masih_menunggu(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->publik()->create();

        EventRegistration::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => StatusPendaftaran::Menunggu,
        ]);

        $response = $this->actingAs($user)->delete(route('cari-acara.daftar.batal', $event));

        $response->assertRedirect(route('cari-acara.show', $event));
        $response->assertSessionHas('sukses');

        $this->assertDatabaseMissing('event_registrations', [
            'event_id' => $event->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_pengguna_tidak_dapat_membatalkan_pendaftaran_yang_sudah_diterima(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->publik()->create();

        EventRegistration::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => StatusPendaftaran::Diterima,
        ]);

        $response = $this->actingAs($user)->delete(route('cari-acara.daftar.batal', $event));

        $response->assertSessionHas('peringatan');
        $this->assertDatabaseHas('event_registrations', [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => StatusPendaftaran::Diterima->value,
        ]);
    }
}
