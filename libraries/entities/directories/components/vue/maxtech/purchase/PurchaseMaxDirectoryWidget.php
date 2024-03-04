<?php

namespace Entities\Directories\Components\Vue\Maxtech\Purchase;

use App\Website\Constructs\Breadcrumb;
use App\Website\Constructs\SubPageLinks;
use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cart\Components\Vue\CartWidget\CartWidget;

class PurchaseMaxDirectoryWidget extends CartWidget
{
    protected string $id = "7ea3d105-41e5-4199-b74c-44d7a48d3aa1";
    protected string $title = "Purchase";

    protected function loadBreadCrumbs(): VueComponent
    {
        $this->addBreadcrumb(new Breadcrumb("Admin","/account/admin/", "link"));
        $this->addSubPageLink(new SubPageLinks("Active","/account/max-directories"))
            ->addSubPageLink(new SubPageLinks("Inactive","/account/max-directories/inactive"))
            ->addSubPageLink(new SubPageLinks("Purchase","/account/max-directories/purchase", true));
        return $this;
    }
}