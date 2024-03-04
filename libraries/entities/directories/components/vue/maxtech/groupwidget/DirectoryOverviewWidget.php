<?php

namespace Entities\Directories\Components\Vue\Maxtech\Groupwidget;

use App\Website\Vue\Classes\Base\VueComponent;

class DirectoryOverviewWidget extends VueComponent
{
    protected string $id =  "ef236a2b-6d37-42c0-94d8-999566b554df";
    protected string $title = "Directory Overview";
    protected string $modalTitleForAddEntity = "Directory Overview";

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
        return '<div class="directoryOverviewWidget">
            <v-style type="text/css">
            </v-style>
            <div class="directoryOverviewInner">
                <div class="pl-3 pr-3">
                    <div class="row mt-3">
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Membership</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Active Events</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Active Members</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Directories</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ';
    }
}