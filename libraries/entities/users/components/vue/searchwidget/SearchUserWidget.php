<?php

namespace Entities\Users\Components\Vue\SearchWidget;

use App\Website\Vue\Classes\Base\VueComponent;

class SearchUserWidget extends VueComponent
{
    protected string $id = "8b7c2376-05b6-4d7f-8a8d-a20e1307ec79";
    protected string $title = "Search User Widget";
    protected string $modalTitleForAddEntity = "Search User Widget";

    protected function renderComponentDataAssignments() : string
    {
        return '
            dynamicOwnerSearch: false,
            customerList: [],
            ownerSearch: "",
        ';
    }

    protected function renderComponentComputedValues() : string
    {
        return '
            cartCustomerSearchList: function()
            {
                return this.parseUsersBySearch(this.customerList, this.ownerSearch.toLowerCase())
            },
        ';
    }

    protected function renderComponentMethods() : string
    {
        return '
            engageDynamicOwnerSearch: function(user)
            {
                this.dynamicOwnerSearch = true
            },
            hideDynamicOwnerSearch: function()
            {
                const self = this
                setTimeout(function() {
                    self.dynamicOwnerSearch = false
                }, 100)
            },
            keyMonitorCustomerList: function(event)
            {
                this.customerList = this.customerList
                this.$forceUpdate()
            },
            loadCustomers: function(callback)
            {
                const self = this;
                this.customerList = []
                
                ajax.Get("/cart/get-all-users", null, function(result) {
                    if (result.success === false) {
                        return
                    }

                    const users = Object.entries(result.response.data.list)

                    users.forEach(function([user_id, currUser]) {
                        if (currUser.user_id === self.entity.owner_id) { 
                            self.ownerSearch = currUser.first_name + " " + currUser.last_name
                        }
                        self.customerList.push(currUser)
                    });
                    
                    self.$forceUpdate()
                });
            },
            assignCustomerToCardOwner: function(user)
            {
                this.ownerSearch = user.first_name + " " + user.last_name
                this.entity.owner_id = user.user_id
                this.dynamicOwnerSearch = false
            },
            parseUsersBySearch(usersList, text)
            {
                const self = this
                let newUserList = []
                
                if (typeof usersList.length !== "number" || usersList.length === 0)
                {
                    return newUserList
                }
                
                let intTotalCount = 0
                
                for (let currUser of usersList)
                {
                    if (intTotalCount > 25) { break; }
                    if (
                        currUser.first_name.toLowerCase().includes(text) || 
                        currUser.last_name.toLowerCase().includes(text) ||
                        (currUser.first_name.toLowerCase() + " " + currUser.last_name.toLowerCase()).includes(text) ||
                        currUser.user_id.toString().toLowerCase().includes(text)
                    )
                    {
                        newUserList.push(currUser)
                        intTotalCount++
                    }
                }
                
                return newUserList
            },
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return parent::renderComponentHydrationScript() . '
            this.entity = this.entityData
            if (this.entity) {
                this.loadCustomers();   
            }
        ';
    }

    protected function renderTemplate() : string
    {
        return '<div class="searchUserWidget">
            <v-style type="text/css">
            </v-style>
            <div v-if="entity" class="searchUserInner">
                <table class="table" style="margin-bottom:2px;">
                    <tr>
                        <td style="width:117px;vertical-align: middle;">Owner</td>
                        <td style="position:relative;">
                            <div class="dynamic-search">
                                <span class="inputpicker-arrow" style="top: 20px;right: 21px;">
                                    <b></b>
                                </span>
                                <input v-on:focus="engageDynamicOwnerSearch" v-on:blur="hideDynamicOwnerSearch" v-model="ownerSearch" v-on:keyup="keyMonitorCustomerList" autocomplete="off" value="" placeholder="Start Typing..." class="form-control ui-autocomplete-input">
                                <div class="dynamic-search-list" style="position:absolute;" v-if="dynamicOwnerSearch === true && ownerSearch !== \'\'">
                                    <table>
                                        <thead>
                                            <th>User Id</th>
                                            <th>Name</th>
                                        </thead>
                                        <tbody>
                                            <tr v-for="currUser in cartCustomerSearchList">
                                                <td @click="assignCustomerToCardOwner(currUser)">{{currUser.user_id}}</td>
                                                <td @click="assignCustomerToCardOwner(currUser)">{{currUser.first_name}} {{currUser.last_name}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                <div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        ';
    }
}