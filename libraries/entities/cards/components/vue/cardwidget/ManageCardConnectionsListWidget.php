<?php

namespace Entities\Cards\Components\Vue\CardWidget;

use App\Website\Vue\Classes\VueComponentList;
use App\website\vue\classes\VueComponentSortableList;
use App\Website\Vue\Classes\VueProps;
use Entities\Cards\Models\CardModel;

class ManageCardConnectionsListWidget extends VueComponentList
{
    protected string $id = "8ab04d1b-6f50-4509-9a8d-964f2f05b886";
    protected string $modalWidth = "750";
    protected string $mountType = "no_mount";

    public function __construct ($props = [])
    {
        $manageCardConnectionsListItemWidget = new ManageCardConnectionsListItemWidget([
            new VueProps("createNew", "boolean", "createNew"),
            new VueProps("connection", "object", "connection"),
            new VueProps("connectionlist", "object", "connectionlist"),
            new VueProps("key", "string", "key"),
            new VueProps("rowType", "string", "rowType"),
        ]);

        $manageCardPageSortableWidget = new VueComponentSortableList();

        $this->addDynamicComponent($manageCardConnectionsListItemWidget, true);
        $this->addDynamicComponent($manageCardPageSortableWidget, true);

        parent::__construct((new CardModel()), $manageCardConnectionsListItemWidget, $manageCardPageSortableWidget, $props);

        $this->modalTitleForAddEntity = "Add Card Connection List";
        $this->modalTitleForEditEntity = "Edit Card Connection List";
        $this->modalTitleForDeleteEntity = "Delete Card Connections List";
        $this->modalTitleForRowEntity = "View Card Connection List";
    }

    protected function renderComponentMethods() : string
    {
        return '
            onSortEnd: function($event,a,b,c)
            {                
                this.arrayMove(this.card.Connections, $event.oldIndex, $event.newIndex); 
                
                let reOrderedConnections = [];
                for(i = 0; i < this.card.Connections.length; i++)
                {
                    reOrderedConnections[i] = {};
                    reOrderedConnections[i].connection_rel_id = this.card.Connections[i].connection_rel_id;
                    reOrderedConnections[i].display_order = (i + 1 );
                    this.card.Connections[i].display_order = (i + 1 );
                }

                let objConnectionsUpdate = {connections: btoa(JSON.stringify(reOrderedConnections))};
                let intCardId = this.card.card_id;

                ajax.Post("cards/card-data/update-card-data?type=reorder-connection&id=" + intCardId, objConnectionsUpdate, function(result) {
                    //console.log(result);
                });
            },
            arrayMove: function(arr, old_index, new_index) 
            {
                if (new_index >= arr.length) {
                    var k = new_index - arr.length + 1;
                    while (k--) {
                        arr.push(undefined);
                    }
                }
                arr.splice(new_index, 0, arr.splice(old_index, 1)[0]);
                return arr; // for testing
            },
            reBind: function()
            {
                let thisData = this.setModalComponentInstance();
                this.dyn'.str_replace("-", "", $this->sortableList->getInstanceId()).'Component = this.dyn'.str_replace("-", "", $this->sortableList->getInstanceId()).';
                this.dyn'.str_replace("-", "", $this->entityTable->getInstanceId()).'Component = this.dyn'.str_replace("-", "", $this->entityTable->getInstanceId()).';
                this.loadConnectionTypeList();
                this.$forceUpdate();              
            },
            reloadComponent:function()
            {
                setTimeout(function() {
                    let el = document.getElementById("myBindReloadManageConnectionsWidget");
                    if (el !== null) { el.click(); }
                    modal.CloseFloatShield();
                }, 100);
            },
            loadConnectionTypeList: function()
            {
                if (this.connectionTypeList.length > 0) return;
                let self = this;
                const url = "/api/v1/users/get-connection-types";
                ajax.Get(url, null, function(result)
                {
                    self.connectionTypeList = result.response.data.list;
                    self.$forceUpdate();
                }, "GET");
            },
        ';
    }

    protected function renderComponentDataAssignments() : string
    {
        return '
            card: null,
            createNew: false,
            editConnection: false,
            swapConnection: false,
            deleteConnection: false,
            createConnection: false,
            connectionTypeList: [],
            rowType: "connections",
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return '
        ';
    }

    protected function renderComponentComputedValues() : string
    {
        return 'cardConnections: function()
                {
                    if (typeof this.card !== "undefined") { return this.card.Connections; }
                    return null;
                },';
    }

    protected function renderTemplate() : string
    {
        // draggedSettlingDuration="100000"
        return '
            <div class="entityDetailsInner">
                <button v-show="false" id="myBindReloadManageConnectionsWidget" v-on:click="reBind()"></button>
                <component :is="dyn'.str_replace("-", "", $this->sortableList->getInstanceId()).'Component" lockAxis="y" :useDragHandle="true" helperClass="slickDraggingItem" @sort-end="onSortEnd($event)">
                    <component :is="dyn'.str_replace("-", "", $this->entityTable->getInstanceId()).'Component" v-for="(currConnection, index) in cardConnections" :index="index" :key="index" :connection="currConnection" :connectionlist="connectionTypeList" :rowType="rowType" :createNew="createNew"  :createConnection="createConnection" :editConnection="editConnection" :swapConnection="swapConnection" :deleteConnection="deleteConnection" :entityList="cardConnections"/>
                </component>
            </div>';
    }
}