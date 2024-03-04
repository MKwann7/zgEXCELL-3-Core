<?php

namespace Entities\Cards\Components\Vue\CardWidget;

use App\Website\Vue\Classes\VueComponentList;
use App\Website\Vue\Classes\VueComponentListTable;
use App\website\vue\classes\VueComponentSortableList;
use App\Website\Vue\Classes\VueProps;
use Entities\Cards\Components\Vue\CardPageWidget\ManageCardPageWidget;
use Entities\Cards\Models\CardPageModel;

class ManageCardPagesWidget extends VueComponentList
{
    protected string $id = "524811d0-486a-4e7d-b2d8-b81cab8efa51";
    protected string $modalWidth = "850";
    protected string $mountType = "default";

    public function __construct ($props = [], ?VueComponentListTable $listTable = null, ?VueComponentSortableList $sortableList = null)
    {
        $manageCardPageListItemWidget = new ManageCardPagesListItemWidget([
            new VueProps("pageDisplayMultiStyle", "object", "pageDisplayMultiStyle"),
            new VueProps("parentComponentInstanceId", "object", "parentComponentInstanceId"),
            new VueProps("page", "object", "page"),
            new VueProps("key", "string", "key"),
            new VueProps("card", "object", "card"),
        ]);

        $manageCardPageSortableWidget = new VueComponentSortableList();

        parent::__construct(new CardPageModel(), $manageCardPageListItemWidget, $manageCardPageSortableWidget, $props);

        $this->modalTitleForAddEntity = "Add Page List";
        $this->modalTitleForEditEntity = "Edit Page List";
        $this->modalTitleForDeleteEntity = "Delete Page List";
        $this->modalTitleForRowEntity = "View Page List";
    }

    protected function renderComponentHydrationScript(): string
    {
        return '
            if (!this.card && this.entity) {
                this.card = this.entity
            }
            
        '.parent::renderComponentHydrationScript();
    }

    protected function renderComponentMethods() : string
    {
        return '
            onSortEnd: function($event)
            {
                if (typeof this.card === "undefined") { return; }
                
                this.arrayMove(this.card.Tabs, $event.oldIndex, $event.newIndex); 
                
                let reOrderedTabs = [];
                for(i = 0; i < this.card.Tabs.length; i++)
                {
                    reOrderedTabs[i] = {};
                    reOrderedTabs[i].card_tab_rel_id = this.card.Tabs[i].card_tab_rel_id;
                    reOrderedTabs[i].rel_sort_order = (i + 1 );
                    this.card.Tabs[i].rel_sort_order = (i + 1 );
                }

                let objTabUpdate = {tabs: btoa(JSON.stringify(reOrderedTabs))};
                let intCardId = this.card.card_id;

                ajax.Post("cards/card-data/update-card-data?type=reorder-tabs&id=" + intCardId, objTabUpdate, function(data) {
                    if (data.success == false)
                    {
                        alert("We apologize for the inconvenience, but there was an error updating your tabs. We\'ve recorded this error and will provide a resolution right away.")
                    }
                },"POST");
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
                
                this.dyn'.str_replace("-", "", $this->entityTable->getInstanceId()).'Component = this.dyn'.str_replace("-", "", $this->entityTable->getInstanceId()).';
                this.dyn'.str_replace("-", "", $this->sortableList->getInstanceId()).'Component = this.dyn'.str_replace("-", "", $this->sortableList->getInstanceId()).';
                
                this.$forceUpdate();
            },
            reloadComponent:function()
            {
                setTimeout(function() {
                    let el = document.getElementById("myBindReloadManageCardWidget");
                    if (el !== null) { el.click(); }
                    modal.CloseFloatShield();
                }, 150);
            },
            createAndAssignNewPage: function() {
                let cardPages = this.cardPages;
                let entity = {}
                let instanceId = this.instanceId
                '. $this->activateDynamicComponentByIdInModal(ManageCardPageWidget::getStaticId(),"instanceId", "edit", "entity", "cardPages", [], "this", true ).'
                return;
            },
        ';
    }

    protected function renderComponentComputedValues() : string
    {
        return 'cardPages: function()
            {
                if (typeof this.card !== "undefined" && this.card !== null && typeof this.card.Tabs !== "undefined") { return this.card.Tabs; }
                if (typeof this.entity !== "undefined" && this.entity !== null && typeof this.entity.Tabs !== "undefined") { return this.entity.Tabs; }
                return [];
            },';
    }

    protected function renderTemplate() : string
    {
        return '
            <div class="entityDetailsInner sortableDetails">
                <div v-if="cardPages.length > 0">
                    <button v-show="false" id="myBindReloadManageCardWidget" v-on:click="reBind()"></button>
                    <component :is="dyn' . str_replace("-", "", $this->sortableList->getInstanceId()) . 'Component" lockAxis="y" :useDragHandle="true" @sort-end="onSortEnd($event)">
                        <component :is="dyn' . str_replace("-", "", $this->entityTable->getInstanceId()) . 'Component" v-for="(cardPage, index) in cardPages" :index="index" :key="index" :page="cardPage" :parentComponentInstanceId="instanceId" :pageDisplayMultiStyle="pageDisplayMultiStyle"/>
                    </component>
                    <button v-on:click="createAndAssignNewPage" class="btn btn-primary w-100">Add New Page</button>
                </div>
                <div v-if="cardPages.length === 0">
                    <table class="table table-striped no-top-border table-shadow">
                        <tbody>
                            <tr>
                                <td style="text-align:center;">No Pages....</td>    
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        ';
    }
}