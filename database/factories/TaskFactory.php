<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
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
            'priority' => TaskPriority::B,
            'status' => TaskStatus::Todo,
            'due_on' => null,
            'estimate_minutes' => null,
        ];
    }
}
