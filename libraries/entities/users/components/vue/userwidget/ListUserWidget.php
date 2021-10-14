<?php

namespace Entities\Users\Components\Vue\UserWidget;

use App\Website\Constructs\Breadcrumb;
use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\VueComponentEntityList;
use Entities\Users\Models\UserModel;

class ListUserWidget extends VueComponentEntityList
{
    protected $id = "03374f26-1780-4c1c-b6fe-db8a69de4576";
    protected $title = "Users";
    protected $singleEntityName = "User";
    protected $batchLoadEndpoint = "users/user-data/get-user-new-batches";
    protected $showCards = false;

    public function __construct($defaultEntity = null, array $components = [])
    {
        if ($defaultEntity === null)
        {
            $defaultEntity = (new UserModel())
                ->setDefaultSortColumn("user_id", "DESC")
                ->setDisplayColumns($this->buildDisplayColumns())
                ->setFilterColumns($this->buildFilterColumns())
                ->setRenderColumns($this->buildRenderColumns());
        }

        parent::__construct($defaultEntity, $components);

        $editorComponent = $this->getEntityDisplayComponent();
        $editorComponent->addParentId($this->getInstanceId(), ["edit"]);
        $this->addComponentsList($editorComponent->getDynamicComponentsForParent());
        $this->addComponent($editorComponent);

        $this->modalTitleForAddEntity = "View Users";
        $this->modalTitleForEditEntity = "View Users";
        $this->modalTitleForDeleteEntity = "View Users";
        $this->modalTitleForRowEntity = "View Users";
        $this->setDefaultAction("view");
    }

    protected function getEntityDisplayComponent() : VueComponent
    {
        return new ManageUserWidget();
    }

    protected function getEntityDisplayComponentId() : string
    {
        return ManageUserWidget::getStaticId();
    }

    protected function getEntityProfileEditorComponentId() : string
    {
        return ManageUserProfileWidget::getStaticId();
    }

    protected function buildDisplayColumns(): array
    {
        global $app;
        $displayColumns = ["avatar", "status"];

        if ($app->userAuthentication() && userCan("manage-platforms"))
        {
            $displayColumns[] = "platform";
        }

        $displayColumns = array_merge($displayColumns, ["user_id", "first_name", "last_name", "username"]);
        if ($this->showCards === true) { $displayColumns = array_merge($displayColumns, ["cards"]); }
        $displayColumns = array_merge($displayColumns, ["created_on", "last_updated"]);

        return $displayColumns;
    }

    protected function buildFilterColumns(): array
    {
        $filterColumns = ["user_id","first_name","last_name"];
        if ($this->showCards === true) { $filterColumns = array_merge($filterColumns, ["cards"]); }
        $filterColumns = array_merge($filterColumns, ["status"]);

        return $filterColumns;
    }

    protected function buildRenderColumns(): array
    {
        $filterColumns = ["avatar","status", "user_id", "first_name", "last_name", "username"];
        if ($this->showCards === true) { $filterColumns = array_merge($filterColumns, ["cards"]); }
        $filterColumns = array_merge($filterColumns, ["platform", "created_on", "last_updated", "sys_row_id"]);

        return $filterColumns;
    }

    protected function loadBreadCrumbs(): VueComponent
    {
        $this->addBreadcrumb(new Breadcrumb("Admin","/account/admin/", "link"));
        return $this;
    }

