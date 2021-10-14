<?php

namespace Entities\Cards\Components\Vue\CardWidget;

use App\website\vue\classes\VueComponentListTable;
use Entities\Cards\Components\Vue\CardPageWidget\ManageCardPageWidget;

class ManageCardPagesListItemWidget extends VueComponentListTable
{
    protected $id = "75b06a2e-0126-4136-a1f0-794780b3a9c5";

    public function __construct(?array $props = [])
    {
        parent::__construct(null, null, $props);
    }

    protected function renderTemplate() : string
    {
        return '
            <tr class="pointer sortable-item" v-on:dblclick="editCardPage(page)" v-bind:class="{\'sortable-item-permanent\':(page.permanent == 1), \'sortable-item-library\':(page.library_tab == 1), \'sortable-item-clone\':(page.card_tab_rel_type == \'mirror\')}">
                <td class="desktop-35px"><span v-handle class="handle"></span></td>
                <td class="desktop-35px mobile-hide" v-if="isModernCard === false">{{ page.rel_sort_order}}</td>
                <td class="desktop-35px mobile-hide"><span v-bind:class="\'tab-type-icon-\' + getCardPageType"></span></td>
                <td>{{ page.title}}</td>
                <td class="mobile-hide">{{ page.card_tab_rel_id }}</td>
                <td class="mobile-hide">{{ page.card_tab_rel_type }}</td>
                <td class="mobile-hide">{{ formatDateForDisplay(page.last_updated) }}</td>
                <td class="text-right">
                    <label class="switch-small">
                        <input name="visibility" type="checkbox" v-model="page.rel_visibility" v-bind:true-value="1" v-bind:false-value="0" v-on:click="updateTabRelVisibility(page)" >
                        <span class="slider round"></span>
                    </label>
                    <span v-on:click="editCardPageColor(page)" class="tab_color_edit_tool" v-bind:style="renderPageColor(page)"></span>
                    <span v-on:click="editCardPage(page)" class="pointer editEntityButton"></span>
                </td>
            </tr>
        ';
    }

    protected function renderComponentComputedValues (): string
    {
        return parent::renderComponentComputedValues() . '
        isModernCard: function()
        {
            let card = (typeof this.$parent.$parent.card !== "undefined" ) ? this.$parent.$parent.card : {};            
            if (typeof card.template_id !== "undefined" && card.template_id > 1) return true;
            return false;
        },
        getCardPageType: function()
        {
            if (typeof this.page.__app !== "undefined") return 4;
            return this.page.card_tab_type_id;
        },
        ';
    }

    protected function renderComponentMethods() : string
    {
        return '
        editCardPage: function(entity)
        {
            let cardPages = (typeof this.$parent.$parent.card !== "undefined" ) ? this.$parent.$parent.card.Tabs : [];
            if (typeof entity.__app === "undefined" || entity.__app === null)
            {
                '. $this->activateDynamicComponentByIdInModal(ManageCardPageWidget::getStaticId(),"", "edit", "entity", "cardPages", [], "this", true ).'
                return;
            }

            this.editMainEntityWithWidget(entity, cardPages);
        },
        editMainEntityWithWidget: function(entity, cardPages)
        {
            let foundComponent = false;
            const modal = this.findModal(this);
            const components = modal.vc.getComponents();
            
            for(let currComponent of components)
            {
                if (entity.__app.app_uuid === currComponent.id)
                {
                    this.loadDynamicModalComponent(currComponent, entity, modal, cardPages);
                    return;
                }
            }
            
            modal.vc.setTitle("Loading...").showModal();
            modal.vc.hideComponents();
            modal.loadModal("edit", this, this.uuidv4(), entity.__app.app_uuid, null, "Loading...", entity, cardPages, {source:"adminCards"}, true);
        },
        loadDynamicModalComponent: function(currComponent, entity, modal, cardPages)
        {
            modal.loadModal("edit", this, currComponent.instanceId, currComponent.id, null, "", entity, cardPages, {source:"adminCards"}, true);
        },
        renderPageColor: function(page)
        {
            return {
                "background-color" : "#" + page.tab_color
            };
        },
        updateTabRelVisibility: function(page)
        {
            let self = this;
            setTimeout(function () 
            {
                let intEntityId = self.$parent.$parent.card.card_id;
                let blnVisibility = page.rel_visibility;
                ajax.Post("/cards/card-data/update-card-data?type=update-tab-rel-visibility&id=" + intEntityId + "&card_tab_id=" + page.card_tab_id + "&card_tab_rel_id=" + page.card_tab_rel_id + "&rel_visibility=" + blnVisibility, null, function (objResult) {
                    //console.log(objResult);
                });
            },500);
        },
        ';
    }
}