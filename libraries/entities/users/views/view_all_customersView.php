<?php
/**
 * Created by PhpStorm.
 * User: Micah.Zak
 * Date: 10/11/2018
 * Time: 9:43 AM
 */

$this->CurrentPage->BodyId            = "view-all-customers-page";
$this->CurrentPage->BodyClasses       = ["admin-page", "view-all-customers-page", "no-columns"];
$this->CurrentPage->Meta->Title       = "Customers | Admin | " . $this->app->objCustomPlatform->getPortalName();
$this->CurrentPage->Meta->Description = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Meta->Keywords    = "";
$this->CurrentPage->SnipIt->Title     = "Customers";
$this->CurrentPage->SnipIt->Excerpt   = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Columns           = 0;


$this->LoadVenderForPageScripts($this->CurrentPage->BodyId, "froala");
$this->LoadVendorForPageStyles($this->CurrentPage->BodyId, "froala");
$this->LoadVenderForPageScripts($this->CurrentPage->BodyId, "slim");
$this->LoadVendorForPageStyles($this->CurrentPage->BodyId, "slim");
$this->LoadVenderForPageScripts($this->CurrentPage->BodyId, ["jquery"=>"input-picker/v1.0"]);
$this->LoadVendorForPageStyles($this->CurrentPage->BodyId, ["jquery"=>"input-picker/v1.0"]);

?>
<div class="breadCrumbs">
    <div class="breadCrumbsInner<?php if ( $strApproach === "view") { echo " edit-entity"; } ?>">
        <input id="entity-page-entrance" data-source="<?php if ( $strApproach === "view") { echo 'edit-entity'; } else { echo 'list-entities'; } ?>" type="hidden"/>
        <a href="/account" class="breadCrumbHomeImageLink">
            <img src="/media/images/home-icon-01_white.png" class="breadCrumbHomeImage" width="15" height="15" />
        </a> &#187;
        <a href="/account" class="breadCrumbHomeImageLink">
            <span class="breadCrumbPage">Home</span>
        </a> &#187;
        <a href="/account/admin" class="breadCrumbHomeImageLink">
            <span class="breadCrumbPage">Admin</span>
        </a> &#187;
        <span id="view-list">
            <span class="breadCrumbPage">Customers</span>
        </span>
        <span id="editing-entity">
            <a id="backToViewEntityList" href="/account/admin/customers/" class="breadCrumbHomeImageLink">
                <span class="breadCrumbPage">Customers</span>
            </a> &#187;
            <span class="breadCrumbPage">Customer Dashboard</span>
        </span>
    </div>
