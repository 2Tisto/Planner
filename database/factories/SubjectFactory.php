<?php

namespace Database\Factories;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->unique()->bothify('Matiere-####'),
            'code' => null,
            'teacher' => null,
            'default_room' => null,
            'coefficient' => null,
            'color' => '#bfdbfe',
        ];
    }
}
