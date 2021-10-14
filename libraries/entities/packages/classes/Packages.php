<?php

namespace Entities\Packages\Classes;

use App\Core\AppEntity;
use App\Utilities\Excell\ExcellCollection;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Packages\Models\PackageLineModel;
use Entities\Packages\Models\PackageModel;
use Entities\Products\Classes\Products;

class Packages extends AppEntity
{
    public $strEntityName       = "packages";
    public $strDatabaseTable    = "package";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = PackageModel::class;
    public $strMainModelPrimary = "package_id";
    public $isPrimaryModule     = true;

    public function GetAllActiveProducts() : ExcellTransaction
    {
        return $this->getFks()->getWhere("status","=","Active");
    }

    public static function getFullPackagesByIds(string $field, array $arPackageIds) : ExcellTransaction
    {
        $packageResults = (new static)->getWhereIn($field, $arPackageIds);
        $packageLineResults = (new PackageLines())->getWhereIn("package_id", $arPackageIds);
        $packageResults->Data->HydrateChildModelData("lines", ["package_id" => "package_id"], $packageLineResults->Data, false);

        $productResult = Products::getProductsByPackageIds($arPackageIds);

        $packageResults->Data->Foreach(function (PackageModel $currPackage) use ($productResult)
        {
            if (empty($currPackage->lines) || !is_a($currPackage->lines, ExcellCollection::class)) { return; }

            $currPackage->lines->Foreach(function (PackageLineModel $currPackageLine) use ($productResult)
            {
                if ($currPackageLine->product_entity !== "product") { return; }

                $product = $productResult->Data->FindEntityByValue("product_id", $currPackageLine->product_entity_id);

                if ($product === null) { return; }

                $currPackageLine->AddUnvalidatedValue("product", $product);

                return $currPackageLine;
            });

            return $currPackage;
        });

        return $packageResults;
    }
}
