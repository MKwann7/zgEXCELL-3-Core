<?php
/**
 * Created by PhpStorm.
 * User: Micah.Zak
 * Date: 10/11/2018
 * Time: 9:43 AM
 */

$this->CurrentPage->BodyId            = "view-my-contacts-page";
$this->CurrentPage->BodyClasses       = ["admin-page", "view-my-contacts-page", "two-columns", "left-side-column"];
$this->CurrentPage->Meta->Title       = "My Contacts | " . $this->app->objCustomPlatform->getPortalDomainName();
$this->CurrentPage->Meta->Description = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Meta->Keywords    = "";
$this->CurrentPage->SnipIt->Title     = "My Contacts";
$this->CurrentPage->SnipIt->Excerpt   = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Columns           = 2;

?>
<div class="breadCrumbs">
    <div class="breadCrumbsInner">
        <a href="/account" class="breadCrumbHomeImageLink">
            <img src="/media/images/home-icon-01_white.png" class="breadCrumbHomeImage" width="15" height="15" />
        </a> &#187;
        <a href="/account" class="breadCrumbHomeImageLink">
            <span class="breadCrumbPage">Home</span>
        </a> &#187;
        <span id="view-list">
            <span class="breadCrumbPage">My Contacts</span>
        </span>
        <span id="editing-entity">
            <a id="backToViewEntityList" href="/account/cards" class="breadCrumbHomeImageLink">
                <span class="breadCrumbPage">My Contacts</span>
            </a> &#187;
            <span class="breadCrumbPage">Contact Dashboard</span>
        </span>
    </div>
</div>
<?php $this->RenderPortalComponent("content-left-menu"); ?>
<div class="BodyContentBox">
    <style type="text/css">
        .BodyContentBox .entityList.table-striped td:nth-child(3),
        .BodyContentBox .entityList.table-striped td:nth-child(5) {
            width:8%;
        }

        .BodyContentBox .entityList.table-striped td {
            width:10%;
        }
        .BodyContentBox .entityList.table-striped td:first-child {
            width:7%;
        }
        .BodyContentBox .entityList.table-striped td:nth-child(6) {
            width:5%;
        }

        /*[v-cloak] { display: none; }*/
    </style>
    <div id="app" class="formwrapper" >
        <div class="formwrapper-outer<?php if ( $strApproach === "view") { echo " edit-entity"; } ?>">
            <div class="formwrapper-control">
                <div class="fformwrapper-header">
                    <table class="table header-table" style="margin-bottom:0px;">
                        <tbody>
                        <tr>
                            <td>
                                <h3 class="account-page-title">My Contacts</h3>
                                <div class="form-search-box" v-cloak>
                                    <input v-model="searchContactQuery" class="form-control" type="text" placeholder="Search for..."/>
                                </div>
                            </td>
                            <td class="text-right page-count-display" style="vertical-align: middle;">
                                <span class="page-count-display-data">
                                    Current: <span>{{ contactIndex }}</span>
                                    Pages: <span>{{ totalPages }}</span>
                                </span>
                                <button v-on:click="prevPage()" class="btn prev-btn" :disabled="contactIndex == 1">Prev</button>
                                <button v-on:click="nextPage()" class="btn" :disabled="contactIndex == totalPages">Next</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="entityListOuter">
                    <table class="table table-striped entityList">
                        <thead>
                        <th v-for="contactColumn in contactColumns" :class="generateListItemClass('contacts', contactColumn)">
                            <a v-on:click="orderByContact(contactColumn)" v-bind:class="{ active : orderKeyContact == contactColumn, sortasc : sortByTypeContact == true, sortdesc : sortByTypeContact == false }">
                                {{ contactColumn | ucWords }}
                            </a>
                        </th>
                        <th class="text-right">
                            Actions
                        </th>
                        </thead>
                        <tbody>
                        <tr v-for="contact in orderedContacts" class="pointer">
                            <td>{{ contact.contact_id }}</td>
                            <td>{{ contact.phone }}</td>
                            <td>{{ contact.email }}</td>
                            <td>{{ contact.first_name }}</td>
                            <td>{{ contact.last_name }}</td>
                            <td class="text-right">
                                <span v-on:click="messageContact(contact)" class="pointer editEntityButton"></span>
                                <span v-on:click="gotoCard(card)" class="pointer editEntityButton"></span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
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
                    return this.contactTotal;
                },

                totalCards: function()
                {
                    return this.cardTotal;
                },

                orderedContacts: function()
                {
                    var self = this;

                    let objSortedContact = this.sortedEntity(this.searchContactQuery, this.contacts, this.orderKeyContact, this.sortByTypeContact, this.contactIndex,  this.contactDisplay, this.contactTotal, function(data) {
                        self.contactTotal = data.pageTotal;
                        self.contactIndex = data.pageIndex;
                    });

                    return objSortedContact;
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

            generateListItemClass: function(label, columnItem)
            {
                return label + "_" + columnItem;
            },

            orderByContact: function(column)
            {

                this.sortByTypeContact = ( this.orderKeyContact == column ) ? ! this.sortByTypeContact : this.sortByTypeContact;

                this.orderKeyContact = column;
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

            editColumn: function(person)
            {
                let stateObj = { foo: "bar" };

                modal.EngageFloatShield();

                $('#dashboard-entity-id').val(person.user_id);
                $('#entityFullName').html(person.first_name + " " + person.last_name);
                $('#entityUserName').html(person.username);
                //$('#entityBusinessName').html(person.business_name);
                $('#entityStatus').html(person.status);

                $(".formwrapper-outer").addClass("edit-entity");
                $(".breadCrumbsInner").addClass("edit-entity");

                let userIdParameter = "user_id=" + person.user_id;

                $(".entityDashboard").show();
                $(".entity404").hide();

                this.cards = [];
                this.connections = [];
                this.addresses = [];

                console.log(userIdParameter);

                ajax.Post("customers/user-data/get-customer-dashboard-info", userIdParameter, function(objUserResult)
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

                    // success
                    history.pushState(stateObj, "View Customer", "/account/admin/customers/view-customer?id=" + person.user_id);

                    dash.loadDashboardTabs();

                    modal.CloseFloatShield();

                });
            },

            deleteColumn: function(person)
            {
                this.people = this.people.filter(function (curPerson) {
                    return person.user_id != curPerson.user_id;
                });
            },

            prevPage: function()
            {
                this.contactIndex--;
                this.contacts = this.contacts;
            },

            nextPage: function()
            {
                this.contactIndex++;
                this.contacts = this.contacts;
            },

            editContactProfile: function(card)
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

                        customerApp.pageTotal = customerApp.people / customerApp.contactDisplay;

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
                orderKeyContact: 'first_name',
                sortByTypeContact: true,
                contactColumns: ['contact_id','phone','email','first_name', 'last_name'],
                searchContactQuery: '',
                contactDisplay: 15,
                contactTotal: 1,
                contactIndex: 1,
                contacts: <?php if(!empty($colCardContacts)) { echo $colCardContacts->ConvertToJavaScriptArray([
                        "contact_id",
                        "phone",
                        "email",
                        "first_name",
                        "last_name",
                    ]) . PHP_EOL; } else { echo "[]"; } ?>,
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
        dash.goBackToEntityList("/account/contacts");
    });

    document.getElementById("back-to-entity-list").addEventListener("click", function(event){
        event.preventDefault()
        dash.goBackToEntityList("/account/contacts");
    });

    document.getElementById("back-to-entity-list-404").addEventListener("click", function(event){
        event.preventDefault()
        dash.goBackToEntityList("/account/contacts");
    });

</script>
