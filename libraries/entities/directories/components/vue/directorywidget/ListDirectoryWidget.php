<?php

namespace Entities\Directories\Components\Vue\Directorywidget;

use App\Core\App;
use App\Website\Constructs\Breadcrumb;
use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\VueComponentEntityList;
use App\Website\Vue\Classes\VueProps;
use Entities\Companies\Classes\Companies;
use Entities\Directories\Models\DirectoryModel;

class ListDirectoryWidget extends VueComponentEntityList
{
    protected string $id = "e0e06662-2b83-4b03-8b1b-d63e331dcd71";
    protected string $title = "Directories";
    protected string $batchLoadEndpoint = "api/v1/directories/get-directory-batches";
    protected string $noEntitiesWarning = "There are no directories in your account.";

    public function __construct($defaultEntity = null, array $components = [])
    {
        if ($defaultEntity === null)
        {
            $defaultEntity = (new DirectoryModel())
                ->setDefaultSortColumn("directory_id", "DESC")
                ->setDisplayColumns(["title", "starts_on", "ends_on",  "created_on", "last_updated"])
                ->setFilterColumns(["title", "starts_on", "ends_on", "created_on","last_updated"])
                ->setRenderColumns(["directory_id", "parent_id", "user_id", "title", "type_id", "template_id", "starts_on", "ends_on", "created_on", "last_updated", "instance_uuid"]);
        }

        parent::__construct($defaultEntity, $components);

        $filterEntity = new VueProps("filterEntityId", "object", "filterEntityId");
        $filterByEntityValue = new VueProps("filterByEntityValue", "boolean", "filterByEntityValue");
        $filterByEntityRefresh = new VueProps("filterByEntityRefresh", "boolean", true);

        $this->addProp($filterEntity);
        $this->addProp($filterByEntityValue);
        $this->addProp($filterByEntityRefresh);

        $editorComponent = $this->getEntityManager();
        $editorComponent->addParentId($this->getInstanceId(), ["edit"]);

        $this->addComponentsList($editorComponent->getDynamicComponentsForParent());
        $this->addComponent($editorComponent);

        $this->modalTitleForAddEntity = "View Directories";
        $this->modalTitleForEditEntity = "View Directories";
        $this->modalTitleForDeleteEntity = "View Directories";
        $this->modalTitleForRowEntity = "View Directories";
        $this->setDefaultAction("view");
    }

    protected function listLayoutType() : string
    {
        return "grid";
    }

    protected function loadBreadCrumbs(): VueComponent
    {
        $this->addBreadcrumb(new Breadcrumb("Admin","/account/admin/", "link"));
        return $this;
    }

    protected function getEntityManager() : ManageDirectoryWidget
    {
        return new ManageDirectoryWidget();
    }

    protected function getManageEntityStaticId() : string
    {
        return ManageDirectoryWidget::getStaticId();
    }

    protected function renderParentData(): void
    {
        parent::renderParentData();
        $this->parentData["singleEntity"] = "false";
    }

    protected function renderComponentMountedScript(): string
    {
        return parent::renderComponentMountedScript() . '
        ';
    }

