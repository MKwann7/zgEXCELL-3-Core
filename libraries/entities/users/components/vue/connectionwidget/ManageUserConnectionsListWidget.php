<?php

namespace Entities\Users\Components\Vue\ConnectionWidget;

use App\Website\Vue\Classes\VueComponentEntityList;
use Entities\Users\Models\ConnectionModel;

class ManageUserConnectionsListWidget extends VueComponentEntityList
{
    protected $id = "14737e0b-16c2-4ccf-ad8f-79d052dd29ba";
    protected $title = "Connections";
    protected $modalWidth = 750;
    protected $noMount = true;

    public function __construct(array $components = [])
    {
        $defaultEntity = (new ConnectionModel())
            ->setDefaultSortColumn("connection_id", "DESC")
            ->setDisplayColumns(["type", "cards", "value"])
            ->setRenderColumns(["connection_id", "type", "value", "is_primary",  "cards", "created_on", "last_updated"]);

        parent::__construct($defaultEntity, $components);

        $editorComponent = new ManageUserConnectionsWidget();
        $editorComponent->addParentId($this->getInstanceId(), ["edit"]);
        $this->addComponent($editorComponent);

        $this->modalTitleForAddEntity = "Add Card Connection List";
        $this->modalTitleForEditEntity = "Edit Card Connection List";
        $this->modalTitleForDeleteEntity = "Delete Card Connection List";
        $this->modalTitleForRowEntity = "View Card Connection List";
        $this->setDefaultAction("view");

        $this->setEntityPageDisplayCount(5);
    }

    protected function renderComponentDataAssignments() : string
    {
        return parent::renderComponentDataAssignments() . '
            mainEntity: null,
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return '
            this.mainEntity = props.mainEntity;
        ';
    }

    protected function renderReloadComponentMethod() : string
    {
        return '
            reloadComponent: function() 
            {
                console.log("reloadComponent...");
                if (typeof this.mainEntity !== "undefined")
                {
                    this.mainEntityList = this.mainEntity.Connections;
                }
                this.$forceUpdate();
                this.batchEnd = true;
            },
        ';
    }

    protected function renderOrderedMainEntityListComputedMethod() : string
    {
        return '
                orderedMainEntityList: function()
                {
                    var self = this;
                    
                    this.mainEntityList = [];
                    
                    if (typeof this.mainEntity.Connections !== "undefined" && typeof this.mainEntity.Connections !== "undefined")
                    {
                        this.mainEntityList = this.mainEntity.Connections;
                    }

                    let objSortedPeople = this.sortedEntity(this.searchMainQuery, this.mainEntityList, this.orderKey, this.sortByType, this.mainEntityPageIndex,  this.mainEntityPageDisplayCount, this.mainEntityPageTotal, function(data) {
                        self.mainEntityPageTotal = data.pageTotal;
                        self.mainEntityPageIndex = data.pageIndex;
                    }'.$this->renderEntityFitlers().');

                    return objSortedPeople;
                },
        ';
    }

    protected function renderComponentMethods() : string
    {
        return parent::renderComponentMethods() . '
            addMainEntity: function()
            {
                '. $this->activateDynamicComponentByIdInModal(ManageUserConnectionsWidget::getStaticId(), "","add", "{}", "this.mainEntityList", ["entityUserId" => "this.mainEntity.user_id"], "this", true).'
            },
            editMainEntity: function(entity)
            {    
                '. $this->activateDynamicComponentByIdInModal(ManageUserConnectionsWidget::getStaticId(), "","edit", "entity", "this.mainEntityList", ["entityUserId" => "this.mainEntity.user_id"], "this", true ).'
            },
            deleteMainEntity: function(entity)
            {    
                '. $this->activateDynamicComponentByIdInModal(ManageUserConnectionsWidget::getStaticId(), "","delete", "entity", "this.mainEntityList", ["entityUserId" => "this.mainEntity.user_id"], "this", true ).'
            },
            swapAndDeleteMainEntity: function(entity)
            {    
                '. $this->activateDynamicComponentByIdInModal(ManageUserConnectionsWidget::getStaticId(), "","swapAndDelete", "entity", "this.mainEntityList", ["entityUserId" => "this.mainEntity.user_id"], "this", true ).'
            },
        ';
    }

