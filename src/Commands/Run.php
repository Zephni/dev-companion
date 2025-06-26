<?php

namespace WebRegulate\DevCompanion\Commands;

use Illuminate\Console\Command;

class Run extends Command
{
    public $signature = 'dev-companion';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
