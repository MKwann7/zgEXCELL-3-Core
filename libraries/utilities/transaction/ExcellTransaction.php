<?php

namespace App\Utilities\Transaction;

use App\Utilities\Excell\ExcellCollection;

class ExcellTransaction
{
    /** @var ExcellTransactionResult  */
    public $Result;
    /** @var $Data ExcellCollection */
    public $Data;

    public function __construct($success = true, $message = "This prcess ran successfully", $data = null, $count = 0, $errors = [], $query = null)
    {
        $this->Result = new ExcellTransactionResult();

        $this->Result->Success = $success;
        $this->Result->Message = $message;

        if (empty($data))
        {
            $this->Data = new ExcellCollection();
        }
        else
        {
            $this->Data = $data;
        }

        if (!empty($count) )
        {
            $this->Result->Count = $count;
        }

        if (!empty($errors) && is_array($errors) && count($errors) > 0)
        {
            $this->Result->Errors = $errors;
        }

        if (!empty($query) )
        {
            $this->Result->Query = $query;
        }
    }
}