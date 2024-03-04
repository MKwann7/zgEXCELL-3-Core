<?php

namespace App\Utilities\Transaction;

use App\Utilities\Excell\ExcellCollection;

class ExcellTransaction
{
    public ExcellTransactionResult $result;
    public ExcellCollection|\stdClass|array $data;
    private array $extraData = [];

    public function __construct(bool $success = true, string $message = "This prcess ran successfully", ExcellCollection|array $data = null, int $count = 0, array $errors = [], string $query = null)
    {
        $this->result = new ExcellTransactionResult();

        $this->result->Success = $success;
        $this->result->Message = $message;

        if (empty($data)) {
            $this->data = new ExcellCollection();
        } else {
            $this->data = $data;
        }

        if (!empty($count) ) {
            $this->result->Count = $count;
        }

        if (!empty($errors) && is_array($errors) && count($errors) > 0) {
            $this->result->Errors = $errors;
        }

        if (!empty($query) ) {
            $this->result->Query = $query;
        }
    }

    public function getData() : ExcellCollection|\stdClass|array
    {
        return $this->data;
    }

    public function setData(ExcellCollection $data) : self
    {
        $this->data = $data;
        return $this;
    }

    public function clearData() : self
    {
        $this->data = new ExcellCollection();
        return $this;
    }

    public function clearExtraData() : self
    {
        $this->extraData = [];
        return $this;
    }

    public function getResult() : ExcellTransactionResult
    {
        return $this->result;
    }

    public function setExtraData($label, $value) : self
    {
        $this->extraData[$label] = $value;
        return $this;
    }

    public function getExtraData($label)
    {
        return $this->extraData[$label] ?? null;
    }
}