    protected function renderComponentMethods() : string
    {
        return parent::renderComponentMethods() . '
            goToCardDashboard: function(entity)
            {
                if (typeof this.filterEntity === "undefined")
                { 
                    modal.EngageFloatShield();
                    '. $this->activateRegisteredComponentById($this->getManageEntityStaticId(), "edit", true, "entity", "this.mainEntityList", ["singleEntity" => "this.singleEntity"], "this", "function() { 
                        modal.CloseFloatShield();
                    }").'
                }
                else
                {
                    '.$this->activateRegisteredComponentByIdInModal($this->getManageEntityStaticId(), "edit", true, "entity", "this.mainEntityList", ["singleEntity" => "this.singleEntity", "filterEntityId" => "this.filterEntityId"], "this", "function() {
                        
                    }").'
                }    
            },
            updateCardEntityList: function(data) {            
                this.updateMainEntityList(data);
            },
            updateMainEntityList: function (data) {
                let assignedCard = false;
                
                for (let currEntityIndex in Array.from(this.mainEntityList)) {
                    if (this.mainEntityList[currEntityIndex].card_id === data.card.card_id) {
                        assignedCard = true
                        this.mainEntityList[currEntityIndex] = null;
                        this.mainEntityList[currEntityIndex] = data.card;
                        break;
                    }
                }
                
                if (!assignedCard) {
                    this.entities.push(data.card);
                }
            },
            imageError: function (entity) {
                entity.banner = "/_ez/images/no-image.jpg";
            },
            statusClass: function(status) {
                switch(status)
                {
                    case "Active": return "activeStatus";
                    case "Build": return "buildStatus";
                    case "BuildComplete": return "bCompleteStatus";
                    case "Pending": return "pendingStatus";
                    case "Disabled": return "disabledStatus";
                    case "Cancelled": case "Canceled": return "canceledStatus";
                    default: return "unknownStatus";
                }
            },
        ';
    }

    protected function customCss(): string
    {
        return '';
    }

    protected function renderTemplate() : string
    {
        /** @var App $app */
        global $app;
        return '<div class="formwrapper-control list-directories-main-wrapper">
                    <v-style type="text/css">
                    </v-style>'.'
                    <div class="fformwrapper-header">
                        <table class="entity-list-header-wrapper table header-table" style="margin-bottom:0px;">
                            <tbody>
                            <tr>
                                <td>
                                    <h3 class="account-page-title">
                                        <span class="componentIcon" v-bind:class="\'componentIcon\' + component_title.replace(\' \', \'\')"></span>
                                        {{ component_title }} 
                                    </h3>
                                    <div class="form-search-box" v-cloak>
                                        <table>
                                            <tr>
                                                <td>
                                                    <select id="entity-search-filter" class="form-control" @change="updatePage()">
                                                        <option value="card_num">Card Num</option>
                                                        <option value="card_owner_name">Card Owner Name</option>
                                                        <option value="card_name">Card Name</option>
                                                        <option value="card_vanity_url">Card Vanity Url</option>
                                                        <option value="last_updated">Last Updated</option>
                                                        <option value="created_on">Created On</option>
                                                        <option value="everything" selected>Everything</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input id="entity-search-input" v-model="searchMainQuery" class="form-control" type="text" placeholder="Search..."/>
                                                </td>
                                                <td>
                                                    ' . ( $app->objCustomPlatform->getApplicationType() === Companies::APP_TYPE_DEFAULT ? '<button class="btn btn-sm btn-primary" v-on:click="openCartPackageSelection()" style="margin-left: 5px;margin-top: -4px;">Purchase New Card</button>' : '') . '
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                                <td class="text-right page-count-display" style="vertical-align: middle;">
                                ' . ( $app->objCustomPlatform->getApplicationType() === Companies::APP_TYPE_DEFAULT ? '
                                    <span class="page-count-display-data">
                                        Current: <span>{{ mainEntityPageIndex }}</span>
                                        Pages: <span>{{ totalMainEntityPages }}</span>
                                    </span>
                                    <button v-on:click="prevMainEntityPage()" class="btn prev-btn" :disabled="mainEntityPageIndex == 1">Prev</button>
                                    <button v-on:click="nextMainEntityPage()" class="btn" :disabled="mainEntityPageIndex == totalMainEntityPages">Next</button>
                                    <span>
                                        <span v-bind:class="{active: listLayoutType === \'grid\'}" v-on:click="toggleLayoutGrid" class="fas fa-th pointer"></span>
                                        <span v-bind:class="{active: listLayoutType === \'list\'}" v-on:click="toggleLayoutList" class="fas fa-list pointer"></span>
                                    </span>
                                    ' : '
                                    <button v-on:click="prevMainEntityPage()" class="btn prev-btn" :disabled="mainEntityPageIndex == 1">Prev</button>
                                    <span class="page-count-display-data">
                                        <span>{{ mainEntityPageIndex }}</span> / <span>{{ totalMainEntityPages }}</span>
                                    </span>
                                    <button v-on:click="nextMainEntityPage()" class="btn" :disabled="mainEntityPageIndex == totalMainEntityPages">Next</button>
                                    <span>
                                        <span v-bind:class="{active: listLayoutType === \'grid\'}" v-on:click="toggleLayoutGrid" class="fas fa-th pointer"></span>
                                        <span v-bind:class="{active: listLayoutType === \'list\'}" v-on:click="toggleLayoutList" class="fas fa-list pointer"></span>
                                    </span>
                                    ') . '
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="entityListOuter" v-bind:class="{tableGridLayout: listLayoutType === \'grid\'}">
                        <table class="card-list-outer table table-striped entityList">
                            <thead>
                            <th v-for="mainEntityColumn in mainEntityColumns">
                                <a v-on:click="orderByColumn(mainEntityColumn)" v-bind:class="{ active : orderKey == mainEntityColumn, sortasc : sortByType == true, sortdesc : sortByType == false }">
                                    {{ mainEntityColumn | ucWords }}
                                </a>
                            </th>
                            <th class="text-right">
                                Actions
                            </th>
                            </thead>
                            <tbody v-if="orderedMainEntityList.length > 0">
                            <tr v-for="mainEntity in orderedMainEntityList" v-on:dblclick="goToCardDashboard(mainEntity)" v-bind:class="{demoCard: (mainEntity.product_id === 1100) }">
                                '.$this->buildMainEntityDisplayFieldsForTable().'
                                <td class="text-right">
                                    <span v-on:click="goToCardDashboard(mainEntity)" class="pointer editEntityButton"></span>
                                    <span v-on:click="deleteMainEntity(mainEntity)" class="pointer deleteEntityButton"></span>
                                </td>
                            </tr>
                            </tbody>
                            <tbody v-if="orderedMainEntityList.length == 0 && batchEnd == true">
                                <tr><td colspan="100"><span><span class="fas fa-exclamation-triangle"></span> '.$this->noEntitiesWarning.'</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>';
    }

    protected function buildMainEntityDisplayFieldsForTable() : string
    {
        global $app;
        $columnList = "";

        foreach( $this->entity->getDisplayColumns() as $currColumn)
        {
            switch($currColumn)
            {
                case "banner":
                    $columnList .= '<td><span v-bind:style="{background: \'url(\' + mainEntity.' . $currColumn . ' + \') no-repeat center center / cover, url(/_ez/images/no-image.jpg) no-repeat center center / cover\'}" width="75" height="75" class="main-list-image entity-banner"></span></td>';
                    break;
                case "status":
                    $columnList .= '<td class="statusColumn"><span v-bind:class="statusClass(mainEntity.' . $currColumn . ')">{{ mainEntity.' . $currColumn . ' }}</span></td>';
                    break;
                case "card_num":
                case "card_vanity_url":
                    $columnList .= '<td><a target="_blank" v-bind:href="\''.$app->objCustomPlatform->getFullPublicDomainName().'/\' + mainEntity.' . $currColumn . '">{{ mainEntity.' . $currColumn . ' }}</a></td>';
                    break;
                case "created_on":
                case "last_updated":
                    $columnList .= '<td>{{ formatDateForDisplay(mainEntity.' . $currColumn . ') }}</td>';
                    break;
                default:
                    $columnList .= "<td>{{ mainEntity.{$currColumn} }}</td>";
                    break;
            }

        }

        return $columnList;
    }
}