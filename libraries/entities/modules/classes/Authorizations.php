<?php

namespace Entities\Modules\Classes;

use App\Core\AppEntity;
use Entities\Modules\Models\AuthorizationModel;

class Authorizations extends AppEntity
{
    public string $strEntityName       = "Modules";
    public $strDatabaseTable    = "authorizations";
    public $strDatabaseName     = "Modules";
    public $strMainModelName    = AuthorizationModel::class;
    public $strMainModelPrimary = "authorization_id";

    public function syncModule($moduleSyncData, ModuleMainModel $module) : ExcellTransaction
    {

    }
}
