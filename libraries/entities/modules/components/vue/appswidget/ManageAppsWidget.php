<?php

namespace Entities\Modules\Components\Vue\AppsWidget;

use App\Website\Constructs\Breadcrumb;
use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cards\Models\CardAddonModel;

class ManageAppsWidget extends VueComponent
{
    protected string $id = "e1dad1f1-60b4-470d-a729-59912b2ef692";
    protected string $title = "Module Dashboard";
    protected string $endpointUriAbstract = "module-dashboard/{id}";

    public function __construct(array $components = [])
    {
        parent::__construct(new CardAddonModel());

        $this->modalTitleForAddEntity = "Add App Widget";
        $this->modalTitleForEditEntity = "Edit App Widget";
        $this->modalTitleForDeleteEntity = "Delete App Widget";
        $this->modalTitleForRowEntity = "View App Widget";
    }

    protected function loadBreadCrumbs(): VueComponent
    {
        $this->addBreadcrumb(new Breadcrumb("Admin","/account/admin/", "link"))
            ->addBreadcrumb(new Breadcrumb("Modules","/account/admin/modules", "link"));
        return $this;
    }
}