<?php

namespace Entities\Modules\Components\Vue\AppsWidget;

use App\Website\Vue\Classes\Base\VueComponent;

class ManageModuleAppsWidget extends VueComponent
{
    protected $id = "4dd25fbf-404f-4a7d-ac67-e22f9e518d59";
    protected $title = "Module Dashboard";
    protected $endpointUriAbstract = "module-dashboard/{id}";

    public function __construct(array $components = [])
    {
        parent::__construct();
    }

}