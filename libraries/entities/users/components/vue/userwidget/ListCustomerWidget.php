<?php

namespace Entities\Users\Components\Vue\UserWidget;

use App\Website\Vue\Classes\Base\VueComponent;

class ListCustomerWidget extends ListUserWidget
{
    protected string $id = "48c7504b-1733-4f0d-9f91-7b3078593a14";
    protected string $title = "Customers";
    protected $singleEntityName = "Customer";
    protected string $batchLoadEndpoint = "users/user-data/get-customer-new-batches";
    protected $showCards = true;

    protected function getEntityDisplayComponent() : VueComponent
    {
        return new ManageCustomerWidget();
    }

    protected function getEntityDisplayComponentId() : string
    {
        return ManageCustomerWidget::getStaticId();
    }

    protected function getEntityProfileEditorComponentId() : string
    {
        return ManageCustomerProfileWidget::getStaticId();
    }
}