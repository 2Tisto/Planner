<?php

declare(strict_types=1);

namespace App\Actions\Category;

use App\Models\Category;

final class UpdateCategory
{
    public function __invoke(Category $category, string $name, string $color): void
    {
        $category->fill([
            'name' => $name,
            'color' => strtolower($color),
        ])->save();
    }
}
