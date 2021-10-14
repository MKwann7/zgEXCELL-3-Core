<?php
/**
 * Created by PhpStorm.
 * User: Micah.Zak
 * Date: 10/11/2018
 * Time: 9:43 AM
 */

$this->CurrentPage->BodyId            = "my-profile-page";
$this->CurrentPage->BodyClasses       = ["admin-page", "my-profile-page", "no-columns"];
$this->CurrentPage->Meta->Title       = "My Profile | Admin | " . $this->app->objCustomPlatform->getPortalName();
$this->CurrentPage->Meta->Description = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Meta->Keywords    = "";
$this->CurrentPage->SnipIt->Title     = "My Profile";
$this->CurrentPage->SnipIt->Excerpt   = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Columns           = 0;

$this->LoadVenderForPageScripts($this->CurrentPage->BodyId, "slim");
$this->LoadVendorForPageStyles($this->CurrentPage->BodyId, "slim");

?>
<div class="breadCrumbs">
    <div class="breadCrumbsInner">
        <a href="/account" class="breadCrumbHomeImageLink">
            <img src="/media/images/home-icon-01_white.png" class="breadCrumbHomeImage" width="15" height="15" />
        </a> &#187;
        <a href="/account" class="breadCrumbHomeImageLink">
            <span class="breadCrumbPage">Home</span>
        </a> &#187;
        <span class="breadCrumbPage">Profile</span>
    </div>
</div>
<div class="BodyContentBox">
    <style type="text/css">
        [v-cloak] { display: none; }
    </style>
    <div id="app" class="formwrapper" >
        <div class="formwrapper-outer">
            <div class="formwrapper-view-entity">
                <div class="entityDashboard">
                    <table class="table header-table" style="margin-bottom:0px;">
                        <tbody>
                        <tr>
                            <td class="mobile-to-table">
                                <h3 class="account-page-title"> My Profile</h3>
                            </td>
                            <td class="mobile-to-table text-right page-count-display dashboard-tab-display" style="vertical-align: middle;">
                                <div data-block="profile" class="dashboard-tab fas fa-user-circle" v-bind:class="{active: sessionStorage.getItem('dashboard-tab') == 'profile'}"><span>Profile</span></div>
                                <div data-block="billing" class="dashboard-tab fas fa-credit-card" v-bind:class="{active: sessionStorage.getItem('dashboard-tab') == 'billing'}"><span>Billing</span></div>
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
                                        <span class="fas-large">Profile Details</span>
                                        <span v-on:click="editProfile()" class="pointer editEntityButton entityButtonFixInTitle"></span>
                                    </h4>
                                    <div class="entityDetailsInner">
                                        <table>
                                            <tbody>
                                            <tr>
                                                <td>Full Name: </td>
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
                                        <?php if ($objUser->user_id == 1000) { ?>
                                            <?php //dump($objUser->Roles); ?>
                                        <?php } ?>
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
                                        <span class="pointer addNewEntityButton entityButtonFixInTitle"  v-on:click="addConnection()" ></span>
                                    </h4>
                                    <div class="entityDetailsInner">
                                        <table class="table table-striped no-top-border table-shadow" v-cloak>
                                            <tbody>
                                            <tr v-for="connection in connections" v-on:dblclick="editConnection(connection)" class="pointer">
                                                <td class="entityConnectionType">{{ connection.connection_type_id }}: </td>
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
                            <div class="width50">
                                <div class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-home fas-large desktop-25px"></span>
                                        <span class="fas-large">Addresses</span>
                                        <span class="pointer addNewEntityButton entityButtonFixInTitle" v-on:click="addAddress()"></span></h4>
                                    <div class="entityDetailsInner">
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

                orderedTrans: function()
                {
                    var self = this;

                    let objSortedTrans = this.sortedEntity(this.searchTransQuery, this.transactions, this.orderKeyTrans, this.sortByTypeTrans, this.transIndex,  this.transDisplay, this.transTotal, function(data) {
                        self.transTotal = data.pageTotal;
                        self.transIndex = data.pageIndex;
                    });

                    return objSortedTrans;
                },
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

            orderByTrans: function(column)
            {

                this.sortByTypeTrans = ( this.orderKeyTrans == column ) ? ! this.sortByTypeTrans : this.sortByTypeTrans;

                this.orderKeyTrans = column;
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

                modal.AssignViewToPopup("/customers/user-data/get-customer-dashboard-views", strViewRequestParameter);
            },

            editProfile: function()
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Edit Customer Profile";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 750, 115, true);

                var intEntityId = $('#dashboard-entity-id').val();
                var strViewRequestParameter = "view=editCustomerProfile&user_id=" + intEntityId;

                modal.AssignViewToPopup("/customers/user-data/get-customer-dashboard-views", strViewRequestParameter, function()
                    {
                        modal.EngageFloatShield();
                    },
                    function (objResult)
                    {
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
                            if (objValidate[intFieldIndex].value == "" && objValidate[intFieldIndex].name != "username" && objValidate[intFieldIndex].name != "password")
                            {
                                intErrors++;
                                $("#editProfileForm input[name=" + objValidate[intFieldIndex].name + "], #editProfileForm select[name=" + objValidate[intFieldIndex].name + "]").addClass("error-validation");
                            }
                        }

                        if (intErrors > 0)
                        {
                            return false;
                        }

                        return true;
                    });
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
                    let intCardId = $('#dashboard-entity-id').val();
                    ajax.Send("users/user-data/update-user-data?type=delete-connection&id=" + intCardId + "&connection_id=" + intConnectionId, null, null,"POST");
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

                modal.AssignViewToPopup("/customers/user-data/get-customer-dashboard-views", strViewRequestParameter);
            },
        },

        data:
            {
                orderKey: 'user_id',
                orderKeyTrans: 'transaction_id',

                sortByType: true,
                sortByTypeTrans: true,

                columns: ['user_id', 'username', 'first_name', 'last_name', 'status', 'created_on'],
                transColumns: ['transaction_id', 'status', 'card_name', 'card_num', 'card_type', 'parent_card_id', 'created_on', 'last_updated'],

                searchQuery: '',
                searchTransQuery: '',

                pageDisplay: 15,
                transDisplay: 5,

                pageTotal: 1,
                transTotal: 1,

                pageIndex: 1,
                transIndex: 1,

                transactions: [],

                connections: <?php if(!empty($colUserConnections)) { echo $colUserConnections->ConvertToJavaScriptArray([
                        "connection_id",
                        "connection_type_id",
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
                    ]) . PHP_EOL; } else { echo "[]"; } ?>
            }
    });

    dash.processTabDisplay(sessionStorage.getItem('dashboard-tab'));
</script>


