<?php

namespace Entities\Division\Classes;

use App\Core\AppEntity;
use Entities\Division\Models\DivisionModel;

class Division extends AppEntity
{
    public $strEntityName       = "Division";
    public $strDatabaseTable    = "division";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = DivisionModel::class;
    public $strMainModelPrimary = "division_id";
    public $isPrimaryModule = true;
}