<?php

namespace Entities\Directories\Components\Vue\Maxtech\Directorypagewidget;

use App\Website\Vue\Classes\Base\VueComponent;

class DirectoryPageWidget extends VueComponent
{
    // This will be called by the page to
    protected string $id = "54615699-b37b-4525-ba3f-df8c8f5da6c8";
    protected string $modalWidth = "750";
    protected ?VueComponent $manageDataWidget = null;

    public function __construct(?AppModel $entity = null, $name = "Card Page Widget", $props = [])
    {
        $this->loadProps($props);
        $this->name = $name;;

        parent::__construct($entity);

        $this->modalTitleForAddEntity = "Add " . $name;
        $this->modalTitleForEditEntity = "Edit " . $name;
        $this->modalTitleForDeleteEntity = "Delete " . $name;
        $this->modalTitleForRowEntity = "View " . $name;
    }

    protected function renderComponentDataAssignments() : string
    {
        return "
        ";
    }

    protected function renderComponentMethods() : string
    {
        return '
        ';
    }

    protected function renderComponentHydrationScript () : string
    {
        return '            
        '.parent::renderComponentHydrationScript();
    }

    protected function renderTemplate() : string
    {
        return '
        <div class="DirectoryPageWidget">
            Here!
        <div>
        ';
    }

    protected function renderComponentComputedValues() : string
    {
        return '
        ';
    }

}