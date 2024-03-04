<?php

namespace App\Core\Abstracts;

use App\Utilities\Transaction\ExcellTransaction;

abstract class AbstractFactory
{
    private bool $success = false;
    private array $errors = [];
    private string $message = "";

    protected function processReturn(bool $success, array $errors = [], string $message = "") : ExcellTransaction
    {
        $this->success = $success;
        $this->errors = $errors;
        $this->message = $message;

        return new ExcellTransaction($success, $message, $errors);
    }

    protected function addError(string $field, string $internalMessage, string $externalMessage) : self
    {
        $this->errors[$field] = ["internal" => $internalMessage, "external" => $externalMessage];
        return $this;
    }

    protected function addMessage(string $value) : self
    {
        $this->message = $value;
        return $this;
    }

    public function getErrors() : array
    {
        return $this->errors;
    }

    public function getProcessResult() : bool
    {
        return $this->success;
    }

    public function getMessage() : string
    {
        return $this->message;
    }
}