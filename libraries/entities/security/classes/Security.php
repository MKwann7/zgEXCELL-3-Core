<?php

namespace Entities\Security\Classes;

use App\Core\AppEntity;
use Entities\Security\Models\SecurityModel;

class Security extends AppEntity
{
    public $strEntityName       = "security";
    public $strDatabaseTable    = "security";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = SecurityModel::class;
    public $strMainModelPrimary = "security_id";
    public $isPrimaryModule     = true;
}