<?php

namespace Entities\Companies\Components\Vue\PlatformWidget;

use App\Website\Vue\Classes\VueComponentEntityList;
use Entities\Companies\Models\CompanyModel;

class ListPlatformWidget extends VueComponentEntityList
{
    protected string $id = "8c1725bb-5b25-4743-9cb7-d074a49313b4";
    protected string $title = "Custom Platforms";
    protected string $batchLoadEndpoint = "companies/get-platform-batches";
    protected $uriPath = "root";

    public function __construct(array $components = [])
    {
        $defaultEntity = (new CompanyModel())
            ->setDefaultSortColumn("company_id", "ASC")
            ->setDisplayColumns(["platform", "company_name","status",  "portal_domain", "public_domain", "owner", "cards", "state", "country", "created_on", "last_updated"])
            ->setRenderColumns(["company_id","platform", "company_name", "status", "portal_domain", "public_domain", "owner", "cards", "state", "country", "created_on", "last_updated", "sys_row_id"]);

        parent::__construct($defaultEntity, $components);

        $editorComponent = new ManagePlatformWidget();
        $editorComponent->addParentId($this->getInstanceId(), ["edit"]);
        $this->addComponentsList($editorComponent->getDynamicComponentsForParent());
        $this->addComponent($editorComponent);

        $this->modalTitleForAddEntity = "View " . $this->title;
        $this->modalTitleForEditEntity = "View " . $this->title;
        $this->modalTitleForDeleteEntity = "View " . $this->title;
        $this->modalTitleForRowEntity = "View " . $this->title;
        $this->setDefaultAction("view");
    }

    protected function renderComponentMethods() : string
    {
        return parent::renderComponentMethods() . '
            addNewCardToCart: function()
            {
                console.log("adding to cart");
            },
            goToPlatformDashboard: function(entity)
            {
                '. $this->activateRegisteredComponentById(ManagePlatformWidget::getStaticId(), "edit", true, "entity", "this.mainEntityList" ).'           
            },
            imageError: function (entity) {
                entity.banner = "/_ez/images/no-image.jpg";
            },
            statusClass: function(status) {
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
        return '<div class="formwrapper-control">
                    <div class="fformwrapper-header">
                        <table class="table header-table" style="margin-bottom:0px;">
                            <tbody>
                            <tr>
                                <td>
                                    <h3 class="account-page-title">{{ component_title }} <span class="pointer addNewEntityButton entityButtonFixInTitle" v-on:click="addNewCardToCart()" ></span></h3>
                                    <div class="form-search-box" v-cloak>
                                        <input v-model="searchMainQuery" class="form-control" type="text" placeholder="Search..."/>
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
                            <tr v-for="mainEntity in orderedMainEntityList" v-on:dblclick="goToPlatformDashboard(mainEntity)">
                                '.$this->buildMainEntityDisplayFieldsForTable().'
                                <td class="text-right">
                                    <span v-on:click="goToPlatformDashboard(mainEntity)" class="pointer editEntityButton"></span>
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
        global $app;
        $columnList = "";

        foreach( $this->entity->getDisplayColumns() as $currColumn)
        {
            switch($currColumn)
            {
                case "banner":
                    $columnList .= '<td><img v-bind:src="mainEntity.' . $currColumn . '" width="75" height="75" class="main-list-image" @error="imageError(mainEntity)"/></td>';
                    break;
                case "status":
                    $columnList .= '<td><span v-bind:class="statusClass(mainEntity.' . $currColumn . ')">{{ mainEntity.' . $currColumn . ' }}</span></td>';
                    break;
                case "card_num":
                case "card_vanity_url":
                    $columnList .= '<td><a target="_blank" v-bind:href="\''.$app->objCustomPlatform->getFullPortalDomainName().'/\' + mainEntity.' . $currColumn . '">{{ mainEntity.' . $currColumn . ' }}</a></td>';
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