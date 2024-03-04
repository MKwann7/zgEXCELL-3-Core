<?php

namespace Entities\Dashboard\Classes;

use App\Core\AppEntity;

class Dashboard extends AppEntity
{
    public string $strEntityName       = "Dashboard";
    public $strDatabaseTable    = "dashboard";
    public $strMainModelName    = "Dashboard";
    public $strMainModelPrimary = "dashboard_id";
    public $strDatabaseName     = "Main";
    public $isPrimaryModule     = true;
}