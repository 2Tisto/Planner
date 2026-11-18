<?php

declare(strict_types=1);

namespace App\Actions\Category;

use App\Models\Category;

final class DeleteCategory
{
    public function __invoke(Category $category): void
    {
        $category->delete();
    }
}
