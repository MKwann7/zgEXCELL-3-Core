<?php

namespace Entities\Modules\Components\Vue\AppsWidget;

use App\Website\Vue\Classes\Base\VueComponent;

class ManageModuleAppsAdminWidget extends VueComponent
{
    protected string $id = "d6466237-8c83-4971-b799-06c2dca1cbc0";
    protected string $title = "Module Dashboard";
    protected string $endpointUriAbstract = "module-dashboard/{id}";

    public function __construct(array $components = [])
    {
        parent::__construct();
    }

}