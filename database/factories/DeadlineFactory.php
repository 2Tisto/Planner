<?php

namespace Database\Factories;

use App\Enums\DeadlineKind;
use App\Models\Deadline;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Deadline>
 */
class DeadlineFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'subject_id' => null,
            'title' => fake()->sentence(3),
            'kind' => DeadlineKind::Exam,
            'happens_at' => now()->addWeek(),
            'location' => null,
            'coefficient' => null,
            'grade' => null,
        ];
    }
}
