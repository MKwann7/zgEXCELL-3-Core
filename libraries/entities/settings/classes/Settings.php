<?php

namespace Entities\Settings\Classes;

use App\Core\AppEntity;

class Settings extends AppEntity
{
    public $strEntityName       = "settings";
    public $strDatabaseTable    = "settings";
    public $strDatabaseName     = "Main";
    public $strMainModelPrimary = "setting_id";
    public $isPrimaryModule     = true;
}