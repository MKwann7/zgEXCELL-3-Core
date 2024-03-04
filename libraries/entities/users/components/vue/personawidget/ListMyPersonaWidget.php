<?php

namespace Entities\Users\Components\Vue\PersonaWidget;

use App\Website\Constructs\Breadcrumb;
use App\Website\Constructs\SubPageLinks;
use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cards\Components\Vue\CardWidget\ManageCardWidget;

class ListMyPersonaWidget extends ListPersonasWidget
{
    protected string $id = "43afc5aa-7577-4f35-9fa4-d3dd43c7489f";
    protected string $title = "My Personas";
    protected string $noEntitiesWarning = "There are no personas in your account.";

    protected function renderComponentHydrationScript() : string
    {
        // TODO - THIS NEEDS TO BE FIXED
        return parent::renderComponentHydrationScript() . '
            this.filterEntityId = Cookie.get("userNum")
            this.singleEntity = true
        ';
    }

    protected function listLayoutType() : string
    {
        return "grid";
    }

    protected function openCartPackageSelection() : string
    {
        return '
            openCartPackageSelection: function()
            {
                appCart.openPackagesByClass("card", {id: this.filterEntityId, type: "user"}, this.filterEntityId, this.filterEntityId)
                    .registerEntityListAndManager("' . $this->getId() . '", "' . ManageCardWidget::getStaticId() . '");
            },
        ';
    }

    protected function renderParentData(): void
    {
        $this->parentData["singleEntity"] = "true";
    }

    protected function loadBreadCrumbs(): VueComponent
    {
        $this->addBreadcrumb(new Breadcrumb("Admin","/account/admin/", "link"));
        $this->addSubPageLink(new SubPageLinks("Active","/account/my-personas"))
            ->addSubPageLink(new SubPageLinks("Inactive","/account/my-personas/inactive"))
            ->addSubPageLink(new SubPageLinks("Purchase","/account/my-personas/purchase"));
        return $this;
    }

    protected function customCss(): string
    {
        return '
            .list-cards-main-wrapper {
                padding-right:15px;
                padding-left:15px;
            }
        ';
    }
}