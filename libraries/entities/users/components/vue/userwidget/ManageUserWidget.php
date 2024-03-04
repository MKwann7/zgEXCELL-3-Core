<?php

namespace Entities\Users\Components\Vue\UserWidget;

use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\Base\VueCustomMethods;
use App\Website\Vue\Classes\VueProps;
use Entities\Cards\Components\Vue\CardWidget\ListCardWidget;
use Entities\Cards\Components\Vue\CardWidget\ManageCardImageWidget;
use Entities\Notes\Components\Vue\NotesCustomerWidget\ListCustomerNotesWidget;
use Entities\Users\Components\Vue\ConnectionWidget\ManageUserConnectionsListWidget;
use Entities\Users\Models\UserModel;

class ManageUserWidget extends VueComponent
{
    protected string $id = "2f4dd5d3-6753-48ce-ac62-b2c584eb0fe9";
    protected string $title = "User Dashboard";
    protected string $endpointUriAbstract = "user-dashboard/{id}";

    public function __construct(array $components = [])
    {
        $defaultEntity = new UserModel();

        parent::__construct($defaultEntity);

        $mainEntityList = new VueProps("mainEntityList", "array", "mainEntityList");
        $this->addProp($mainEntityList);

        $this->modalTitleForAddEntity = "Add Customer Widget";
        $this->modalTitleForEditEntity = "Edit Customer Widget";
        $this->modalTitleForDeleteEntity = "Delete Customer Widget";
        $this->modalTitleForRowEntity = "View Customer Widget";
    }

    protected function renderComponentDataAssignments() : string
    {
        return "
        dashboardTab: 'profilewidget',
        entityNotFound: false,
        
        orderKeyCardGroup: 'order_number',
        orderKeyCardUser: 'first_name',
        orderKeyCardContact: 'created_on',
        orderKeyCardAffiliate: 'epp_level',
        orderKeyTrans: 'transaction_id',
        
        sortByTypeCardGroup: true,
        sortByTypeCardUser: true,
        sortByTypeCardContact: false,
        sortByTypeCardAffiliate: true,
        sortByTypeTrans: true,
        
        cardGroupColumns: ['title', 'card_tab_type_id', 'visibility','permanent', 'created_on', 'last_updated'],
        cardUserColumns: ['role','first_name', 'last_name', 'username'],
        cardContactColumns: ['contact_id', 'first_name', 'last_name', 'phone_number', 'email', 'created_on'],
        cardAffiliateColumns: ['epp_level', 'user_id', 'first_name','last_name', 'affiliate_type', 'status', 'epp_value'],
        transColumns: ['transaction_id', 'created_on', 'card_type', 'card_name', 'card_num'],
        
        searchCardPageQuery: '',
        searchCardUserQuery  : '',
        searchCardContactQuery : '',
        
        cardGroupDisplay: 15,
        cardUserDisplay: 5,
        cardContactDisplay: 15,
        cardAffiliateDisplay: 5,
        transDisplay: 5,
        
        cardGroupTotal: 1,
        cardUserTotal: 1,
        cardContactTotal: 1,
        cardAffiliateTotal: 1,
        transTotal: 1,
        
        cardGroupIndex: 1,
        cardUserIndex: 1,
        contactPageIndex: 1,
        cardAffiliateIndex: 1,
        transIndex: 1,
                
        cardUsers : [], 
        cardGroups : [],
        cardContacts : [],
        transactions : [],
                
        mainCardColor: 'ff0000',
        cardWidth: '400',
        pageHeight: '55',
        ";
    }

