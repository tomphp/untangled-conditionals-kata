<?php


namespace UntangledConditionals\Dependencies;


interface Config
{
    public function sendEmailSummary(): bool;
}