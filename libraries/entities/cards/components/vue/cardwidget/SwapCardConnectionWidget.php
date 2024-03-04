<?php

namespace Entities\Cards\Components\Vue\CardWidget;

use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cards\Models\CardModel;

class SwapCardConnectionWidget extends VueComponent
{
    protected string $id = "b02738ce-20b8-4690-9704-8604f4e78251";
    protected string $modalWidth = "750";

    public function __construct (array $components = [])
    {
        parent::__construct((new CardModel()), $components);

        $this->modalTitleForAddEntity = "Add Card Connection";
        $this->modalTitleForEditEntity = "Swap Card Connection";
        $this->modalTitleForDeleteEntity = "Delete Card Rel Connections";
        $this->modalTitleForRowEntity = "View Card Rel Connection";
    }

    protected function renderComponentDataAssignments() : string
    {
        return '
            functionType: "swap",
            swapType: "connections",
            actionButton: "Swap Connection",
            userConnectionList: [],
            connectionTypeList: [],
            ownerId: null,
            dynamicSearch: false,
            entityClone : null,
            entityNew : {},
            customerList: [],
            connectionSearch: "",
            connectionSearchResult: "",
            connectionSearchHighlight: 0,
            searchBox: 0,
            searchBoxInner: 0,
            totalSearchDisplayCount: 0,
            createNew: false,
        ';
    }

