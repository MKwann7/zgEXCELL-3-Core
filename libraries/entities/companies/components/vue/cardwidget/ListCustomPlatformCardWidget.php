<?php

namespace Entities\Companies\Components\Vue\CardWidget;

use App\Website\Vue\Classes\VueProps;
use Entities\Cards\Components\Vue\CardWidget\ListCardWidget;

class ListCustomPlatformCardWidget extends ListCardWidget
{
    protected string $id = "00fcc717-1d00-4481-b962-5ff525b434ac";
    protected string $title = "Custom Platform Cards";
    protected string $batchLoadEndpoint = "cards/card-data/get-custom-platform-card-batches";

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