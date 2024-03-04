<?php
/**
 * Created by PhpStorm.
 * User: Micah.Zak
 * Date: 10/11/2018
 * Time: 9:43 AM
 */

$this->CurrentPage->BodyId            = "view-all-packages-page";
$this->CurrentPage->BodyClasses       = ["admin-page", "view-all-packages-page", "no-columns"];
$this->CurrentPage->Meta->Title       = "Packages | Admin | " . $this->app->objCustomPlatform->getPortalName();
$this->CurrentPage->Meta->Description = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Meta->Keywords    = "";
$this->CurrentPage->SnipIt->Title     = "Packages";
$this->CurrentPage->SnipIt->Excerpt   = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Columns           = 0;

?>
<div class="breadCrumbs">
    <div class="breadCrumbsInner">
        <a href="/account" class="breadCrumbHomeImageLink">
            <img src="/media/images/home-icon-01_white.png" class="breadCrumbHomeImage" width="15" height="15" />
        </a> &#187;
        <a href="/account" class="breadCrumbHomeImageLink">
            <span class="breadCrumbPage">Home</span>
        </a> &#187;
        <a href="/account" class="breadCrumbHomeImageLink">
            <span class="breadCrumbPage">Admin</span>
        </a> &#187;
        <span id="view-list">
            <span class="breadCrumbPage">Packages</span>
        </span>
        <span id="editing-entity">
            <a id="backToViewEntityList" href="/account/admin/packages/" class="breadCrumbHomeImageLink">
                <span class="breadCrumbPage">Packages</span>
            </a> &#187;
            <span class="breadCrumbPage">Package Dashboard</span>
        </span>
    </div>