</div>
<div class="BodyContentBox BodyContentBoxOld">
    <style type="text/css">
        .BodyContentBox .entityList.table-striped td {
            width:10%;
        }
        .BodyContentBox .entityList.table-striped td:first-child {
            width:5%;
        }
        .BodyContentBox .entityList.table-striped td:nth-child(5) {
            width:5%;
        }
        .breadCrumbs #editing-entity {
            display:none;
        }
        .breadCrumbs .breadCrumbsInner.edit-entity #editing-entity {
            display:inline;
        }
        .breadCrumbs .breadCrumbsInner.edit-entity #view-list {
            display:none;
        }
        .BodyContentBox .editEntityButton:not(:last-child) {
            margin-right:5px;
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
                                <h3 class="account-page-title">Customers <span class="pointer addNewEntityButton entityButtonFixInTitle"  v-on:click="addCustomer()" ></span></h3>
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
                        <th class="person_banner">Avatar</th>
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
                        <tr v-for="person in orderedPeople" v-on:dblclick="editColumn(person)">
                            <td class="person_banner"><img class="main-list-image" v-bind:src="person.main_thumb" width="50" height="50" /></td>
                            <td>{{ person.user_id }}</td>
                            <td>{{ person.username }}</td>
                            <td>{{ person.first_name }}</td>
                            <td>{{ person.last_name }}</td>
                            <td>{{ person.status }}</td>
                            <td>{{ person.created_on }}</td>
                            <td class="text-right">
                                <span v-on:click="editColumn(person)" class="pointer editEntityButton"></span>
                                <span v-on:click="deleteColumn(person)" class="pointer deleteEntityButton"></span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="formwrapper-manage-entity">
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
                                <h3 class="account-page-title"><a id="back-to-entity-list" class="back-to-entity-list pointer"></a> Customer Dashboard</h3>
                                <div class="form-search-box" v-cloak>
                                    <input v-model="searchCardQuery" class="form-control" type="text" placeholder="Search for..."/>
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
                            <div class="width33">
                                <div class="card-tile-33">
                                    <div class="width100">
                                        <h4>
                                            <span class="fas fa-user-circle fas-large desktop-25px"></span>
                                            <span class="fas-large">Profile</span>
                                            <span v-on:click="editProfile()" class="pointer editEntityButton entityButtonFixInTitle"></span>
                                            <span onclick="app.Impersonate($('#dashboard-entity-id').val());" class="pointer loginUserButton fas fa-sign-in-alt"></span>
                                        </h4>
                                        <div class="width175px">
                                            <div class="entityDetailsInner">
                                                <div class="divTable widthAuto mobile-to-100">
                                                    <div class="divRow">
                                                        <div class="divCell mobile-to-table mobile-text-center">
                                                            <img v-on:click="editProfilePhoto()" class="pointer mobile-to-75 mobile-to-block mobile-vertical-margins-15 mobile-to-heightAuto mobile-center" id="entityMainImage" width="160" height="160" data-src="<?php echo $objUser->main_thumb; ?>" src="<?php echo $objUser->main_thumb; ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="widthAutoTo175px">
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
                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="width33">
                                <div class="card-tile-33">
                                    <h4>
                                        <span class="fas fa-share-alt fas-large desktop-25px"></span>
                                        <span class="fas-large">Communication</span>
                                        <span class="pointer addNewEntityButton entityButtonFixInTitle" v-on:click="addConnection()" ></span>
                                    </h4>
                                    <div class="entityDetailsInner" style="max-height: 180px;min-height: 180px;overflow: hidden auto;">
                                        <table class="table table-striped no-top-border table-shadow" v-cloak>
                                            <tbody>
                                            <tr v-for="connection in connections" v-on:dblclick="editConnection(connection)" class="pointer">
                                                <td class="entityConnectionType" style="width:35px;text-align:center;" v-bind:alt="connection.connection_type_name" v-bind:title="connection.connection_type_name"><span v-bind:class="connection.connection_type_icon"></span></td>
                                                <td class="entityConnectionValueBox"><strong class="entityConnectionValue">{{ connection.connection_value }}</strong></td>
                                                <td class="text-right">
                                                    <span v-on:click="editConnection(connection)" class="pointer editEntityButton"></span>
                                                    <span v-on:click="deleteConnection(connection)" class="pointer deleteEntityButton"></span>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="width33">
                                <div class="card-tile-33">
                                    <h4>
                                        <span class="fas fa-home fas-large desktop-25px"></span>
                                        <span class="fas-large">Addresses</span>
                                        <span class="pointer addNewEntityButton entityButtonFixInTitle" v-on:click="addAddress()"></span>
                                    </h4>
                                    <div class="entityDetailsInner" style="max-height: 180px;min-height: 180px;overflow: hidden auto;">
                                        <table class="table table-striped no-top-border table-shadow" v-cloak>
                                            <tbody>
                                            <tr v-for="address in addresses" v-on:dblclick="editAddress(address)" class="pointer">
                                                <td>{{ address.display_name }}: </td>
                                                <td><strong id="entityAddressName">{{ address.address_1 }} {{ address.address_2 }}, {{ address.city }} {{ address.state }} {{ address.zip }}</strong></td>
                                                <td class="text-right">
                                                    <span v-on:click="editAddress(address)" class="pointer editEntityButton"></span>
                                                    <span v-on:click="deleteAddress(address)" class="pointer deleteEntityButton"></span>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="width100 entityDetails" style="display:none;">
                            <div class="width50">
                                <div class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-sticky-note fas-large desktop-25px"></span>
                                        <span class="fas-large">Notes</span>
                                        <span class="pointer addNewEntityButton entityButtonFixInTitle" v-on:click="addNote()" ></span>
                                    </h4>
                                    <div class="entityDetailsInner">
                                        <table class="table table-striped no-top-border table-shadow" v-cloak>
                                            <tbody>
                                            <tr v-for="note in notes" v-on:dblclick="editNote(note)" class="pointer">
                                                <td>{{ note.created_on }}: </td>
                                                <td><strong id="entityAddressName">{{ note.type }}</strong></td>
                                                <td><strong id="entityAddressName">{{ note.summary }}</strong></td>
                                                <td class="text-right">
                                                    <span v-on:click="editNote(note)" class="pointer editEntityButton"></span>
                                                    <span v-on:click="deleteNote(note)" class="pointer deleteEntityButton"></span>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="width50">
                                <div class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-credit-card fas-large desktop-25px"></span>
                                        <span class="fas-large">Transactions</span>
                                        <span class="pointer addNewEntityButton entityButtonFixInTitle" v-on:click="addTransaction()" ></span>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    <div class="entityTab" data-tab="cards">
                        <div class="width100 entityDetails">
                            <div class="card-tile-100">
                                <table class="table header-table" style="margin-bottom:0px;">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <h4 class="account-page-subtitle">Cards <span style="display:none;" class="pointer addNewEntityButton entityButtonFixInTitle"  v-on:click="addCard()" ></span></h4>
                                            <div class="form-search-box" v-cloak>
                                                <input v-model="searchCardQuery" class="form-control" type="text" placeholder="Search for..."/>
                                            </div>
                                        </td>
                                        <td class="text-right page-count-display" style="vertical-align: middle;">
                                            <span class="page-count-display-data">
                                                Current: <span>{{ cardIndex }}</span>
                                                Pages: <span>{{ totalCards }}</span>
                                            </span>
                                            <button v-on:click="prevCard()" class="btn prev-btn" :disabled="cardIndex == 1">Prev</button>
                                            <button v-on:click="nextCard()" class="btn" :disabled="cardIndex == totalCards">Next</button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                                <table class="table table-striped" style="margin-top:10px;">
                                    <thead>
                                    <th>Banner</th>
                                    <th v-for="cardColumn in cardColumns">
                                        <a v-on:click="orderByCard(cardColumn)" v-bind:class="{ active : orderKeyCard == cardColumn, sortasc : sortByTypeCard == true, sortdesc : sortByTypeCard == false }">
                                            {{ cardColumn | ucWords }}
                                        </a>
                                    </th>
                                    <th class="text-right">
                                        Actions
                                    </th>
                                    </thead>
                                    <tbody>
                                    <tr v-for="card in orderedCards" v-on:dblclick="gotoCard(card)">
                                        <td class="cards_banner"><img class="main-list-image" v-bind:src="card.main_thumb" width="75" height="75" /></td>
                                        <td>{{ card.card_id }}</td>
                                        <td class="cards_card_num"><a v-bind:href="'<?php echo $this->app->objCustomPlatform->getFullPublicDomainName(); ?>/' + card.card_num" target="_blank">{{ card.card_num }}</a></td>
                                        <td class="cards_card_vanity_url"><a v-bind:href="'<?php echo $this->app->objCustomPlatform->getFullPublicDomainName(); ?>' + ((card.card_vanity_url) ? '/' + card.card_vanity_url : '')" target="_blank">{{ card.card_vanity_url }}</a></td>
                                        <td>{{ card.status }}</td>
                                        <td>{{ card.card_name }}</td>
                                        <td>{{ card.card_type_id }}</td>
                                        <td>{{ card.created_on }}</td>
                                        <td>{{ card.last_updated }}</td>
                                        <td class="text-right">
                                            <span v-on:click="editCardProfile(card)" class="pointer editEntityButton"></span>
                                            <span v-on:click="deleteCard(card)" class="pointer deleteEntityButton"></span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>

                    <div class="entityTab" data-tab="billing">
                        <div class="width100 entityDetails">
                            <div class="card-tile-100">
                                <h4 class="account-page-subtitle">
                                    <span class="fas fa-credit-card fas-large desktop-25px"></span>
                                    <span class="fas-large">Transactions</span>
                                </h4>
                                <table class="table table-striped" style="margin-top:10px;">
                                    <thead>
                                    <th v-for="transColumn in transColumns">
                                        <a v-on:click="orderByTrans(transColumn)" v-bind:class="{ active : orderKeyTrans == transColumn, sortasc : sortByTypeTrans == true, sortdesc : sortByTypeTrans == false }">
                                            {{ transColumn | ucWords }}
                                        </a>
                                    </th>
                                    <th class="text-right">
                                        Actions
                                    </th>
                                    </thead>
                                    <tbody>
                                    <tr v-for="trans in orderedTrans" v-on:dblclick="viewTrans(trans)">
                                        <td>{{ trans.card_id }}</td>
                                        <td>{{ trans.status }}</td>
                                        <td>{{ trans.card_name }}</td>
                                        <td>{{ trans.card_num }}</td>
                                        <td>{{ trans.card_type_id }}</td>
                                        <td>{{ trans.created_on }}</td>
                                        <td>{{ trans.last_updated }}</td>
                                        <td class="text-right">
                                            <span v-on:click="deleteTrans(trans)" class="pointer deleteEntityButton"></span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="entityTab" data-tab="activity">
                        <div class="width100 entityDetails">
                            <div class="card-tile-100">
                                <h4 class="account-page-subtitle">
                                    <span class="fas fa-walking fas-large desktop-20px"></span>
                                    <span class="fas-large">Customer Activity</span>
                                </h4>
                                <div class="form-search-box" v-cloak>
                                    <input v-model="searchActivityQuery" class="form-control" type="text" placeholder="Search for..."/>
                                </div>
                                <table class="table table-striped" style="margin-top:10px;">
                                    <thead>
                                    <th v-for="activityColumn in activityColumns">
                                        <a v-on:click="orderByActivity(activityColumn)" v-bind:class="{ active : orderKeyActivity == activityColumn, sortasc : sortByTypeActivity == true, sortdesc : sortByTypeActivity == false }">
                                            {{ activityColumn | ucWords }}
                                        </a>
                                    </th>
                                    </thead>
                                    <tbody>
                                    <tr v-for="activity in orderedActivity">
                                        <td>{{ activity.created_on }}</td>
                                        <td>{{ activity.action }}</td>
                                        <td>{{ activity.entity_name }}</td>
                                        <td>{{ activity.entity_id }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript">
    let customerApp = new Vue({

        el: '#app',

        computed:
            {
                totalPages: function()
                {
                    return this.pageTotal;
                },

                totalCards: function()
                {
                    return this.cardTotal;
                },

                orderedCards: function()
                {
                    var self = this;

                    let objSortedCards = this.sortedEntity(this.searchCardQuery, this.cards, this.orderKeyCard, this.sortByTypeCard, this.cardIndex,  this.cardDisplay, this.cardTotal, function(data) {
                        self.cardTotal = data.pageTotal;
                        self.cardIndex = data.pageIndex;
                    });

                    return objSortedCards;
                },

                orderedPeople: function()
                {
                    var self = this;

                    let objSortedPeople = this.sortedEntity(this.searchQuery, this.people, this.orderKey, this.sortByType, this.pageIndex,  this.pageDisplay, this.pageTotal, function(data) {
                        self.pageTotal = data.pageTotal;
                        self.pageIndex = data.pageIndex;
                    });

                    return objSortedPeople;
                },

                orderedTrans: function()
                {
                    var self = this;

                    let objSortedTrans = this.sortedEntity(this.searchTransQuery, this.transactions, this.orderKeyTrans, this.sortByTypeTrans, this.transIndex,  this.transDisplay, this.transTotal, function(data) {
                        self.transTotal = data.pageTotal;
                        self.transIndex = data.pageIndex;
                    });

                    return objSortedTrans;
                },

                orderedActivity: function()
                {
                    var self = this;

                    let objSortedActivity = this.sortedEntity(this.searchActivityQuery, this.userActivities, this.orderKeyActivity, this.sortByTypeActivity, this.transIndex,  this.transDisplay, this.transTotal, function(data) {
                        self.activityTotal = data.pageTotal;
                        self.activityIndex = data.pageIndex;
                    });

                    return objSortedActivity;
                }
            },

        filters: {
            ucWords: function(str)
            {
                return str.replace(/_/g," ").replace(/\w\S*/g, function (txt) {
                    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                });
            },

            orderBy: function(type)
            {

            }
        },

        methods: {
            orderByColumn: function(column)
            {

                this.sortByType = ( this.orderKey == column ) ? ! this.sortByType : this.sortByType;

                this.orderKey = column;
            },

            orderByCard: function(column)
            {

                this.sortByTypeCard = ( this.orderKeyCard == column ) ? ! this.sortByTypeCard : this.sortByTypeCard;

                this.orderKeyCard = column;
            },

            orderByTrans: function(column)
            {

                this.sortByTypeTrans = ( this.orderKeyTrans == column ) ? ! this.sortByTypeTrans : this.sortByTypeTrans;

                this.orderKeyTrans = column;
            },

            orderByActivity: function(column)
            {

                this.sortByTypeActivity = ( this.orderKeyActivity == column ) ? ! this.sortByTypeActivity : this.sortByTypeActivity;

                this.orderKeyActivity = column;
            },

            sortedEntity: function (searchQuery, entity, orderkey, sortByType, pageIndex, pageDisplay, pageTotal, callback)
            {
                var returnData = {};

                returnData.pageIndex = pageIndex;

                let objOrderedEntity = _.orderBy(entity, orderkey, sortByType ? 'asc' : 'desc');

                let intStartIndex = ((returnData.pageIndex-1) * pageDisplay);
                let intIndexOffset = entity.length - intStartIndex;
                let intEndIndex = intStartIndex + (( pageDisplay <= intIndexOffset ) ? pageDisplay : intIndexOffset);

                if (!searchQuery) {
                    var intTotalPages = 1;

                    if (pageDisplay < objOrderedEntity.length) {
                        intTotalPages = objOrderedEntity.length / pageDisplay;
                    }

                    returnData.pageTotal = Math.ceil(intTotalPages);

                    if ( typeof callback === "function") {
                        callback(returnData);
                    }

                    return objOrderedEntity.slice(intStartIndex, intEndIndex);
                }

                let objFilteredEntity = objOrderedEntity.filter(function (currEntity)
                {
                    let searchRegex = new RegExp(searchQuery, 'i');
                    let intFoundMatch = false;
                    let arFoundMatch = false;
                    let arEntityKeys = Object.keys(currEntity);

                    for (let entityField in currEntity)
                    {
                        if ( typeof filterFields !== "undefined")
                        {
                            if(arFoundMatch[entityField] === true)
                            {
                                continue;
                            }

                            for (let indexFilters in filterFields)
                            {
                                if (arEntityKeys[entityField] == filterFields[indexFilters] )
                                {
                                    arFoundMatch[entityField] = true;
                                    continue;
                                }
                            }
                        }

                        if (searchRegex.test(currEntity[entityField])) {
                            //console.log(searchQuery);
                            intFoundMatch = true;
                        }
                    }

                    if (intFoundMatch == true) {

                        if ( typeof callback === "function") {
                            callback(returnData);
                        }

                        return currEntity;
                    }
                });

                let intOrderedIndexOffset = objOrderedEntity.length - intStartIndex;
                let intOrderedEndIndex = intStartIndex + (( pageDisplay <= intOrderedIndexOffset ) ? pageDisplay : intOrderedIndexOffset);

                if (objFilteredEntity.length < intStartIndex) {

                    intStartIndex = Math.floor(objFilteredEntity.length / pageDisplay) * pageDisplay;
                    returnData.pageIndex =  Math.ceil(objFilteredEntity.length / pageDisplay);
                    intOrderedIndexOffset = objFilteredEntity.length - intStartIndex;
                    intOrderedEndIndex = intStartIndex + intOrderedIndexOffset;
                }

                var intTotalFilteredPages = 1;

                if (pageDisplay < objFilteredEntity.length) {
                    intTotalFilteredPages = objFilteredEntity.length / pageDisplay;
                }

                returnData.pageTotal = Math.ceil(intTotalFilteredPages);

                if ( typeof callback === "function") {
                    callback(returnData);
                }

                return objFilteredEntity.slice(intStartIndex, intOrderedEndIndex);
            },

            addPerson: function()
            {
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

            editColumn: function(person)
            {
                let stateObj = { foo: "bar" };

                modal.EngageFloatShield();

                $('#dashboard-entity-id').val(person.user_id);
                $('#entityFullName').html(person.first_name + " " + person.last_name);
                $('#entityUserName').html(person.username);
                $('#entityStatus').html(person.status);

                $(".formwrapper-outer").addClass("edit-entity");
                $(".breadCrumbsInner").addClass("edit-entity");

                let userIdParameter = "user_id=" + person.user_id;

                $(".entityDashboard").show();
                $(".entity404").hide();

                this.cards = [];
                this.connections = [];
                this.addresses = [];
                this.notes = [];

                console.log(userIdParameter);

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

                    $('#entityMainImage').attr("data-src", objUserResult.data.user.main_thumb);
                    $('#entityMainImage').attr("src", objUserResult.data.user.main_thumb);
                    $('#entityPrimaryPhone').html(formatAsPhoneIfApplicable(objUserResult.data.user.user_phone));
                    $('#entityPrimaryEmail').html(objUserResult.data.user.user_email);

                    for(let intCardIndex in objUserResult.data.cards)
                    {
                        customerApp.cards.push(objUserResult.data.cards[intCardIndex]);
                    }

                    for(let intConnectionIndex in objUserResult.data.connections)
                    {
                        customerApp.connections.push(objUserResult.data.connections[intConnectionIndex]);
                    }

                    for(let intAddressIndex in objUserResult.data.addresses)
                    {
                        customerApp.addresses.push(objUserResult.data.addresses[intAddressIndex]);
                    }

                    for(let intActivitiesIndex in objUserResult.data.activities)
                    {
                        customerApp.userActivities.push(objUserResult.data.activities[intActivitiesIndex]);
                    }

                    for(let intActivitiesIndex in objUserResult.data.notes)
                    {
                        customerApp.notes.push(objUserResult.data.notes[intActivitiesIndex]);
                    }

                    // success
                    history.pushState(stateObj, "View Customer", "/account/admin/customers/view-customer?id=" + person.user_id);

                    dash.loadDashboardTabs();

                    modal.CloseFloatShield();

                },"POST");
            },

            deleteColumn: function(person)
            {
                this.people = this.people.filter(function (curPerson) {
                    return person.user_id != curPerson.user_id;
                });
            },

            prevPage: function()
            {
                this.pageIndex--;
                this.people = this.people;
            },

            nextPage: function()
            {
                this.pageIndex++;
                this.people = this.people;
            },

            prevCard: function()
            {
                this.cardIndex--;
                this.cards = this.cards;
            },

            nextCard: function()
            {
                this.cardIndex++;
                this.cards = this.cards;
            },

            addCustomer: function()
            {
                modal.EngageFloatShield();
                let data = {};
                data.title = "Add Customer";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 750, 115, true);

                let strViewRequestParameter = "view=addCustomer";

                modal.AssignViewToPopup("/customers/user-data/get-customer-dashboard-views", strViewRequestParameter, function()
                    {
                        modal.EngageFloatShield();
                    },
                    function (objResult)
                    {
                        let objNewUser = objResult.data.user[Object.keys(objResult.data.user)[0]];

                        customerApp.cards.push(objNewUser);

                        modal.CloseFloatShield(function() {
                            modal.CloseFloatShield();
                        },500);

                        customerApp.editColumn(objNewUser);
                    },
                    function (objValidate)
                    {
                        let intErrors = 0;

                        if ($("#addCustomerForm .error-text").length > 0)
                        {
                            return false;
                        }

                        $("#addCustomerForm .error-validation[name!=username]").removeClass("error-validation");

                        for(let intFieldIndex in objValidate)
                        {
                            if (objValidate[intFieldIndex].value == "" && objValidate[intFieldIndex].name != "username" && objValidate[intFieldIndex].name != "password")
                            {
                                intErrors++;
                                $("#addCustomerForm input[name=" + objValidate[intFieldIndex].name + "], #addCustomerForm select[name=" + objValidate[intFieldIndex].name + "]").addClass("error-validation");
                            }
                        }

                        if (intErrors > 0)
                        {
                            return false;
                        }

                        return true;
                    });
            },

            addCard: function()
            {
                modal.EngageFloatShield();
                let data = {};
                data.title = "Add New EZcard";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 750, 115, true);

                let intUserId = $("#dashboard-entity-id").val();
                let strViewRequestParameter = "view=addCardAdmin&user_id=" + intUserId;

                modal.AssignViewToPopup("/cards/card-data/get-card-dashboard-views", strViewRequestParameter, function()
                {
                    modal.EngageFloatShield();
                },
                function (objResult)
                {
                    let objNewCard = objResult.data.card;
                    customerApp.cards.push(objNewCard);

                    modal.CloseFloatShield(function() {
                        modal.CloseFloatShield();
                    },500);
                },
                function (objValidate)
                {
                    let intErrors = 0;
                    if ($("#addCardAdminForm .error-text").length > 0)
                    {
                        return false;
                    }

                    $("#addCardAdminForm .error-validation[name!=card_vanity_url][name!=card_keyword]").removeClass("error-validation");

                    for(let intFieldIndex in objValidate)
                    {
                        if (objValidate[intFieldIndex].value == "" && objValidate[intFieldIndex].name !== "card_vanity_url" && objValidate[intFieldIndex].name !== "card_keyword")
                        {
                            intErrors++;
                            $("#addCardAdminForm input[name=" + objValidate[intFieldIndex].name + "], #addCardAdminForm select[name=" + objValidate[intFieldIndex].name + "]").addClass("error-validation");
                        }
                    }

                    if ( $("#addCardAdminForm .error-validation[name=card_owner]").val() == "")
                    {
                        intErrors++;
                        let strCardOwnerId = $("#addCardAdminForm .error-validation[name=card_owner]").attr("id").replace("_id","");
                        $("#" + strCardOwnerId).addClass("error-validation").blur(function() {
                            $("#" + strCardOwnerId).removeClass("error-validation");
                        });
                    }

                    if ( $("#addCardAdminForm .error-validation[name=card_affiliate]").val() == "")
                    {
                        intErrors++;
                        let strCardAffiliateId = $("#addCardAdminForm .error-validation[name=card_affiliate]").attr("id").replace("_id","");
                        $("#" + strCardAffiliateId).addClass("error-validation").blur(function() {
                            $("#" + strCardAffiliateId).removeClass("error-validation");
                        });
                    }

                    if (intErrors > 0)
                    {
                        return false;
                    }

                    return true;
                });
            },

            gotoCard: function(card)
            {
                window.location = "<?php echo getFullUrl() . "/"; ?>account/admin/cards/card-dashboard/" + card.sys_row_id;
            },

            editCardProfile: function(card)
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Edit Card Profile";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 750, 115, true);

                var intEntityId = card.card_id;
                var strViewRequestParameter = "view=editCardAdmin&card_id=" + intEntityId;

                modal.AssignViewToPopup("/cards/card-data/get-card-dashboard-views", strViewRequestParameter, function()
                    {
                        modal.EngageFloatShield();
                    },
                    function (objResult)
                    {
                        $('#entityCardName').html(objResult.card.card_name);

                        if (objResult.card.card_vanity_url)
                        {
                            $('#entityVanityUrl').html("<?php echo getFullPublicUrl(); ?>/" + objResult.card.card_vanity_url);
                        }
                        else
                        {
                            $('#entityVanityUrl').html("<?php echo getFullPublicUrl(); ?>/" + objResult.card.card_num);
                        }

                        console.log($("#editCardAdminForm input[name=status]").val());

                        $('#entityCardType').html($("#editCardAdminForm select[name=template_id] option:selected").text());
                        $('#entityPackage').html($("#editCardAdminForm select[name=product_id] option:selected").text());
                        $('#entityStatus').html($("#editCardAdminForm select[name=status] option:selected").text());

                        modal.CloseFloatShield(function() {
                            modal.CloseFloatShield();
                        },500);
                    },
                    function (objValidate)
                    {
                        let intErrors = 0;

                        if ($("#editCardAdminForm .error-text").length > 0)
                        {
                            return false;
                        }

                        $("#editCardAdminForm .error-validation[name!=card_vanity_url][name!=card_keyword]").removeClass("error-validation");

                        for(let intFieldIndex in objValidate)
                        {
                            if (objValidate[intFieldIndex].value == "" && objValidate[intFieldIndex].name != "card_vanity_url" && objValidate[intFieldIndex].name != "card_keyword")
                            {
                                intErrors++;
                                $("#editCardAdminForm input[name=" + objValidate[intFieldIndex].name + "], #editCardAdminForm select[name=" + objValidate[intFieldIndex].name + "]").addClass("error-validation");
                            }
                        }

                        if (intErrors > 0)
                        {
                            console.log("errors: " + intErrors);
                            return false;
                        }

                        return true;
                    });
            },

            editProfilePhoto: function()
            {
                modal.EngageFloatShield();
                let data = {};
                data.title = "Edit Profile Photo";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 500, 115, true);

                let strViewRequestParameter = "view=editProfilePhoto&user_id=" + $('#dashboard-entity-id').val();

                modal.AssignViewToPopup("/customers/user-data/get-customer-dashboard-views", strViewRequestParameter);
            },

            addConnection: function()
            {
                modal.EngageFloatShield();
                let data = {};
                data.title = "Add Customer Connection";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 750, 115, true);

                let intEntityId = $('#dashboard-entity-id').val();
                let strViewRequestParameter = "view=addConnection&user_id=" + intEntityId;

                console.log(strViewRequestParameter);

                modal.AssignViewToPopup("/customers/user-data/get-customer-dashboard-views", strViewRequestParameter, function()
                    {
                        modal.EngageFloatShield();
                    },
                    function (objResult)
                    {
                        customerApp.connections.push(objResult.connection);

                        modal.CloseFloatShield(function() {
                            modal.CloseFloatShield();
                        },500);
                    },
                    function (objValidate)
                    {
                        if ($("#addConnectionForm #connection_type_id").val() == "")
                        {
                            $("#addConnectionForm #connection_type_id").addClass("error-validation").blur(function() {
                                $(this).removeClass("error-validation");
                            });

                            return false;
                        }

                        if ($("#addConnectionForm #connection_value").val() == "")
                        {
                            $("#addConnectionForm #connection_value").addClass("error-validation").blur(function() {
                                $(this).removeClass("error-validation");
                            });

                            return false;
                        }

                        return true;
                    });
            },

            addAddress: function()
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Add Customer Address";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 750, 115, true);

                var intEntityId = $('#dashboard-entity-id').val();
                var strViewRequestParameter = "view=addAddress&user_id=" + intEntityId;

                modal.AssignViewToPopup("/customers/user-data/get-customer-dashboard-views", strViewRequestParameter, function()
                    {
                        modal.EngageFloatShield();
                    },
                    function (objResult)
                    {
                        console.log(objResult);
                        customerApp.addresses.push(objResult.address);

                        modal.CloseFloatShield(function() {
                            modal.CloseFloatShield();
                        },500);
                    },
                    function (objValidate)
                    {
                        let intErrors = 0;

                        if ($("#addAddressForm .error-text").length > 0)
                        {
                            return false;
                        }

                        $("#addAddressForm .error-validation").removeClass("error-validation");

                        for(let intFieldIndex in objValidate)
                        {
                            if (objValidate[intFieldIndex].value == "" && objValidate[intFieldIndex].name != "address_2")
                            {
                                intErrors++;
                                $("#addAddressForm input[name=" + objValidate[intFieldIndex].name + "], #addAddressForm select[name=" + objValidate[intFieldIndex].name + "]").addClass("error-validation");
                            }
                        }

                        if (intErrors > 0)
                        {
                            console.log("errors: " + intErrors);
                            return false;
                        }

                        return true;
                    });
            },

            editProfile: function()
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Edit Customer Profile";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 750, 115, true);

                var intEntityId = $('#dashboard-entity-id').val();
                var strViewRequestParameter = "view=editProfile&user_id=" + intEntityId;

                modal.AssignViewToPopup("/customers/user-data/get-customer-dashboard-views", strViewRequestParameter, function()
                    {
                        modal.EngageFloatShield();
                    },
                    function (objResult)
                    {
                        console.log(objResult);

                        $('#entityFullName').html(objResult.customer.first_name + " " + objResult.customer.last_name);
                        $('#entityUserName').html(objResult.customer.username);
                        $('#entityStatus').html(objResult.customer.status);

                        modal.CloseFloatShield(function() {
                            modal.CloseFloatShield();
                        },500);
                    },
                    function (objValidate)
                    {
                        let intErrors = 0;

                        if ($("#editProfileForm .error-text").length > 0)
                        {
                            return false;
                        }

                        $("#editProfileForm .error-validation[name!=username]").removeClass("error-validation");

                        for(let intFieldIndex in objValidate)
                        {
                            if (objValidate[intFieldIndex].value == "" && objValidate[intFieldIndex].name != "username" && objValidate[intFieldIndex].name != "password" && objValidate[intFieldIndex].name != "card_affiliate")
                            {
                                intErrors++;
                                console.log(objValidate[intFieldIndex].name);
                                $("#editProfileForm input:not(.inputpicker-original)[name=" + objValidate[intFieldIndex].name + "], #editProfileForm select[name=" + objValidate[intFieldIndex].name + "]").addClass("error-validation");
                            }

                            if ( objValidate[intFieldIndex].name == "card_affiliate" && objValidate[intFieldIndex].value == "" )
                            {
                                intErrors++;
                                $("#editProfileForm input.affiliate_name_for_user").addClass("error-validation").blur(function() {
                                    $(this).removeClass("error-validation");
                                });
                            }
                        }

                        if (intErrors > 0)
                        {
                            return false;
                        }

                        return true;
                    });
            },

            editAccount: function()
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Edit Customer Account";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 750, 115, true);

                var intEntityId = $('#dashboard-entity-id').val();
                var strViewRequestParameter = "view=editAccount&user_id=" + intEntityId;

                modal.AssignViewToPopup("/customers/user-data/get-customer-dashboard-views", strViewRequestParameter);
            },

            editConnection: function(connection)
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Edit Customer Connection";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 750, 115, true);

                var strViewRequestParameter = "view=editConnection&connection_id=" + connection.connection_id;

                modal.AssignViewToPopup("/customers/user-data/get-customer-dashboard-views", strViewRequestParameter, function()
                    {
                        modal.EngageFloatShield();
                    },
                    function (objResult)
                    {
                        connection.connection_type_id = objResult.connection.connection_type_id;
                        connection.connection_value = objResult.connection.connection_value;

                        modal.CloseFloatShield(function() {
                            modal.CloseFloatShield();
                        },500);
                    },
                    function (objValidate)
                    {
                        if ($("#addConnectionForm #connection_type_id").val() == "")
                        {
                            $("#addConnectionForm #connection_type_id").addClass("error-validation").blur(function() {
                                $(this).removeClass("error-validation");
                            });

                            return false;
                        }

                        if ($("#addConnectionForm #connection_value").val() == "")
                        {
                            $("#addConnectionForm #connection_value").addClass("error-validation").blur(function() {
                                $(this).removeClass("error-validation");
                            });

                            return false;
                        }

                        return true;
                    });
            },

            deleteConnection: function(connection)
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Delete Customer Connection?";
                data.html = "Are you sure?<br>This will remove it from all other card associations as well.";
                modal.EngagePopUpConfirmation(data, function() {
                    let intConnectionId = connection.connection_id;
                    let intUserId = $('#dashboard-entity-id').val();
                    ajax.Send("users/user-data/update-user-data?type=delete-connection&id=" + intUserId + "&connection_id=" + intConnectionId, null, function(result){
                        console.log(JSON.stringify(result));
                    },"POST");
                    customerApp.connections = customerApp.connections.filter(function (currConnection) {
                        return connection.connection_id != currConnection.connection_id;
                    });
                    modal.CloseFloatShield(function() {
                        modal.CloseFloatShield();
                    });
                }, 400, 115);
            },

            editAddress: function(address)
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Edit Customer Address";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 750, 115, true);

                var strViewRequestParameter = "view=editAddress&address_id=" + address.address_id;

                modal.AssignViewToPopup("/customers/user-data/get-customer-dashboard-views", strViewRequestParameter, function()
                    {
                        modal.EngageFloatShield();
                    },
                    function (objResult)
                    {
                        address.display_name = objResult.address.display_name;
                        address.address_1 = objResult.address.address_1;
                        address.address_2 = objResult.address.address_2;
                        address.city = objResult.address.city;
                        address.state = objResult.address.state;
                        address.zip = objResult.address.zip;
                        address.country = objResult.address.country;

                        modal.CloseFloatShield(function() {
                            modal.CloseFloatShield();
                        },500);
                    },
                    function (objValidate)
                    {
                        let intErrors = 0;

                        if ($("#addAddressForm .error-text").length > 0)
                        {
                            return false;
                        }

                        $("#addAddressForm .error-validation").removeClass("error-validation");

                        for(let intFieldIndex in objValidate)
                        {
                            if (objValidate[intFieldIndex].value == "" && objValidate[intFieldIndex].name != "address_2")
                            {
                                intErrors++;
                                $("#addAddressForm input[name=" + objValidate[intFieldIndex].name + "], #addAddressForm select[name=" + objValidate[intFieldIndex].name + "]").addClass("error-validation");
                            }
                        }

                        if (intErrors > 0)
                        {
                            console.log("errors: " + intErrors);
                            return false;
                        }

                        return true;
                    });
            },

            deleteAddress: function(address)
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Delete Customer Address?";
                data.html = "Are you sure?<br>This will remove it from all other associations as well.";
                modal.EngagePopUpConfirmation(data, function() {
                    let intAddressId = address.address_id;
                    let intUserId = $('#dashboard-entity-id').val();
                    ajax.Send("users/user-data/update-user-data?type=delete-address&id=" + intUserId + "&address_id=" + intAddressId, null, function(result){
                        console.log(JSON.stringify(result));
                    },"POST");
                    customerApp.addresses = customerApp.addresses.filter(function (currAddress) {
                        return address.address_id != currAddress.address_id;
                    });
                    modal.CloseFloatShield(function() {
                        modal.CloseFloatShield();
                    });
                }, 400, 115);
            },

            addNote: function(note)
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Add Note";
                var strViewRequestParameter = "view=editConnection&connection_id=" + note.note_id;

                modal.AssignViewToPopup("/customers/user-data/get-customer-dashboard-views", strViewRequestParameter, function()
                    {
                        modal.EngageFloatShield();
                    },
                    function (objResult)
                    {

                    },
                    function (objValidate)
                    {

                    });
            },


            editNote: function(note)
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Edit Note";
                var strViewRequestParameter = "view=editConnection&connection_id=" + note.note_id;

                modal.AssignViewToPopup("/customers/user-data/get-customer-dashboard-views", strViewRequestParameter, function()
                    {
                        modal.EngageFloatShield();
                    },
                    function (objResult)
                    {

                    },
                    function (objValidate)
                    {

                    });
            },

            renderConnectionIcon: function(string)
            {
                let iconEl = document.createElement('span');
                iconEl.classList.add("fas");

                switch(string)
                {
                    case "Business Phone":
                        return '<span class="fas fa-business"></span>';
                }
            },

            BatchLoadCards: function()
            {
                this.batchOffset++;

                setTimeout(function()
                {
                    let strBatchUrl = "users/user-data/get-customer-batches?offset=" + customerApp.batchOffset;

                    ajax.Send(strBatchUrl, null, function(objCardResult)
                    {
                        for(let currCardIndex in objCardResult.data.people)
                        {
                            customerApp.people.push(objCardResult.data.people[currCardIndex]);
                        }

                        customerApp.pageTotal = customerApp.people / customerApp.pageDisplay;

                        if (objCardResult.end == "false")
                        {
                            customerApp.BatchLoadCards();
                        }
                    });
                },50);
            },
        },

        data:
        {
            batchOffset: 0,
            orderKey: 'user_id',
            orderKeyCard: 'last_updated',
            orderKeyTrans: 'transaction_id',
            orderKeyActivity: 'created_on',

            sortByType: true,
            sortByTypeCard: false,
            sortByTypeTrans: true,
            sortByTypeActivity: false,

            columns: ['user_id', 'username', 'first_name', 'last_name', 'status', 'created_on'],
            cardColumns: ['card_id', 'card_num', 'card_vanity_url', 'status', 'card_name', 'card_type', 'created_on', 'last_updated'],
            transColumns: ['transaction_id', 'status', 'card_name', 'card_num', 'card_type', 'parent_card_id', 'created_on', 'last_updated'],
            activityColumns: ['created_on', 'action', 'entity_name', 'entity_id'],

            searchQuery: '',
            searchCardQuery: '',
            searchTransQuery: '',
            searchActivityQuery: '',

            pageDisplay: 15,
            cardDisplay: 10,
            transDisplay: 5,
            activityDisplay: 15,

            pageTotal: 1,
            cardTotal: 1,
            transTotal: 1,
            activityTotal: 1,

            pageIndex: 1,
            cardIndex: 1,
            transIndex: 1,
            activityIndex: 1,

            people: <?php if ($objActiveCustomers->Data !== null) { echo $objActiveCustomers->getData()->ConvertToJavaScriptArray([
                    "main_thumb",
                    "user_id",
                    "username",
                    "created_on",
                    "status",
                    "first_name",
                    "last_name",
                ]) . PHP_EOL; } else { echo "[]"; } ?>,

            cards: <?php if(!empty($colUserCards)) { echo $colUserCards->ConvertToJavaScriptArray([
                    "main_thumb",
                    "card_id",
                    "card_num",
                    "card_vanity_url",
                    "status",
                    "card_name",
                    "card_type_id",
                    "created_on",
                    "last_updated",
                    "sys_row_id",
                ]) . PHP_EOL; } else { echo "[]"; } ?>,

            transactions: [],

            notes:  <?php if(!empty($colNotes)) { echo $colNotes->ConvertToJavaScriptArray([
                    "note_id",
                    "type",
                    "summary",
                    "description",
                    "created_on"
                ]) . PHP_EOL; } else { echo "[]"; } ?>,

            connections: <?php if(!empty($colUserConnections)) { echo $colUserConnections->ConvertToJavaScriptArray([
                    "connection_id",
                    "connection_type_name",
                    "connection_type_icon",
                    "connection_value"
                ]) . PHP_EOL; } else { echo "[]"; } ?>,

            addresses: <?php if(!empty($colUserAddresses)) { echo $colUserAddresses->ConvertToJavaScriptArray([
                    "address_id",
                    "display_name",
                    "address_1",
                    "address_2",
                    "address_3",
                    "city",
                    "state",
                    "zip",
                    "country",
                    "phone_number",
                    "is_primary",
                ]) . PHP_EOL; } else { echo "[]"; } ?>,

            userActivities: <?php if(!empty($colUserActivities)) { echo $colUserActivities->ConvertToJavaScriptArray([
                    "log_user_id",
                    "user_id",
                    "action",
                    "entity_name",
                    "entity_id",
                    "created_on"
                ]) . PHP_EOL; } else { echo "[]"; } ?>
            },
        mounted() {
            this.BatchLoadCards();
        }
    });

    dash.processTabDisplay(sessionStorage.getItem('dashboard-tab'));

    window.addEventListener('popstate', function(e) {
        // going back from edit?
        if (e.state == null)
        {
            $(".formwrapper-outer").removeClass("edit-entity");
            $(".breadCrumbsInner").removeClass("edit-entity");
        }
    });

    document.getElementById("backToViewEntityList").addEventListener("click", function(event){
        event.preventDefault()
        dash.goBackToEntityList("/account/admin/customers");
    });

    document.getElementById("back-to-entity-list").addEventListener("click", function(event){
        event.preventDefault()
        dash.goBackToEntityList("/account/admin/customers");
    });

    document.getElementById("back-to-entity-list-404").addEventListener("click", function(event){
        event.preventDefault()
        dash.goBackToEntityList("/account/admin/customers");
    });

</script>
