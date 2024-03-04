<?php

namespace Entities\Users\Components\Vue;

use App\Website\Vue\Classes\VueApp;
use App\Website\Vue\Classes\VueModal;
use Entities\Users\Components\Vue\PersonaWidget\ListMyPersonaWidget;

class MyPersonasApp extends VueApp
{
    protected string $appNamePlural = "My Personas";
    protected string $appNameSingular = "My Personas";

    public function __construct($domId, ?VueModal &$modal = null)
    {
        $this->enableSlickSortContainerMixin();
        $this->enableSlickSortElementMixin();
        $this->enableSlickSortHandleDirective();

        $this->setDefaultComponentId(ListMyPersonaWidget::getStaticId())
            ->setDefaultComponentAction("view");

        parent::__construct($domId, $modal);
    }
}