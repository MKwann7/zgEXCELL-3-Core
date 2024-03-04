<?php

namespace Entities\Marketplace\Classes;

use App\Core\AppEntity;
use App\Utilities\Database;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Marketplace\Models\MarketPlaceColumnModel;

class MarketPlaceColumns extends AppEntity
{
    public string $strEntityName       = "Marketplace";
    public $strDatabaseTable    = "marketplace_column";
    public $strMainModelName    = MarketPlaceColumnModel::class;
    public $strMainModelPrimary = "marketplace_column_id";
    public $strDatabaseName     = "Apps";

    public function getColumnsByMarketplaceId($marketplaceId) : ExcellTransaction
    {
        $recordQuery = "SELECT mpc.* FROM ezdigital_v2_apps.marketplace_column mpc LEFT JOIN ezdigital_v2_apps.marketplace_template mpt ON mpt.marketplace_template_id = mpc.template_id LEFT JOIN ezdigital_v2_apps.marketplace mp ON mp.template_id = mpt.marketplace_template_id WHERE mp.marketplace_id = '" . $marketplaceId . "' ORDER BY mpc.order;";
        $marketplaceColumnResult = Database::getSimple($recordQuery);

        if ($marketplaceColumnResult->Result->Count === 0)
        {
            return $marketplaceColumnResult;
        }

        $marketplaceColumnResult->Data->HydrateModelData(MarketPlaceColumnModel::class);

        return $marketplaceColumnResult;
    }


}