<?php
/**
 * Created by PhpStorm.
 * User: Micah.Zak
 * Date: 10/11/2018
 * Time: 9:43 AM
 */

$this->CurrentPage->BodyId            = "view-all-users-page";
$this->CurrentPage->BodyClasses       = ["admin-page", "view-all-users-page", "no-columns"];
$this->CurrentPage->Meta->Title       = "Users | Admin | " . $this->app->objCustomPlatform->getPortalName();
$this->CurrentPage->Meta->Description = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Meta->Keywords    = "";
$this->CurrentPage->SnipIt->Title     = "Users";
$this->CurrentPage->SnipIt->Excerpt   = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Columns           = 0;

?>
<div class="breadCrumbs">
    <div class="breadCrumbsInner<?php if ( $strApproach === "view") { echo " edit-entity"; } ?>">
        <input id="entity-page-entrance" data-source="<?php if ( $strApproach === "view") { echo 'edit-entity'; } else { echo 'list-entities'; } ?>" type="hidden"/>
        <a href="/account" class="breadCrumbHomeImageLink">
            <img src="/media/images/home-icon-01_white.png" class="breadCrumbHomeImage" width="15" height="15" />
        </a> &#187;
        <a href="/account/admin" class="breadCrumbHomeImageLink">
            <span class="breadCrumbPage">Admin</span>
        </a> &#187;
        <span id="view-list">
            <span class="breadCrumbPage">Users</span>
        </span>
        <span id="editing-entity">
        <a id="backToViewEntityList" href="/account/admin/customers/" class="breadCrumbHomeImageLink">
            <span class="breadCrumbPage">Members</span>
        </a> &#187;
        <span class="breadCrumbPage">Member Dashboard</span>
        </span>
    </div>
