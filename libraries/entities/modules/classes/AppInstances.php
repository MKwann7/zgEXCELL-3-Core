<?php

namespace Entities\Modules\Classes;

use App\Core\AppEntity;
use Entities\Modules\Models\AppInstanceModel;

class AppInstances extends AppEntity
{
    public $strEntityName       = "Modules";
    public $strDatabaseTable    = "app_instance";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = AppInstanceModel::class;
    public $strMainModelPrimary = "app_instance_id";
}