    protected function renderComponentMethods() : string
    {
        return '
            loadConnections: function(type)
            {
                switch(type)
                {
                    case "connections": this.loadConnectionsForUser(); return;
                    case "shares": this.loadSharesForUser(); return;
                    case "socialmedia": this.loadSocialsForUser(); return;
                }
            },
            getDisplayType: function()
            {
                switch(this.swapType)
                {
                    case "connections": return "Connection";
                    case "shares": return "Share";
                    case "socialmedia": return "Social Media";
                }
            },
            loadConnectionTypeList: function()
            {
                if (this.connectionTypeList.length > 0) return;
                
                let self = this;
                const url = "/api/v1/users/get-connection-types";
                ajax.Get(url, null, function(result)
                {
                    self.connectionTypeList = result.response.data.list;
                }, "GET");
            },
            showCreateNewCard: function()
            {
                slideUp(elm("assignConnection"),250, function() {
                    slideDown(elm("createNewConnection"));  
                });
            },
            backToAssignment: function()
            {
                slideUp(elm("createNewConnection"),250, function() {
                    slideDown(elm("assignConnection"));  
                });
            },
            loadConnectionsForUser: function()
            {        
                this.actionButton = this.ucwords(this.functionType) + " Connection";                       
                let self = this;
                const url = "/api/v1/users/get-available-connections-for-card?card_id=" + this.entity.card_id + "&owner_id=" + this.ownerId;
                ajax.Get(url, null, function(result)
                {   
                    if (result.response.data.success === false) { return; }                                        
                    self.userConnectionList = result.response.data.list;
                });
            },
            loadSharesForUser: function()
            {
                this.actionButton = this.ucwords(this.functionType) + " Share Button";                           
                let self = this;
                const url = "/api/v1/users/get-available-shares-for-card?card_id=" + this.entity.card_id + "&owner_id=" + this.ownerId;
                ajax.Get(url, null, function(result)
                {   
                    if (result.response.data.success === false) { return; }                                        
                    self.userConnectionList = result.response.data.list;
                });
            },
            loadSocialsForUser: function()
            {       
                this.actionButton = this.ucwords(this.functionType) + " Social Media";                           
                let self = this;
                const url = "/api/v1/users/get-available-socials-for-card?card_id=" + this.entity.card_id + "&owner_id=" + this.ownerId; 
               
                ezLog(url, "loadSocialsForUser.url");
                         
                ajax.Get(url, null, function(result)
                {   
                    if (result.response.data.success === false) { return; }                                        
                    self.userConnectionList = result.response.data.list;
                });
            },
            engageDynamicSearch: function(user)
            {
                this.dynamicSearch = true;
            },
            toggleDynamicSearch: function(user)
            {
                this.dynamicSearch = !this.dynamicSearch;
            },
            hideDynamicSearch: function()
            {
                if (this.connectionSearch === "" && this.connectionSearchResult === "") { this.updateActionSelection(); }
                const self = this;
                setTimeout(function() {
                    if (self.connectionSearchResult === "") {
                        self.dynamicSearch = false;
                    }
                }, 100);
            },
            keyMonitorConnectionsList: function(event)
            {
                if (this.connectionSearch === "" && this.connectionSearchResult === "") { this.updateActionSelection(); }
                
                switch(event.keyCode)
                {
                    case 38:
                        this.decreaseConnectionSearchHighlight();
                        break;
                    case 40:
                        this.increaseConnectionSearchHighlight();
                        break;
                    case 13:
                        let connectionByIndex = this.getConnectionByIndex(this.connectionSearchHighlight);
                        this.assignConnection(connectionByIndex, this.connectionSearchHighlight);
                        break;
                    default:
                        this.connectionSearchHighlight = 0;
                        break;
                }
                
                this.userConnectionList = this.userConnectionList;
                this.$forceUpdate();
            },
            getMiddleOffset: function()
            {
                const boxHeight = (this.searchBoxInner.offsetHeight / (this.totalSearchDisplayCount + 1));
                const boxContains = Math.ceil(this.searchBox.offsetHeight / boxHeight);
                return [boxHeight, (boxContains / 2) - 2];
            },
            increaseConnectionSearchHighlight: function()
            {
                this.connectionSearchHighlight++;
                const [boxHeight, middleOffset] = this.getMiddleOffset();                
                this.searchBox.scroll(0, ((this.connectionSearchHighlight - middleOffset) * boxHeight));
            },
            decreaseConnectionSearchHighlight: function()
            {
                if (this.connectionSearchHighlight === 0) { return; }
                this.connectionSearchHighlight--;
                const [boxHeight, middleOffset] = this.getMiddleOffset();             
                this.searchBox.scroll(0, ((this.connectionSearchHighlight - middleOffset) * boxHeight));
            },
            connectionSearchMatchesIndex: function(index)
            {
                if (index !== this.connectionSearchHighlight)
                {
                    return false;
                }
                
                return true;
            },
            parseConnectionsTypeListBySwapType: function(connectionsTypeList)
            {
                if (this.swapType === "socialmedia")
                {
                    let newConnectionTypeList = [];
                    for (let currConnection of connectionsTypeList)
                    {
                        if (![1,2,3,4,5,6,7].includes(currConnection.connection_type_id))
                        {
                            newConnectionTypeList.push(currConnection);
                        }
                    }
                    
                    return newConnectionTypeList;
                }
                
                return connectionsTypeList;
            },
            parseConnectionsBySearch: function(connectionsList)
            {
                const self = this;
                let newConnectionList = [];
                
                if (typeof connectionsList === "undefined" || typeof connectionsList.length !== "number" || connectionsList.length === 0)
                {
                    return newConnectionList;
                }
                
                this.totalSearchDisplayCount = 0; 
                let connectionSearchValue = this.connectionSearch.toString().toLowerCase();

                for (let currConnection of connectionsList)
                {
                    if (this.totalSearchDisplayCount > 25) { break; }
                    
                    let firstName = ((typeof currConnection.first_name !== "undefined") ? currConnection.first_name : "").toString().toLowerCase();
                    let lastName = ((typeof currConnection.last_name !== "undefined") ? currConnection.last_name : "").toString().toLowerCase();
                    let connectionValue = currConnection.connection_value.toString().toLowerCase();
                    let userId = currConnection.user_id.toString().toLowerCase();
                    
                    if ( connectionValue.includes("-") ) { connectionValue = connectionValue.replace("-", ""); }
                    if ( connectionValue.includes("(") ) { connectionValue = connectionValue.replace("(", ""); }
                    if ( connectionValue.includes(")") ) { connectionValue = connectionValue.replace(")", ""); }
                    if ( connectionValue.includes(" ") ) { connectionValue = connectionValue.replace(" ", ""); }
                    
                    if (
                         typeof currConnection !== "undefined" && 
                         currConnection !== null && 
                         typeof currConnection.connection_value !== "undefined" && 
                         currConnection.connection_value !== null && 
                         typeof self.connectionSearch === "string" &&
                         (
                            connectionValue.includes(connectionSearchValue) || 
                            firstName.includes(connectionSearchValue) || 
                            lastName.includes(connectionSearchValue) ||
                            (firstName + " " + lastName).includes(connectionSearchValue) ||
                            userId.includes(connectionSearchValue)
                         )
                    )
                    {
                        newConnectionList.push(currConnection);
                        this.totalSearchDisplayCount++;
                    }
                }
                
                return newConnectionList;
            },
            getConnectionByIndex: function(index)
            {
                const connections = this.connectionSearchList;
                
                for(let currConnectionIndex in connections)
                {
                    if (currConnectionIndex == index) 
                    { 
                        return connections[currConnectionIndex]; 
                    }
                }
                
                return null;
            },
            assignConnection: function(connection, index)
            {
                if (connection === null) { return; }
                
                this.connectionSearch = "";
                this.connectionSearchResult = connection.connection_value;
                this.entityClone.connection_id = connection.connection_id;
                this.entityClone.connection_type_id = connection.connection_type_id;
                this.entityClone.connection_type_name = connection.connection_type_name;
                this.entityClone.font_awesome = connection.font_awesome;
                this.entityClone.connection_value = connection.connection_value;
                this.entityClone.action = connection.action;
                this.dynamicSearch = false;
                this.connectionSearchHighlight = index;

                this.updateActionSelection(this.getConnectionTypeFromId(connection.connection_type_id));
            },
            getConnectionTypeFromId: function(id)
            {
                for (let currConnectionType of this.connectionTypeList)
                {
                    if (currConnectionType.connection_type_id === id)
                    {
                        return currConnectionType.action;
                    }
                }
            
                return "link";
            },
            updateConnectionRel: function()
            {
                let self = this;
                
                if (this.connectionSearchResult === "") return;
                
                let updateAction = this.functionType === "update" ? "swap" : "new";
                let sourceType = this.swapType;
                const url = "/api/v1/cards/update-card-connection?card_id=" + this.entity.card_id + "&connection_id=" + this.entity.connection_id + "&action=" + updateAction + "&sourceType=" + sourceType;
                
                let connectionData = {
                    card_id: this.entity.card_id, 
                    user_id: this.ownerId, 
                    connection_id: this.entityClone.connection_id, 
                    connection_type_id: this.entityClone.connection_type_id, 
                    action: this.entityClone.action, 
                    connection_value: this.entityClone.connection_value, 
                    connection_display_order: this.entityClone.display_order, 
                };
                
                if( this.swapType !== "socialmedia")
                {
                    connectionData.connection_rel_id = this.entityClone.connection_rel_id;
                }
                else
                {
                    connectionData.card_socialmedia_id = this.entityClone.card_socialmedia_id;
                }
                
                ajax.Post(url, connectionData, function(result)
                {
                    if (result.success === false) 
                    {
                        return;
                    }
                    
                    if( self.swapType === "socialmedia")
                    {
                        self.entity.card_socialmedia_id = result.response.data.connection.card_socialmedia_id;
                    }
                    else
                    {
                        self.entity.connection_rel_id = self.entityClone.connection_rel_id;
                    }
                    
                    self.entity.connection_id = self.entityClone.connection_id;
                    self.entity.connection_type_id = self.entityClone.connection_type_id;
                    self.entity.connection_type_name = self.entityClone.connection_type_name;
                    self.entity.connection_value = self.entityClone.connection_value;
                    self.entity.font_awesome = self.entityClone.font_awesome;
                    self.entity.action = self.entityClone.action;
                    
                    if (updateAction === "new")
                    {
                        result.response.data.connection.connection_type_id = self.entityClone.connection_type_id;
                        result.response.data.connection.connection_type_name = self.entityClone.connection_type_name;
                        result.response.data.connection.connection_value = self.entityClone.connection_value;
                        result.response.data.connection.font_awesome = self.entityClone.font_awesome;
                        self.entities.push(result.response.data.connection);
                    }
                   
                    let vue = self.findApp(self);
                    vue.$forceUpdate();
                                 
                    let modal = self.findModal(self);                 
                    modal.close();         
                });
            },
            updateConnectionValue: function()
            {
                let self = this;
                const url = "/api/v1/users/update-user-connection?connection_id=" + this.entity.connection_id + "&action=add";
                
                const connectionData = {
                    connection_id: this.entity.connection_id, 
                    connection_type_id: this.entityNew.connection_type_id, 
                    connection_value: this.entityNew.connection_value, 
                    user_id: this.ownerId, 
                };
                
                ajax.Post(url, connectionData, function(result)
                {
                    if (result.success === false) 
                    {
                        return;
                    }
                    
                    self.entityClone.connection_type_id = self.entityNew.connection_type_id;
                    self.entityClone.connection_type_name = self.getConnectionTypeNameById(self.entityNew.connection_type_id);
                    self.entityClone.font_awesome = self.getConnectionTypeNameById(self.entityNew.connection_type_id);
                    self.entityClone.connection_value = self.entityNew.connection_value;
                    self.connectionSearchResult = self.entityNew.connection_value;
                    
                    self.entityClone.connection_id = result.response.data.connection.connection_id;
                    
                    self.loadConnections(self.swapType);
                    
                    self.entityNew.connection_value = "";
                    self.entityNew.connection_type_id = null;

                    let vue = self.findApp(self);
                    vue.$forceUpdate();
                                 
                    self.backToAssignment();
                });
            },
            getConnectionTypeNameById: function(id)
            {
                for(let currConnectionType of this.connectionTypeList)
                {
                    if (currConnectionType.connection_type_id === id)
                    {
                        return currConnectionType.name;
                    }
                }
                
                return "Unknown";
            },
            updateActionSelectionWhenReady: function()
            {
                if ($(".select-action-for-card-connection").length === 0 || this.entityClone === null)
                {
                    let self = this;
                    setTimeout(function() {
                        self.updateActionSelectionWhenReady();
                    }, 100);
                    return;
                }

                this.updateActionSelection(this.getConnectionTypeFromId(this.entityClone.connection_type_id));
            },
            updateActionSelection: function(action)
            {  
                let self = this;
                
                if (this.connectionSearchResult === "")
                {
                    this.entityClone.action = "";
                    $(".select-action-for-card-connection option[data-type!=default]").hide();
                    return;
                }
                
                $(".select-action-for-card-connection option").show();
                $(".select-action-for-card-connection option[data-type!=" + action + "][data-type!=default]").hide();

                let blnMatchingValue = false;
                
                $.each($(".select-action-for-card-connection option[data-type=" + action + "][data-type!=default]"), function(index,el) 
                {
                    if(self.entityClone.action == $(el).val())
                    {
                        blnMatchingValue = true;
                    }
                });

                if ( blnMatchingValue != false)
                {
                    return;
                }

                let strFirstMatchingValue = $(".select-action-for-card-connection option[data-type=" + action + "][data-type!=default]:first").val();
                
                this.entityClone.action = strFirstMatchingValue;
            },
            clearSelectedValue: function()
            {
                this.connectionSearchResult = "";
                this.connectionSearch = "";
                this.updateActionSelection();
            },
            ucwords: function(str)
            {
                if (typeof str === "undefined") return "";
                return str.replace(/_/g," ").replace(/\w\S*/g, function (txt) {
                    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                });
            },
            loadConnectionList: function(props)
            {
                if (typeof props.connectionList === "undefined" || props.connectionList.length ===0)
                {
                    this.loadConnectionTypeList();
                    return;
                }
                
                this.connectionTypeList = props.connectionList;
            },
        ';
    }

