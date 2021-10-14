<?php

namespace Entities\Tickets\Components\Vue\TicketsWidget;

use App\Website\Constructs\Breadcrumb;
use App\Website\Vue\Classes\VueComponentEntityList;
use Entities\Cards\Components\Vue\CardWidget\ManageCardWidget;
use Entities\Tickets\Models\TicketModel;

class ListTicketsAdminWidget extends VueComponentEntityList
{
    protected $id = "10415a08-5c90-46a1-a88c-bf737c3aa7d3";
    protected $title = "Tickets";
    protected $batchLoadEndpoint = "api/v1/tickets/get-ticket-batches";
    protected $queueFilterCacheId = "ticket-queue-filter-id";

    public function __construct(array $components = [])
    {
        $defaultEntity = (new TicketModel())
            ->setDefaultSortColumn("card_num", "DESC")
            ->setDisplayColumns($this->buildDisplayColumns())
            ->setFilterColumns($this->buildFilterColumns())
            ->setRenderColumns($this->buildRenderColumns());

        parent::__construct($defaultEntity, $components);

        $this->addBreadcrumb(new Breadcrumb("Admin","/account/admin/", "link"));

        $editorComponent = new ManageTicketsAdminWidget();
        $editorComponent->addParentId($this->getInstanceId(), ["edit"]);

        $this->addComponentsList($editorComponent->getDynamicComponentsForParent());
        $this->addComponent($editorComponent);

        $this->modalTitleForAddEntity = "View Tickets";
        $this->modalTitleForEditEntity = "View Tickets";
        $this->modalTitleForDeleteEntity = "View Tickets";
        $this->modalTitleForRowEntity = "View Tickets";
        $this->setDefaultAction("view");
    }

    protected function buildDisplayColumns(): array
    {
        global $app;
        $displayColumns = ["ticket_id", "status"];

        $displayColumns = array_merge($displayColumns, ["owner", "summary", "department", "type", "parent_ticket_id"]);
        $displayColumns = array_merge($displayColumns, ["duration", "ticket_opened", "expected_completion", "ticket_closed"]);

        return $displayColumns;
    }

    protected function buildFilterColumns(): array
    {
        $filterColumns = ["ticket_id","status","owner","parent_ticket_id","duration"];
        $filterColumns = array_merge($filterColumns, ["duration", "ticket_opened", "expected_completion", "ticket_closed"]);

        return $filterColumns;
    }

    protected function buildRenderColumns(): array
    {
        $filterColumns = ["ticket_id","company_id","company_id", "department", "queue_name", "queue_id", "parent_ticket_id", "assignee_id", "owner", "status", "summary", "description"];

        global $app;
        if ($app->userAuthentication() && userCan("manage-platforms"))
        {
            $filterColumns[] = "platform";
        }

        $filterColumns = array_merge($filterColumns, ["entity_id", "ticket_queue_id",  "journey_id", "type", "ticket_opened", "expected_completion", "ticket_closed", "duration", "created_on", "last_updated", "sys_row_id"]);

        return $filterColumns;
    }

    protected function openCartPackageSelection() : string
    {
        return '
            openCartPackageSelection: function()
            {
                appCart.openPackagesByClass("card")
                    .registerEntityListAndManager();
            },
        ';
    }

    protected function renderComponentDataAssignments() : string
    {
        return parent::renderComponentDataAssignments() . '
            queueLabelList: [],
            filterByQueueId: null,
        ';
    }

