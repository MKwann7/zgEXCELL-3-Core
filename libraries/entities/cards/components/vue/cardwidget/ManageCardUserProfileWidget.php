<?php

namespace Entities\Cards\Components\Vue\CardWidget;

use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cards\Models\CardModel;

class ManageCardUserProfileWidget extends VueComponent
{
    protected string $id = "b2268935-cfd5-405a-86ea-8c362b11d20c";
    protected string $modalWidth = "750";

    public function __construct (array $components = [])
    {
        parent::__construct(new CardModel(), $components);

        $this->modalTitleForAddEntity = "Add Card User Profile";
        $this->modalTitleForEditEntity = "Edit Card User Profile";
        $this->modalTitleForDeleteEntity = "Delete Card User Profile";
        $this->modalTitleForRowEntity = "View Card User Profile";
    }
    protected function renderComponentDataAssignments (): string
    {
        return '
            entityClone: false,
            dynamicSearch: false,
            customerList: [],
            userSearch: "",
            userSearchResult: "",
            userSearchHighlight: 0,
            searchBox: 0,
            searchBoxInner: 0,
            totalSearchDisplayCount: 0,
            cardUserTitle: "",
        ';
    }

    protected function renderComponentHydrationScript () : string
    {
        return '
            if (this.entity)
            {
                this.entityClone = _.clone(this.entity);
                this.cardUserTitle = getJsonSettingDecoded(this.entity.card_data, "card_user.title", "");
            }
            
            this.userSearchResult = "";
            
            try {
                const editCardUserProfile = document.getElementsByClassName("editEntityUserProfile");
                const cardUserProfile = Array.from(editCardUserProfile)[0];
                this.searchBox = cardUserProfile.getElementsByClassName("dynamic-search-list")[0]; 
                this.searchBoxInner = this.searchBox.getElementsByClassName("table")[0];
            } 
            catch(ex) { console.log(ex); }
            
            this.loadCustomers();
            
        '.parent::renderComponentHydrationScript();
    }