    protected function renderComponentComputedValues() : string
    {
        return '
            connectionSearchList: function()
            {
                return this.parseConnectionsBySearch(this.userConnectionList);
            },
            connectionTypeFilteredList: function()
            {
                return this.parseConnectionsTypeListBySwapType(this.connectionTypeList);
            },
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return parent::renderComponentHydrationScript() . '
            this.ownerId = props.ownerId;
            
            this.loadConnectionList(props);
            
            if (typeof props.swapType !== "undefined") this.swapType = props.swapType;
            if (typeof props.functionType !== "undefined") this.functionType = props.functionType;
            this.entityClone = _.clone(this.entity);
           
            if (this.entityClone.connection_type_id !== 0 && this.functionType !== "save new")
            {
                this.connectionSearchResult = this.entityClone.connection_value;
                this.updateActionSelectionWhenReady();
            }
            else
            {
                this.connectionSearchResult = "";
            }
            
            const swapConnections = document.getElementsByClassName("swapConnection");
            const swapConnection = Array.from(swapConnections)[0];
            this.searchBox = swapConnection.getElementsByClassName("dynamic-search-list")[0];
            this.searchBoxInner = this.searchBox.getElementsByClassName("table")[0];
            
            this.loadConnections(this.swapType);
            this.loadConnectionTypeList();
            
            if (elm("createNewConnection") !== null) elm("createNewConnection").style.display = "none";
            elm("assignConnection").style.removeProperty("display");
        ';
    }

