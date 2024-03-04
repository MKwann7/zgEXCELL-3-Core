<?php

namespace Entities\Packages\Classes;

use App\Core\AppEntity;
use App\entities\packages\classes\PackageVariations;
use App\entities\packages\models\PackageVariationModel;
use App\Utilities\Excell\ExcellCollection;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Packages\Models\PackageLineModel;
use Entities\Packages\Models\PackageModel;
use Entities\Products\Classes\Products;

class Packages extends AppEntity
{
    public string $strEntityName       = "packages";
    public $strDatabaseTable    = "package";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = PackageModel::class;
    public $strMainModelPrimary = "package_id";
    public $isPrimaryModule     = true;

    public function GetAllActiveProducts() : ExcellTransaction
    {
        return $this->getFks()->getWhere("status","=","Active");
    }

    public static function getFullPackagesByVariationIds(array $arPackageVariationIds) : ExcellTransaction
    {
        $packageVariationResults = (new PackageVariations())->getWhereIn("package_variation_id", $arPackageVariationIds);

        if ($packageVariationResults->getResult()->Count == 0) {
            return $packageVariationResults;
        }

        $packageIds = $packageVariationResults->getData()->FieldsToArray(["package_id"]);
        $packageResults = (new static)->getWhereIn("package_id", $packageIds);
        $packageLineResults = (new PackageLines())->getWhereIn("package_variation_id", $arPackageVariationIds);
        $packageVariationResults->getData()->HydrateChildModelData("lines", ["package_variation_id" => "package_variation_id"], $packageLineResults->data, false);
        $packageVariationResults->getData()->HydrateChildModelData("package", ["package_id" => "package_id"], $packageResults->data, true);

        // TODO - Change this to be a dependency so it can be unit tested.
        $productResult = Products::getProductsByPackageIds($packageIds);

        $packageVariationResults->getData()->Foreach(function (PackageVariationModel $currPackage) use ($productResult)
        {
            if (empty($currPackage->lines) || !is_a($currPackage->lines, ExcellCollection::class)) { return null; }

            $currPackage->lines->Foreach(function (PackageLineModel $currPackageLine) use ($productResult)
            {
                if ($currPackageLine->product_entity !== "product") { return null; }

                $product = $productResult->getData()->FindEntityByValue("product_id", $currPackageLine->product_entity_id);

                if ($product === null) { return null; }

                $currPackageLine->AddUnvalidatedValue("product", $product);

                return $currPackageLine;
            });

            return $currPackage;
        });

        return $packageVariationResults;
    }
}
