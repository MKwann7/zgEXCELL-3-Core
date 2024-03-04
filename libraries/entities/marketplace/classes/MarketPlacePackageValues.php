<?php

namespace Entities\Marketplace\Classes;

use App\Core\AppEntity;
use Entities\Marketplace\Models\MarketplacePackageValueModel;

class MarketPlacePackageValues extends AppEntity
{
    public string $strEntityName       = "Marketplace";
    public $strDatabaseTable    = "marketplace_package_value";
    public $strMainModelName    = MarketplacePackageValueModel::class;
    public $strMainModelPrimary = "marketplace_package_value_id";
    public $strDatabaseName     = "Apps";
}
