<?php

namespace Entities\Companies\Components\Vue\CustomerWidget;

use App\Website\Vue\Classes\VueProps;
use Entities\Users\Components\Vue\UserWidget\ListCustomerWidget;

class ListCustomPlatformCustomerWidget extends ListCustomerWidget
{
    protected string $id = "b6269df8-564c-4ca7-9146-c7c51e475c6d";
    protected string $title = "Custom Platform Customers";
    protected string $batchLoadEndpoint = "users/user-data/get-custom-platform-customer-batches";

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