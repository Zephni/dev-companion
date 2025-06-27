<?php

namespace WebRegulate\DevCompanion\Commands;

use Illuminate\Console\Command;

use Symfony\Component\Console\Input\InputDefinition;
use function Laravel\Prompts\select;
use WebRegulate\DevCompanion\Classes\InlineCommand;
use WebRegulate\DevCompanion\DevCompanion;

class RunCommand extends Command
{
    public $signature = 'dev-companion';

    public $description = 'Run DevCompanion';

    public function handle(): int
    {
        $this->line('');
        $this->comment('Welcome to DevCompanion!');
        $this->line('----------------------------------------');

        // Get commands from config
        $commandsConfig = config('dev-companion.commands', []);
        if (empty($commandsConfig)) {
            $this->error('No commands available. Please check your configuration.');

            return self::FAILURE;
        }

        while (true) {
            // Get commands
            $commands = DevCompanion::getCommands($commandsConfig);

            // Choose command to run
            $commandKey = select(
                label: 'Available Commands',
                options: $commands + ['exit' => 'Exit'],
                scroll: 10,
            );

            // Special case for exit command
            if ($commandKey === 'exit') {
                $this->info('Exiting DevCompanion. Goodbye!');
                break;
            }

            // Find the command class by index
            $commandClass = $commandsConfig[$commandKey] ?? null;

            // If command class does not exist, show error and continue
            if (! $commandClass) {
                $this->error('Command not found. Please try again.');

                continue;
            }

            // Run artisan command
            $this->call($commandClass);
        }

        return self::SUCCESS;
    }
}
