<?php

namespace App\Utilities\Excell;

class ExcellRelationship
{
    public $Database;
    public $Table;
    public $Field;
    public $Label;
    public $LocalKey;
    public $ForeignKey;
    public $AdditionalBindings;

    public function __construct()
    {
    }
}