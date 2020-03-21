<?php declare(strict_types=1);

namespace UntangledConditionals\Dependencies;

interface Config
{
    public function sendEmailSummary(): bool;
}