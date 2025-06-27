<?php

namespace WebRegulate\DevCompanion\Commands\AvailableCommands;

use Illuminate\Console\Command;
use WebRegulate\DevCompanion\DevCompanion;

use function Laravel\Prompts\select;

class LocalCommand extends Command
{
    public $signature = 'dev-companion:local {commands?*}';

    public $description = 'Access ssh shell';

    public function handle(): int
    {
        // Get passed arguments
        $passedCommands = $this->argument('commands');

        // If no commands are passed, just return success
        if (empty($passedCommands)) {
            $this->comment('No commands provided. Exiting.');
            return self::SUCCESS;
        }

        // Display a message to the user
        $this->comment('Running local commands.');

        foreach ($passedCommands as $command) {
            $this->newLine();
            $this->line("<info>Command:</info>   <comment>{$command}</comment>");

            passthru($command, $exitCode);
            
            if ($exitCode !== 0) {
                $this->error("Command '{$command}' failed with exit code {$exitCode}.");
                break;
            }
        }

        return self::SUCCESS;
    }
}
