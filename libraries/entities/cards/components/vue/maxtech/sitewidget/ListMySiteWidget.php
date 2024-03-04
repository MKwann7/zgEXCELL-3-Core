<?php

namespace Entities\Cards\Components\Vue\Maxtech\Sitewidget;

use App\Website\Constructs\Breadcrumb;
use App\Website\Constructs\SubPageLinks;
use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cards\Components\Vue\CardWidget\ListMyCardWidget;

class ListMySiteWidget extends ListMyCardWidget
{
    protected string $id = "b9060b0a-1d4e-4e64-bd55-33f9867536c4";
    protected string $title = "My Sites";
    protected string $noEntitiesWarning = "There are no sites in your account.";

    protected function getEntityManager() : ?VueComponent
    {
        return new ManageSiteWidget();
    }

    protected function listLayoutType() : string
    {
        return "grid";
    }

    protected function getManageEntityStaticId() : string
    {
        return ManageSiteWidget::getStaticId();
    }

    protected function loadBreadCrumbs(): VueComponent
    {
        $this->addBreadcrumb(new Breadcrumb("Admin","/account/admin/", "link"));
        $this->addSubPageLink(new SubPageLinks("Active","/account/my-sites", true))
            ->addSubPageLink(new SubPageLinks("Inactive","/account/my-sites/inactive"))
            ->addSubPageLink(new SubPageLinks("Purchase","/account/my-sites/purchase"))
            ->addSubPageLink(new SubPageLinks("Add CRM","/account/my-sites/add-crm"));
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