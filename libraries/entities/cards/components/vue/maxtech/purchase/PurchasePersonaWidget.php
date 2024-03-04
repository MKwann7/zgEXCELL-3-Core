<?php

namespace Entities\Cards\Components\Vue\Maxtech\Purchase;

use App\Website\Constructs\Breadcrumb;
use App\Website\Constructs\SubPageLinks;
use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cart\Components\Vue\CartWidget\CartWidget;

class PurchasePersonaWidget extends CartWidget
{
    protected string $id = "b8f59a41-9e9c-4e54-8aa0-6818404a6b60";
    protected string $title = "Purchase";

    protected function loadBreadCrumbs(): VueComponent
    {
        $this->addBreadcrumb(new Breadcrumb("Admin","/account/admin/", "link"));
        $this->addSubPageLink(new SubPageLinks("Active","/account/my-personas"))
            ->addSubPageLink(new SubPageLinks("Inactive","/account/my-personas/inactive"))
            ->addSubPageLink(new SubPageLinks("Purchase","/account/my-personas/purchase", true));
        return $this;
    }
}