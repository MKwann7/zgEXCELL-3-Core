<?php

namespace Entities\Cards\Components\Vue\CardWidget;

use App\Website\Vue\Classes\Base\VueComponent;
use App\website\vue\classes\VueComponentListTable;
use App\website\vue\classes\VueComponentSortableList;
use Entities\Cards\Models\CardModel;
use Entities\Users\Components\Vue\ConnectionWidget\ManageUserConnectionsWidget;

class ManageCardCommunicationWidget extends VueComponent
{
    protected string $id = "8c7cb199-b362-425c-9e2c-080f113b7439";
    protected string $modalWidth = "750";
    protected string $mountType = "no_mount";

    public function __construct ($props = [], ?VueComponentListTable $listTable = null, ?VueComponentSortableList $sortableList = null)
    {
        parent::__construct((new CardModel()), $listTable, $sortableList, $props);

        $this->modalTitleForAddEntity = "Add Card Communication";
        $this->modalTitleForEditEntity = "Edit Card Communication";
        $this->modalTitleForDeleteEntity = "Delete Card Communication";
        $this->modalTitleForRowEntity = "View Card Communication";
    }

    protected function renderComponentDataAssignments() : string
    {
        return '
            card: null,
            shareTypeList: [],
            createConnection: false,
            editConnection: false,
            swapConnection: false,
            deleteConnection: false,
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

    protected function renderComponentHydrationScript() : string
    {
        return parent::renderComponentHydrationScript() . '
            this.loadShareTypeList();
        ';
    }

    protected function renderTemplate() : string
    {
        return '
            <div class="entityDetailsInner entityListActionColumn">
                <v-style>
                    .cardConnectionRel .cardConnectionHandle,
                    .cardConnectionRel .cardConnectionLabel,
                    .cardConnectionRel .cardConnectionType,
                    .cardConnectionRel .cardConnectionOrder {
                        width: 15px;
                    }
                </v-style>
                <table class="table table-striped no-top-border table-shadow" v-cloak>
                    <tbody>
                        <tr v-for="(connection, index) in cardConnections" class="cardConnectionRel pointer sortable-item" v-on:dblclick="editConnectionRel(connection)">
                            <td class="cardConnectionOrder mobile-hide">{{ connection.display_order }}</td>
                            <td class="cardConnectionType" style="width:35px;text-align:center;" v-bind:alt="connection.connection_type_name" v-bind:title="connection.connection_type_name"><span v-bind:class="connection.font_awesome"></span></td>
                            <td class="cardConnectionLabel mobile-hide">{{ connection.connection_type_name }}</td>
                            <td class="cardConnectionLabel mobile-hide"><b>{{ displayAction(connection.action) }}</b></td>
                            <td><strong class="entityEmailName">{{ trunc(connection.connection_value, 35, true) }}</strong></td>
                            <td class="text-right">
                                <span style="display:none;" v-bind:class="{ disabledButton: connection.connection_type_name == \'blank\' }" v-on:click="editConnection(connection)" class="pointer editEntityButton"></span>
                                <span v-on:click="editConnectionRel(connection)" class="pointer swapEntityButton fas fa-retweet"></span>
                                <span v-bind:class="{ disabledButton: connection.connection_type_name == \'blank\' }" v-on:click="removeConnection(connection)" class="pointer deleteEntityButton" style="margin-left:6px;"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>';
    }

    protected function renderComponentMethods() : string
    {
        return '
        displayConnectionType: function(id)
        {            
            for (let currConnectionType of this.connectionlist)
            {
                if (currConnectionType.connection_type_id === id)
                {
                    return currConnectionType.name;
                }
            }
            
            return "Unknown";
        },
        displayAction: function(action)
        {
            if (action === null || typeof action === "undefined") { return "none"; }
            
            return action;
        },
        editConnection: function(entity)
        {   
            if (entity.connection_type_name === "blank") { return; }
            let cardConnections = this.$parent.$parent.card.Connections;
            '. $this->activateDynamicComponentByIdInModal(ManageUserConnectionsWidget::getStaticId(),"", "edit", "entity", "cardConnections", [], "this", true ).'
        },
        editConnectionRel: function(entity)
        {   
            let self = this;
            
            this.loadShareTypeList(function() 
            {
                let cardConnections = self.$parent.entity.Connections;
                let ownerId = self.$parent.entity.owner_id;
                let connectionList = self.shareTypeList;
                let swapType = "shares";
                let createNew = true;
 
                '. $this->activateDynamicComponentByIdInModal(SwapCardConnectionWidget::getStaticId(),"", "edit", "entity", "cardConnections", ["ownerId"=> "ownerId", "connectionList" => "connectionList", "swapType" => "swapType", "functionType" =>"'update'", "createNew" => "createNew"], "self", true,"function(component) {
                        let modal = self.findModal(self);
                        modal.vc.setTitle('Swap Share Button Link');
                }") . '
            });
        },
        updateTabRelVisibility: function(tab)
        {
            let self = this;
            setTimeout(function () 
            {
                let intEntityId = self.$parent.$parent.card.card_id;
                let blnVisibility = tab.rel_visibility;
                ajax.Post("/cards/card-data/update-card-data?type=update-tab-rel-visibility&id=" + intEntityId + "&card_tab_id=" + tab.card_tab_id + "&card_tab_rel_id=" + tab.card_tab_rel_id + "&rel_visibility=" + blnVisibility, null, function (objResult) {
                    //console.log(objResult);
                });
            },500);
        },
        removeConnection: function(connection)
        {
            if (connection.connection_type_name === "blank") { return; }
            let self = this;
            let intEntityId = self.$parent.entity.card_id;
            modal.EngageFloatShield();
            let data = {title: "Remove Card Connection?", html: "Are you sure you want to proceed?<br>Please confirm."};
            modal.EngagePopUpConfirmation(data, function() 
            {
                let intConnectionRelId = connection.connection_rel_id;
                let intConnectionId = connection.connection_id;
                let intCardId = intEntityId;
                ajax.Post("cards/card-data/update-card-data?type=remove-connection&id=" + intCardId + "&connection_id=" + intConnectionId + "&connection_rel_id=" + intConnectionRelId);
                connection.connection_rel_id = null;
                connection.connection_id = null;
                connection.connection_type_id = 0;
                connection.connection_type_name = "blank";
                connection.font_awesome = "fas fa-question";
                connection.connection_value = null;
                connection.action = null;
                connection.is_primary = null;
                connection.status = null;
                modal.CloseFloatShield(function() {
                    modal.CloseFloatShield();
                });
            }, 400, 115);
        },
        loadShareTypeList: function(callback)
        {
            if (this.shareTypeList.length > 0) {
                if (typeof callback === "function") callback();
            }
            let self = this;
            const url = "/api/v1/users/get-connection-types";
            ajax.Get(url, null, function(result)
            {
                self.shareTypeList = result.response.data.list;
                self.$forceUpdate();
                if (typeof callback === "function") callback();
            });
        },
        ';
    }
}