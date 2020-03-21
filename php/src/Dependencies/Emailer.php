<?php


namespace UntangledConditionals\Dependencies;


interface Emailer
{
    public function send(string $message);
}