</div>
<div class="BodyContentBox">
    <style type="text/css">
        .BodyContentBox .table-striped td {
            width:10%;
        }
        .BodyContentBox .table-striped td:first-child {
            width:5%;
        }
        .BodyContentBox .table-striped td:nth-child(5) {
            width:5%;
        }

        .BodyContentBox .account-page-title #back-to-entity-list,
        .BodyContentBox .account-page-title #back-to-entity-list-404 {
            background: #cc0000 url(/website/images/mobile-back.png) center center / auto 75% no-repeat !important;
            text-indent: -99999px;
            padding: 5px 0px !important;
            width: 24px;
            height: 23px;
            display: inline-block;
            top: 2px;
            position: relative;
            border-radius: 5px;
        }

        .entityDashboard .width50:nth-of-type(odd) .card-tile-50 {
            width:calc( 100% - 7px );
            margin-right:7px;
            padding: 15px 25px;
            background: #fff;
        }

        .entityDashboard .width50:nth-of-type(even) .card-tile-50 {
            width:calc( 100% - 8px );
            margin-left:8px;
            padding: 15px 25px;
            background: #fff;
        }
        .entityDashboard .entityDetailsInner table tr td:nth-of-type(odd) {
            padding-right:15px;
        }
        .table.table-shadow {
            box-shadow: 0 0 5px rgba(0,0,0,.3);
        }
        .table.no-top-border td {
            border-top:0px;
        }
        @media (max-width:750px) {
            .main-list-image {
                width: 25px;
                height: 25px;
            }
        }
        .cards_banner {
            width:40px;
        }

        [v-cloak] { display: none; }
    </style>
    <div id="app" class="formwrapper" >
        <div class="formwrapper-outer<?php if ( $strApproach === "view") { echo " edit-entity"; } ?>">
            <div class="formwrapper-control" v-cloak>
                <div class="fformwrapper-header">
                    <table class="table header-table" style="margin-bottom:0px;">
                        <tbody>
                        <tr>
                            <td>
                                <h3 class="account-page-title">Users <span class="pointer addNewEntityButton entityButtonFixInTitle"  v-on:click="addUser()"></span></h3>
                                <div class="form-search-box" v-cloak>
                                    <input v-model="searchQuery" class="form-control" type="text" placeholder="Search for..."/>
                                </div>
                            </td>
                            <td class="text-right page-count-display" style="vertical-align: middle;">
                                <span class="page-count-display-data">
                                    Current: <span>{{ pageIndex }}</span>
                                    Pages: <span>{{ totalPages }}</span>
                                </span>
                                <button v-on:click="prevPage()" class="btn prev-btn" :disabled="pageIndex == 1">Prev</button>
                                <button v-on:click="nextPage()" class="btn" :disabled="pageIndex == totalPages">Next</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="entityListOuter">
                    <table class="table table-striped entityList">
                        <thead>
                        <th v-for="column in columns">
                            <a v-on:click="orderByColumn(column)" v-bind:class="{ active : orderKey == column, sortasc : sortByType == true, sortdesc : sortByType == false }">
                                {{ column | ucWords }}
                            </a>
                        </th>
                        <th class="text-right">
                            Actions
                        </th>
                        </thead>
                        <tbody>
                        <tr v-for="person in orderedPeople" v-on:dblclick="editUser(person)">
                            <td>{{ person.user_id }}</td>
                            <td>{{ person.username }}</td>
                            <td>{{ person.first_name }}</td>
                            <td>{{ person.last_name }}</td>
                            <td>{{ person.status }}</td>
                            <td>{{ person.created_on }}</td>
                            <td class="text-right">
                                <img v-on:click="editUser(person)" class="pointer editEntityButton" />
                                <img v-on:click="deleteUser(person)" class="pointer deleteEntityButton" />
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="formwrapper-inner" v-cloak style="display:none;">
                <div class="entity404" <?php if ($strApproach === "view" && $blnUserViewFound === true ) { echo ' style="display:none;"'; } ?>>
                    <table class="table header-table" style="margin-bottom:0px;">
                        <tbody>
                        <tr>
                            <td class="mobile-to-table">
                                <h3 class="account-page-title"><a id="back-to-entity-list-404" class="pointer"></a> No Customer Found [404  ]</h3>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="entityDashboard" <?php if ($strApproach === "view" && $blnUserViewFound === false ) { echo ' style="display:none;"'; } ?>>
                    <table class="table header-table" style="margin-bottom:0px;">
                        <tbody>
                        <tr>
                            <td class="mobile-to-table">
                                <h3 class="account-page-title"><a id="back-to-entity-list" class="back-to-entity-list pointer"></a> Member Dashboard</h3>
                                <div class="form-search-box" v-cloak>
                                    <input v-model="searchQuery" class="form-control" type="text" placeholder="Search for..."/>
                                </div>
                            </td>
                            <td class="mobile-to-table text-right page-count-display dashboard-tab-display" style="vertical-align: middle;">
                                <div data-block="profile" class="dashboard-tab fas fa-user-circle" v-bind:class="{active: sessionStorage.getItem('dashboard-tab') == 'profile'}"><span>Profile</span></div>
                                <div data-block="cards" class="dashboard-tab fas fa-id-card" v-bind:class="{active: sessionStorage.getItem('dashboard-tab') == 'cards'}"><span>Cards</span></div>
                                <div data-block="billing" class="dashboard-tab fas fa-credit-card" v-bind:class="{active: sessionStorage.getItem('dashboard-tab') == 'billing'}"><span>Billing</span></div>
                                <div data-block="activity" class="dashboard-tab fas fa-walking" v-bind:class="{active: sessionStorage.getItem('dashboard-tab') == 'activity'}"><span>Activity</span></div>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <input id="dashboard-entity-id" type="hidden" value="<?php echo !empty($objUser->user_id) ? $objUser->user_id : ""; ?>" />
                    <div class="entityTab" data-tab="profile">
                        <div class="width100 entityDetails">
                            <div class="width50">
                                <div class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-user-circle fas-large desktop-25px"></span>
                                        <span class="fas-large">Profile</span>
                                        <span v-on:click="editProfile()" class="pointer editEntityButton entityButtonFixInTitle"></span>
                                        <span onclick="app.Impersonate($('#dashboard-entity-id').val());" class="pointer loginUserButton fas fa-sign-in-alt"></span>
                                    </h4>
                                    <div class="entityDetailsInner">
                                        <table>
                                            <tbody>
                                            <tr>
                                                <td>Full Name:</td>
                                                <td><strong id="entityFullName"><?php echo !empty($objUser->first_name) ? $objUser->first_name : ""; ?><?php echo !empty($objUser->last_name) ? " " . $objUser->last_name : ""; ?></strong></td>
                                            </tr>
                                            <tr>
                                                <td>User Name </td>
                                                <td><strong id="entityUserName"><?php echo !empty($objUser->username) ? $objUser->username : ""; ?></strong></td>
                                            </tr>
                                            <tr>
                                                <td>Password: </td>
                                                <td><strong id="entityPassword">********</strong></td>
                                            </tr>
                                            <tr>
                                                <td>Status: </td>
                                                <td><strong id="entityStatus"><?php echo !empty($objUser->status) ? $objUser->status : ""; ?></strong></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <table style="margin-top:8px;">
                                            <tbody>
                                            <tr>
                                                <td>Phone: </td>
                                                <td><strong id="entityPrimaryPhone"><?php echo formatAsPhoneIfApplicable($objUser->user_phone); ?></strong></td>
                                            </tr>
                                            <tr>
                                                <td>E-mail: </td>
                                                <td><strong id="entityPrimaryEmail"><?php echo $objUser->user_email; ?></strong></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <table style="margin-top:8px;">
                                            <tbody>
                                            <tr>
                                                <td>Affiliate: </td>
                                                <td><strong id="entityAffiliate"><?php  ?></strong></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="width50">
                                <div class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-images fas-large desktop-30px"></span>
                                        <span class="fas-large">Photos</span>
                                    </h4>
                                    <div class="entityDetailsInner" style="margin-top:5px;">
                                        <div class="divTable widthAuto mobile-to-100">
                                            <div class="divRow">
                                                <div class="divCell mobile-to-table mobile-text-center">
                                                    <strong class="mobile-center mobile-to-75">Profile Photo</strong><br>
                                                    <img v-on:click="editProfilePhoto()" class="pointer mobile-to-75 mobile-to-block mobile-vertical-margins-15 mobile-to-heightAuto mobile-center" id="entityMainImage" width="160" height="160" data-src="<?php echo $objUser->main_thumb; ?>" src="<?php echo $objUser->main_thumb; ?>" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="width100 entityDetails">
                            <div class="width50">
                                <div class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-share-alt fas-large desktop-25px"></span>
                                        <span class="fas-large">Connections</span>
                                        <span class="pointer addNewEntityButton entityButtonFixInTitle" v-on:click="addConnection()" ></span>
                                    </h4>
                                    <div class="entityDetailsInner">
                                        <table class="table table-striped no-top-border table-shadow" v-cloak>
                                            <tbody>
