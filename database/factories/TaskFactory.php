<?php

namespace Database\Factories;

use App\Enums\StatusTugas;
use App\Models\Division;
use App\Models\Event;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'division_id' => Division::factory(),
            'penerima_id' => User::factory(),
            'created_by' => User::factory(),
            'judul' => fake()->sentence(4),
            'deskripsi' => fake()->paragraph(),
            'deadline' => fake()->dateTimeBetween('+1 day', '+1 month')->format('Y-m-d'),
            'status' => StatusTugas::BelumDikerjakan,
            'completed_at' => null,
        ];
    }

    public function terlewat(): static
    {
        return $this->state(fn () => [
            'deadline' => Carbon::yesterday()->format('Y-m-d'),
            'status' => StatusTugas::BelumDikerjakan,
            'completed_at' => null,
        ]);
    }

    public function sedangDikerjakan(): static
    {
        return $this->state(fn () => [
            'status' => StatusTugas::SedangDikerjakan,
        ]);
    }

    public function selesai(): static
    {
        return $this->state(fn () => [
            'status' => StatusTugas::SudahDiselesaikan,
            'completed_at' => now(),
        ]);
    }
}
