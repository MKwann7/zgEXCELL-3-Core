<?php

namespace Entities\Cards\Components\Vue\Maxtech\Groupwidget;

use App\Website\Constructs\Breadcrumb;
use App\Website\Constructs\SubPageLinks;
use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cards\Components\Vue\Maxtech\Sitewidget\ManageSiteWidget;

class ManageGroupWidget extends ManageSiteWidget
{
    protected string $id = "2131a635-d79e-4d47-8aea-7e3da260a44b";
    protected string $title = "Max Group";
    protected string $endpointUriAbstract = "group-profile/{id}";

    protected function loadBreadCrumbs(): VueComponent
    {
        $this->addBreadcrumb(new Breadcrumb("Admin","/account/admin/", "link"));
        $this->addSubPageLink(new SubPageLinks("Active","/account/my-groups"))
            ->addSubPageLink(new SubPageLinks("Inactive","/account/my-groups/inactive"))
            ->addSubPageLink(new SubPageLinks("Purchase","/account/my-groups/purchase"));
        return $this;
    }

    public function __construct(array $components = [])
    {
        parent::__construct($components);

        $this->modalTitleForAddEntity = "Add Group Widget";
        $this->modalTitleForEditEntity = "Edit Group Widget";
        $this->modalTitleForDeleteEntity = "Delete Group Widget";
        $this->modalTitleForRowEntity = "View Group Widget";
    }
}