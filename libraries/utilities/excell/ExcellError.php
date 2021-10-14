<?php

namespace App\Utilities\Excell;

class ExcellError
{
    public $Message;
    public $Type;

    public function __construct($Message = null, $Type = null)
    {
        $this->Message = $Message;
        $this->Type = $Type;
    }
}