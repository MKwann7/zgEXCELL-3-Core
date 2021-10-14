<?php

namespace Entities\Users\Components\Vue;

use App\Website\Vue\Classes\VueApp;
use App\Website\Vue\Classes\VueModal;
use Entities\Users\Components\Vue\UserWidget\MyProfileWidget;

class MyProfileApp extends VueApp
{
    protected $appNamePlural = "My Profile";
    protected $appNameSingular = "My Profile";

    public function __construct($domId, ?VueModal &$modal = null)
    {
        $this->enableSlickSortContainerMixin();
        $this->enableSlickSortElementMixin();
        $this->enableSlickSortHandleDirective();

        $this->setDefaultComponentId(MyProfileWidget::getStaticId())->setDefaultComponentAction("view");

        parent::__construct($domId, $modal);
    }
}