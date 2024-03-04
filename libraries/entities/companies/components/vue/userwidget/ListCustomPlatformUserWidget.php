<?php

namespace Entities\Companies\Components\Vue\UserWidget;

use App\Website\Vue\Classes\VueProps;
use Entities\Users\Components\Vue\UserWidget\ListUserWidget;

class ListCustomPlatformUserWidget extends ListUserWidget
{
    protected string $id = "d164722a-3e0b-40e3-a968-f8953d12a890";
    protected string $title = "Custom Platform Users";
    protected string $batchLoadEndpoint = "users/user-data/get-custom-platform-user-batches";

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