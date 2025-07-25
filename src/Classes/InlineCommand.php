<?php
namespace WebRegulate\DevCompanion\Classes;

class InlineCommand extends BaseCommand
{
    public $signature = 'dev-companion:inline-command';

    public $description = 'Custom inline command for DevCompanion';

    public function __construct(
        protected string $label,
        protected $callback,
        protected ?array $options = null,
    ) {
        $this->description = $this->label;
    }

    public static function make(
        string $label,
        mixed $callback,
        ?array $options = null,
    ): self {
        return new self($label, $callback, $options);
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