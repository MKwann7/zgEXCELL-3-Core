<?php

namespace Entities\Marketplace\Classes;

use App\Core\AppEntity;
use Entities\Marketplace\Models\MarketplacePackageModel;

class MarketPlacePackages extends AppEntity
{
    public string $strEntityName       = "Marketplace";
    public $strDatabaseTable    = "marketplace_package";
    public $strMainModelName    = MarketplacePackageModel::class;
    public $strMainModelPrimary = "marketplace_package_id";
    public $strDatabaseName     = "Apps";
}
