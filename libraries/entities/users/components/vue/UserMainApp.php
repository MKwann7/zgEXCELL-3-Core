<?php

namespace Entities\Users\Components\Vue;

use App\Website\Vue\Classes\VueApp;
use App\Website\Vue\Classes\VueModal;
use Entities\Users\Components\Vue\UserWidget\ListUserWidget;

class UserMainApp extends VueApp
{
    protected $appNamePlural = "Users";
    protected $appNameSingular = "User";

    public function __construct($domId, ?VueModal &$modal = null)
    {
        $this->enableSlickSortContainerMixin();
        $this->enableSlickSortElementMixin();
        $this->enableSlickSortHandleDirective();

        $this->setDefaultComponentId(ListUserWidget::getStaticId())->setDefaultComponentAction("view");

        parent::__construct($domId, $modal);
    }
}