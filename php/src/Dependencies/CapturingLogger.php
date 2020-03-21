<?php

namespace UntangledConditionals\Dependencies;

final class CapturingLogger implements Logger
{
    private $messages = [];

    public function info(string $string)
    {
        array_push($this->messages, "INFO: $string");
    }

    public function error(string $string)
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