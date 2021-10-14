<?php

namespace Entities\Cards\Components\Vue\CardPageWidget;

use App\Website\Vue\Classes\VueComponentEntityList;
use Entities\Cards\Models\CardPageModel;

class ListCardPageWidget extends VueComponentEntityList
{
    protected $id = "1d6d624d-9635-4028-96e9-e0a92e1dadaf";
    protected $title = "Widget Library";
    protected $batchLoadEndpoint = "cards/card-data/get-card-library-tab-batches";

    public function __construct(array $components = [])
    {
        $defaultEntity = (new CardPageModel())
            ->setDefaultSortColumn("card_tab_id", "DESC")
            ->setDisplayColumns(["card_tab_id", "title", "type", "card_count", "created_on", "last_updated"])
            ->setRenderColumns(["card_tab_id", "title", "user_id", "card_count", "type", "created_on", "last_updated", "__app"]);

        parent::__construct($defaultEntity, $components);

        $editorComponent = new ManageCardPageWidget();
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
            addMainEntity: function()
            {
                '. $this->activateRegisteredComponentByIdInModal(ManageCardPageWidget::getStaticId(), "add", false, "{}", "this.mainEntityList").'
            },
            editMainEntity: function(entity)
            {    
                if (typeof entity.__widget === "undefined" || entity.__widget === null)
                {
                    '. $this->activateRegisteredComponentByIdInModal(ManageCardPageWidget::getStaticId(), "edit", false,"entity", "this.mainEntityList" ).'
                    return;
                }

                this.editMainEntityWithWidget(entity);
            },
            editMainEntityWithWidget: function(entity)
            {
                let foundComponent = false;
                const modal = this.findModal(this);
                const components = modal.vc.getComponents();
                
                for(let currComponent of components)
                {
                    if (entity.__widget.app_uuid === currComponent.id)
                    {
                        this.loadDynamicModalComponent(currComponent, entity, modal);
                        return;
                    }
                }
                
                modal.vc.setTitle("Loading...").showModal();
                modal.vc.hideComponents();
                modal.loadModal("edit", this, this.uuidv4(), entity.__widget.app_uuid, null, "Loading...", entity, this.mainEntityList, null, true);
            },
            loadDynamicModalComponent: function(currComponent, entity, modal)
            {
                modal.loadModal("edit", this, currComponent.instanceId, currComponent.id, null, "", entity, this.mainEntityList, null, true);
            },
        ';
    }

    protected function renderTemplate() : string
    {
        return '
        <div class="formwrapper-control" v-cloak>
                <div class="fformwrapper-header">
                    <table class="table header-table" style="margin-bottom:0px;">
                        <tbody>
                        <tr>
                            <td>
                                <h3 class="account-page-title">{{ component_title }} <span class="pointer addNewEntityButton entityButtonFixInTitle"  v-on:click="addMainEntity()" ></span></h3>
                                <div class="form-search-box" v-cloak>
                                    <table>
                                    <tr>
                                        <td>
                                            <input v-model="searchMainQuery" class="form-control" type="text" placeholder="Search..."/>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" v-on:click="openCartPackageSelection()" style="margin-left: 5px;margin-top: -4px;">Purchase New Card</button>
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
                                <span v-on:click="deleteMainEntity(connection)"  v-if="(mainEntity.installed_count == 0)" class="pointer deleteEntityButton"></span>
                                <span v-if="(mainEntity.installed_count == 1)" style="opacity:.3;" class="pointer deleteEntityButton"></span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        ';
    }
}