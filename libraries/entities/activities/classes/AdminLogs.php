<?php

namespace Entities\Activities\Classes;

use App\Core\AppEntity;
use Entities\Activities\Models\AdminLogModel;

class AdminLogs extends AppEntity
{
    public $strEntityName       = "Activities";
    public $strDatabaseName     = "Activity";
    public $strDatabaseTable    = "log_admin";
    public $strMainModelName    = AdminLogModel::class;
    public $strMainModelPrimary = "log_admin_id";
}