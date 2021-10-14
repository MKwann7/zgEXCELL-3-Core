<?php

namespace Entities\Settings\Components\Vue;

use App\Website\Vue\Classes\VueApp;
use App\Website\Vue\Classes\VueModal;
use Entities\Settings\Components\Vue\SettingsWidget\ManageSettingsWidget;

class SettingsMainApp extends VueApp
{
    protected $appNamePlural = "Settings";
    protected $appNameSingular = "Setting";

    public function __construct($domId, ?VueModal &$modal = null)
    {
        $this->enableSlickSortContainerMixin();
        $this->enableSlickSortElementMixin();
        $this->enableSlickSortHandleDirective();

        $this->setDefaultComponentId(ManageSettingsWidget::getStaticId())->setDefaultComponentAction("view");

        parent::__construct($domId, $modal);
    }

    public function renderAppData() : string
    {
        return "
        showNewSelection: true,
        ";
    }
}