<?php

namespace Entities\Marketplace\Classes;

use App\Core\AppEntity;
use Entities\Marketplace\Models\MarketplaceTemplateModel;

class MarketPlaceTemplates extends AppEntity
{
    public string $strEntityName       = "Marketplace";
    public $strDatabaseTable    = "marketplace_template";
    public $strMainModelName    = MarketplaceTemplateModel::class;
    public $strMainModelPrimary = "marketplace_template_id";
    public $strDatabaseName     = "Apps";
}