<?php

namespace Entities\Marketplace\Classes;

use App\Core\AppEntity;
use Entities\Marketplace\Models\MarketPlaceDefaultModel;

class MarketPlaceDefaults extends AppEntity
{
    public string $strEntityName       = "Marketplace";
    public $strDatabaseTable    = "marketplace_default";
    public $strMainModelName    = MarketPlaceDefaultModel::class;
    public $strMainModelPrimary = "marketplace_default_id";
    public $strDatabaseName     = "Apps";
}
