<?php

namespace Entities\Notes\Components\Vue\NotesWidget;

use App\Website\Vue\Classes\VueComponentEntityList;
use Entities\Notes\Models\NoteModel;

class ListNotesWidget extends VueComponentEntityList
{
    protected string $id = "297d0665-47aa-44c2-837c-b9f8419a32d2";
    protected string $title = "Notes";
    protected string $modalWidth = "750";
    protected string $mountType = "no_mount";
    protected string $batchLoadEndpoint = "api/v1/notes/get-note-batches";

    public function __construct($defaultEntity = null, array $components = [])
    {
        if ( $defaultEntity === null)
        {
            $defaultEntity = (new NoteModel())
                ->setDefaultSortColumn("note_id", "DESC")
                ->setDisplayColumns(["date", "visibility", "type", "summary", "ticket"])
                ->setRenderColumns(["note_id", "summary", "type", "ticket", "created_on", "last_updated"]);
        }

        parent::__construct($defaultEntity, $components);

        $editorComponent = new ManageNotesWidget();
        $editorComponent->addParentId($this->getInstanceId(), ["view"]);
        $this->addComponent($editorComponent);

        $this->modalTitleForAddEntity = "Add Card Notes";
        $this->modalTitleForEditEntity = "Edit Card Notes";
        $this->modalTitleForDeleteEntity = "Delete Card Notes";
        $this->modalTitleForRowEntity = "View Card Notes";
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

    protected function renderEntityManagementModals() : string
    {
        return '
            addMainEntity: function()
            {
                '. $this->activateDynamicComponentByIdInModal(ManageNotesWidget::getStaticId(), "","add", "{}", "this.mainEntityList", ["entityUserId" => "this.mainEntity.user_id", "entityType" => "general"], "this", true).'
            },
            editMainEntity: function(entity)
            {    
                '. $this->activateDynamicComponentByIdInModal(ManageNotesWidget::getStaticId(), "","edit", "entity", "this.mainEntityList", ["entityUserId" => "this.mainEntity.user_id", "entityType" => "general"], "this", true ).'
            },';
    }

    protected function renderComponentMethods() : string
    {
        return parent::renderComponentMethods() .
            $this->renderEntityManagementModals() . '
            formatVisibility: function(visibility)
            {
                switch(visibility)
                {
                    case "public":
                        return {fas: true, \'fa-eye\': true};
                    case "admin":
                        return {fas: true, \'fa-user-shield\': true};
                    case "ezdigital":
                        return {fas: true, \'fa-users-cog\': true};
                }
            },
            formatType: function(visibility)
            {
                switch(visibility)
                {
                    case "information":
                        return {fas: true, \'fa-info-circle\': true};
                    case "card-build":
                        return {fas: true, \'fa-hammer\': true};
                    case "card-maintenance":
                        return {fas: true, \'fa-tools\': true};
                    case "module-tools":
                        return {fas: true, \'fa-th-large\': true};
                    case "billing":
                        return {fas: true, \'fa-money-check-alt\': true};
                    case "technical":
                        return {fas: true, \'fa-file-code\': true};
                }
            },
            checkForMobileColumns: function(mainEntityColumn)
            {
                return ( mainEntityColumn === \'date\' || mainEntityColumn === \'ticket\' || mainEntityColumn === \'visibility\')
            },
        ';
    }

    protected function renderTemplate() : string
    {
        return '
        <div class="vue-app-component-notes formwrapper-control" v-cloak>
            <v-style type="text/css">
                .vue-app-component-notes .form-search-box {
                    top: 5px !important;
                }
                .vue-app-component-notes  .account-page-title {
                    font-size: 1.5rem;
                    font-weight: 500;
                    top:-5px;
                }
                .vue-app-component-notes .account-page-title .componentIconNotes {
                    margin-right:5px;
                }
                .vue-app-component-notes .account-page-title .componentIconNotes:before {
                    content: "\\\f249";
                }
                .BodyContentBox .vue-app-component-notes .form-search-box .form-control {
                    position: relative;
                    top: -1px;
                    font-size: 13px;
                    padding: .100rem .75rem .150rem;
                    width: 140px;
                    line-height: 1.1;
                    height: calc(1.55rem + 2px);
                }
                .vue-app-component-notes .entityListOuter table td:nth-child(4) {
                    max-width: 0;
                    width:100%;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                }
                .vue-app-component-notes .entityListOuter table td:nth-child(3),
                .vue-app-component-notes .entityListOuter table td:nth-child(1) {
                    white-space: nowrap;
                }
                .vue-app-component-notes .fformwrapper-header table td {
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
                                            <button class="btn btn-sm btn-primary" v-on:click="addMainEntity()" style="margin-left: 5px;margin-top: -4px;">New Note</button>
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
                        <th v-for="mainEntityColumn in mainEntityColumns" v-bind:class="{\'mobile-hide\': checkForMobileColumns(mainEntityColumn)}">
                            <a v-on:click="orderByColumn(mainEntityColumn)" v-bind:class="{ active : orderKey == mainEntityColumn, sortasc : sortByType == true, sortdesc : sortByType == false }">
                                {{ mainEntityColumn | ucWords }}
                            </a>
                        </th>
                        <th class="text-right">
                            Actions
                        </th>
                        </thead>
                        <tbody v-if="orderedMainEntityList.length > 0">
                            <tr v-for="mainEntity in orderedMainEntityList" v-on:dblclick="editMainEntity(mainEntity)">
                                '.$this->buildMainEntityDisplayFieldsForTable().'
                                <td class="text-right">
                                    <span v-on:click="editMainEntity(mainEntity)" class="pointer editEntityButton"></span>
                                    <span v-on:click="deleteMainEntity(connection)"  v-if="(mainEntity.installed_count == 0)" class="pointer deleteEntityButton"></span>
                                    <span v-if="(mainEntity.installed_count == 1)" style="opacity:.3;" class="pointer deleteEntityButton"></span>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-if="orderedMainEntityList.length == 0 && batchEnd == true">
                            <tr><td colspan="100"><span><span class="fas fa-exclamation-triangle"></span> '.$this->noEntitiesWarning.'</span></td></tr>
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
                    case "date":
                        dbColumn = "created_on";
                        break;
                }
                
                this.sortByType = ( this.orderKey == dbColumn ) ? ! this.sortByType : this.sortByType;
                this.orderKey = dbColumn;
            },
            prevMainEntityPage: function()
            {
                this.mainEntityPageIndex--;
                this.mainEntityList = this.mainEntityList;
            },
            nextMainEntityPage: function()
            {
                this.mainEntityPageIndex++;
                this.mainEntityList = this.mainEntityList;
            },
        ';
    }

    protected function buildMainEntityDisplayFieldsForTable() : string
    {
        $columnList = "";

        foreach( $this->entity->getDisplayColumns() as $currColumn)
        {
            switch ($currColumn)
            {
                case "date":
                case "created_on":
                    $columnList .= '<td>{{ formatDateForDisplay(mainEntity.' . $currColumn . ') }}</td>';
                    break;
                case "visibility":
                    $columnList .= "<td><span v-bind:class=\"formatVisibility(mainEntity.{$currColumn})\"></span></td>";
                    break;
                case "type":
                    $columnList .= "<td><span v-bind:class=\"formatType(mainEntity.{$currColumn})\"></span></td>";
                    break;
                default:
                    $columnList .= "<td>{{ mainEntity.{$currColumn} }}</td>";
                    break;
            }

        }
        return $columnList;
    }
}