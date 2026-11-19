<?php

declare(strict_types=1);

namespace App\Actions\Event;

use Illuminate\Support\Carbon;

final readonly class Occurrence
{
    public function __construct(
        public int $id,
        public string $date,
        public Carbon $starts,
        public Carbon $ends,
        public string $title,
        public string $meta,
        public ?string $color,
        public bool $recurring,
    ) {}
}
