<?php

namespace Entities\Cards\Components\Vue\Maxtech\Groupwidget;

use App\Website\Constructs\Breadcrumb;
use App\Website\Constructs\SubPageLinks;
use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cards\Components\Vue\CardWidget\ListMyCardWidget;
use Entities\Cards\Components\Vue\Maxtech\Groupwidget\ManageGroupWidget;

class ListMyGroupInactiveWidget extends ListMyCardWidget
{
    protected string $id = "12ee83c4-42d9-45ce-ad9b-bd0e7fb28d49";
    protected string $title = "My Inactive Groups";
    protected string $batchLoadEndpoint = "cards/card-data/get-group-new-batches";
    protected string $noEntitiesWarning = "There are no inactive groups in your account.";

    protected function getEntityManager() : ?VueComponent
    {
        return new ManageGroupWidget();
    }

    protected function listLayoutType() : string
    {
        return "grid";
    }

    protected function getManageEntityStaticId() : string
    {
        return ManageGroupWidget::getStaticId();
    }

    protected function loadBreadCrumbs(): VueComponent
    {
        $this->addBreadcrumb(new Breadcrumb("Admin","/account/admin/", "link"));
        $this->addSubPageLink(new SubPageLinks("Active","/account/my-groups"))
            ->addSubPageLink(new SubPageLinks("Inactive","/account/my-groups/inactive", true))
            ->addSubPageLink(new SubPageLinks("Purchase","/account/my-groups/purchase"))
            ->addSubPageLink(new SubPageLinks("Add CRM","/account/my-groups/add-crm"));
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