<?php

namespace Entities\Directories\Components\Vue\Directorywidget;

use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Directories\Models\DirectoryModel;

class ManageDirectoryWidget extends VueComponent
{
    protected string $id =  "83804d87-2b4e-44ef-adf7-c237124829c9";
    protected string $title = "Directory Dashboard";
    protected string $endpointUriAbstract = "directory-dashboard/{instance_uuid}";

    public function __construct(array $components = [])
    {
        $defaultEntity = (new DirectoryModel())
            ->setDefaultSortColumn("card_tab_id", "DESC")
            ->setDisplayColumns(["title", "type", "group_count", "site_count", "created_on", "last_updated"])
            ->setRenderColumns(["directory_id", "title", "user_id", "group_count", "site_count", "type", "created_on", "last_updated", "__app"]);

        parent::__construct($defaultEntity, $components);

        $this->modalTitleForAddEntity = "Add Directory";
        $this->modalTitleForEditEntity = "Manage Directory";
        $this->modalTitleForDeleteEntity = "Delete Directory";
        $this->modalTitleForRowEntity = "View Directory";
    }

    protected function renderComponentDataAssignments() : string
    {
        return "
        dashboardTab: 'overview',
        entityNotFound: false,
        singleEntity: false,
        activeDirectoryId: '',
        mainMenu: [
            {title:'Overview',tag:'overview',icon:'fa fa-id-card'},  
            {title:'Profile',tag:'profile',icon:'fa fa-id-card'},  
            {title:'Events',tag:'events',icon:'fa fa-calendar-alt'},  
            {title:'Directories',tag:'directories',icon:'fa fa-cog'},
            {title:'Billing',tag:'billing',icon:'fa fa-credit-card'},   
        ],
        ";
    }

    protected function renderComponentMethods() : string
    {
        return '
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return '
        this.disableModalLoadingSpinner();  
        if (this.entity && typeof this.entity.card_tab_id !== "undefined") 
        {
            console.log(this.entity);   
        }
        else
        {
        
        }
        ';
    }

    protected function renderTemplate() : string
    {
        return '<div class="manangeSitePageWidget container">
            <div class="row mb-2">
                <div class="col-12">
                    <input placeholder="Page Title" class="form-control">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6"><input placeholder="Menu Name" class="form-control"></div>
                <div class="col-6"><input placeholder="Uri Path" class="form-control"></div>
            </div>
            <div class="row">
                <div class="col-12">
                    <select class="form-control">
                        <option>Content Builder Widget</option>
                        <option>Directory Widget</option>
                    </select>
                </div>
            </div>
        </div>
        ';
    }
}