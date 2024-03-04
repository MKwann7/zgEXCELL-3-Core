<?php

namespace Entities\Cards\Components\Vue\CardWidget;

use App\Website\Vue\Classes\VueComponentList;
use App\website\vue\classes\VueComponentSortableList;
use App\Website\Vue\Classes\VueProps;
use Entities\Cards\Models\CardModel;

class ManageCardSocialMediaWidget extends VueComponentList
{
    protected string $id = "69c9e6f4-89ac-4f02-b71e-a60166c54018";
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

        $this->modalTitleForAddEntity = "Add Card Social Media";
        $this->modalTitleForEditEntity = "Edit Card Social Media";
        $this->modalTitleForDeleteEntity = "Delete Card Social Media";
        $this->modalTitleForRowEntity = "View Card Social Media";
    }

    protected function renderComponentMethods() : string
    {
        return '
            onSortEnd: function($event)
            {
                this.arrayMove(this.card.SocialMedia, $event.oldIndex, $event.newIndex); 
                
                let reOrderedConnections = [];
                for(i = 0; i < this.card.SocialMedia.length; i++)
                {
                    reOrderedConnections[i] = {};
                    reOrderedConnections[i].connection_rel_id = this.card.SocialMedia[i].connection_rel_id;
                    reOrderedConnections[i].display_order = (i + 1 );
                    this.card.SocialMedia[i].display_order = (i + 1 );
                }

                let objConnectionsUpdate = {connections: btoa(JSON.stringify(reOrderedConnections))};
                let intCardId = this.card.card_id;

                ajax.Post("cards/card-data/update-card-data?type=reorder-social-media&id=" + intCardId, objConnectionsUpdate, function(result) {
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
                    let el = document.getElementById("myBindReloadManageSocialMediaWidget");
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
            connectionTypeList: [],
            createNew: false,
            editConnection: false,
            swapConnection: false,
            deleteConnection: false,
            rowType: "socialmedia",
        ';
    }

    protected function renderComponentComputedValues() : string
    {
        return 'cardSocialMedia: function()
                {
                    if (typeof this.card !== "undefined") { return this.card.SocialMedia; }
                    return null;
                },';
    }

    protected function renderComponentHydrationScript() : string
    {
        return '
            
        ';
    }

    protected function renderTemplate() : string
    {
        return '
            <div class="entityDetailsInner">
                <button v-show="false" id="myBindReloadManageSocialMediaWidget" v-on:click="reBind()"></button>
                <div v-if="typeof cardSocialMedia !== \'undefined\' && cardSocialMedia !== null">
                    <component :is="dyn'.str_replace("-", "", $this->sortableList->getInstanceId()).'Component" lockAxis="y" :useDragHandle="true" helperClass="slickDraggingItem" @sort-end="onSortEnd($event)">
                        <component :is="dyn'.str_replace("-", "", $this->entityTable->getInstanceId()).'Component" v-for="(currSocialMedia, index) in cardSocialMedia" :index="index" :key="index" :connection="currSocialMedia" :connectionlist="connectionTypeList" :rowType="rowType" :createNew="createNew" :editConnection="editConnection" :swapConnection="swapConnection" :deleteConnection="deleteConnection"  :entityList="cardSocialMedia"/>
                    </component>
                </div>
                <div v-if="typeof cardSocialMedia === \'undefined\' || cardSocialMedia === null">
                    <table class="table table-striped no-top-border table-shadow">
                        <tbody>
                            <tr>
                                <td style="text-align:center;">No Social Media Links....</td>    
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>';
    }
}