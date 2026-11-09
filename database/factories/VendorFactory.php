<?php

namespace Database\Factories;

use App\Enums\KategoriVendor;
use App\Enums\StatusVerifikasi;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->vendor(),
            'nama_vendor' => fake()->company(),
            'kategori_vendor' => fake()->randomElement([
                KategoriVendor::Catering->value,
                KategoriVendor::Dekorasi->value,
                KategoriVendor::Dokumentasi->value,
                KategoriVendor::SoundSystem->value,
                KategoriVendor::PerlengkapanAcara->value,
            ]),
            'alamat' => fake()->address(),
            'kota' => fake()->city(),
            'kontak' => '08'.fake()->numerify('##########'),
            'deskripsi' => fake()->paragraph(),
            'status_verifikasi' => StatusVerifikasi::Menunggu,
            'alasan_penolakan' => null,
            'verified_at' => null,
        ];
    }

    public function terverifikasi(): static
    {
        return $this->state(fn () => [
            'status_verifikasi' => StatusVerifikasi::Terverifikasi,
            'verified_at' => now(),
        ]);
    }

    public function ditolak(): static
    {
        return $this->state(fn () => [
            'status_verifikasi' => StatusVerifikasi::Ditolak,
            'alasan_penolakan' => 'Dokumen usaha tidak lengkap.',
            'verified_at' => null,
        ]);
    }
}
