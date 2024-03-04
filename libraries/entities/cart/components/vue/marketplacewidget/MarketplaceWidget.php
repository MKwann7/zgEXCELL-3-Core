<?php

namespace Entities\Cart\Components\Vue\MarketplaceWidget;

use App\Website\Vue\Classes\Base\VueComponent;

class MarketplaceWidget extends VueComponent
{
    protected string $id = "5240aa56-d65f-4ba5-8abe-a0c62c41d8b3";
    protected string $title = "Marketplace";

    public function __construct(array $components = [])
    {
        parent::__construct();

        $this->modalTitleForAddEntity = "Marketplace";
        $this->modalTitleForEditEntity = "Marketplace";
        $this->modalTitleForDeleteEntity = "Marketplace";
        $this->modalTitleForRowEntity = "Marketplace";

        $this->setDefaultAction("view");
    }

    protected function renderComponentDataAssignments() : string
    {
        return '
            
        ';
    }

    protected function renderComponentMethods() : string
    {
        return '
            
        ';
    }

    protected function renderComponentComputedValues() : string
    {
        return '
        ';
    }

    protected function renderComponentDismissalScript() : string
    {
        return '
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return '
        
        ';
    }

    protected function renderTemplate() : string
    {
        return '
        <div class="marketplace-app-wrapper">
            Here!
        </div>';
    }
}