<?php

namespace Entities\Users\Components\Vue\PersonaWidget;

use App\Website\Vue\Classes\Base\VueComponent;

class DirectoryRegistrationsWidget extends VueComponent
{
    protected string $id =  "e12ab8a7-3ce8-4948-8c93-5cc05039b74b";
    protected string $title = "Directory Registration Management";

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
        return '<div class="directoryRegistrationManagementWidget">
            <v-style type="text/css">
            </v-style>
            <div class="directoryRegistrationManagementInner">
                Manage Directory Registrations here...
            </div>
        </div>
        ';
    }
}