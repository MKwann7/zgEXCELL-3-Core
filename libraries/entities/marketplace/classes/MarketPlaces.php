<?php

namespace Entities\Marketplace\Classes;

use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Marketplace\Models\MarketplaceModel;

class MarketPlaces extends AppEntity
{
    public string $strEntityName       = "Marketplace";
    public $strDatabaseTable    = "marketplace";
    public $strMainModelName    = MarketplaceModel::class;
    public $strMainModelPrimary = "marketplace_id";
    public $strDatabaseName     = "Apps";

    public function getFullRecordByUuid(string $uuid) : ExcellTransaction
    {
        $objDirectoryResult = $this->getWhere(["instance_uuid" => $uuid]);
        return $this->buildFullDirectory($objDirectoryResult);
    }

    public function getFullRecordById(int $id) : ExcellTransaction
    {
        $objDirectoryResult = $this->getById($id);
        return $this->buildFullDirectory($objDirectoryResult);
    }

    protected function buildFullDirectory(ExcellTransaction $marketplaceResult) : ExcellTransaction
    {
        if ($marketplaceResult->Result->Count !== 1)
        {
            return $marketplaceResult;
        }

        $colMarketplaceTemplate = (new MarketPlaceTemplates())->getById($marketplaceResult->Data->First()->template_id)->Data;
        $marketplaceResult->Data->HydrateChildModelData("template", ["marketplace_template_id" => "template_id"], $colMarketplaceTemplate, true);

        $marketplaceDefaultResult = (new MarketPlaceDefaults())->getWhere(["marketplace_id" => $marketplaceResult->Data->First()->marketplace_id]);
        $marketplaceResult->Data->HydrateChildModelData("defaults", ["marketplace_id" => "marketplace_id"], $marketplaceDefaultResult->Data, false);

        return $marketplaceResult;
    }
}