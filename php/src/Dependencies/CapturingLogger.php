<?php declare(strict_types=1);

namespace UntangledConditionals\Dependencies;

final class CapturingLogger implements Logger
{
    private $messages = [];

    public function info(string $string): void
    {
        array_push($this->messages, "INFO: $string");
    }

    public function error(string $string): void
    {
        array_push($this->messages, "ERROR: $string");
    }

    /**
     * @return string[]
     */
    public function getLoggedLines(): array
    {
        return $this->messages;
    }
}