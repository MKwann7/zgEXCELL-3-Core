<?php

namespace Entities\Modules\Components\Vue\AppsWidget;

use App\Website\Vue\Classes\Base\VueComponent;

class ManageModuleAppsAdminWidget extends VueComponent
{
    protected $id = "d6466237-8c83-4971-b799-06c2dca1cbc0";
    protected $title = "Module Dashboard";
    protected $endpointUriAbstract = "module-dashboard/{id}";

    public function __construct(array $components = [])
    {
        parent::__construct();
    }

}