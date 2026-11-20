<?php

declare(strict_types=1);

namespace App\Enums;

enum TaskStatus: string
{
    case Todo = 'todo';
    case Doing = 'doing';
    case Done = 'done';

    public function next(): self
    {
        return match ($this) {
            self::Todo => self::Doing,
            self::Doing => self::Done,
            self::Done => self::Todo,
        };
    }
}
