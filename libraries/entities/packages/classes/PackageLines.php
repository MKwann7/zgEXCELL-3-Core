<?php

namespace Entities\Packages\Classes;

use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Packages\Models\PackageLineModel;

class PackageLines extends AppEntity
{
    public $strEntityName       = "packages";
    public $strDatabaseTable    = "package_line";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = PackageLineModel::class;
    public $strMainModelPrimary = "package_line_id";

    public function GetAllActiveProductLines() : ExcellTransaction
    {
        return $this->getFks()->getWhere("status","=","Active");
    }
}
