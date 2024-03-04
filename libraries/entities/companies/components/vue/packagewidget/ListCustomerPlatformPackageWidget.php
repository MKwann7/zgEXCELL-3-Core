<?php

namespace Entities\Companies\Components\Vue\PackageWidget;

use App\Website\Vue\Classes\VueProps;
use Entities\Packages\Components\Vue\PackageWidget\ListPackageWidget;

class ListCustomerPlatformPackageWidget extends ListPackageWidget
{
    protected string $id = "ee52f108-529c-4c7e-b0b2-0d2a1c206f95";
    protected string $title = "Custom Platform Packages";
    protected string $batchLoadEndpoint = "packages/get-custom-platform-package-batches";

    public function __construct(array $components = [])
    {
        parent::__construct();

        $filterEntity = new VueProps("filterEntityId", "object", "filterEntityId");
        $filterByEntityValue = new VueProps("filterByEntityValue", "boolean", "filterByEntityValue");
        $filterByEntityRefresh = new VueProps("filterByEntityRefresh", "boolean", true);

        $this->addProp($filterEntity);
        $this->addProp($filterByEntityValue);
        $this->addProp($filterByEntityRefresh);
    }
}