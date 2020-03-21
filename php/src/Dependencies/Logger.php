<?php


namespace UntangledConditionals\Dependencies;


interface Logger
{
    public function info(string $string);

    public function error(string $string);
}