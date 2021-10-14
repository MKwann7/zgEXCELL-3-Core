<?php

namespace Entities\Products\Components\Vue\MarketplaceWidget;

use App\Website\Vue\Classes\VueComponentEntityList;

class ListMarketplaceProductsWidget extends VueComponentEntityList
{
    protected $id = "00cb416e-c38f-4ce7-b3bb-fc4aad63d10f";
    protected $title = "Marketplace Products";
    protected $batchLoadEndpoint = "products/marketplace/get-product-batches";
    protected $noEntitiesWarning = "There are no marketplace products to display.";
}