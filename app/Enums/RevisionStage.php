<?php

declare(strict_types=1);

namespace App\Enums;

enum RevisionStage: string
{
    case Reading = 'reading';
    case Notes = 'notes';
    case Exercises = 'exercises';
    case Final = 'final';

    public function next(): self
    {
        return match ($this) {
            self::Reading => self::Notes,
            self::Notes => self::Exercises,
            self::Exercises => self::Final,
            self::Final => self::Reading,
        };
    }
}