    protected function renderComponentMethods() : string
    {
        global $app;
        return '
                entityFound: function()
                {
                    return true;
                },
                entityNotFound: function()
                {
                    return false;
                },
                addCardPageItem: function()
                {
                    appCart.openPackagesByClass("card page", {id: this.entity.card_id, type: "card"}, this.entity.owner_id, this.entity.owner_id)
                        .registerEntityListAndManager();
                },
                editCardProfile: function(entity)
                {
                    modal.EngageFloatShield();
                    '. $this->activateDynamicComponentByIdInModal(ManageCustomerProfileWidget::getStaticId(),"", "edit", "this.entity", "this.mainEntityList", null, "this", true, "function(component) { 
                        modal.CloseFloatShield();
                    }").' 
                },
                setDashbaordTab: function(tabName) {
                    this.dashboardTab = tabName;
                    sessionStorage.setItem(\'dashboard-tab\', tabName);
                },
                loadFromUriAbstract: function(id) 
                {
                    this.engageComponentLoadingSpinner();
                    let self = this;
                    this.component_title = this.component_title_original;
                    
                    this.loadEntityDataByUuid(id, function() {
                        self.disableComponentLoadingSpinner();  
                    });
                },
                loadEntityDataByUuid: function(id, callback) 
                {
                    let self = this;
                    ajax.Get("api/v1/users/get-user-by-uuid?uuid=" + id, null, function(result)
                    {
                        if (result.success === false || typeof result.response.data === "undefined" || result.response.data.length === 0) 
                        { 
                            self.entityNotFound = true;
                            return;
                        }
                        
                        self.entity = result.response.data.user;
                        self.filterEntityId = self.entity.user_id;
                        self.component_title = self.component_title_original + ": " + self.entity.user_id;
                        
                        let vc = self.findVc(self);
                        vc.reloadComponents("'.$this->getInstanceId().'");

                        self.$forceUpdate();
                        
                        self.loadCustomSettings();     
                                                                                   
                        if (typeof callback === "function") { callback(result.response.data); }
                    });          
                },
                displayCardOwnerName: function(entity)
                {
                    if (entity.card_owner_name === "") { return "Unknown"; }
                    return entity.card_owner_name;
                },
                impersonateCustomer: function(user) {
                    let strAuthUrl = "users/impersonate-user?user_id=" + user.user_id;
                    ajax.Send(strAuthUrl, null, function(objResult) {
                        if (objResult.success == false)
                        {
                            ezLog(objResult.message);
                            return;
                        }
            
                        window.location.href = "/account";
                    });
                },
                editProfilePhoto: function()
                {
                    const type = "";
                    const imageClass = "";
                    const field = "";
                    const imageSize = "";
                    ' . $this->activateDynamicComponentByIdInModal(ManageCardImageWidget::getStaticId(), "", "edit", "this.entity", "this.mainEntityList", ["imageType" => "type", 'imageClass'=> 'imageClass', 'entityField'=> 'field',  'imageSize'=> "imageSize"], "this", true,"function(component) {
                        //console.log(component);
                    }") . '
                },
                renderFullName: function(entity)
                {
                    let fullName = [];
                    
                    if (typeof entity.name_prefx !== "undefined") { fullName.push(entity.name_prefx); }
                    if (typeof entity.first_name !== "undefined") { fullName.push(entity.first_name); }
                    if (typeof entity.middle_name !== "undefined") { fullName.push(entity.middle_name); }
                    if (typeof entity.last_name !== "undefined") { fullName.push(entity.last_name); }
                    if (typeof entity.name_sufx !== "undefined") { fullName.push(entity.name_sufx); }
                    
                    return fullName.join(" ");
                },
                loadCustomSettings: function()
                {
                    //console.log(this.entity.__settings);
                },
                updateCardData: function(strStyleLabel, objValue, callback)
                {
                    let intEntityId = this.entity.card_id;
    
                    if (!intEntityId)
                    {
                        return;
                    }
    
                    let strCardUpdateDataParameters = "fieldlabels=" + btoa(strStyleLabel) + "&value=" + btoa(objValue);
                    
                    ajax.Send("cards/card-data/update-card-data?id=" + intEntityId + "&type=card-data", strCardUpdateDataParameters, function(objCardResult)
                    {
                        if(typeof callback === "function")
                        {
                            callback(objCardResult);
                        }
                    });
                },
                prevContactPage: function()
                {
                    this.contactPageIndex--;
                    //this.updatePage();
                },
                nextContactPage: function()
                {
                    this.contactPageIndex++;
                    //this.updatePage();
                },
                editContact: function(contact)
                {
                    modal.EngageFloatShield();
                    var data = {};
                    data.title = "Edit Contact ";
                    data.html = "Manage this subscribed contact.";
                    modal.EngagePopUpDialog(data, 750, 115, true);
    
                    var intEntityId = this.entity.card_id;
                    var strViewRequestParameter = "card_id=" + intEntityId + "&contact_id=" + contact.contact_id;
    
                    modal.AssignViewToPopup("contacts/card-data/message-contacts-modal", strViewRequestParameter, function()
                    {
                        modal.EngageFloatShield();
                    },
                        function (objResult)
                        {
    
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
                        }
                    );
                },
                messageContactsModal: function(user) {
                    modal.EngageFloatShield();
                    var data = {};
                    data.title = "Message All Contacts";
                    data.html = "Sending a text message from here will go to all the contacts connected to this card.";
                    modal.EngagePopUpDialog(data, 750, 115, true);
    
                    let intEntityId = this.entity.card_id;
                    let strViewRequestParameter = "card_id=" + intEntityId;
    
                    modal.AssignViewToPopup("cards/card-data/message-contacts-modal", strViewRequestParameter, function () {
                            modal.EngageFloatShield();
                        },
                        function (objResult) {
    
                            let data = {};
                            data.title = "Sending Messages Is Complete!";
                            data.html = "We just sent a text message to the contacts on your EZcard.";
                            modal.EngagePopUpAlert(data, function() {
                                modal.CloseFloatShield(function() {
                                    modal.CloseFloatShield();
                                },500);
                            }, 350, 115);
                        },
                        function (objValidate) {
                            let intErrors = 0;
    
                            if ($("#editCardAdminForm .error-text").length > 0) {
                                return false;
                            }
    
                            $("#editCardAdminForm .error-validation[name!=card_vanity_url][name!=card_keyword]").removeClass("error-validation");
    
                            for (let intFieldIndex in objValidate) {
                                if (objValidate[intFieldIndex].value == "" && objValidate[intFieldIndex].name != "card_vanity_url" && objValidate[intFieldIndex].name != "card_keyword") {
                                    intErrors++;
                                    $("#editCardAdminForm input[name=" + objValidate[intFieldIndex].name + "], #editCardAdminForm select[name=" + objValidate[intFieldIndex].name + "]").addClass("error-validation");
                                }
                            }
    
                            if (intErrors > 0) {
                                console.log("errors: " + intErrors);
                                return false;
                            }
    
                            return true;
                        }
                    );
                },
                messageContactModal: function(contact)
                {
                    modal.EngageFloatShield();
                    var data = {};
                    data.title = "Message Contact: " + contact.first_name;
                    data.html = "Sending a text message from here will go to " + contact.first_name + " " + contact.last_name + " on this card.";
                    modal.EngagePopUpDialog(data, 750, 115, true);
    
                    var intEntityId = this.entity.card_id;
                    var strViewRequestParameter = "card_id=" + intEntityId + "&contact_id=" + contact.id;
    
                    modal.AssignViewToPopup("cards/card-data/message-contact-modal", strViewRequestParameter, function()
                    {
                        modal.EngageFloatShield();
                    },
                        function (objResult)
                        {
                            let data = {};
                            data.title = "Sending Message Is Complete!";
                            data.html = "We just sent a text message to "  + contact.first_name  + ".";
                            modal.EngagePopUpAlert(data, function() {
                                modal.CloseFloatShield(function() {
                                    modal.CloseFloatShield();
                                },500);
                            }, 350, 115);
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
                        }
                    );
                },
                messageSelectedContactsModal: function()
                {
                    let intSelectedCount = 0;
                    let strSelectedContacts = "";
                    let arContactList = [];
    
                    for(let objContact of this.entity.Contacts)
                    {
                        if (objContact.selected === true)
                        {
                            intSelectedCount++;
                            if (objContact.first_name !== "" && typeof objContact.first_name !== "undefined")
                            {
                                strSelectedContacts += "<li><b>" + objContact.first_name + " " + objContact.last_name + "</b></li>";
                            }
                            else
                            {
                                strSelectedContacts += "<li><b>" + formatAsPhoneIfApplicable(objContact.phone_number) + "</b></li>";
                            }
                            arContactList.push(objContact.id);
                        }
                    }
    
                    let strViewRequestParameter = {};
                    strViewRequestParameter.contact = arContactList;
    
                    if (intSelectedCount === 0)
                    {
                        modal.EngageFloatShield();
                        data = {title: "No Selected Contacts", html: "In order to send to a selected few contacts... you have to select a few first."};
                        modal.EngagePopUpAlert(data, function() {
                            modal.CloseFloatShield();
                        },  500, 115, true);
                        return; 
                    }
    
                    $("div[aria-labelledby=\'btnGroupDrop1\']").removeClass("show");
    
                    modal.EngageFloatShield();
                    var data = {};
    
                    data.title = "Message [" + intSelectedCount + "] Selected Contacts";
    
                    data.html = "Sending a text message from here will go to the selected contacts connected to this card:<hr style=\'margin-bottom: 10px;\'><ul>" + strSelectedContacts + "</ul>";
                    modal.EngagePopUpDialog(data, 750, 115, true);
    
                    var intEntityId = this.entity.card_id;
                    strViewRequestParameter.card_id = intEntityId;
    
                    modal.AssignViewToPopup("cards/card-data/message-selected-contacts-modal", strViewRequestParameter, function () {
                            modal.EngageFloatShield();
                        },
                        function (objResult) {
    
                            let data = {};
                            data.title = "Sending Messages Is Complete!";
                            data.html = "We just sent a text message to the selected contacts on your EZcard.";
                            modal.EngagePopUpAlert(data, function() {
                                modal.CloseFloatShield(function() {
                                    modal.CloseFloatShield();
                                },500);
                            }, 350, 115);
                        },
                        function (objValidate) {
                            let intErrors = 0;
    
                            if ($("#editCardAdminForm .error-text").length > 0) {
                                return false;
                            }
    
                            $("#editCardAdminForm .error-validation[name!=card_vanity_url][name!=card_keyword]").removeClass("error-validation");
    
                            for (let intFieldIndex in objValidate) {
                                if (objValidate[intFieldIndex].value == "" && objValidate[intFieldIndex].name != "card_vanity_url" && objValidate[intFieldIndex].name != "card_keyword") {
                                    intErrors++;
                                    $("#editCardAdminForm input[name=" + objValidate[intFieldIndex].name + "], #editCardAdminForm select[name=" + objValidate[intFieldIndex].name + "]").addClass("error-validation");
                                }
                            }
    
                            if (intErrors > 0) {
                                console.log("errors: " + intErrors);
                                return false;
                            }
    
                            return true;
                        }
                    );
                },
                toggleDropDown: function()
                {
                    if ($("div[aria-labelledby=\'btnGroupDrop1\']").length > 0)
                    {
                        $("div[aria-labelledby=\'btnGroupDrop1\']").toggleClass("show");
                    }
                },
                generateListItemClass: function(label, columnItem)
                {
                    return label + "_" + columnItem;
                },
                showErrorImage: function(entity, label)
                {
                    entity[label] = "'.$app->objCustomPlatform->getFullPublicDomainName().'/_ez/images/users/no-user.jpg";
                },
                ' . VueCustomMethods::renderSortMethods() . '
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return '
            this.dashboardTab = sessionStorage.getItem(\'dashboard-tab\');
            
            if (this.dashboardTab === null || (
                this.dashboardTab !== "profilewidget" &&
                this.dashboardTab !== "cards" &&
                this.dashboardTab !== "contacts" &&
                this.dashboardTab !== "billing"
                )
            ) { this.dashboardTab = "profilewidget"; sessionStorage.setItem(\'dashboard-tab\', "profilewidget"); }
            
            this.component_title = this.component_title_original;
            let self = this;
            
            if (this.entity && typeof this.entity.sys_row_id !== "undefined") 
            {
                this.loadEntityDataByUuid(this.entity.sys_row_id, function() {
                    self.disableComponentLoadingSpinner();
                    modal.CloseFloatShield();
                });
            }
            else
            {
                this.showNewSelection = true;
            }
        ';
    }

    protected function renderTemplate() : string
    {
        return '
            <div class="formwrapper-manage-entity">
                <v-style type="text/css">
                    .card-main-color-block {
                        width: 80px;
                        height: 160px;
                        cursor: pointer;
                    }
                    .formwrapper-manage-entity .custom-card-handle {
                        width: 3em;
                        height: 1.6em;
                        top: 50%;
                        margin-top: -.8em;
                        text-align: center;
                        line-height: 1.6em;
                        margin-left: -20px;
                    }
                    .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after {
                        background-image: url("data:image/svg+xml,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 8 8\'%3e%3cpath fill=\'%23fff\' d=\'M6.564.75l-3.59 3.612-1.538-1.55L0 4.26 2.974 7.25 8 2.193z\'/%3e%3c/svg%3e");
                    }
            
                    .custom-checkbox .custom-control-label::after {
                        position: absolute;
                        top: 0.25rem;
                        left: -1.5rem;
                        display: block;
                        width: 1rem;
                        height: 1rem;
                        content: "";
                        background: no-repeat 50% / 50% 50%;
                    }
            
                    .custom-checkbox .custom-control-input:indeterminate ~ .custom-control-label::before {
                        border-color: #007bff;
                        background-color: #007bff;
                    }
            
                    .custom-checkbox .custom-control-label::before {
                        border-radius: 0.25rem;
                    }
            
                    .custom-control-label::before, .custom-file-label, .custom-select {
                        transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
                    }
            
                    .custom-control-label::before {
                        position: absolute;
                        top: 0.25rem;
                        left: -1.5rem;
                        display: block;
                        width: 1rem;
                        height: 1rem;
                        pointer-events: none;
                        content: "";
                        background-color: #fff;
                        border: #adb5bd solid 1px;
                    }
            
                    .contact-multiple-selection {
                        position: relative;
                        left: -5px;
                        top: 2px;
                        z-index: 4;
                        cursor:pointer;
                    }
                    @media (max-width:1000px) {
                        .formwrapper-manage-entity .entityTab .width175px,
                        .formwrapper-manage-entity .entityTab .widthAutoTo175px {
                            float: none;
                            width: 100%;
                        }
                        .formwrapper-manage-entity .entityTab .width175px img {
                            width: 100%;
                            height: auto;
                            margin-bottom: 10px;
                        }
                    }
                    @media (max-width:600px) {
                        .formwrapper-manage-entity .entityTab .width175px,
                        .formwrapper-manage-entity .entityTab .widthAutoTo175px {
                            float: none;
                            width: 100%;
                        }
                    }
                </v-style>
                <div v-if="entityNotFound" class="entityDashboard">
                    <!-- 404 here -->
                </div>
                <div class="entityDashboard">
                    <table class="table header-table" style="margin-bottom:0px;">
                        <tbody>
                        <tr>
                            <td class="mobile-to-table">
                                <h3 class="account-page-title">
                                <a v-show="hasParent" v-on:click="backToComponent()" id="back-to-entity-list" class="back-to-entity-list pointer"></a> 
                                {{ component_title }}
                                </h3>
                            </td>
                            <td class="mobile-to-table text-right page-count-display dashboard-tab-display" style="vertical-align: middle;">
                                <div data-block="profilewidget" v-on:click="setDashbaordTab(\'profilewidget\')"  class="dashboard-tab fas fa-user-circle" v-bind:class="{active: dashboardTab === \'profilewidget\'}"><span>Profile</span></div>
                                <div data-block="cards" v-on:click="setDashbaordTab(\'cards\')"  class="dashboard-tab fas fa-id-card" v-bind:class="{active: dashboardTab === \'cards\'}"><span>Cards</span></div>
                                <div data-block="contacts" v-on:click="setDashbaordTab(\'contacts\')"  class="dashboard-tab fas fa-users" v-bind:class="{active: dashboardTab === \'contacts\'}"><span>Contacts</span></div>
                                <div data-block="billing" v-on:click="setDashbaordTab(\'billing\')"  class="dashboard-tab fas fa-credit-card" v-bind:class="{active: dashboardTab === \'billing\'}"><span>Billing</span></div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="entityTab" data-tab="profilewidget" v-bind:class="{showTab: dashboardTab === \'profilewidget\'}">
                        <div class="width100 entityDetails">
                            <div class="width50">
                                <div v-if="entity" class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-user-circle fas-large desktop-25px"></span>
                                        <span class="fas-large">Profile</span>
                                        <span v-on:click="editCardProfile(entity)" class="pointer editEntityButton entityButtonFixInTitle"></span>
                                        <span v-if="userAdminRole" v-on:click="impersonateCustomer(entity);" class="pointer loginUserButton fas fa-sign-in-alt"></span>
                                    </h4>
                                    <div class="width175px">
                                        <div class="entityDetailsInner">
                                            <div class="divTable widthAuto mobile-to-100">
                                                <div class="divRow">
                                                    <div class="divCell mobile-to-table mobile-text-center">
                                                        <img v-on:click="editProfilePhoto()" v-bind:src="entity.avatar" @error="showErrorImage(entity,\'avatar\')"  class="pointer mobile-to-75 mobile-to-block mobile-vertical-margins-15 mobile-to-heightAuto mobile-center" id="entityMainImage" width="160" height="160" />
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
                                                    <td class="width100px">Full Name:</td>
                                                    <td class="width100"><strong id="entityFullName">{{ renderFullName(entity) }} </strong></td>
                                                </tr>
                                                <tr>
                                                    <td class="width100px">User Name </td>
                                                    <td class="width100"><strong id="entityUserName">{{ entity.username }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td class="width100px">Password: </td>
                                                    <td class="width100"><strong id="entityPassword">********</strong></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <table style="margin-top:8px;">
                                                <tbody>
                                                <tr>
                                                    <td class="width100px">Phone: </td>
                                                    <td class="width100"><strong id="entityPrimaryPhone">{{ entity.user_phone_value }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td class="width100px">E-mail: </td>
                                                    <td class="width100"><strong id="entityPrimaryEmail">{{ entity.user_email_value }}</strong></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <table style="margin-top:8px;">
                                                <tbody>
                                                <tr>
                                                    <td class="width100px">Status: </td>
                                                    <td class="width100"><strong id="entityStatus">{{ entity.status }}</strong></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="width50">
                                <div v-if="entity" class="card-tile-50">
                                    '.$this->registerAndRenderDynamicComponent(
                new ListCustomerNotesWidget(),
                "view",
                [
                    new VueProps("mainEntity", "object", "entity"),
                    new VueProps("filterEntityId", "object", "entity.user_id"),
                    new VueProps("filterByEntityValue", "boolean", true),
                    new VueProps("filterByEntityRefresh", "boolean", true)
                ]
            ).'
                                </div>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="width100 entityDetails">
                            <div class="width50">
                                <div v-if="entity" class="card-tile-50">
                                    '.$this->registerAndRenderDynamicComponent(
                new ManageUserConnectionsListWidget(),
                "view",
                [new VueProps("mainEntity", "object", "entity")]
            ).'
                                </div>
                            </div>

                            <div class="width50">
                                <div v-if="entity" class="card-tile-50">
                                    
                                </div>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    
                    <div class="entityTab" data-tab="cards" v-bind:class="{showTab: dashboardTab === \'cards\'}">
                        <div class="width100 entityDetails">
                            <div class="card-tile-100">
                                '.$this->renderRegisteredDynamicComponent(
                                    $this->registerDynamicComponentViaHub(
                                        ListCardWidget::getStaticId(),
                                        "view",
                                        [
                                            new VueProps("filterEntityId", "object", "entity.user_id"),
                                            new VueProps("filterByEntityValue", "boolean", true),
                                            new VueProps("filterByEntityRefresh", "boolean", true)
                                        ]
                                    ),
                                    ["v-if" => "entity"]
                                ).'
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    
                    <div class="entityTab" data-tab="contacts" v-bind:class="{showTab: dashboardTab === \'contacts\'}">
                        <div class="width100 entityDetails">
                            <div class="card-tile-100">
                                <h4 class="account-page-subtitle">
                                    <span class="fas fa-users fas-large desktop-30px"></span>
                                    <span class="fas-large">User Contacts</span>
                                </h4>
                                <div class="form-search-box" v-cloak>
                                    <input v-model="searchCardContactQuery" class="form-control" type="text" placeholder="Search for..."/>
                                </div>
                                <div class="page-right-hand-tools page-count-display-data">
                                    <div class="btn-group" role="group" aria-label="Button group with nested dropdown" style="margin-right:10px;">
                                        <button v-on:click="messageContactsModal()" class="btn btn-primary btn-sm">Message All Contacts</button>
                                        <div class="btn-group" role="group">
                                            <button v-on:click="toggleDropDown" id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDrop1">
                                                <a class="dropdown-item pointer" v-on:click="messageSelectedContactsModal"><i class="fas fa-envelope" style="margin-right: 5px;"></i>Message Selected</a>
                                            </div>
                                        </div>
                                    </div>
                                    <span>Current: <span>{{ contactPageIndex }}</span></span>
                                    <span>Pages: <span>{{ cardContactTotal }}</span></span>&nbsp;&nbsp;
                                    <span>Count: <span>{{ cardContacts.length }}</span></span>&nbsp;&nbsp;
                                    <button v-on:click="prevContactPage()" class="btn prev-btn" :disabled="contactPageIndex == 1">Prev</button>
                                    <button v-on:click="nextContactPage()" class="btn" :disabled="contactPageIndex == cardContactTotal">Next</button>
                                </div>
                                <div class="entityDetailsInner">
                                    <table class="table table-striped" style="margin-top:10px;" v-cloak>
                                        <thead>
                                        <th v-for="cardContactColumn in cardContactColumns" :class="generateListItemClass(\'card-contacts\', cardContactColumn)">
                                            <a v-on:click="orderByCardContact(cardContactColumn)" v-bind:class="{ active : orderKeyCardContact == cardContactColumn, sortasc : sortByTypeCardContact == true, sortdesc : sortByTypeCardContact == false }">
                                                {{ cardContactColumn | ucWords }}
                                            </a>
                                        </th>
                                        <th class="text-right">
                                            Actions
                                        </th>
                                        </thead>
                                        <tbody v-if="orderedCardContacts.length > 0">
                                            <tr v-for="contact in orderedCardContacts" class="pointer">
                                                <td class="contacts_rel_id">{{ contact.contact_id }}</td>
                                                <td class="contacts_first_name">{{ contact.first_name }}</td>
                                                <td class="contacts_last_name">{{ contact.last_name }}</td>
                                                <td class="contacts_phone">{{ formatAsPhoneIfApplicable(contact.phone_number) }}</td>
                                                <td class="contacts_email">{{ contact.email }}</td>
                                                <td class="contacts_created_on">{{ contact.created_on }}</td>
                                                <td class="text-right">
                                                    <span>
                                                        <div class="custom-control custom-checkbox" style="display:inline;">
                                                            <input v-model="contact.selected" type="checkbox" class="custom-control-input contact-multiple-selection">
                                                            <label class="custom-control-label" for="defaultChecked">&nbsp;</label>
                                                        </div>
                                                    </span>
                                                    <span v-on:click="messageContactModal(contact)" style="margin-right: 5px;" class="pointer mailEntityButton fas fa-envelope"></span>
                                                    <span style="display:none;" v-on:click="editContact(contact)" class="pointer editEntityButton"></span>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tbody v-if="orderedCardContacts.length === 0">
                                            <tr>
                                                <td colspan="7" style="text-align:center;">No Contacts</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>

                    <div class="entityTab" data-tab="billing" v-bind:class="{showTab: dashboardTab === \'billing\'}">
                        <div class="width100 entityDetails">
                            <div class="width50">
                                <div class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-credit-card fas-large desktop-30px"></span>
                                        <span class="fas-large">Payment Accounts</span>
                                        <span v-on:click="editCardPaymentAccount()" class="pointer editEntityButton entityButtonFixInTitle"></span></h4>
                                    <div class="entityDetailsInner">
                                    </div>
                                </div>
                            </div>
                            <div class="width50">
                                <div class="card-tile-50">
                                    <h4 class="account-page-subtitle">Billing History</h4>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ';
    }

    protected function renderComponentComputedValues() : string
    {
        return '
            orderedCardContacts: function()
            {
                let self = this;
                if (typeof self.entity === "undefined" || self.entity === null || typeof self.entity.Contacts === "undefined") { return []; }
                return this.sortedEntity(this.searchCardContactQuery, self.entity.Contacts, this.orderKeyCardContact, this.sortByTypeCardContact, this.contactPageIndex,  this.cardContactDisplay, this.cardContactTotal, function(data) {
                    self.cardContactTotal = data.pageTotal;
                    self.contactPageIndex = data.pageIndex;
                });
            },
        ';
    }
}