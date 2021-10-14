<?php

namespace Entities\Modules\Classes;

use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Modules\Models\ModuleAppWidgetModel;

class ModuleAppWidgets extends AppEntity
{
    public $strEntityName       = "Modules";
    public $strDatabaseTable    = "module_app_widgets";
    public $strDatabaseName     = "Modules";
    public $strMainModelName    = ModuleAppWidgetModel::class;
    public $strMainModelPrimary = "module_app_widget_id";

    public function syncModule($moduleSyncData, ModuleMainModel $module) : ExcellTransaction
    {

    }
}