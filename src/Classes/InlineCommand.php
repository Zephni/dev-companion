<?php
namespace WebRegulate\DevCompanion\Classes;

use Illuminate\Console\Command;
use WebRegulate\DevCompanion\DevCompanion;

class InlineCommand extends Command
{
    public $signature = 'dev-companion:inline-command';

    public $description = 'Custom inline command for DevCompanion';

    public function __construct(
        protected string $label,
        protected $callback,
        protected ?array $options = null,
    ) {
        $this->description = $this->label ?? 'Unnamed Inline Command';
    }

    public static function make(
        string $label,
        mixed $callback,
        ?array $options = null,
    ): self {
        return new self($label, $callback, $options);
    }

    public function localCommands(array $commands): void {
        $this->call('dev-companion:local', [
            'commands' => $commands,
        ]);
    }

    public function sshCommand(?string $connectionKey, array $commands = []): void {
        $this->call('dev-companion:ssh', [
            'connection_key' => $connectionKey,
            'commands' => $commands,
        ]);
    }

    public function runRegisteredCommand(string $commandKey): void
    {
        $this->newLine();
        $this->line("<info>Registered Command:</info>   <comment>{$commandKey}</comment>");

        $this->call(DevCompanion::$registeredCommands[$commandKey]);
    }

    public function getDescription(): string
    {
        return $this->label;
    }

    public function handle(): int
    {
        $result = $this->callback ? call_user_func($this->callback, $this) : null;
        if($result !== null) {
            return $result;
        }

        return self::SUCCESS;
    }
}