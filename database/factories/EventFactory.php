<?php

namespace Database\Factories;

use App\Enums\StatusEvent;
use App\Models\Category;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'leader_id' => User::factory(),
            'category_id' => Category::factory(),
            'nama_event' => 'Acara ' . fake()->words(2, true),
            'tujuan' => fake()->paragraph(),
            'tanggal' => fake()->dateTimeBetween('+1 week', '+6 months')->format('Y-m-d'),
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '17:00',
            'lokasi' => fake()->address(),
            'target_peserta' => 'Mahasiswa dan Umum',
            'jumlah_peserta' => fake()->numberBetween(50, 500),
            'status_event' => StatusEvent::Perencanaan,
            'evaluasi' => null,
            'closed_at' => null,
            'penyelenggara' => fake()->company(),
            'is_publik' => false,
            'pendaftaran_dibuka' => false,
            'batas_pendaftaran' => null,
            'kuota' => null,
        ];
    }

    public function publik(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_publik' => true,
            'pendaftaran_dibuka' => true,
            'batas_pendaftaran' => fake()->dateTimeBetween('now', $attributes['tanggal'] ?? '+1 month')->format('Y-m-d'),
            'kuota' => 150,
        ]);
    }

    public function selesai(): static
    {
        return $this->state(fn () => [
            'status_event' => StatusEvent::Selesai,
            'closed_at' => now(),
            'evaluasi' => 'Acara berjalan dengan lancar.',
        ]);
    }
}

