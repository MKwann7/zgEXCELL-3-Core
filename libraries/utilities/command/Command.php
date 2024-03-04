<?php

namespace App\Utilities\Command;

abstract class Command
{
    public string $name;
    public string $description;
    public int $tries;

    public CommandCaller $caller;

    public function __construct()
    {

    }

    public function Init(&$caller): void
    {
        $this->caller = $caller;
    }

    public function Run() : void
    {

    }
}