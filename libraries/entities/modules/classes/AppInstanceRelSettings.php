<?php

namespace Entities\Modules\Classes;

use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Models\AppInstancesModel;
use Entities\Modules\Models\AppInstanceRelSettingModel;

class AppInstanceRelSettings extends AppEntity
{
    public string $strEntityName    = "Modules";
    public $strDatabaseTable        = "app_instance_rel_setting";
    public $strDatabaseName         = "Main";
    public $strMainModelName        = AppInstanceRelSettingModel::class;
    public $strMainModelPrimary     = "app_instance_rel_setting_id";

    public function getByInstanceRelId($cardPageRelId) : ExcellTransaction
    {
        return $this->getWhere(["app_instance_rel_id" => $cardPageRelId]);
    }

    public function getByInstanceRelIds($cardPageRelIds) : ExcellTransaction
    {
        return $this->getWhereIn("app_instance_rel_id", $cardPageRelIds);
    }
}
