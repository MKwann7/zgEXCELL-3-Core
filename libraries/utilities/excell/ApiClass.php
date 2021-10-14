<?php

namespace App\Utilities\Excell;

use App\Utilities\Transaction\ExcellTransaction;

interface ApiClass
{
    public function GetById($strGuid) : ExcellTransaction;

    public function GetAll() : ExcellTransaction;

    public function GetWhere() : ExcellTransaction;

    public function Update(ExcellModel $objModel) : ExcellTransaction;

    public function CreateNew(ExcellModel $objModel) : ExcellTransaction;

    public function Delete(ExcellModel $objModel) : ExcellTransaction;
}