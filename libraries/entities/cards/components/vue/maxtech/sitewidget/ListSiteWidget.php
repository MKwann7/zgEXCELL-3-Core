<?php

namespace Entities\Cards\Components\Vue\Maxtech\Sitewidget;

use App\Website\Constructs\Breadcrumb;
use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cards\Components\Vue\CardWidget\ListCardWidget;

class ListSiteWidget extends ListCardWidget
{
    protected string $id = "9a7b22b1-f0b8-4f0e-b98e-332272b2eaf6";
    protected string $title = "Sites";
    protected string $noEntitiesWarning = "There are no sites in your account.";

    protected function getEntityManager() : ?VueComponent
    {
        return new ManageSiteWidget();
    }

    protected function listLayoutType() : string
    {
        return "list";
    }

    protected function getManageEntityStaticId() : string
    {
        return ManageSiteWidget::getStaticId();
    }

    protected function loadBreadCrumbs(): VueComponent
    {
        $this->addBreadcrumb(new Breadcrumb("Admin","/account/admin/", "link"));
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