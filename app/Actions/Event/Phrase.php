<?php

declare(strict_types=1);

namespace App\Actions\Event;

final readonly class Phrase
{
    public function __construct(
        public string $title,
        public string $subject,
        public ?int $weekday,
        public ?string $starts,
        public ?string $ends,
        public ?string $category,
        public string $location,
        public bool $weekly,
    ) {}
}
