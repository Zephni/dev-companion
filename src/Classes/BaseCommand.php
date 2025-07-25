<?php
namespace WebRegulate\DevCompanion\Classes;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use function Laravel\Prompts\select;
use WebRegulate\DevCompanion\DevCompanion;

class BaseCommand extends Command
{
    public function displayCommands(bool $bool = true): static
    {
        DevCompanion::$displayCommands = $bool;
        
        return $this;
    }

    public function localCommand(array $commands): static
    {
        $this->call('dev-companion:local', [
            'commands' => $commands,
        ]);

        return $this;
    }

    public function sshCommand(?string $connectionKey, array $commands = []): static
    {
        $this->call('dev-companion:ssh', [
            'connection_key' => $connectionKey,
            'commands' => $commands,
        ]);

        return $this;
    }

    public function next(callable $callback): static
    {
        call_user_func($callback, $this);

        return $this;
    }

    public function registeredCommand(string $commandKey, array $arguments): static
    {
        $this->newLine();
        $this->line("<info>Registered Command:</info>   <comment>{$commandKey}</comment>");

        $this->call(DevCompanion::$registeredCommands[$commandKey], $arguments);

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description ?? 'Unnamed Command';
    }

    public function selectServerKey(?string $label = 'Select a server')
    {
        return $this->select($label, DevCompanion::getSshConnectionKeys());
    }

    /**
     * Wrapper for Laravel Prompts select function.
     *
     * @param  array<int|string, string>|Collection<int|string, string>  $options
     * @param  true|string  $required
     */
    public function select(string $label, array|Collection $options, int|string|null $default = null, int $scroll = 5, mixed $validate = null, string $hint = '', bool|string $required = true, ?Closure $transform = null): int|string
    {
        return select($label, $options, $default, $scroll, $validate, $hint, $required, $transform);
    }
}