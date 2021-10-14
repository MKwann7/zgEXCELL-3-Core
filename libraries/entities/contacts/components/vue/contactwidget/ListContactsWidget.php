<?php

namespace Entities\Contacts\Components\Vue\ContactWidget;

use App\Website\Vue\Classes\VueComponentEntityList;
use App\Website\Vue\Classes\VueProps;
use Entities\Cards\Components\Vue\CardWidget\ManageCardWidget;
use Entities\Modules\Components\Vue\AppsWidget\ManageAppsWidget;
use Entities\Modules\Models\AppInstanceRelModel;

class ListContactsWidget extends VueComponentEntityList
{
    protected $id = "679a7c38-29b8-4643-918b-1b220f912104";
    protected $title = "Contacts";
    protected $batchLoadEndpoint = "api/v1/contacts/get-contact-batches";

    public function __construct(array $components = [])
    {
        $defaultEntity = (new AppInstanceRelModel())
            ->setDefaultSortColumn("app_instance_rel_id", "DESC")
            ->setDisplayColumns(["id","status", "display_name", "on_card", "on_page", "created_on", "last_updated"])
            ->setRenderColumns(["id","status", "display_name", "on_card", "on_page", "created_on", "last_updated"]);

        parent::__construct($defaultEntity, $components);

        $filterEntity = new VueProps("filterEntityId", "object", "filterEntityId");
        $filterByEntityValue = new VueProps("filterByEntityValue", "boolean", "filterByEntityValue");
        $filterByEntityRefresh = new VueProps("filterByEntityRefresh", "boolean", true);

        $this->addProp($filterEntity);
        $this->addProp($filterByEntityValue);
        $this->addProp($filterByEntityRefresh);

        $editorComponent = new ManageAppsWidget();
        $editorComponent->addParentId($this->getInstanceId(), ["edit"]);
        $this->addComponent($editorComponent);

        $this->modalTitleForAddEntity = "Widget Library";
        $this->modalTitleForEditEntity = "Widget Library";
        $this->modalTitleForDeleteEntity = "Widget Library";
        $this->modalTitleForRowEntity = "Widget Library";
        $this->setDefaultAction("view");
    }

    protected function renderComponentMethods() : string
    {
        return parent::renderComponentMethods() . '
            openCartPackageSelection: function()
            {
                appCart.openPackagesByClass("card app")
                    .registerEntityListAndManager();
            },
            goToCardDashboard: function(entity)
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
                          user  <tbody>
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
                                                    <button class="btn btn-sm btn-primary" v-on:click="openCartPackageSelection()" style="margin-left: 5px;margin-top: -4px;">Purchase Card App</button>
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
                            <tr v-if="orderedMainEntityList.length > 0" v-for="mainEntity in orderedMainEntityList" v-on:dblclick="goToCardDashboard(mainEntity)">
                                '.$this->buildMainEntityDisplayFieldsForTable().'
                                <td class="text-right">
                                    <span v-on:click="goToCardDashboard(mainEntity)" class="pointer editEntityButton"></span>
                                    <span v-on:click="deleteMainEntity(mainEntity)" class="pointer deleteEntityButton"></span>
                                </td>
                            </tr>
                            <tr v-if="orderedMainEntityList.length === 0" v-for="mainEntity in orderedMainEntityList" v-on:dblclick="goToCardDashboard(mainEntity)">
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