<?php

namespace App\entities\packages\classes;

use App\Core\AppEntity;
use App\entities\packages\models\PackageVariationModel;
use App\Utilities\Transaction\ExcellTransaction;

class PackageVariations extends AppEntity
{
    public string $strEntityName       = "packages";
    public $strDatabaseTable    = "package_variation";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = PackageVariationModel::class;
    public $strMainModelPrimary = "package_variation_id";
    public $isPrimaryModule     = true;

    public function GetAllActiveProducts() : ExcellTransaction
    {
        return $this->getFks()->getWhere("status","=","Active");
    }
}
