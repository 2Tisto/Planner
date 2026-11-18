<?php

declare(strict_types=1);

namespace App\Actions\Category;

use App\Models\Category;
use App\Models\User;

final class SeedDefaultCategories
{
    public function __invoke(User $user): void
    {
        /** @var list<array{name: string, color: string}> $defaults */
        $defaults = config('planner.default_categories', []);
        $existing = Category::query()->forUser($user)->pluck('name')->all();

        foreach ($defaults as $position => $category) {
            if (in_array($category['name'], $existing, true)) {
                continue;
            }

            Category::query()->create([
                'user_id' => $user->id,
                'name' => $category['name'],
                'color' => strtolower($category['color']),
                'icon' => null,
                'position' => $position,
                'is_system' => true,
            ]);
        }
    }
}