    protected function renderComponentMethods (): string
    {
        global $app;
        return '
            updateCardUserProfile: function()
            {
                let self = this;
                
                const url = "/api/v1/cards/update-card-user-profilewidget?card_id=" + this.entityClone.card_id;
                const updateCardUser = {
                    card_user_id: this.entityClone.card_user_id,
                    card_user_title: this.cardUserTitle
                };
                
                modal.EngageFloatShield();
                
                ajax.Post(url, updateCardUser, function(result) 
                {                    
                    if (result.success === false) 
                    {
                        return;
                    }
                    
                    self.entity.card_user_id = result.response.data.card.card_user_id;
					self.entity.card_owner_name = result.response.data.card.card_owner_name;
                    self.entity.card_user_name = result.response.data.card.card_user_name;
                    self.entity.card_user_email = result.response.data.card.card_user_email;
                    self.entity.card_data = result.response.data.card.card_data;
                    
                    let vue = self.findApp(self);
                    vue.$forceUpdate();
                    
                    modal.CloseFloatShield();
                                 
                    let objModal = self.findModal(self);                 
                    objModal.close(); 
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
                const self = this;
                setTimeout(function() {
                    if (self.userSearchResult === "") {
                        self.dynamicSearch = false;
                    }
                }, 250);
            },
            keyMonitorCustomerList: function(event)
            {
                switch(event.keyCode)
                {
                    case 38:
                        this.decreaseUserSearchHighlight();
                        break;
                    case 40:
                        this.increaseUserSearchHighlight();
                        break;
                    case 13:
                        let userByIndex = this.getUserByIndex(this.userSearchHighlight);
                        this.assignCustomerToCard(userByIndex, this.userSearchHighlight);
                        break;
                    default:
                        this.userSearchHighlight = 0;
                        break;
                }
                
                this.customerList = this.customerList;
                this.$forceUpdate();
            },
            getMiddleOffset: function()
            {
                const boxHeight = (this.searchBoxInner.offsetHeight / (this.totalSearchDisplayCount + 1));
                const boxContains = Math.ceil(this.searchBox.offsetHeight / boxHeight);
                return [boxHeight, (boxContains / 2) - 2];
            },
            increaseUserSearchHighlight: function()
            {
                this.userSearchHighlight++;
                const [boxHeight, middleOffset] = this.getMiddleOffset();                
                this.searchBox.scroll(0, ((this.userSearchHighlight - middleOffset) * boxHeight));
            },
            decreaseUserSearchHighlight: function()
            {
                if (this.userSearchHighlight === 0) { return; }
                this.userSearchHighlight--;
                const [boxHeight, middleOffset] = this.getMiddleOffset();             
                this.searchBox.scroll(0, ((this.userSearchHighlight - middleOffset) * boxHeight));
            },
            userConnectionSearchHighlight: function()
            {
                if (this.userSearchHighlight === 0) { return; }
                this.userSearchHighlight--;
                const [boxHeight, middleOffset] = this.getMiddleOffset();             
                this.searchBox.scroll(0, ((this.userSearchHighlight - middleOffset) * boxHeight));
            },
            userSearchMatchesIndex: function(index)
            {
                if (index !== this.userSearchHighlight)
                {
                    return false;
                }
                
                return true;
            },
            parseUsersBySearch(usersList)
            {
                const self = this;
                let newUserList = [];
                
                if (typeof usersList.length !== "number" || usersList.length === 0)
                {
                    return newUserList;
                }
                
                let intTotalCount = 0;
                
                for (let currUser of usersList)
                {
                    if (intTotalCount > 25) { break; }
                    if (
                        currUser.first_name.toLowerCase().includes(self.userSearch.toLowerCase()) || 
                        currUser.last_name.toLowerCase().includes(self.userSearch.toLowerCase()) ||
                        (currUser.first_name.toLowerCase() + " " + currUser.last_name.toLowerCase()).includes(self.userSearch.toLowerCase()) ||
                        currUser.user_id.toString().toLowerCase().includes(self.userSearch.toLowerCase())
                    )
                    {
                        newUserList.push(currUser);
                        intTotalCount++;
                    }
                }
                
                return newUserList;
            },
            getUserByIndex: function(index)
            {
                const users = this.cartCustomerSearchList;
                
                for(let currUserIndex in users)
                {
                    if (currUserIndex == index) 
                    { 
                        return users[currUserIndex]; 
                    }
                }
                
                return null;
            },
            assignCustomerToCard: function(user, index)
            {
                if (user === null) { return; }
            
                this.userSearch = ""
                this.userSearchResult = user.first_name + " " + user.last_name;
                this.entityClone.card_user_id = user.user_id;
                this.entityClone.card_user_email = user.user_email;
                this.entityClone.card_user_phone = user.user_phone;
                this.dynamicSearch = false;
                this.userSearchHighlight = index;
            },
            loadCustomers: function(callback)
            {
                const self = this;
                const url = "' . $app->objCustomPlatform->getFullPortalDomainName() . '/cart/get-all-card-users-count";
                
                ajax.GetExternal(url, {}, true, function(result) 
                {
                    if (result.success === false)
                    {
                        return;
                    }
                    
                    if (self.customerList.length == result.response.data.count) 
                    {
                        const customersList = Object.entries(self.customerList);
                        customersList.forEach(function([user_id, currUser])
                        {
                            if (currUser.user_id == self.entityClone.card_user_id) 
                            { 
                                self.userSearchResult = currUser.first_name + " " + currUser.last_name; 
                            }
                        });
                        
                        return; 
                    }
                    
                    self.customerList = [];
                    const url = "' . $app->objCustomPlatform->getFullPortalDomainName() . '/cart/get-all-card-users";
                    
                    ajax.GetExternal(url, {}, true, function(result) 
                    {
                        if (result.success === false)
                        {
                            return;
                        }
    
                        const users = Object.entries(result.response.data.list);
                                            
                        users.forEach(function([user_id, currUser])
                        {
                            if (user_id == self.entityClone.card_user_id) 
                            { 
                                self.userSearchResult = currUser.first_name + " " + currUser.last_name; 
                            }
                            
                            self.customerList.push(currUser);
                        });
                        
                        self.$forceUpdate();
                    });
                });
            },
            clearSelectedValue: function()
            {
                this.userSearchResult = "";
                this.userSearch = "";
            },
        ';
    }

    protected function renderComponentComputedValues() : string
    {
        return '
            cartCustomerSearchList: function()
            {
                return this.parseUsersBySearch(this.customerList);
            },
        ';
    }

    protected function renderTemplate() : string
    {
        return '
        <div class="editEntityUserProfile">
            <v-style type="text/css">
                .editEntityUserProfile .dynamic-search-list {
                    position: absolute;
                    width: calc(100% - 35px);
                    background: #fff;
                    margin-left: 5px;
                    z-index: 1000;
                    max-height:40vh;
                    overflow-y:auto;
                }
                .editEntityUserProfile .dynamic-search-list > table {
                    width: 100%;
                }
                .editEntityUserProfile .dynamic-search-list > table > thead {
                    box-shadow: rgba(0,0,0,0.2) 0px 2px 5px;
                    background-color: #007bff;
                    color: #fff !important;
                }
                .editEntityUserProfile .dynamic-search-list > table tr {
                    cursor:pointer;
                }
                .editEntityUserProfile .dynamic-search-list > table tr:hover {
                    background-color:#d5e9ff !important;
                }
                .editEntityUserProfile .augmented-form-items {
                    background:#ddd;padding: 0px 8px 0px;border-radius:5px;box-shadow:rgba(0,0,0,.2) 0 0 10px inset
                }
                .editEntityUserProfile .dynamic-search-list > table tr.userSearchHighlight {
                    background-color:#afd2f7 !important;
                }
                .editEntityUserProfile .selected-user {
                    position: absolute;
                    top: calc(50% - 15px);
                    left: 20px;
                    padding: 3px 30px 3px 8px;
                    background: #eee;
                    border: 1px solid #ccc;
                }
                .editEntityUserProfile .clearSelectedValue {
                    position: absolute;
                    top: 5px;
                    right: 5px;
                }
            </v-style>
            <div>
                <div v-if="userAdminRole" class="augmented-form-items">
                    <table class="table" style="margin-bottom:2px;">
                        <tr>
                            <td style="width:117px;vertical-align: middle;">Card User</td>
                            <td style="position:relative;">
                                <div class="dynamic-search">
                                    <span class="inputpicker-arrow" @click="engageDynamicSearch" style="top: 20px;right: 21px;">
                                        <b></b>
                                    </span>
                                    <span v-if="userSearchResult !== \'\'" class="selected-user">{{ userSearchResult }} <span v-on:click="clearSelectedValue" class="clearSelectedValue general-dialog-close"></span></span>
                                    <input v-on:focus="engageDynamicSearch" v-on:blur="hideDynamicSearch" v-model="userSearch" v-on:keyup="keyMonitorCustomerList" autocomplete="off" value="" placeholder="Start Typing..." class="form-control ui-autocomplete-input">
                                    <div class="dynamic-search-list" style="position:absolute;" v-show="dynamicSearch === true && userSearchResult === \'\'">
                                        <table class="table">
                                            <thead>
                                                <th>User Id</th>
                                                <th>Name</th>
                                            </thead>
                                            <tbody>
                                                <tr v-for="currUser, index in cartCustomerSearchList" v-bind:class="{userSearchHighlight: userSearchMatchesIndex(index)}">
                                                    <td @click="assignCustomerToCard(currUser,index)">{{currUser.user_id}}</td>
                                                    <td @click="assignCustomerToCard(currUser,index)">{{currUser.first_name}} {{currUser.last_name}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    <div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <table class="table no-top-border" style="margin-bottom: 0;">
                    <tr>
                        <td style="width:125px;vertical-align: middle;">Title</td>
                        <td><input v-model="cardUserTitle" class="form-control" type="text" placeholder="Enter User Title"></td>
                    </tr>
                </table>
                <div class="augmented-form-items">
                    <table class="table no-top-border">
                        <tr>
                            <td style="width:117px;vertical-align: middle;">Email</td>
                            <td><input v-model="entityClone.card_user_email" class="form-control pass-validation" type="text" readonly></td>
                        </tr>
                        <tr>
                            <td style="width:117px;vertical-align: middle;">Phone</td>
                            <td>
                                <input v-model="entityClone.card_user_phone" class="form-control pass-validation" type="text" readonly>
                            </td>
                        </tr>
                    </table>
                </div>
                <button v-on:click="updateCardUserProfile" class="buttonID9234597e456 btn btn-primary w-100">Update Card User</button>
            </div>
        </div>';
    }
}