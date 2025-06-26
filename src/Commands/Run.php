<?php

namespace WebRegulate\DevCompanion\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\select;

class Run extends Command
{
    public $signature = 'dev-companion';

    public $description = 'Run DevCompanion';

    public function handle(): int
    {
        $this->comment('Welcome to DevCompanion!');
        $this->line('----------------------------------------');

        // Get all available commands from config
        $commandsConfig = config('dev-companion.available-commands', []);
        if (empty($commandsConfig)) {
            $this->error('No commands available. Please check your configuration.');

            return self::FAILURE;
        }

        while (true) {
            // Get available commands
            $availableCommands = [];
            foreach ($commandsConfig as $key => $commandClass) {
                if (class_exists($commandClass)) {
                    $commandInstance = new $commandClass;
                    $availableCommands[$key] = $commandInstance->getDescription();
                } else {
                    $this->error("Command class {$commandClass} does not exist.");
                }
            }

            // Choose command to run
            $commandKey = select(
                label: 'Available Commands',
                options: $availableCommands + ['exit' => 'Exit'],
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
