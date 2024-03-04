<?php

namespace Entities\Cards\Components\Vue\Maxtech\Purchase;

use App\Website\Constructs\Breadcrumb;
use App\Website\Constructs\SubPageLinks;
use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cart\Components\Vue\CartWidget\CartWidget;

class PurchaseGroupWidget extends CartWidget
{
    protected string $id = "6b5906f9-96cb-499d-8649-71a8ec349bb0";
    protected string $title = "Purchase";

    protected function loadBreadCrumbs(): VueComponent
    {
        $this->addBreadcrumb(new Breadcrumb("Admin","/account/admin/", "link"));
        $this->addSubPageLink(new SubPageLinks("Active","/account/my-groups"))
            ->addSubPageLink(new SubPageLinks("Inactive","/account/my-groups/inactive"))
            ->addSubPageLink(new SubPageLinks("Purchase","/account/my-groups/purchase", true));
        return $this;
    }
}