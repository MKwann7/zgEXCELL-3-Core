<?php

namespace Entities\Products\Components\Vue\MarketplaceWidget;

use App\Website\Vue\Classes\VueComponentEntityList;

class ListMarketplaceProductsWidget extends VueComponentEntityList
{
    protected string $id = "00cb416e-c38f-4ce7-b3bb-fc4aad63d10f";
    protected string $title = "Marketplace Products";
    protected string $batchLoadEndpoint = "products/marketplace/get-product-batches";
    protected string $noEntitiesWarning = "There are no marketplace products to display.";
}