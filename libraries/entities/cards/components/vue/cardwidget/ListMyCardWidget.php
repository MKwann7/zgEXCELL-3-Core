<?php

namespace Entities\Cards\Components\Vue\CardWidget;

use App\Website\Vue\Classes\Base\VueComponent;

class ListMyCardWidget extends ListCardWidget
{
    protected $id = "fb2f5673-342a-4cba-a18d-522bff21123a";
    protected $title = "My Cards";
    protected $noEntitiesWarning = "There are no cards in your account.";

    protected function renderComponentHydrationScript() : string
    {
        global $app;
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
                appCart.openPackagesByClass("card", {id: this.filterEntityId, type: "user"}, this.filterEntityId)
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
        return $this;
    }
}