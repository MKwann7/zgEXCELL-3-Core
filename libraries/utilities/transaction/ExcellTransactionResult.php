<?php

namespace App\Utilities\Transaction;

class ExcellTransactionResult
{
    public $Count = 0;
    public $Success = false;
    public $Message = "";
    public $Depth = 0;
    public $Total = 0;
    public $Query = "";
    public $Trace = "";
    public $Errors = [];
}