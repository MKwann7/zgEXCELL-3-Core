<?php

namespace Entities\Cards\Components\Vue\Maxtech\Groupwidget;

use App\Website\Constructs\Breadcrumb;
use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cards\Components\Vue\CardWidget\ListCardWidget;

class ListMaxGroupWidget extends ListCardWidget
{
    protected string $id = "c4037a65-d5e9-4aeb-884d-b259a12fe2f4";
    protected string $title = "Groups";
    protected string $batchLoadEndpoint = "cards/card-data/get-group-new-batches";
    protected string $noEntitiesWarning = "There are no groups in your account.";

    protected function getEntityManager() : ?VueComponent
    {
        return new ManageGroupWidget();
    }

    protected function listLayoutType() : string
    {
        return "list";
    }

    protected function getManageEntityStaticId() : string
    {
        return ManageGroupWidget::getStaticId();
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