    protected function renderTemplate() : string
    {
        return '<div class="swapConnection">
            <v-style type="text/css">
                .swapConnection .dynamic-search-list {
                    position: absolute;
                    width: calc(100% - 10px);
                    background: #fff;
                    margin-left: 5px;
                    z-index: 1000;
                    max-height:40vh;
                    overflow-y:auto;
                }
                .swapConnection .dynamic-search-list > table {
                    width: 100%;
                }
                .swapConnection .dynamic-search-list > table > thead {
                    box-shadow: rgba(0,0,0,0.2) 0px 2px 5px;
                    background-color: #007bff;
                    color: #fff !important;
                }
                .swapConnection .dynamic-search-list > table tr {
                    cursor:pointer;
                }
                .swapConnection .dynamic-search-list > table tr:hover {
                    background-color:#d5e9ff !important;
                }
                .swapConnection .dynamic-search-list > table tr.connectionSearchHighlight {
                    background-color:#afd2f7 !important;
                }
                .swapConnection .selected-connection {
                    position: absolute;
                    top: calc(50% - 15px);
                    left: 6px;
                    padding: 2px 36px 2px 8px;
                    background: #eee;
                    border: 1px solid #ccc;
                    min-width:150px;
                }
                .swapConnection .clearSelectedValue {
                    position: absolute;
                    top: 5px;
                    right: 5px;
                }
                .swapConnection .auto-complete-search-wrapper {
                    display:flex;
                    flex-direction:row;
                    width:100%;       
                }
                .swapConnection .auto-complete-search-wrapper .auto-complete-search-cell {
                    display:flex;
                    position:relative;
                }
                .swapConnection .auto-complete-search-wrapper.auto-complete-create-new-width .auto-complete-search-cell:first-child {
                    width:82%;
                }
                .swapConnection .auto-complete-search-wrapper .auto-complete-search-cell:last-child button {
                    margin-left:15px;
                }
                .auto-complete-create-new-width-2 {
                    width: calc(100% - 130px) !important;
                }
                .auto-complete-create-original-width {
                    width:100%;
                }
                .dual-action-buttons {
                    display:flex;
                }
                .dual-action-buttons button:first-child {
                    margin-right:10px;
                }
            </v-style>
            <div id="assignConnection" class="swapConnectionItem">
                <table class="table no-top-border">
                    <tbody>
                        <tr>
                            <td style="width:100px;vertical-align: middle;">{{ getDisplayType() }}s</td>
                            <td>
                                <div class="dynamic-search" style="position: relative;">
                                    <div class="auto-complete-search-wrapper" v-bind:class="{\'auto-complete-create-new-width\': createNew === true}">
                                        <div class="auto-complete-search-cell" v-bind:class="{\'auto-complete-create-original-width\': createNew === false}">
                                            <span v-on:click="toggleDynamicSearch" class="inputpicker-arrow" style="top: 10px;right: 10px;">
                                                <b></b>
                                            </span>
                                            <span v-if="connectionSearchResult !== \'\'" class="selected-connection">{{ connectionSearchResult }} <span v-on:click="clearSelectedValue" class="clearSelectedValue general-dialog-close"></span></span>
                                            <input v-on:focus="engageDynamicSearch" v-on:blur="hideDynamicSearch" v-model="connectionSearch" v-on:keyup="keyMonitorConnectionsList" autocomplete="off" value="" placeholder="Start Typing..." class="form-control ui-autocomplete-input">
                                        </div>
                                        <div v-if="createNew === true" class="auto-complete-search-cell">
                                            <button @click="showCreateNewCard()" class="btn btn-primary">Create New</button>
                                        </div>
                                    </div>
                                    <div class="dynamic-search-list" style="position:absolute;" v-show="dynamicSearch === true && connectionSearchResult === \'\'" v-bind:class="{\'auto-complete-create-new-width-2\': createNew === true}">
                                        <table class="table">
                                            <thead>
                                                <th>Connection</th>
                                                <th>Owner Name</th>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(currConnection, index) in connectionSearchList" v-bind:class="{connectionSearchHighlight: connectionSearchMatchesIndex(index) }">
                                                    <td @click="assignConnection(currConnection, index)">{{currConnection.connection_value}}</td>
                                                    <td @click="assignConnection(currConnection, index)">{{currConnection.first_name}} {{currConnection.last_name}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    <div>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="entityClone">
                            <td style="width:100px;vertical-align: middle;">Action</td>
                            <td>
                                <select v-model="entityClone.action" class="form-control select-action-for-card-connection">
                                    <option data-type="default">--Select Action--</option>
                                    <option data-type="phone" value="phone" selected="">Phone</option>
                                    <option data-type="phone" value="sms">SMS</option>
                                    <option data-type="fax" value="fax">Fax</option>
                                    <option data-type="link" value="link">Link</option>
                                    <option data-type="email" value="email">Email</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button class="btn btn-primary w-100" v-on:click="updateConnectionRel" v-bind:class="{disabled: connectionSearchResult === \'\'}">{{ actionButton }}</button>
            </div>
            <div id="createNewConnection" v-if="createNew === true" class="swapConnectionItem" style="display: none;">
                <table v-if="entityClone" class="table no-top-border">
                    <tbody>
                        <tr>
                            <td style="width:100px;vertical-align: middle;">Type</td>
                            <td>
                                <select v-model="entityNew.connection_type_id" class="form-control">
                                    <option>--Select {{ getDisplayType() }} Type--</option>
                                    <option v-if="connectionTypeList" v-for="connectionType in connectionTypeFilteredList" :value="connectionType.connection_type_id">{{ connectionType.name }}</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:100px;vertical-align: middle;">Value</td>
                            <td><input class="form-control" v-model="entityNew.connection_value" type="text" placeholder="Enter Connection Value..."></td>
                        </tr>
                    </tbody>
                </table>
                <div class="dual-action-buttons">
                    <button class="btn btn-danger w-25" v-on:click="backToAssignment" style="color:#fff !important;">Back To Assignment</button>
                    <button class="btn btn-primary w-75" v-on:click="updateConnectionValue">Add New Connection</button>
                </div>

            </div>
        </div>';
    }
}