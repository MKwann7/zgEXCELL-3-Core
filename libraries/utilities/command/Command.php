<?php

namespace App\Utilities\Command;

abstract class Command
{
    public $name;
    public $description;
    public $tries;
    /** @var CommandCaller $caller */
    public $caller;

    public function __construct()
    {

    }

    public function Init(&$caller)
    {
        $this->caller = $caller;
    }

    public function Run()
    {

    }
}