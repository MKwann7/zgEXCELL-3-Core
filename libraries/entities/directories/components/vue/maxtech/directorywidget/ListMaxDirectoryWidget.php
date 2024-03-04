<?php

namespace Entities\Directories\Components\Vue\Maxtech\Directorywidget;

use App\Website\Constructs\Breadcrumb;
use App\Website\Constructs\SubPageLinks;
use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Directories\Components\Vue\Directorywidget\ListDirectoryWidget;

class ListMaxDirectoryWidget extends ListDirectoryWidget
{
    protected string $id = "dd9cbdf1-c154-4d7c-9fcd-e2b3cf1096cb";
    protected string $title = "Max Directories";
    protected string $noEntitiesWarning = "There are no groups in your account.";

    protected function getEntityManager() : ManageMaxDirectoryWidget
    {
        return new ManageMaxDirectoryWidget();
    }

    protected function getManageEntityStaticId() : string
    {
        return ManageMaxDirectoryWidget::getStaticId();
    }

    protected function loadBreadCrumbs(): VueComponent
    {
        $this->addBreadcrumb(new Breadcrumb("Admin","/account/admin/", "link"));
        $this->addSubPageLink(new SubPageLinks("Active","/account/max-directories", true))
            ->addSubPageLink(new SubPageLinks("Inactive","/account/max-directories/inactive"))
            ->addSubPageLink(new SubPageLinks("Purchase","/account/max-directories/purchase"));
        return $this;
    }
}