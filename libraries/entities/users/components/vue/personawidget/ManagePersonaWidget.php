<?php

namespace Entities\Users\Components\Vue\PersonaWidget;

use App\Website\Constructs\Breadcrumb;
use App\Website\Constructs\SubPageLinks;
use App\Website\Vue\Classes\Base\VueComponent;
use App\website\Vue\Classes\VueHub;
use App\Website\Vue\Classes\VueProps;
use Entities\Cards\Components\Vue\CardWidget\ManageCardProfileWidget;
use Entities\Cards\Components\Vue\Maxtech\Sitewidget\ManageSiteWidget;

class ManagePersonaWidget extends ManageSiteWidget
{
    protected string $id = "bef49185-66dc-4363-bfaf-a4d25bc61943";
    protected string $title = "Max Persona";
    protected string $endpointUriAbstract = "persona-profile/{id}";

    protected function loadBreadCrumbs(): VueComponent
    {
        $this->addBreadcrumb(new Breadcrumb("Admin","/account/admin/", "link"));
        $this->addSubPageLink(new SubPageLinks("Active","/account/my-personas"))
            ->addSubPageLink(new SubPageLinks("Inactive","/account/my-personas/inactive"))
            ->addSubPageLink(new SubPageLinks("Purchase","/account/my-personas/purchase"));
        return $this;
    }

    public function __construct(array $components = [])
    {
        parent::__construct($components);

        $this->modalTitleForAddEntity = "Add Card Widget";
        $this->modalTitleForEditEntity = "Edit Card Widget";
        $this->modalTitleForDeleteEntity = "Delete Card Widget";
        $this->modalTitleForRowEntity = "View Card Widget";
    }
}