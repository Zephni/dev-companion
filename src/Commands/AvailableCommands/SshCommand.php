<?php

namespace WebRegulate\DevCompanion\Commands\AvailableCommands;

use Illuminate\Console\Command;

class SshCommand extends Command
{
    public $signature = 'ssh';

    public $description = 'Access ssh shell';

    public function handle(): int
    {
        $this->comment('TODO: Access SSH Shell.');

        return self::SUCCESS;
    }
}
