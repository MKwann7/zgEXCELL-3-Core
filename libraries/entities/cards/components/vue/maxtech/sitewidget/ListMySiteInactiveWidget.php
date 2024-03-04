<?php

namespace Entities\Cards\Components\Vue\Maxtech\Sitewidget;

use App\Website\Constructs\Breadcrumb;
use App\Website\Constructs\SubPageLinks;
use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cards\Components\Vue\CardWidget\ListMyCardWidget;

class ListMySiteInactiveWidget extends ListMyCardWidget
{
    protected string $id = "c9af38c4-ef44-40be-b2f1-2f3c857892f7";
    protected string $title = "My Inactive Sites";
    protected string $noEntitiesWarning = "There are no INACTIVE sites in your account.";

    protected function getEntityManager() : ?VueComponent
    {
        return new ManageSiteWidget();
    }

    protected function getManageEntityStaticId() : string
    {
        return ManageSiteWidget::getStaticId();
    }

    protected function loadBreadCrumbs(): VueComponent
    {
        $this->addBreadcrumb(new Breadcrumb("Admin","/account/admin/", "link"));
        $this->addSubPageLink(new SubPageLinks("Active","/account/my-sites"))
            ->addSubPageLink(new SubPageLinks("Inactive","/account/my-sites/inactive", true))
            ->addSubPageLink(new SubPageLinks("Purchase","/account/my-sites/purchase"))
            ->addSubPageLink(new SubPageLinks("Add CRM","/account/my-sites/add-crm"));
        return $this;
    }
}