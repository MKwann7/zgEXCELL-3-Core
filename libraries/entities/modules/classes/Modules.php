<?php

namespace Entities\Modules\Classes;

use App\Core\AppEntity;
use App\Utilities\Database;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Modules\Models\ModuleMainModel;

class Modules extends AppEntity
{
    public $strEntityName       = "Modules";
    public $strDatabaseTable    = "modules";
    public $strDatabaseName     = "Modules";
    public $strMainModelName    = ModuleMainModel::class;
    public $strMainModelPrimary = "module_id";
    public $isPrimaryModule     = true;

    public function getLatestModulesByNameAsc() : ExcellTransaction
    {
        $strModuleQuery = "SELECT m1.* FROM modules m1 JOIN modules m2 ON m1.module_uuid = m2.module_uuid AND m1.version > m2.version ORDER BY name ASC";

        $objModuleResult = Database::getSimple($strModuleQuery,"module_uuid");

        return $objModuleResult;
    }

    public function syncModule($moduleSyncData, ModuleMainModel $module) : ExcellTransaction
    {

    }
}