    protected function renderComponentHydrationScript (): string
    {
        return parent::renderComponentHydrationScript() . '
            this.filterByQueueId = sessionStorage.getItem(\''.$this->queueFilterCacheId.'\');
        ';
    }

    protected function additionalFilterQueries() : string
    {
        return "objSortedPeople = this.filterByQueue(objSortedPeople);";
    }


    protected function renderComponentMethods() : string
    {
        return parent::renderComponentMethods() .
            $this->openCartPackageSelection() . '
            goToCardDashboard: function(entity)
            {
                if (typeof this.filterEntity === "undefined")
                { 
                    modal.EngageFloatShield();
                '. $this->activateRegisteredComponentById(ManageTicketsAdminWidget::getStaticId(), "edit", true, "entity", "this.mainEntityList", null, "this", "function() { 
                    
                }").'
                }
                else
                {
                    '.$this->activateRegisteredComponentByIdInModal(ManageTicketsAdminWidget::getStaticId(), "edit", true, "entity", "this.mainEntityList", null, "this", "function() {
                        
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
                    case "pending": return "pendingStatusInline";
                    case "queued": return "queuedStatusInline";
                    case "open": return "activeStatusInline";
                    case "closed": return "disabledStatusInline";
                    default: return "unknownStatus";
                }
            },
            renderOwner: function(entity) 
            {
                if (entity.parent_ticket_id === "" && (typeof entity.owner === "undefined" || !entity.owner)) return "Master Ticket";
                if (typeof entity.owner === "undefined" || !entity.owner) return "Unassigned";
                
                return entity.owner;
            },
            renderParentTicketId: function(entity) 
            {
                if (entity.parent_ticket_id === "") return "Root";
                
                return entity.parent_ticket_id;
            },
            renderTicketOpenTime: function(entity) 
            {  
                const start = new Date(entity.ticket_opened).getTime() / 1000;
                const end = new Date();
                
                return this.numDaysBetween(start,end) + " Days";
            },
            numDaysBetween: function(d1, d2) {
              var today = d2.getTime() / 1000
              var diff = Math.abs(d1 - (d2.getTime() / 1000));
              return Math.floor(diff / (60 * 60 * 24));
            },
            filterByQueue: function(objSortedTickets)
            {
                if (typeof this.filterByQueueId === "undefined" || this.filterByQueueId === null || this.filterByQueueId === "" || this.filterByQueueId === "all") { return objSortedTickets; }
                
                ezLog(this.filterByQueueId, "this.filterByQueueId")
                
                const self = this;
                let objFilteredTickets = objSortedTickets.filter(function (currEntity)
                {
                    if (currEntity.queue_id == self.filterByQueueId) {
                        return currEntity;
                    }
                });

                return objFilteredTickets;
            },
            filterOnQueue: function(queueId)
            {
                this.filterByQueueId = queueId;
                sessionStorage.setItem(\''.$this->queueFilterCacheId.'\', this.filterByQueueId);
                this.mainEntityList = this.mainEntityList;
            },
            filterQueueLabelList: function()
            {
                let queueLabels = [];
                let queueLabelName = [];

                for(let currEntity of this.mainEntityList)
                {
                    if (!queueLabelName.includes(currEntity.queue_name) && currEntity.queue_name !== "")
                    {
                        queueLabelName.push(currEntity.queue_name);
                        queueLabels.push({name: currEntity.queue_name, id: currEntity.queue_id});
                    }
                }
                
                return queueLabels;
            },
        ';
    }

    protected function renderBatchMethods() : string
    {
        return '
            processBatchLoop: function(column)
            {
                this.queueLabelList = this.filterQueueLabelList();
                this.$forceUpdate();
            },
            processBatchCompletion: function()
            {
                this.queueLabelList = this.filterQueueLabelList();
                this.$forceUpdate();
            },
        ';
    }

    protected function renderTemplate() : string
    {
        return '<div class="formwrapper-control my-tickets-queue">
                    <v-style type="text/css">
                        .my-tickets-queue .entityFilterOuter {
                            background: #ccc;
                            padding: 5px 20px;
                            border-radius: 5px;
                            border: 1px solid #bbb;
                        }
                        .my-tickets-queue .filterLabel {
                            padding-right: 16px;
                            font-weight: bold;
                            font-size: 12px;
                            line-height: 16px;
                        }
                        .module-wrapper-list {
                            display:flex;
                            flex-direction: row;
                            justify-content: left; 
                            padding: 8px 0;
                        }
                        .module-wrapper-list li {
                            display: flex;
                            align-items: center;
                        }
                        .module-wrapper-list li div {
                            width: 100%;
                            display: block;
                            text-align: center;
                            margin: 0 5px;
                            padding: 10px 20px;
                            border-radius: 4px;
                            cursor:pointer;
                            background:#fff;
                        }
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
                        .vue-app-body-component .formwrapper-outer .formwrapper-control .vue-modal-wrapper .account-page-title .componentIconCards {
                            margin-right:5px;
                        }
                        .vue-app-body-component .formwrapper-outer .formwrapper-control .vue-modal-wrapper .account-page-title .componentIconCards:before {
                            content: "\\\f2c2";
                        }
                        @media (max-width:750px) {
                            .vue-app-body-component .vue-app-body-component .formwrapper-outer .formwrapper-control .vue-modal-wrapper .fformwrapper-header {
                                top:0;
                                position:relative;
                            }
                        }
                    </v-style>
                    <div class="fformwrapper-header">
                        <div class="entityListOuter entityFilterOuter">
                            <div class="width100 entityDetails">
                                <div class="card-tile-100">
                                    <ul class="module-wrapper-list">
                                        <li class="filterLabel">QUEUE<br>FILTER</li>
                                        <li v-if="queueLabelList.length > 1" v-on:click="filterOnQueue(\'all\')"><div><i class="fas fa-globe"></i> All</div></li>
                                        <li v-for="currCount in queueLabelList" v-on:click="filterOnQueue(currCount.id)"><div><i class="fas fa-sign"></i> {{ currCount.name }}</div></li>
                                    </ul>
                                </div>
                            </div>
                        </div>                    
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
                                                        <option value="ticket_id">Ticket Id</option>
                                                        <option value="status">Status</option>
                                                        <option value="owner">Owner Name</option>
                                                        <option value="type">Type</option>
                                                        <option value="parent_ticket_id">Parent Id</option>
                                                        <option value="expected_completion">Expecated Completion</option>
                                                        <option value="last_updated">Last Updated</option>
                                                        <option value="created_on">Created On</option>
                                                        <option value="everything">Everything</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input id="entity-search-input" v-model="searchMainQuery" class="form-control" type="text" placeholder="Search..."/>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" v-on:click="openCartPackageSelection()" style="margin-left: 5px;margin-top: -4px;">Open New Ticket</button>
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
                    $columnList .= '<td class="statusColumn"><span v-bind:class="statusClass(mainEntity.' . $currColumn . ')">{{ ucwords(mainEntity.' . $currColumn . ') }}</span></td>';
                    break;
                case "type":
                    $columnList .= '<td><span>{{ ucwords(mainEntity.' . $currColumn . ') }}</span></td>';
                    break;
                case "parent_ticket_id":
                    $columnList .= '<td>{{ renderParentTicketId(mainEntity) }}</td>';
                    break;
                case "duration":
                    $columnList .= '<td>{{ renderTicketOpenTime(mainEntity) }}</td>';
                    break;
                case "owner":
                    $columnList .= '<td>{{ renderOwner(mainEntity) }}</td>';
                    break;
                case "card_num":
                case "card_vanity_url":
                    $columnList .= '<td><a target="_blank" v-bind:href="\''.$app->objCustomPlatform->getFullPublicDomain().'/\' + mainEntity.' . $currColumn . '">{{ mainEntity.' . $currColumn . ' }}</a></td>';
                    break;
                case "ticket_opened":
                case "expected_completion":
                case "ticket_closed":
                case "created_on":
                case "last_updated":
                    $columnList .= '<td>{{ formatDateForDisplay(mainEntity.' . $currColumn . ', "Pending") }}</td>';
                    break;
                default:
                    $columnList .= "<td>{{ mainEntity.{$currColumn} }}</td>";
                    break;
            }

        }
        return $columnList;
    }
}