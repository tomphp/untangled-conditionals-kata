<?php declare(strict_types=1);

namespace UntangledConditionals\Dependencies;

interface Logger
{
    public function info(string $string): void;

    public function error(string $string): void;
}