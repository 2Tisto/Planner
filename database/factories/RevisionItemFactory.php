<?php

namespace Database\Factories;

use App\Enums\RevisionStage;
use App\Models\RevisionItem;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RevisionItem>
 */
class RevisionItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'subject_id' => Subject::factory(),
            'title' => fake()->sentence(3),
            'stage' => RevisionStage::Reading,
            'confidence' => 1,
        ];
    }
}
