<?php

namespace Entities\Cards\Components\Vue\CardWidget;

use App\website\vue\classes\VueComponentListTable;
use App\Website\Vue\Classes\VueProps;
use Entities\Users\Components\Vue\ConnectionWidget\ManageUserConnectionsWidget;

class ManageCardConnectionsListItemWidget extends VueComponentListTable
{
    protected string $id = "342ff244-4185-4ddc-9b7f-720783cdcf16";
    protected string $mountType = "no_mount";

    public function __construct(?array $props = [])
    {
        $defaultProps = [new VueProps("editConnection", "boolean", "editConnection"),
            new VueProps("swapConnection", "boolean", "swapConnection"),
            new VueProps("deleteConnection", "boolean", "deleteConnection"),
            new VueProps("createConnection", "boolean", "createConnection"),
            new VueProps("entityList", "object", "entityList")];
        parent::__construct(null, null, array_merge($defaultProps, $props));
    }

    protected function renderTemplate() : string
    {
        return '
            <tr class="cardConnectionRel pointer sortable-item" v-on:dblclick="editConnectionRelAction(connection)">
                <v-style>
                    .cardConnectionRel .cardConnectionHandle,
                    .cardConnectionRel .cardConnectionLabel,
                    .cardConnectionRel .cardConnectionType,
                    .cardConnectionRel .cardConnectionOrder {
                        width: 15px;
                    }
                </v-style>
                <td class="cardConnectionHandle"><span v-handle class="handle"></span></td>
                <td class="cardConnectionOrder mobile-hide">{{ connection.display_order }}</td>
                <td class="cardConnectionType" style="width:35px;text-align:center;" v-bind:alt="connection.connection_type_name" v-bind:title="connection.connection_type_name"><span v-bind:class="connection.font_awesome"></span></td>
                <td class="cardConnectionLabel mobile-hide">{{ connection.connection_type_name }}</td>
                <td class="cardConnectionLabel mobile-hide"><b>{{ displayAction(connection.action) }}</b></td>
                <td><strong class="entityEmailName">{{ trunc(connection.connection_value, 35, true) }}</strong></td>
                <td class="text-right">
                    <span v-if="createConnection === true" v-on:click="editConnectionRelAction(connection)" class="pointer addNewEntityButton"></span>
                    <span v-if="editConnection === true" v-on:click="editConnectionRelAction(connection)" class="pointer editEntityButton"></span>
                    <span v-if="swapConnection === true" v-on:click="editConnectionRelAction(connection)" class="pointer swapEntityButton fas fa-retweet"></span>
                    <span v-if="deleteConnection === true" v-bind:class="{ disabledButton: connection.connection_type_name == \'blank\' }" v-on:click="removeConnection(connection)" class="pointer deleteEntityButton" style="margin-left:6px;"></span>
                </td>
            </tr>
        ';
    }

    protected function renderComponentDataAssignments() : string
    {
        return parent::renderComponentDataAssignments() . '
            entityList: [],
            editConnection: false,
            swapConnection: false,
            deleteConnection: false,
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return '
            let self = this;
        ';
    }

    protected function renderComponentMethods() : string
    {
        return '
        displayAction: function(action)
        {
            if (action === null || typeof action === "undefined") { return "none"; }
            
            return action;
        },
        editConnectionAction: function(entity)
        {   
            if (entity.connection_type_name === "blank") { return; }
            let cardConnections = this.$parent.$parent.card.Connections;
            '. $this->activateDynamicComponentByIdInModal(ManageUserConnectionsWidget::getStaticId(),"", "edit", "entity", "cardConnections", [], "this", true ).'
        },
        editConnectionRelAction: function(entity)
        {
            let self = this;
            let cardConnections = this.$parent.$parent.card.Connections;
            let ownerId = this.$parent.$parent.card.owner_id;
            let connectionlist = this.connectionlist;
            let swapType = this.rowType;
            let createNew = (this.createNew === true ? true : false);
            if (this.createConnection === true) createNew = true;
            
            '. $this->activateDynamicComponentByIdInModal(SwapCardConnectionWidget::getStaticId(),"", "edit", "entity", "cardConnections", ["ownerId"=> "ownerId", "connectionList" => "connectionlist", "swapType" => "swapType", "functionType" =>"'update'", "createNew" =>"createNew"], "this", true,"function(component) {
                    let modal = self.findModal(self);
                    let modalAction = 'Swap';
                    if (createNew === true) { modalAction = 'Customize'; }
                    if (swapType === \"socialmedia\") modal.vc.setTitle(modalAction + ' Social Media Link');
                    if (swapType === \"shares\") modal.vc.setTitle(modalAction + ' Share Button Link');
                }") . '
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
            modal.EngageFloatShield();
            let intEntityId = self.$parent.$parent.card.card_id;
        
            if (this.rowType === "socialmedia")
            {
                let data = {title: "Unlink Social Media?", html: "Are you sure you want to disconnect this?<br>Please confirm."};
                modal.EngagePopUpConfirmation(data, function() 
                {
                    let socialMediaId = connection.card_socialmedia_id;
                    let intConnectionId = connection.connection_id;
                    let intCardId = intEntityId;
                    
                    ajax.Post("cards/card-data/update-card-data?type=remove-social-media&id=" + intCardId + "&connection_id=" + intConnectionId + "&card_socialmedia_id=" + socialMediaId, null, null);
                    
                    for(let currSocialMediaIndex in self.entityList)
                    {
                        if (self.entityList[currSocialMediaIndex].card_socialmedia_id === connection.card_socialmedia_id)
                        {
                            self.entityList.splice(currSocialMediaIndex, 1);
                        }
                    }
                    
                    modal.CloseFloatShield(function() {
                        modal.CloseFloatShield();
                    });
                }, 400, 115);
            }
            else
            {
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
            }
        },
        ';
    }
}