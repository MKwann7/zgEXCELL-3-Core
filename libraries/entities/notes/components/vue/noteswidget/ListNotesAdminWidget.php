<?php

namespace Entities\Notes\Components\Vue\NotesWidget;

use App\Website\Constructs\Breadcrumb;
use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\VueComponentEntityList;
use Entities\Cards\Components\Vue\CardWidget\ManageCardWidget;
use Entities\Notes\Models\NoteModel;

class ListNotesAdminWidget extends VueComponentEntityList
{
    protected string $id = "5858f194-630b-45f3-9326-a3f6a58368a2";
    protected string $title = "Notes";
    protected string $batchLoadEndpoint = "api/v1/notes/get-note-batches";

    public function __construct(array $components = [])
    {
        $displayColumns = ["banner", "status"];

        global $app;

        if ($app->userAuthentication() && userCan("manage-platforms"))
        {
            $displayColumns[] = "platform";
        }

        $displayColumns = array_merge($displayColumns, ["card_name", "card_num", "card_vanity_url", "card_owner_name", "card_contacts", "product", "created_on", "last_updated"]);

        $defaultEntity = (new NoteModel())
            ->setDefaultSortColumn("card_num", "DESC")
            ->setDisplayColumns($displayColumns)
            ->setFilterColumns(["card_name","card_num","card_vanity_url","card_owner_name","status"])
            ->setRenderColumns(["card_id", "owner_id", "card_owner_name", "card_name", "card_num", "card_vanity_url", "card_keyword", "product", "card_contacts", "status", "order_line_id", "platform", "company_id", "banner", "favicon", "created_on", "last_updated", "sys_row_id",]);

        parent::__construct($defaultEntity, $components);

        $editorComponent = new ManageNotesAdminWidget();
        $editorComponent->addParentId($this->getInstanceId(), ["edit"]);

        $this->addComponentsList($editorComponent->getDynamicComponentsForParent());
        $this->addComponent($editorComponent);

        $this->modalTitleForAddEntity = "View Cards";
        $this->modalTitleForEditEntity = "View Cards";
        $this->modalTitleForDeleteEntity = "View Cards";
        $this->modalTitleForRowEntity = "View Cards";
        $this->setDefaultAction("view");
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
                appCart.openPackagesByClass("card app")
                    .registerEntityListAndManager();
            },
            goToNoteDashboard: function(entity)
            {
                if (typeof this.filterEntityId === null)
                { 
                    modal.EngageFloatShield();
                '. $this->activateRegisteredComponentById(ManageCardWidget::getStaticId(), "edit", true, "entity", "this.mainEntityList", null, "this", "function() { 
                    
                }").'
                }
                else
                {
                    '.$this->activateRegisteredComponentByIdInModal(ManageCardWidget::getStaticId(), "edit", true, "entity", "this.mainEntityList", null, "this", "function() {
                        
                    }").'
                }    
            },
            imageError: function (entity) 
            {
                entity.banner = "/_ez/images/no-image.jpg";
            },
            statusClass: function(status) 
            {
                switch(status)
                {
                    case "active": return "activeStatusInline";
                    case "pending": return "pendingStatusInline";
                    case "disabled": return "disabledStatusInline";
                    case "cancelled": case "canceled": return "canceledStatusInline";
                    default: return "unknownStatusInline";
                }
            },
            renderOnCard: function(cardPageRel) 
            {
                if (typeof cardPageRel === "undefined" || cardPageRel === null)
                {
                    return "Not Assigned";
                }
                return cardPageRel;
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
                        .vue-app-body-component .formwrapper-outer .formwrapper-control .vue-modal-wrapper .fformwrapper-header {
                            top:-9px;
                            position:relative;
                        }
                        .vue-app-body-component .formwrapper-outer .formwrapper-control .vue-modal-wrapper .account-page-title {
                            font-size: 1.5rem;
                            font-weight: 500;
                            top:-5px;
                        }
                        .vue-app-body-component .formwrapper-outer .formwrapper-control .vue-modal-wrapper .account-page-title .componentIconModules {
                            margin-right:5px;
                        }
                        .vue-app-body-component .formwrapper-outer .formwrapper-control .vue-modal-wrapper .account-page-title .componentIconModules:before {
                            content: "\\\f009";
                        }
                        @media (max-width:750px) {
                            .vue-app-body-component .vue-app-body-component .formwrapper-outer .formwrapper-control .vue-modal-wrapper .fformwrapper-header {
                                top:0;
                                position:relative;
                            }
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
                                                    <select id="entity-search-filter" class="form-control" @change="updatePage()">
                                                        <option value="status">Status</option>
                                                        <option value="display_name">Display Name</option>
                                                        <option value="on_card">On Card</option>
                                                        <option value="on_page">On Page</option>
                                                        <option value="last_updated">Last Updated</option>
                                                        <option value="created_on">Created On</option>
                                                        <option value="everything">Everything</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input id="entity-search-input" v-model="searchMainQuery" class="form-control" type="text" placeholder="Search..."/>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" v-on:click="openCartPackageSelection()" style="margin-left: 5px;margin-top: -4px;">Write New Note</button>
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
                            <tr v-if="orderedMainEntityList.length > 0" v-for="mainEntity in orderedMainEntityList" v-on:dblclick="goToNoteDashboard(mainEntity)">
                                '.$this->buildMainEntityDisplayFieldsForTable().'
                                <td class="text-right">
                                    <span v-on:click="goToCardDashboard(mainEntity)" class="pointer editEntityButton"></span>
                                    <span v-on:click="deleteMainEntity(mainEntity)" class="pointer deleteEntityButton"></span>
                                </td>
                            </tr>
                            <tr v-if="orderedMainEntityList.length === 0" v-for="mainEntity in orderedMainEntityList" v-on:dblclick="goToNoteDashboard(mainEntity)">
                                <td colspan="100">
                                    No Records....
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
                case "on_card":
                case "on_page":
                    $columnList .= "<td>{{ renderOnCard(mainEntity.{$currColumn}) }}</td>";
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