<!--                                            <tr v-for="connection in connections" v-on:dblclick="editConnection(connection)" class="pointer">-->
<!--                                                <td class="entityConnectionType">{{ connection.connection_type_id }}: </td>-->
<!--                                                <td class="entityConnectionValueBox"><strong class="entityConnectionValue">{{ connection.connection_value }}</strong></td>-->
<!--                                                <td class="text-right">-->
<!--                                                    <span v-on:click="editConnection(connection)" class="pointer editEntityButton"></span>-->
<!--                                                    <span v-on:click="deleteConnection(connection)" class="pointer deleteEntityButton"></span>-->
<!--                                                </td>-->
<!--                                            </tr>-->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="width50">
                                <div class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-home fas-large desktop-25px"></span>
                                        <span class="fas-large">Addresses</span>
                                        <span class="pointer addNewEntityButton entityButtonFixInTitle" v-on:click="addAddress()"></span></h4>
                                    <div class="entityDetailsInner">
                                        <table class="table table-striped no-top-border table-shadow" v-cloak>
                                            <tbody>
<!--                                            <tr v-for="address in addresses" v-on:dblclick="editAddress(address)" class="pointer">-->
<!--                                                <td>{{ address.display_name }}: </td>-->
<!--                                                <td><strong id="entityAddressName">{{ address.address_1 }} {{ address.address_2 }}, {{ address.city }} {{ address.state }} {{ address.zip }}</strong></td>-->
<!--                                                <td class="text-right">-->
<!--                                                    <span v-on:click="editAddress(address)" class="pointer editEntityButton"></span>-->
<!--                                                    <span v-on:click="deleteAddress(address)" class="pointer deleteEntityButton"></span>-->
<!--                                                </td>-->
<!--                                            </tr>-->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.17/vue.min.js"></script>
<script type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.11/lodash.min.js"></script>
<script type="application/javascript">
    let pageApp = new Vue({

        el: '#app',

        computed: {
            totalPages: function() {
                return this.pageTotal;
            },

            orderedPeople: function() {
                let objSortedPeople = this.sortedPeople;

                return objSortedPeople;
            },

            sortedPeople: function () {
                let objOrderedPeople = _.orderBy(this.people, this.orderKey, this.sortByType ? 'asc' : 'desc');

                let intStartIndex = ((this.pageIndex-1) * this.pageDisplay);
                let intIndexOffset = this.people.length - intStartIndex;
                let intEndIndex = intStartIndex + (( this.pageDisplay <= intIndexOffset ) ? this.pageDisplay : intIndexOffset);

                var self = this;

                if (!self.searchQuery) {
                    var intTotalPages = 1;

                    if (this.pageDisplay < objOrderedPeople.length) {
                        intTotalPages = objOrderedPeople.length / this.pageDisplay;
                    }

                    this.pageTotal = Math.ceil(intTotalPages);

                    return objOrderedPeople.slice(intStartIndex, intEndIndex);
                }

                let objFilteredPeople = objOrderedPeople.filter(function (person) {
                    var searchRegex = new RegExp(self.searchQuery, 'i');
                    if ( searchRegex.test(person.user_id) ||  searchRegex.test(person.username) || searchRegex.test(person.first_name) || searchRegex.test(person.last_name) || searchRegex.test(person.created_on)) {
                        return person;
                    }
                });

                let intOrderedIndexOffset = objOrderedPeople.length - intStartIndex;
                let intOrderedEndIndex = intStartIndex + (( this.pageDisplay <= intOrderedIndexOffset ) ? this.pageDisplay : intOrderedIndexOffset);

                if ( objFilteredPeople.length < intStartIndex ) {

                    intStartIndex = Math.floor(objFilteredPeople.length/this.pageDisplay)*this.pageDisplay;
                    this.pageIndex =  Math.ceil(objFilteredPeople.length / this.pageDisplay);
                    intOrderedIndexOffset = objFilteredPeople.length - intStartIndex;
                    intOrderedEndIndex = intStartIndex + intOrderedIndexOffset;
                }

                var intTotalFilteredPages = 1;

                if (this.pageDisplay < objFilteredPeople.length) {
                    intTotalFilteredPages = objFilteredPeople.length / this.pageDisplay;
                }

                this.pageTotal = Math.ceil(intTotalFilteredPages);

                return objFilteredPeople.slice(intStartIndex, intOrderedEndIndex);
            }
        },

        filters: {
            ucWords: function(str) {
                return str.replace("_"," ").replace(/\w\S*/g, function (txt) {
                    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                });
            },

            orderBy: function(type) {

            }
        },

        methods: {
            orderByColumn: function(column) {

                this.sortByType = ( this.orderKey == column ) ? ! this.sortByType : this.sortByType;

                this.orderKey = column;
            },

            addUser: function() {
                var strUserName = this.personToAdd.username;
                var strFirstName = this.personToAdd.first_name;
                var strLastName = this.personToAdd.last_name;
                var strStatus = this.personToAdd.status;
                var strCreatedOn = this.personToAdd.created_on;

                if (!strFirstName || !strLastName) {
                    return;
                }

                var intId = this.people.length + 1;

                this.people.push({user_id: intId, username: strUserName,  first_name: strFirstName, last_name: strLastName, status: strStatus, created_on: strCreatedOn});

                this.personToAdd.username = "";
                this.personToAdd.first_name = "";
                this.personToAdd.last_name = "";
                this.personToAdd.status = "";
                this.personToAdd.created_on = "";
                this.personToAdd.user_id = "";
            },

            editUser: function(person) {

                let stateObj = { foo: "bar" };

                modal.EngageFloatShield();

                $('#dashboard-entity-id').val(person.user_id);
                $('#entityFullName').html(person.first_name + " " + person.last_name);
                $('#entityUserName').html(person.username);
                $('#entityStatus').html(person.status);

                $(".formwrapper-outer").addClass("edit-entity");
                $(".breadCrumbsInner").addClass("edit-entity");

                let userIdParameter = "user_id=" + person.user_id;

                this.cards = [];
                this.connections = [];
                this.addresses = [];

                ajax.Send("customers/user-data/get-customer-dashboard-info", userIdParameter, function(objUserResult)
                {
                    if (objUserResult.success == false)
                    {
                        console.log(objUserResult.message);
                        var data = {};
                        data.title = "Customer Conversion to V2 Error...";
                        data.html = objUserResult.message;
                        modal.AddFloatDialogMessage(data);
                        return false;
                    }

                    if (objUserResult.data.blnUserViewFound == false)
                    {
                        $(".entityDashboard").hide();
                        $(".entity404").show();
                    }

                    setTimeout(function(){
                        $(".formwrapper-outer.edit-entity .formwrapper-control").fadeTo(100,0,function() {
                            $(this).hide();
                        });
                    },10);

                    console.log(objUserResult);

                    // $('#entityMainImage').attr("data-src", objUserResult.data.user.main_thumb);
                    // $('#entityMainImage').attr("src", objUserResult.data.user.main_thumb);
                    $('#entityPrimaryPhone').html(formatAsPhoneIfApplicable(objUserResult.data.user.user_phone));
                    $('#entityPrimaryEmail').html(objUserResult.data.user.user_email);

                    for(let intCardIndex in objUserResult.data.cards)
                    {
                        pageApp.cards.push(objUserResult.data.cards[intCardIndex]);
                    }

                    for(let intConnectionIndex in objUserResult.data.connections)
                    {
                        pageApp.connections.push(objUserResult.data.connections[intConnectionIndex]);
                    }

                    for(let intAddressIndex in objUserResult.data.addresses)
                    {
                        pageApp.addresses.push(objUserResult.data.addresses[intAddressIndex]);
                    }

                    for(let intActivitiesIndex in objUserResult.data.activities)
                    {
                        pageApp.userActivities.push(objUserResult.data.activities[intActivitiesIndex]);
                    }

                    // success
                    history.pushState(stateObj, "View Customer", "/account/admin/customers/view-customer?id=" + person.user_id);

                    dash.loadDashboardTabs();

                    modal.CloseFloatShield();

                },"POST");
            },

            deleteColumn: function(person) {
                this.people = this.people.filter(function (curPerson) {
                    return person.user_id != curPerson.user_id;
                });
            },

            prevPage: function() {
                this.pageIndex--;

                this.people = this.people;
            },

            nextPage: function() {
                this.pageIndex++;

                this.people = this.people;
            }
        },

        data: {


            orderKey: 'name',

            sortByType: true,

            columns: ['user_id', 'username', 'first_name', 'last_name', 'status', 'created_on'],

            personToAdd: {user_id: "", first_name: "", last_name: "", status: ""},

            searchQuery: '',

            pageDisplay: 15,

            pageTotal: 1,

            pageIndex: 1,

            people: <?php echo $objActiveWebsiteUsers->Data->ConvertToJavaScriptArray(["user_id","@division_id","@company_id","username","created_on","status","first_name","last_name","display_name"]) . PHP_EOL; ?>
        }
    });

</script>