    protected function renderTemplate() : string
    {
        return '
        <div class="vue-app-component-connections formwrapper-control" v-cloak>
            <v-style type="text/css">
                .vue-app-component-connections .form-search-box {
                    top: 5px !important;
                }
                .vue-app-component-connections  .account-page-title {
                    font-size: 1.5rem;
                    font-weight: 500;
                    top:-5px;
                }
                .vue-app-component-connections .account-page-title .componentIconConnections {
                    margin-right:5px;
                }
                .vue-app-component-connections .account-page-title .componentIconConnections:before {
                    content: "\\\f1e0";
                }
                .BodyContentBox .vue-app-component-connections .form-search-box .form-control {
                    position: relative;
                    top: -1px;
                    font-size: 13px;
                    padding: .100rem .75rem .150rem;
                    width: 140px;
                    line-height: 1.1;
                    height: calc(1.55rem + 2px);
                }
                .vue-app-component-connections .entityListOuter table td:nth-child(3) {
                    max-width: 0;
                    width:100%;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                }
                .vue-app-component-connections .fformwrapper-header table td {
                    padding-top:0px !important;
                }
            </v-style>
                <div class="fformwrapper-header">
                    <table class="table header-table" style="margin-bottom:0px;">
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
                                            <input v-model="searchMainQuery" class="form-control" type="text" placeholder="Search..."/>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" v-on:click="addMainEntity()" style="margin-left: 5px;margin-top: -4px;">Add New</button>
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
                    <table class="table table-striped entityList">
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
                        <tr v-for="mainEntity in orderedMainEntityList" v-on:dblclick="editMainEntity(mainEntity)">
                            '.$this->buildMainEntityDisplayFieldsForTable().'
                            <td class="text-right">
                                <span v-on:click="editMainEntity(mainEntity)" class="pointer editEntityButton"></span>
                                <span v-on:click="deleteMainEntity(mainEntity)"  v-if="(mainEntity.cards == 0)" class="pointer deleteEntityButton"></span>
                                <span v-on:click="swapAndDeleteMainEntity(mainEntity)" v-if="(mainEntity.cards > 1)" style="opacity:.3;" class="pointer deleteEntityButton"></span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        ';
    }

    protected function renderPaginationMethods() : string
    {
        return '
            orderByColumn: function(column)
            {
                let dbColumn = column;
                switch(column)
                {
                    case "id":
                        dbColumn = "connection_id";
                        break;
                    case "type":
                        dbColumn = "connection_type_id";
                        break;
                    case "value":
                        dbColumn = "connection_value";
                        break;
                }
                
                this.sortByType = ( this.orderKey == dbColumn ) ? ! this.sortByType : this.sortByType;
                this.orderKey = dbColumn;
            },
            prevMainEntityPage: function()
            {
                this.mainEntityPageIndex--;
                if (typeof this.mainEntity !== "undefined")
                {
                    this.mainEntityList = this.mainEntity.Connections;
                }
            },
            nextMainEntityPage: function()
            {
                this.mainEntityPageIndex++;
                if (typeof this.mainEntity !== "undefined")
                {
                    this.mainEntityList = this.mainEntity.Connections;
                }
            },
        ';
    }

    protected function buildMainEntityDisplayFieldsForTable() : string
    {
        $columnList = "";

        foreach( $this->entity->getDisplayColumns() as $currColumn)
        {
            switch($currColumn)
            {
                case "type":
                    $columnList .= '<td class="cardConnectionType" style="width:35px;text-align:center;"><span v-bind:class="mainEntity.font_awesome"></span></td>';
                    break;
                case "value":
                    $columnList .= '<td>{{ mainEntity.connection_value }}</td>';
                    break;
                default:
                    $columnList .= "<td data-title='{$currColumn}'>{{ mainEntity.{$currColumn} }}</td>";
                    break;
            }
        }

        return $columnList;
    }
}