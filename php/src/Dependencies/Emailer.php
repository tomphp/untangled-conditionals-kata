<?php declare(strict_types=1);

namespace UntangledConditionals\Dependencies;

interface Emailer
{
    public function send(string $message);
}