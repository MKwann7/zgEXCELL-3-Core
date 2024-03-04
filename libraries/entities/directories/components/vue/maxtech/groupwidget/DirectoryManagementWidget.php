<?php

namespace Entities\Directories\Components\Vue\Maxtech\Groupwidget;

use App\Website\Vue\Classes\Base\VueComponent;

class DirectoryManagementWidget extends VueComponent
{
    protected string $id =  "603cce71-a3e9-4f9b-b7f9-401889a5b935";
    protected string $title = "Directory Management";
    protected string $modalTitleForAddEntity = "Directory Management";

    protected function renderComponentDataAssignments() : string
    {
        return "
        activeDirectoryId: '',
        ";
    }

    protected function renderComponentComputedValues() : string
    {
        return '
        ';
    }

    protected function renderComponentMethods() : string
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
        return '<div class="directoryManagementWidget">
            <v-style type="text/css">
            </v-style>
            <div class="directoryManagementInner">
                Management here...
            </div>
        </div>
        ';
    }
}