<?php

declare(strict_types=1);

namespace App\Enums;

enum DeadlineKind: string
{
    case Test = 'ds';
    case Exam = 'exam';
    case Talk = 'expose';
    case Project = 'projet';
    case Report = 'rapport';
}
