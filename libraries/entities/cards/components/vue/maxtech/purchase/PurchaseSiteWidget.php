<?php

namespace Entities\Cards\Components\Vue\Maxtech\Purchase;

use App\Website\Constructs\Breadcrumb;
use App\Website\Constructs\SubPageLinks;
use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cart\Components\Vue\CartWidget\CartWidget;

class PurchaseSiteWidget extends CartWidget
{
    protected string $id = "177bf28f-e2d6-49af-895b-011d0f258b47";
    protected string $title = "Purchase";

    protected function loadBreadCrumbs(): VueComponent
    {
        $this->addBreadcrumb(new Breadcrumb("Admin","/account/admin/", "link"));
        $this->addSubPageLink(new SubPageLinks("Active","/account/my-sites"))
            ->addSubPageLink(new SubPageLinks("Inactive","/account/my-sites/inactive"))
            ->addSubPageLink(new SubPageLinks("Purchase","/account/my-sites/purchase", true))
            ->addSubPageLink(new SubPageLinks("Add CRM","/account/my-sites/add-crm"));
        return $this;
    }
}