</div>
<div class="BodyContentBox">
    <style type="text/css">
        .BodyContentBox .entityList.table-striped td {
            width: 15%;
        }
        .BodyContentBox .entityList.table-striped td:first-child {
            width:5%;
        }
        .BodyContentBox .entityList.table-striped td:nth-child(2) {
            width:8%;
        }
        .BodyContentBox .entityList.table-striped td:nth-child(9),
        .BodyContentBox .entityList.table-striped td:nth-child(6),
        .BodyContentBox .entityList.table-striped td:nth-child(7),
        .BodyContentBox .entityList.table-striped td:nth-child(4),
        .BodyContentBox .entityList.table-striped td:nth-child(5) {
            width:6%;
        }
        .BodyContentBox .entityList.table-striped td:nth-child(8) {
            width:7%;
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
        .BodyContentBox .account-page-title .back-to-entity-list {
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
                                <h3 class="account-page-title">Packages <span class="pointer addNewEntityButton entityButtonFixInTitle"  v-on:click="addCustomer()" ></span></h3>
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
                        <tr v-for="package in orderedPackage" v-on:dblclick="editColumn(package)">
                            <td>{{ package.product_id }}</td>
                            <td>{{ package.package_class_id }}</td>
                            <td>{{ package.title }}</td>
                            <td>{{ package.quantity }}</td>
                            <td>{{ package.cycle }}</td>
                            <td>{{ "$" + package.value }}</td>
                            <td>{{ "$" + package.promo_value }}</td>
                            <td>{{ package.promo_cycle_duration }}</td>
                            <td>{{ package.v1_plan_id }}</td>
                            <td>{{ package.last_updated }}</td>
                            <td class="text-right">
                                <span v-on:click="editColumn(package)" class="pointer editEntityButton"></span>
                                <span v-on:click="deleteColumn(package)" class="pointer deleteEntityButton"></span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="formwrapper-manage-entity">
                <div class="entityDashboard">
                    <h3 class="account-page-title"><a id="back-to-entity-list" class="back-to-entity-list pointer"></a> Package Dashboard</h3>
                    <input id="dashboard-entity-id" type="hidden" value="<?php echo !empty($objPackage->product_id) ? $objPackage->product_id : ""; ?>" />
                    <div class="width100 entityDetails">
                        <div class="width50">
                            <div class="card-tile-50">
                                <h4>Profile <span v-on:click="editProfile()" class="pointer editEntityButton entityButtonFixInTitle"></span></h4>
                                <div class="entityDetailsInner">
                                    <table>
                                        <tbody>
                                        <tr>
                                            <td>Package Title: </td>
                                            <td><strong id="entityPackageTitle"><?php echo !empty($objPackage->title) ? $objPackage->title : ""; ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td>Abbreviation: </td>
                                            <td><strong id="entityAbbreviation"><?php echo !empty($objPackage->abbreviation) ? $objPackage->abbreviation : ""; ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td>Description: </td>
                                            <td><strong id="entityDescription"><?php echo !empty($objPackage->description) ? $objPackage->description : ""; ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td>Package Class: </td>
                                            <td><strong id="entityClass"><?php echo !empty($objPackage->package_class_id) ? $objPackage->package_class_id : ""; ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td>Package Type: </td>
                                            <td><strong id="entityType"><?php echo !empty($objPackage->package_type_id) ? $objPackage->package_type_id : ""; ?></strong></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="width50">
                            <div class="card-tile-50">
                                <h4>Logistics <span v-on:click="editLogistics()" class="pointer editEntityButton entityButtonFixInTitle"></span></h4>
                                <div class="entityDetailsInner">
                                    <table>
                                        <tbody>
                                        <tr>
                                            <td>Card Quantity </td>
                                            <td><strong id="entityCardQuantity"><?php echo !empty($objPackage->quantity) ? $objPackage->quantity : ""; ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td>Price (Per Cycle): </td>
                                            <td><strong id="entityPrice">$<?php echo !empty($objPackage->value) ? $objPackage->value : "0.00"; ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td>Accrual Cycle Type: </td>
                                            <td><strong id="entityCycleType"><?php echo !empty($objPackage->cycle) ? $objPackage->cycle : ""; ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td>Cycles Count: </td>
                                            <td><strong id="entityCycleCount"><?php echo isset($objPackage->billing_count) ? ($objPackage->billing_count != 0 ? $objPackage->billing_count : "On-Going") : "null"; ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td>Billing Cycle: </td>
                                            <td><strong id="entityBilling"><?php echo !empty($objPackage->billing) ? $objPackage->billing : ""; ?></strong></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="clear:both;"></div>
                    <div class="width100 entityDetails">
                        <h4 class="account-page-subtitle">Cards <span class="pointer addNewEntityButton entityButtonFixInTitle"  v-on:click="addCard()"></span></h4>
                        <div class="form-search-box" v-cloak>
                            <input v-model="searchCardQuery" class="form-control" type="text" placeholder="Search for..."/>
                        </div>
                        <table class="table table-striped" style="margin-top:10px;">
                            <thead>
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
                                <td>{{ card.card_id }}</td>
                                <td>{{ card.status }}</td>
                                <td>{{ card.card_name }}</td>
                                <td>{{ card.card_num }}</td>
                                <td>{{ card.card_type_id }}</td>
                                <td>{{ card.parent_card_id }}</td>
                                <td>{{ card.created_on }}</td>
                                <td>{{ card.last_updated }}</td>
                                <td class="text-right">
                                    <span v-on:click="gotoCard(card)" class="pointer editEntityButton"></span>
                                    <span v-on:click="deleteCard(card)" class="pointer deleteEntityButton"></span>
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
<script type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.17/vue.min.js"></script>
<script type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.11/lodash.min.js"></script>
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

                orderedPackage: function()
                {
                    var self = this;

                    let objSortedPeople = this.sortedEntity(this.searchQuery, this.packages, this.orderKey, this.sortByType, this.pageIndex,  this.pageDisplay, this.pageTotal, function(data) {
                        self.pageTotal = data.pageTotal;
                        self.pageIndex = data.pageIndex;
                    });

                    return objSortedPeople;
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

                var intId = this.packages.length + 1;

                this.packages.push({user_id: intId, username: strUserName,  first_name: strFirstName, last_name: strLastName, status: strStatus, created_on: strCreatedOn});

                this.personToAdd.username = "";
                this.personToAdd.first_name = "";
                this.personToAdd.last_name = "";
                this.personToAdd.status = "";
                this.personToAdd.created_on = "";
                this.personToAdd.user_id = "";
            },

            editColumn: function(package)
            {
                var stateObj = { foo: "bar" };

                console.log(JSON.stringify(package));

                $('#dashboard-entity-id').val(package.product_id);
                $('#entityPackageTitle').html(package.title);
                $('#entityAbbreviation').html(package.abbreviation);
                $('#entityDescription').html(package.description);
                $('#entityClass').html(package.package_class_id);
                $('#entityType').html(package.package_type_id);

                $('#entityCardQuantity').html(package.quantity);
                $('#entityPrice').html(package.value);
                $('#entityCycleType').html(package.cycle);
                $('#entityCycleCount').html(package.billing_count != 0 ? package.billing_count : "On-Going" );
                $('#entityBilling').html(package.billing);

                $(".formwrapper-outer").addClass("edit-entity");
                $(".breadCrumbsInner").addClass("edit-entity");

                var packageIdParameter = "product_id=" + package.product_id;

                this.cards = [];

                ajax.Post("packages/package-data/get-package-dashboard-info", packageIdParameter, function(objUserResult)
                {
                    if (objUserResult.success == false)
                    {
                        console.log(objUserResult.message);
                        var data = {};
                        data.title = "Dashboard Retreival Error...";
                        data.html = objUserResult.message;
                        modal.AddFloatDialogMessage(data);
                        return false;
                    }

                    for(var intCardIndex in objUserResult.data.cards)
                    {
                        customerApp.cards.push(objUserResult.data.cards[intCardIndex]);
                    }

                    // success
                    history.pushState(stateObj, "View Package", "/account/admin/packages/view-package?id=" + package.product_id);

                },"POST");
            },

            deleteColumn: function(package)
            {
                this.packages = this.packages.filter(function (currPackage) {
                    return package.product_id != currPackage.product_id;
                });
            },

            prevPage: function()
            {
                this.pageIndex--;

                this.packages = this.packages;
            },

            nextPage: function()
            {
                this.pageIndex++;

                this.packages = this.packages;
            },

            addCustomer: function()
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Add Package";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 750, 115, true);

                var strViewRequestParameter = "view=addPackage";

                modal.AssignViewToPopup("/packages/package-data/get-package-dashboard-views", strViewRequestParameter);
            },

            addCard: function()
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Add Card With Package";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 750, 115, true);

            },

            gotoCard: function(card)
            {
                window.location = "<?php echo getFullUrl() . "/"; ?>account/admin/cards/view-card?id=" + card.card_num
            },

            editProfile: function()
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Edit Package Profile";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 750, 115, true);

                var intEntityId = $('#dashboard-entity-id').val();
                var strViewRequestParameter = "view=editProfile&product_id=" + intEntityId;

                modal.AssignViewToPopup("/packages/package-data/get-package-dashboard-views", strViewRequestParameter);
            },

            editLogistics: function()
            {
                modal.EngageFloatShield();
                var data = {};
                data.title = "Edit Package Logistics";
                //data.html = "We are logging you in.<br>Please wait a moment.";
                modal.EngagePopUpDialog(data, 750, 115, true);

                var intEntityId = $('#dashboard-entity-id').val();
                var strViewRequestParameter = "view=editLogistics&product_id=" + intEntityId;

                modal.AssignViewToPopup("/packages/package-data/get-package-dashboard-views", strViewRequestParameter);
            }
        },

        data:
            {
                orderKey: '',
                orderKeyCard: 'card_name',

                sortByType: true,
                sortByTypeCard: true,

                columns: ['product_id', 'package_class_id',  'title', 'quantity', 'cycle', 'value', 'promo_value', 'promo_duration', 'v1_plan_id', 'last_updated'],
                cardColumns: ['card_id', 'status', 'card_name', 'card_num', 'card_type', 'parent_card_id', 'created_on', 'last_updated'],

                personToAdd: {user_id: "", first_name: "", last_name: "", username: "", display_name: "", status: ""},

                searchQuery: '',
                searchCardQuery: '',

                pageDisplay: 15,
                cardDisplay: 5,

                pageTotal: 1,
                cardTotal: 1,

                pageIndex: 1,
                cardIndex: 1,

                packages: <?php echo $objActivePackages->getData()->ConvertToJavaScriptArray([
                        "product_id",
                        "package_class_id",
                        "package_type_id",
                        "title",
                        "abbreviation",
                        "description",
                        "quantity",
                        "cycle",
                        "value",
                        "promo_value",
                        "promo_cycle_duration",
                        "billing_count",
                        "billing",
                        "v1_plan_id",
                        "last_updated",
                    ]) . PHP_EOL; ?>,

                cards: <?php if(!empty($colPackageCards)) { echo $colPackageCards->ConvertToJavaScriptArray([
                        "card_id",
                        "status",
                        "card_name",
                        "card_num",
                        "card_type_id",
                        "parent_card_id",
                        "created_on",
                        "last_updated",
                    ]) . PHP_EOL; } else { echo "[]"; } ?>
            }
    });

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
        history.go(-1);
        $(".formwrapper-outer").removeClass("edit-entity");
        $(".breadCrumbsInner").removeClass("edit-entity");
    });

    document.getElementById("back-to-entity-list").addEventListener("click", function(event){
        event.preventDefault()
        history.go(-1);
        $(".formwrapper-outer").removeClass("edit-entity");
        $(".breadCrumbsInner").removeClass("edit-entity");
    });

</script>


