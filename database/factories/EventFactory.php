<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $starts = now()->utc()->setTime(14, 0);

        return [
            'user_id' => User::factory(),
            'category_id' => null,
            'subject_id' => null,
            'title' => fake()->sentence(3),
            'location' => null,
            'notes' => null,
            'starts_at' => $starts,
            'ends_at' => $starts->copy()->addHour(),
            'timezone' => 'Africa/Porto-Novo',
            'is_all_day' => false,
            'recurrence_frequency' => null,
            'recurrence_interval' => 1,
            'recurrence_until' => null,
        ];
    }
}
