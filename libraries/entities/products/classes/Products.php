<?php

namespace Entities\Products\Classes;

use App\Core\AppController;
use App\Core\AppEntity;
use App\Utilities\Database;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Products\Models\ProductModel;

class Products extends AppEntity
{
    public string $strEntityName       = "products";
    public $strDatabaseTable    = "product";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = ProductModel::class;
    public $strMainModelPrimary = "product_id";
    public $isPrimaryModule     = true;

    public function GetAllActiveProducts()
    {
        return $this->getFks()->getWhere("status","=","Active");
    }

    public static function getProductsByPackageIds(array $arPackageIds) : ExcellTransaction
    {
        $whereClause = "SELECT pr.*, ps.package_id
            FROM product pr 
            LEFT JOIN package_line pl ON pl.product_entity_id = pr.product_id AND pl.product_entity = 'product'
            LEFT JOIN package ps ON ps.package_id = pl.package_id
            WHERE ps.package_id IN (" . implode(",", $arPackageIds) . ")";

        $objProducts = Database::getSimple($whereClause,"product_id");
        $objProducts->getData()->HydrateModelData(ProductModel::class, true);

        return $objProducts;
    }
}
