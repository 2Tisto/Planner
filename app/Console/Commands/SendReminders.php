<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Planner\SendDueReminders;
use Illuminate\Console\Command;

class SendReminders extends Command
{
    protected $signature = 'planner:remind';

    protected $description = 'Email what arrives during the next day.';

    public function handle(SendDueReminders $send): int
    {
        $count = $send();
        $this->info((string) $count);

        return self::SUCCESS;
    }
}
