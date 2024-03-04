<?php

namespace Entities\Modules\Classes;

use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Modules\Models\ModuleAppEndpointModel;

class ModuleAppEndpoints extends AppEntity
{
    public string $strEntityName       = "Modules";
    public $strDatabaseTable    = "module_app_endpoints";
    public $strDatabaseName     = "Modules";
    public $strMainModelName    = ModuleAppEndpointModel::class;
    public $strMainModelPrimary = "module_app_endpoint_id";

    public function syncModule($moduleSyncData, ModuleMainModel $module) : ExcellTransaction
    {

    }
}