    protected function renderComponentMethods() : string
    {
        return parent::renderComponentMethods() . '
            openCartPackageSelection: function()
            {
                appCart.openPackagesByClass("card")
                    .registerEntityListAndManager();
            },
            goToCardDashboard: function(entity)
            {
                modal.EngageFloatShield();
                '. $this->activateRegisteredComponentById($this->getEntityDisplayComponentId(), "edit", true, "entity", "this.mainEntityList", null, "this", "function() { 
                    
                }").'           
            },
            createNewUser: function()
            {
                modal.EngageFloatShield();
                '. $this->activateDynamicComponentByIdInModal($this->getEntityProfileEditorComponentId(), "", "add", "null", "this.mainEntityList", null, "this", true, "function(component) { 
                    modal.CloseFloatShield();
                }").'           
            },
            imageError: function (entity) {
                entity.avatar = "/_ez/images/users/no-user.jpg";
            },
            statusClass: function(status) 
            {
                switch(status)
                {
                    case "Active": return "activeStatus";
                    case "Pending": return "pendingStatus";
                    case "Disabled": return "disabledStatus";
                    case "Cancelled": case "Canceled": return "canceledStatus";
                    default: return "unknownStatus";
                }
            },
        ';
    }

    protected function renderTemplate() : string
    {
        return '<div class="formwrapper-control list-cards-main-wrapper">
                    <v-style type="text/css">
                        .BodyContentBox .list-cards-main-wrapper .form-search-box {
                            top: 0px;
                        }
                        .BodyContentBox .list-cards-main-wrapper .form-control {
                            position: relative;
                            top: -1px;
                            font-size: 13px;
                            padding: .100rem .75rem .150rem;
                            width: 140px;
                            line-height: 1.1;
                            height: calc(1.55rem + 2px);
                        }
                        .BodyContentBox .list-cards-main-wrapper #entity-search-input {
                            margin-left: 5px;
                            position: relative;
                            top: -1px;
                        }
                        .BodyContentBox .customerList td:nth-child(1),
                        .BodyContentBox .customerList td:nth-child(2) {
                            width:25px;
                        }
                    </v-style>
                    <div class="fformwrapper-header">
                        <table class="table header-table" style="margin-bottom:0px;">
                            <tbody>
                            <tr>
                                <td>
                                    <h3 class="account-page-title">{{ component_title }}</span></h3>
                                    <div class="form-search-box" v-cloak>
                                        <table>
                                            <tr>
                                                <td>
                                                    <select id="entity-search-filter" class="form-control" @change="updatePage()">
                                                        <option value="user_id">User Id</option>
                                                        <option value="first_name">First Name</option>
                                                        <option value="last_name">Last Name</option>
                                                        <option value="username">Username</option>
                                                        <option value="status">Status</option>
                                                        <option value="last_updated">Last Updated</option>
                                                        <option value="created_on">Created On</option>
                                                        <option value="everything" selected>Everything</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input id="entity-search-input" v-model="searchMainQuery" class="form-control" type="text" placeholder="Search..."/>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" v-on:click="createNewUser()" style="margin-left: 5px;margin-top: -4px;">Create New ' . $this->singleEntityName . '</button>
                                                </td>
                                            </tr>
                                        </table>
                                        
                                    </div>
                                </td>
                                <td class="text-right page-count-display" style="vertical-align: middle;">
                                    <span class="page-count-display-data">
                                        Current: <span>{{ mainEntityPageIndex }}</span>
                                        Pages: <span>{{ totalMainEntityPages }}</span>
                                    </span>
                                    <button v-on:click="prevMainEntityPage()" class="btn prev-btn" :disabled="mainEntityPageIndex == 1">Prev</button>
                                    <button v-on:click="nextMainEntityPage()" class="btn" :disabled="mainEntityPageIndex == totalMainEntityPages">Next</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="entityListOuter">
                        <table class="table table-striped entityList customerList">
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
                            <tbody>
                            <tr v-for="mainEntity in orderedMainEntityList" v-on:dblclick="goToCardDashboard(mainEntity)">
                                '.$this->buildMainEntityDisplayFieldsForTable().'
                                <td class="text-right">
                                    <span v-on:click="goToCardDashboard(mainEntity)" class="pointer editEntityButton"></span>
                                    <span v-on:click="deleteMainEntity(mainEntity)" class="pointer deleteEntityButton"></span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>';
    }

    protected function buildMainEntityDisplayFieldsForTable() : string
    {
        $columnList = "";

        foreach( $this->entity->getDisplayColumns() as $currColumn)
        {
            switch($currColumn)
            {
                case "avatar":
                    $columnList .= '<td><img v-bind:src="mainEntity.' . $currColumn . '" width="75" height="75" class="main-list-image" @error="imageError(mainEntity)"/></td>';
                    break;
                case "status":
                    $columnList .= '<td class="statusColumn"><span v-bind:class="statusClass(mainEntity.' . $currColumn . ')">{{ mainEntity.' . $currColumn . ' }}</span></td>';
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