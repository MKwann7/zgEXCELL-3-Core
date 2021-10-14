<?php

namespace Entities\Cards\Components\Vue\CardWidget;

use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\Base\VueCustomMethods;
use App\Website\Vue\Classes\VueProps;
use Entities\Cards\Components\Vue\CardBuildWidget\CardBuildWidget;
use Entities\Cards\Components\Vue\CardPaymentWidget\CardPaymentAccountWidget;
use Entities\Cards\Components\Vue\CardPaymentWidget\CardPaymentHistoryWidget;
use Entities\Cards\Models\CardModel;
use Entities\Modules\Components\Vue\AppsWidget\ListAppsWidget;
use Entities\Notes\Components\Vue\NotesCardWidget\ListCardNotesWidget;
use Entities\Users\Components\Vue\ConnectionWidget\ManageUserConnectionsWidget;

class ManageCardWidget extends VueComponent
{
    protected $id = "fb37ec26-43bb-4753-9c32-1ec7d2069cb5";
    protected $title = "Card Dashboard";
    protected $endpointUriAbstract = "card-dashboard/{id}";
    protected $appManagementComponent = null;

    public function __construct(array $components = [])
    {
        parent::__construct(new CardModel());

        $mainEntityList = new VueProps("mainEntityList", "array", "mainEntityList");
        $this->addProp($mainEntityList);

        $this->modalTitleForAddEntity = "Add Card Widget";
        $this->modalTitleForEditEntity = "Edit Card Widget";
        $this->modalTitleForDeleteEntity = "Delete Card Widget";
        $this->modalTitleForRowEntity = "View Card Widget";
    }

    protected function renderComponentDataAssignments() : string
    {
        return "
        dashboardTab: 'profile',
        entityNotFound: false,
        singleEntity: false,
        
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
        
        dynCardBuildComponent: null,
        dynPaymentAccountComponent: null,
        dynPaymentHistoryComponent: null,
        ";
    }

    protected function renderComponentMethods() : string
    {
        global $app;
        $loggedInUser = $app->getActiveLoggedInUser();
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
                    appCart.openPackagesByClass("card page", {id: this.entity.card_id, type: "card"}, this.entity.owner_id)
                        .registerEntityListAndManager("", "' . self::getStaticId() . '");
                },
                addCardApp: function()
                {
                    appCart.openPackagesByClass("card app", {id: this.entity.card_id, type: "card"}, this.entity.owner_id)
                        .registerEntityListAndManager();
                },
                addCardSocialMedia: function()
                {
                    let self = this;
                    let socialMedia = this.entity.SocialMedia;
                    let connectionList = [];
                    let swapType = "socialmedia";
                    let ownerId = this.entity.owner_id;
                    
                    '. $this->activateDynamicComponentByIdInModal(SwapCardConnectionWidget::getStaticId(),"", "add", "this.entity", "socialMedia", ["ownerId"=> "ownerId", "connectionList" => "connectionList", "swapType" => "swapType", "functionType" =>"'save new'"], "this", true,"function(component) {
                        let modal = self.findModal(self);
                        modal.vc.setTitle('Add Social Media Link');
                    }") . '
                },
                editCardProfile: function(entity)
                {
                    ' . $this->activateDynamicComponentByIdInModal(ManageCardProfileWidget::getStaticId(), "", "edit", "this.entity", "this.mainEntityList", null, "this", true,"function(component) {
                        //console.log(component);
                    }") . '
                },
                editCardUserProfile: function(entity)
                {
                    ' . $this->activateDynamicComponentByIdInModal(ManageCardUserProfileWidget::getStaticId(), "", "edit", "this.entity", "this.mainEntityList", null, "this", true,"function(component) {
                        //console.log(component);
                    }") . '
                },
                editCardImage: function(entity, type, imageClass, field, imageSize)
                {
                    ' . $this->activateDynamicComponentByIdInModal(ManageCardImageWidget::getStaticId(), "", "edit", "this.entity", "this.mainEntityList", ["imageType" => "type", 'imageClass'=> 'imageClass', 'entityField'=> 'field',  'imageSize'=> "imageSize"], "this", true,"function(component) {
                        //console.log(component);
                    }") . '
                },
                editCardCustomizableImage: function(entity, type, imageClass, field, imageSize)
                {
                    ' . $this->activateDynamicComponentByIdInModal(ManageCardCustomizableImageWidget::getStaticId(), "", "edit", "this.entity", "this.mainEntityList", ["imageType" => "type", 'imageClass'=> 'imageClass', 'entityField'=> 'field',  'imageSize'=> "imageSize"], "this", true,"function(component) {
                        //console.log(component);
                    }") . '
                },
                addCardConnection: function(entity)
                {
                    ' . $this->activateDynamicComponentByIdInModal(ManageUserConnectionsWidget::getStaticId(), "", "add", "this.entity", "this.mainEntityList", null, "this", true,"function(component) {
                        //console.log(component);
                    }") . '
                },
                editCardConnection: function(entity, connection)
                {
                    ' . $this->activateDynamicComponentByIdInModal(ManageUserConnectionsWidget::getStaticId(), "", "edit", "this.entity", "this.mainEntityList", null, "this", true,"function(component) {
                        //console.log(component);
                    }") . '
                },
                editCardMainColor: function(entity)
                {
                    ' . $this->activateDynamicComponentByIdInModal(ManageCardMainColorWidget::getStaticId(), "", "edit", "this.entity", "this.mainEntityList", null, "this", true,"function(component) {
                        //console.log(component);
                    }") . '
                },
                getCardNumUrl: function(entity) 
                {
                    if (entity.card_vanity_url === "")
                    {
                        return "' . getFullPublicUrl() . '/" + entity.card_num;
                    }
                    
                    return "' . getFullPublicUrl() . '/" + entity.card_vanity_url;
                },
                renderKeyword: function(entity)
                {
                    if (entity.card_keyword !== "")
                    {
                        return entity.card_keyword;
                    }
                    
                    return "NONE";
                },
                goToLiveCard: function(entity) 
                {
                    if (entity.card_vanity_url === "")
                    {
                        window.open("' . getFullPublicUrl() . '/" + entity.card_num, "_blank");
                    }
                    
                    window.open("' . getFullPublicUrl() . '/" + entity.card_vanity_url, "_blank");
                },
                goToCustomerProfile: function(id) 
                {
                    window.open("' . getFullPortalUrl() . '/account/admin/customers/customer-dashboard/" + id, "_self");
                },
                setDashboardTab: function(tabName) 
                {
                    this.dashboardTab = tabName;
                    sessionStorage.setItem(\'card-dashboard-tab\', tabName);
                    
                    if (this.dashboardTab === "buildoutcomplete") 
                    {
                        console.log(this.dynCardBuildComponent);
                        this.dynCardBuildComponent = this.dynCardBuildComponent;
                    }
                    
                },
                refreshCard: function(callback)
                {
                    this.loadCardDataById(this.entity.sys_row_id, function(data) 
                    {
                        if (!this.canUserViewCard()) {
                            this.backToComponent();
                        }
                        if (typeof callback === "function") { callback(); }
                    });
                },
                stallGoingBackToComponent: function(vc)
                {
                    let self = this;
                    setTimeout(function() {
                        if (vc.isChangingComponents()) {
                           self.stallGoingBackToComponent(vc);
                           return;
                        }
                        self.backToComponent();
                    },500);
                },
                showEntityNotFoundModal: function()
                {
                    let self = this;
                    let vc = self.findRootVc(self);
                    if (vc.isChangingComponents()) {
                        self.stallGoingBackToComponent(vc);
                    }
                    else
                    {
                        self.backToComponent();
                    }
                    
                    setTimeout(function() {
                        modal.EngageFloatShield();
                        let data = {title: "Card Not Found!", html: "Oops. That card cannot be accessed or doesn\'t exist."};
                        
                        modal.EngagePopUpAlert(data, function() {
                            modal.CloseFloatShield();
                        }, 500, 115, true);
                    }, 500);

                },
                loadFromUriAbstract: function(id) 
                {
                    this.engageComponentLoadingSpinner();
                    let self = this;
                    this.component_title = this.component_title_original;     
                    this.loadCardDataById(id, function(data) {
                        self.disableComponentLoadingSpinner();
                        if (!self.canUserViewCard()) {
                            self.showEntityNotFoundModal();
                        }
                        self.checkForCardBuildOut();
                        self.loadCardPaymentData();
                    });
                },
                loadCardDataById: function(id, callback) 
                {
                    let self = this;
                    const url = "api/v1/cards/get-card-by-uuid?uuid=" + id + "&addons=paymentAccount|paymentHistory";                    
                    ajax.Get(url, null, function(result)
                    {
                        if (result.success === false || result.response.success === false || typeof result.response.data === "undefined" || result.response.data.length === 0) 
                        { 
                            self.entityNotFound = true;
                            self.showEntityNotFoundModal();
                            return;
                        }
                        
                        self.entity = result.response.data.card;
                        self.filterEntityId = self.entity.card_id;
                        self.component_title = self.component_title_original + ": " + self.entity.card_num;
                        
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
                displayCardUserName: function(entity)
                {
                    if (entity.card_user_name === "") { return "Unknown"; }
                    return entity.card_user_name;
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
                impersonateCustomer: function(user_id) 
                {
                    const self = this;
                    let strAuthUrl = "users/impersonate-user?user_id=" + user_id;
                    
                    ajax.Post(strAuthUrl, null, function(objResult) 
                    {
                        if (objResult.success == false)
                        {
                            return;
                        }
                        
                        vueApplication.authentication.validate();            
                        window.location.href = "/account";
                    });
                },
                editTemplateSettings: function(entity) {
                   ' . $this->activateDynamicComponentByIdInModal(ManageCardTemplateSettingsWidget::getStaticId(), "", "edit", "this.entity", "this.mainEntityList", null, "this", true,"function(component) {
                        //console.log(component);
                    }") . '
                },
                loadCustomSettings: function()
                {
                    const mainCardColor = getJsonSetting(this.entity.card_data, "style.card.color.main");
                    const cardWidth = getJsonSetting(this.entity.card_data, "style.card.width");
                    const pageHeight = getJsonSetting(this.entity.card_data, "style.tab.height");
                    this.mainCardColor = (mainCardColor !== null) ? atob(mainCardColor) : this.mainCardColor;
                    this.cardWidth = (cardWidth !== null) ? atob(cardWidth) : this.cardWidth;
                    this.pageHeight = (pageHeight !== null) ? atob(pageHeight) : this.pageHeight;
                    
                    let self = this;
                    
                    $( function() {
                        let handleWidth = $( "#custom-width-handle" );
                        let handleHeight = $( "#custom-height-handle" );
    
                        $( "#widthSlider" ).slider({
                            min: 300,
                            max: 500,
                            create: function() {
                                $(this).slider("value", self.cardWidth);
                                handleWidth.text( $( this ).slider( "value" ) );
                            },
                            slide: function( event, ui ) {
                                handleWidth.text( ui.value );
                            },
                            change: function( event, ui ) {
                                self.updateCardData("style.card.width", $( this ).slider( "value" ));
                            }
                        });
    
                        $( "#heightSlider" ).slider({
                            min: 40,
                            max: 70,
                            create: function() {
                                $(this).slider("value", self.pageHeight);
                                handleHeight.text( $( this ).slider( "value" ) );
                            },
                            slide: function( event, ui ) {
                                handleHeight.text( ui.value );
                            },
                            change: function( event, ui ) {
                                self.updateCardData("style.tab.height", $( this ).slider( "value" ));
                            }
                        });
                    });
                },
                updateCardData: function(strStyleLabel, objValue, callback)
                {
                    let intEntityId = this.entity.card_id;
    
                    if (!intEntityId)
                    {
                        return;
                    }
    
                    let strCardUpdateDataParameters = "fieldlabels=" + btoa(strStyleLabel) + "&value=" + btoa(objValue);
                    
                    ajax.Post("cards/card-data/update-card-data?id=" + intEntityId + "&type=card-data", strCardUpdateDataParameters, function(objCardResult)
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
                                    `modal.CloseFloatShield();`
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
                    entity[label] = "'.$app->objCustomPlatform->getFullPortalDomain().'/_ez/images/no-image.jpg";
                },
                showErrorUser: function(entity, label)
                {
                    entity[label] = "'.$app->objCustomPlatform->getFullPortalDomain().'/_ez//images/users/no-user.jpg";
                },
                canUserViewCard: function()
                {
                    if (this.parentData.singleEntity == true && typeof this.parentData.loggedInUser !== "undefined" && !this.userIdMatchesCardUser(this.parentData.loggedInUser.user_id)) return false;
                    return true;
                },
                userIdMatchesCardUser: function (userId)
                {
//                    if (userId == this.entity.owner_id) return true;
//                    if (userId == this.entity.card_user_id) return true;
//                    
//                    return false;
                      return true;
                },
                cardIsEditable: function(entity)
                {
                    if (typeof entity === "undefined" || entity === null) return false;
                    if (this.userAdminRole === true) return true;
                    if (entity.status === "Build" || entity.status === "BuildComplete") return false;
                    
                    return true;
                },
                loadCardBuildComponent: function(component)
                {
                    let self = this;
                    if (typeof self.$refs.dynCardBuildRef === "undefined")
                    {
                        setTimeout(function() {
                            self.loadCardBuildComponent(component);
                        }, 50);
                        return;
                    }
                    
                    self.dynCardBuildComponentInstance = self.$refs.dynCardBuildRef;
                    self.dynCardBuildComponentInstance
                        .setModalComponentInstance(component.instanceId, true)
                        .injectDefaultData(component.instanceId, component.parentInstanceId, "edit", self.entity, self.mainEntityList, {}, {}, \''.$loggedInUser->sys_row_id.'\', \''.$loggedInUser->user_id.'\')
                        .hydrateComponent({entity:self.entity}, true, function(result) {
                    });
                },
                checkForCardBuildOut: function()
                {
                    let self = this;
                    if (this.entity.status === "Build" || this.entity.status === "BuildComplete") {
                        ' . $this->activateDynamicComponentById(CardBuildWidget::getStaticId(), "", "edit", "self.entity", "self.mainEntityList", null, "this", false,"function(component) {
                            self.dynCardBuildComponent = component.rawInstance;                            
                            self.loadCardBuildComponent(component);
                        }", false) . '
                    }
                },
                loadCardPaymentComponent: function(component)
                {
                    let self = this;
                    if (typeof self.$refs.dynPaymentAccountRef === "undefined")
                    {
                        setTimeout(function() {
                            self.loadCardPaymentComponent(component);
                        }, 50);
                        return;
                    }
                    
                    self.dynPaymentAccountComponentInstance = self.$refs.dynPaymentAccountRef;
                    self.dynPaymentAccountComponentInstance
                        .setModalComponentInstance(component.instanceId, true)
                        .injectDefaultData(component.instanceId, component.parentInstanceId, "edit", self.entity, self.mainEntityList, {}, {}, \''.$loggedInUser->sys_row_id.'\', \''.$loggedInUser->user_id.'\')
                        .hydrateComponent({entity:self.entity}, true, function(result) {
                    });
                    elm("payment-account-outer").classList.remove("ajax-loading-anim-inline");
                },
                loadCardPaymentHistoryComponent: function(component)
                {
                    let self = this;
                    if (typeof self.$refs.dynPaymentHistoryRef === "undefined")
                    {
                        setTimeout(function() {
                            self.loadCardPaymentHistoryComponent(component);
                        }, 50);
                        return;
                    }
                    
                    self.dynPaymentHistoryComponentInstance = self.$refs.dynPaymentHistoryRef;
                    self.dynPaymentHistoryComponentInstance
                        .setModalComponentInstance(component.instanceId, true)
                        .injectDefaultData(component.instanceId, component.parentInstanceId, "edit", self.entity, self.mainEntityList, {}, {}, \''.$loggedInUser->sys_row_id.'\', \''.$loggedInUser->user_id.'\')
                        .hydrateComponent({entity:self.entity}, true, function(result) {
                    });
                    elm("payment-history-outer").classList.remove("ajax-loading-anim-inline");
                },
                loadCardPaymentData: function()
                {
                    let self = this;
                    this.loadCardPaymentAccountData();
                    this.loadCardPaymentHistoryData();
                },
                loadCardPaymentAccountData: function()
                {
                    let self = this;
                    if (typeof self.dynPaymentAccountComponentInstance !== "undefined")
                    {
                        self.dynPaymentAccountComponentInstance.hydrateComponent({entity:self.entity}, true);
                        return;
                    }
                    ' . $this->activateDynamicComponentById(CardPaymentAccountWidget::getStaticId(), "", "edit", "self.entity", "self.mainEntityList", null, "this", false,"function(component) {
                        self.dynPaymentAccountComponent = component.rawInstance;                            
                        self.loadCardPaymentComponent(component);
                    }", false) . '
                },
                loadCardPaymentHistoryData: function()
                {
                    let self = this;
                    if (typeof self.dynPaymentHistoryComponentInstance !== "undefined")
                    {
                        self.dynPaymentHistoryComponentInstance.hydrateComponent({entity:self.entity}, true);
                        return;
                    }
                    ' . $this->activateDynamicComponentById(CardPaymentHistoryWidget::getStaticId(), "", "edit", "self.entity", "self.mainEntityList", null, "this", false,"function(component) {
                        self.dynPaymentHistoryComponent = component.rawInstance;                            
                        self.loadCardPaymentHistoryComponent(component);
                    }", false) . '
                },
                cardIsBuildOut: function(entity)
                {
                    if (typeof entity === "undefined" || entity === null) return false;
                    if (entity.status === "Build" && this.userAdminRole === true) return true;
                    if (entity.status !== "Build") return false;
                    
                    return true;
                },
                cardIsBuildOutComplete: function(entity)
                {
                    if (typeof entity === "undefined" || entity === null) return false;
                    if (entity.status === "BuildComplete" && this.userAdminRole === true) return true;
                    if (entity.status !== "BuildComplete") return false;
                    
                    return true;
                },
                ' . VueCustomMethods::renderSortMethods() . '
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return '
            this.dashboardTab = sessionStorage.getItem(\'card-dashboard-tab\');
            
            if (this.dashboardTab === null || (
                this.dashboardTab !== "profile" &&
                this.dashboardTab !== "pages" &&
                this.dashboardTab !== "contacts" &&
                this.dashboardTab !== "share" &&
                this.dashboardTab !== "apps" &&
                this.dashboardTab !== "users" &&
                this.dashboardTab !== "groups" &&
                this.dashboardTab !== "buildoutcomplete" &&
                this.dashboardTab !== "billing"
                )
            ) { 
                this.dashboardTab = "profile"; sessionStorage.setItem(\'card-dashboard-tab\', "profile"); 
            }
            
            this.component_title = this.component_title_original;
            let self = this;
            
            if (this.entity && typeof this.entity.sys_row_id !== "undefined") 
            {         
                this.loadCardDataById(this.entity.sys_row_id, function(data)
                {
                    if (!self.canUserViewCard()) {
                        self.backToComponent();
                    }
                    
                    self.disableComponentLoadingSpinner();
                    self.checkForCardBuildOut();
                    self.loadCardPaymentData();
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
                    
                    .cardBuildBanner,
                    .cardPromoBanner {
                        background: linear-gradient(to left, rgba(255,255,255,0.5) 0%, rgba(255,255,255,0) 100%);
                        padding: 15px 15px 0 15px;
                    }
                    .cardPromoBanner div,
                    .cardBuildBanner div {
                        width: 100%;
                        color: #fff !important;
                        text-align: center;
                        padding: .35em;
                        border-radius: 10px;
                        font-size: 1.2em;
                        font-weight: bold;
                    }
                    .cardPromoBanner div {
                        background: #931593;
                    }
                    .cardBuildBanner div {
                        background: #ff0000;
                    }
                    
                    .cardPromoBanner span,
                    .cardBuildBanner span {
                        color: #fff !important;
                    }
                    
                    @media (max-width:550px) {
                        .cardUserCard .card-tile-50 .width175px,
                        .cardUserCard .card-tile-50 .widthAutoTo175px,
                        .cardGraphicsCard .card-tile-50 .entityDetailsInner .divTable .divCell,
                        .cardGraphicsCard .card-tile-50 .entityDetailsInner .divTable .divCell strong {
                            display:block;
                            width:100% !important;
                            float:none;
                            clear:both;
                        }
                        
                        .cardGraphicsCard .card-tile-50 .entityDetailsInner .divTable .divCell img {
                            width:75% !important;
                            max-width:75% !important;
                            margin-top:0 !important;
                            margin-bottom: 25px !important;
                        }
                        .cardGraphicsCard .card-tile-50 .entityDetailsInner .divTable .divCell br {
                            display: none;
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
                                <div v-if="!cardIsEditable(entity) && cardIsBuildOut(entity)" data-block="profile" v-on:click="setDashboardTab(\'profile\')"  class="dashboard-tab fas fa-hammer" v-bind:class="{active: dashboardTab === \'profile\'}"><span>Build Out</span></div>
                                <div v-if="cardIsEditable(entity)" data-block="profile" v-on:click="setDashboardTab(\'profile\')"  class="dashboard-tab fas fa-user-circle" v-bind:class="{active: dashboardTab === \'profile\'}"><span>Profile</span></div>
                                <div v-if="cardIsBuildOutComplete(entity) && !cardIsBuildOut(entity)" data-block="buildoutcomplete" v-on:click="setDashboardTab(\'buildoutcomplete\')"  class="dashboard-tab fas fa-hammer" v-bind:class="{active: dashboardTab === \'buildoutcomplete\'}"><span>Build Out</span></div>
                                <div v-if="entity && entity.template_id == 1 && cardIsEditable(entity)" data-block="pages" v-on:click="setDashboardTab(\'pages\')"  class="dashboard-tab fas fa-list-ol" v-bind:class="{active: dashboardTab === \'pages\'}"><span>Pages</span></div>
                                <div v-if="entity && entity.template_id == 1 && cardIsEditable(entity)" data-block="share" v-on:click="setDashboardTab(\'share\')"  class="dashboard-tab fas fa-share-alt" v-bind:class="{active: dashboardTab === \'share\'}"><span>Share</span></div>
                                <div v-if="entity && entity.template_id > 1 && cardIsEditable(entity)" data-block="public" v-on:click="setDashboardTab(\'public\')"  class="dashboard-tab fas fa-globe-americas" v-bind:class="{active: dashboardTab === \'public\'}"><span>Public</span></div>
                                <div v-if="entity && entity.template_id > 1 && cardIsEditable(entity)" data-block="private" v-on:click="setDashboardTab(\'private\')"  class="dashboard-tab fas fa-key" v-bind:class="{active: dashboardTab === \'private\'}"><span>Private</span></div>
                                <div v-if="entity && entity.template_id > 1 && cardIsEditable(entity)" data-block="apps" v-on:click="setDashboardTab(\'apps\')"  class="dashboard-tab fas fa-th-large" v-bind:class="{active: dashboardTab === \'apps\'}"><span>Modules</span></div>
                                <div v-if="cardIsEditable(entity)" data-block="contacts" v-on:click="setDashboardTab(\'contacts\')"  class="dashboard-tab fas fa-id-card" v-bind:class="{active: dashboardTab === \'contacts\'}"><span>Contacts</span></div>
                                <div data-block="billing" v-on:click="setDashboardTab(\'billing\')"  class="dashboard-tab fas fa-credit-card" v-bind:class="{active: dashboardTab === \'billing\'}"><span>Billing</span></div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="cardBuildBanner" v-if="entity && entity.status === \'Build\'"><div><span class="fas fa-hammer fas-large desktop-30px"></span>Card Is In Build Stage</div></div>
                    <div class="cardPromoBanner" v-if="entity && entity.product_id === 1100"><div><span class="fas fa-exclamation-triangle fas-large desktop-30px"></span>This Is A Promo Card</div></div>
                    <div v-show="!cardIsBuildOut(entity) || userAdminRole === true " class="entityTab" data-tab="profile" v-bind:class="{showTab: dashboardTab === \'profile\'}">
                        <div class="width100 entityDetails">
                            <div class="width50">
                                <div v-if="entity" class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-address-card fas-large desktop-30px"></span>
                                        <span class="fas-large">Card Info</span>
                                        <span v-on:click="editCardProfile(entity)" class="pointer editEntityButton entityButtonFixInTitle"></span>
                                    </h4>
                                    <div class="entityDetailsInner cardProfile">
                                        <table>
                                            <tbody>
                                            <tr>
                                                <td style="width:150px;">Card Name: </td>
                                                <td><strong>{{ entity.card_name }}</strong></td>
                                            </tr>
                                            <tr class="highlighed-field btn-primary pointer" v-on:click="goToLiveCard(entity)">
                                                <td v-if="entity.card_vanity_url" >Vanity URL: </td>
                                                <td v-if="!entity.card_vanity_url" >Card Number: </td>
                                                <td><strong>{{ getCardNumUrl(entity) }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>Card Keyword: </td>
                                                <td><strong>{{ renderKeyword(entity) }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>Template: </td>
                                                <td>
                                                    <strong>{{ entity.template_name }}</strong>
                                                    <span v-on:click="editTemplateSettings(entity)" class="pointer manageDataButton fas fa-cog" style="top:1px;left:3px;position:relative;"></span>
                                                </td>
                                            </tr>
                                            <tr v-if="userAdminRole === true && parentData.singleEntity != true">
                                                <td>Card Owner: </td>
                                                <td>
                                                    <strong id="entityOwner"><a style="text-decoration: underline;" class="pointer" v-on:click="goToCustomerProfile(entity.card_owner_uuid)">{{ displayCardOwnerName(entity) }}</a></strong>
                                                    <span v-if="userAdminRole" v-on:click="impersonateCustomer(entity.owner_id)" class="pointer loginUserButton fas fa-sign-in-alt" style="top:1px;left:3px;position:relative;"></span>
                                                </td>
                                            </tr>
                                            <tr v-if="entity.card_user_id != entity.owner_id">
                                                <td>Card User: </td>
                                                <td>
                                                    <strong id="entityOwner"><a style="text-decoration: underline;" class="pointer" v-on:click="goToCustomerProfile(entity.card_user_uuid)">{{ displayCardUserName(entity) }}</a></strong>
                                                    <span v-if="userAdminRole" v-on:click="impersonateCustomer(entity.card_user_id)" class="pointer loginUserButton fas fa-sign-in-alt" style="top:1px;left:3px;position:relative;"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Status: </td>
                                                <td><strong>{{ entity.status }}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="width50 cardUserCard">
                                <div v-if="entity" class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-user-circle fas-large desktop-25px"></span>
                                        <span class="fas-large">Card User: {{ displayCardUserName(entity) }}</span>
                                        <span v-on:click="editCardUserProfile(entity)" class="pointer editEntityButton entityButtonFixInTitle"></span>
                                    </h4>
                                    <div class="width175px">
                                        <div class="entityDetailsInner ">
                                            <div class="divTable widthAuto mobile-to-100">
                                                <div class="divRow">
                                                    <div class="divCell mobile-to-table mobile-text-center">
                                                        <img v-on:click="editCardImage(entity, \'user_avatar\', \'user-avatar-image\', \'user_avatar\', \'650,650\')" v-bind:src="entity.user_avatar"  @error="showErrorImage(entity,\'user_avatar\')" class="pointer mobile-to-75 mobile-to-block mobile-vertical-margins-15 mobile-to-heightAuto mobile-center" width="160" height="160" style="max-width:160px;width:100%;height:auto;" />
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
                                                        <td class="width100px">Id:</td>
                                                        <td class="width100"><strong>{{ entity.card_user_id }} </strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="width100px">Title: </td>
                                                        <td class="width100"><strong>{{ getJsonSettingDecoded(this.entity.card_data, "card_user.title", "No title") }}</strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="augmented-form-items" style="margin: 5px -6px 5px;padding: 5px 7px !important;">
                                                <table>
                                                    <tbody>
                                                        <tr>
                                                            <td class="width100px">Email: </td>
                                                            <td class="width100"><strong>{{ entity.card_user_email }}</strong></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="width100px">Phone: </td>
                                                            <td class="width100"><strong>{{ entity.card_user_phone }}</strong></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <table>
                                                <tbody>
                                                <tr>
                                                    <td class="width100px">Status: </td>
                                                    <td class="width100"><strong>{{ entity.card_user_status }}</strong></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                        <div v-if="cardIsEditable(entity)" class="width100 entityDetails">
                            <div class="width50 cardGraphicsCard">
                                <div v-if="entity" class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-images fas-large desktop-30px"></span>
                                        <span class="fas-large">Graphics / Images</span>
                                    </h4>
                                    <div class="entityDetailsInner" style="margin-top:5px;">
                                        <div v-if="entity.template_id == 1" class="divTable widthAuto mobile-to-100">
                                            <div class="divRow">
                                                <div class="divCell mobile-to-table mobile-text-center">
                                                    <strong class="mobile-center mobile-to-75">Card Banner</strong><br>
                                                    <img v-on:click="editCardImage(entity, \'banner\', \'main-image\', \'banner\', \'650,650\')" v-bind:src="entity.banner" @error="showErrorImage(entity,\'banner\')" class="pointer mobile-to-75 mobile-to-block mobile-vertical-margins-15 mobile-to-heightAuto mobile-center" width="160" height="160" style="max-width:160px;width:100%;height:auto;"/>
                                                </div>
                                                <div class="divCell mobile-hide" style="width:15px;"></div>
                                                <div class="divCell mobile-to-table mobile-text-center">
                                                    <strong class="mobile-center mobile-to-75">Card Favicon</strong><br>
                                                    <img v-on:click="editCardImage(entity, \'favicon\', \'favicon-image\', \'favicon\', \'180,180\')" v-bind:src="entity.favicon" @error="showErrorImage(entity,\'favicon\')" class="pointer" width="64" height="64" style="max-width:64px;width:100%;height:auto;"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="entity.template_id >= 2" class="divTable widthAuto mobile-to-100">
                                            <div class="divRow">
                                                <div class="divCell mobile-to-table mobile-text-center" style="width: 295px;">
                                                    <strong class="mobile-center mobile-to-75">Card Banner</strong><br>
                                                    <img v-on:click="editCardImage(entity, \'banner\', \'main-image\', \'banner\', \'750,446\')" v-bind:src="entity.banner" @error="showErrorImage(entity,\'banner\')" class="pointer mobile-to-75 mobile-to-block mobile-vertical-margins-15 mobile-to-heightAuto mobile-center" id="entityMainImage" width="284" height="160" style="max-width:284px;width:100%;height:auto;"/>
                                                </div>
                                                <div class="divCell mobile-hide" style="width:15px;"></div>
                                                <div class="divCell mobile-to-table mobile-text-center" style="width: 100px;" >
                                                    <strong class="mobile-center mobile-to-75">Splash Cover</strong><br>
                                                    <img v-on:click="editCardImage(entity, \'splash_cover\', \'splash-cover-image\', \'splash_cover\', \'750,1334\')" v-bind:src="entity.splash_cover" @error="showErrorImage(entity,\'splash_cover\')" class="pointer" width="90" height="160" style="max-width:90px;width:100%;height:auto;"/>
                                                </div>
                                                <div class="divCell mobile-to-table mobile-text-center" style="width: 100px;" >
                                                    <strong class="mobile-center mobile-to-75">Logo</strong><br>
                                                    <img v-on:click="editCardCustomizableImage(entity, \'logo\', \'logo-image\', \'logo\', \'250,250\')" v-bind:src="entity.logo" @error="showErrorImage(entity,\'logo\')" class="pointer" width="90" height="90" style="max-width:90px;width:100%;height:auto;"/>
                                                </div>
                                                <div class="divCell mobile-hide" style="width:15px;"></div>
                                                <div class="divCell mobile-to-table mobile-text-center" style="width: auto;">
                                                    <strong class="mobile-center mobile-to-75">Card Favicon</strong><br>
                                                    <img v-on:click="editCardImage(entity, \'favicon\', \'favicon-image\', \'favicon\', \'180,180\')" v-bind:src="entity.favicon" @error="showErrorImage(entity,\'favicon\')" class="pointer" width="64" height="64" style="max-width:64px;width:100%;height:auto;"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="width50">
                                <div v-if="entity" class="card-tile-50">
                                ' . $this->registerAndRenderDynamicComponent(
                                    new ListCardNotesWidget(),
                                        "view",
                                        [
                                            new VueProps("mainEntity", "object", "entity"),
                                            new VueProps("filterEntityId", "object", "entity.card_id"),
                                            new VueProps("filterByEntityValue", "boolean", true),
                                            new VueProps("filterByEntityRefresh", "boolean", true)
                                        ]
                                    ) . '
                                </div>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    <div v-show="cardIsBuildOut(entity) || ((cardIsBuildOutComplete(entity) && dashboardTab !== \'profile\'))" class="entityTab" data-tab="profile" v-bind:class="{showTab: dashboardTab === \'profile\' || dashboardTab === \'buildoutcomplete\'}">
                        <div class="width100 entityDetails">
                            <div class="width100">
                                <div v-if="entity" class="card-tile-100">
                                    <h4>
                                        <span class="fas fa-hammer fas-large desktop-30px"></span>
                                        <span v-if="cardIsBuildOut(entity)" class="fas-large">Card Build Form</span>
                                        <span v-if="cardIsBuildOutComplete(entity)" class="fas-large">Card Build Submission</span>
                                    </h4>
                                    <component ref="dynCardBuildRef" :is="dynCardBuildComponent" :entity="entity"></component>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="entityTab" data-tab="pages" v-bind:class="{showTab: (dashboardTab === \'pages\' || dashboardTab === \'public\') }">
                        <div class="width100 entityDetails">
                            <div v-bind:class="{\'width50\': (entity && entity.template_id > 1), \'width100\': (entity && entity.template_id == 1)}">
                                <div v-bind:class="{\'card-tile-50\': (entity && entity.template_id > 1), \'card-tile-100\': (entity && entity.template_id == 1)}">
                                    <div>
                                        <h4 class="account-page-subtitle">
                                            <span class="fas fa-list-ol fas-large desktop-30px"></span>
                                            <span class="fas-large">Card Pages</span>
                                            <button class="btn btn-sm btn-primary" v-on:click="addCardPageItem()" style="margin-left: 5px;margin-top: -4px;">Purchase Page/App</button>
                                        </h4>
                                    </div>
                                    ' . $this->registerAndRenderDynamicComponent(
                                            new ManageCardPagesWidget(),
                                            "view",
                                            [new VueProps("card", "object", "entity")]
                                    ) . '
                                </div>
                            </div>
                            <div class="width50" v-if="entity && entity.template_id >= 2">
                                <div class="card-tile-50">
                                    <h4 class="account-page-subtitle">
                                        <span class="fas fa-share-alt fas-large desktop-25px"></span>
                                        <span class="fas-large">Share Buttons</span>
                                    </h4>
                                   ' . $this->registerAndRenderDynamicComponent(
                                            new ManageCardCommunicationWidget(),
                                            "view",
                                            [new VueProps("card", "object", "entity"), new VueProps("swapConnection", "boolean", true), new VueProps("deleteConnection", "boolean", true), new VueProps("createConnection", "boolean", true)]
                                    ) . '
                                </div>
                                <div style="clear:both;height:15px;"></div>
                                <div class="card-tile-50">
                                    <h4 class="account-page-subtitle">
                                        <span class="fas fa-share-alt fas-large desktop-25px"></span>
                                        <span class="fas-large">Social Media</span>
                                        <button class="btn btn-sm btn-primary" v-on:click="addCardSocialMedia()" style="margin-left: 5px;margin-top: -4px;">Add New</button>
                                    </h4>
                                    ' . $this->registerAndRenderDynamicComponent(
                                            new ManageCardSocialMediaWidget(),
                                            "view",
                                            [new VueProps("card", "object", "entity")]
                                    ) . '
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    
                    <div class="entityTab" data-tab="private" v-bind:class="{showTab: dashboardTab === \'private\'}">
                        <div v-if="entity && entity.template_id >= 2" class="width100 entityDetails">
                            <div class="width50">
                                <div class="card-tile-50">
                                    <h4 class="account-page-subtitle">
                                        <span class="fas fa-share-alt fas-large desktop-25px"></span>
                                        <span class="fas-large">Favorites</span>
                                    </h4>
                                   ' . $this->registerAndRenderDynamicComponent(
                                        new ManageCardCommunicationWidget(),
                                        "view",
                                        [new VueProps("card", "object", "entity")]
                                    ) . '
                                </div>
                            </div>
                            <div class="width50">
                                <div class="card-tile-50">
                                    <h4 class="account-page-subtitle">
                                        <span class="fas fa-share-alt fas-large desktop-25px"></span>
                                        <span class="fas-large">Social Media</span>
                                        <button class="btn btn-sm btn-primary" v-on:click="addCardSocialMedia()" style="margin-left: 5px;margin-top: -4px;">Add New</button>
                                    </h4>
                                    ' . $this->registerAndRenderDynamicComponent(
                                        new ManageCardSocialMediaWidget(),
                                        "view",
                                        [new VueProps("card", "object", "entity")]
                                    ) . '
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    
                    <div class="entityTab" data-tab="apps" v-bind:class="{showTab: dashboardTab === \'apps\'}">
                        <div v-if="entity" class="width100 entityDetails">
                            <div class="card-tile-100">
                                ' . $this->renderRegisteredDynamicComponent(
                                        $this->registerDynamicComponentViaHub(
                                        ListAppsWidget::getStaticId(),
                                    "view",
                                        [
                                            new VueProps("filterEntityId", "object", "entity.card_id"),
                                            new VueProps("filterByEntityValue", "boolean", true),
                                            new VueProps("filterByEntityRefresh", "boolean", true)
                                        ])
                                ) . '
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    
                    <div class="entityTab" data-tab="groups" v-bind:class="{showTab: dashboardTab === \'share\'}">
                        <div class="width100 entityDetails">
                            <div v-if="entity && entity.template_id == 1"  class="card-tile-100">
                                <h4 class="account-page-subtitle">
                                    <span class="fas fa-share-alt fas-large desktop-25px"></span>
                                    <span class="fas-large">Share Buttons</span>
                                </h4>
                               ' . $this->renderRegisteredDynamicComponent(
                                    $this->registerDynamicComponent(
                                        new ManageCardConnectionsListWidget(),
                                    "view",
                                        [new VueProps("card", "object", "entity"), new VueProps("swapConnection", "boolean", true), new VueProps("deleteConnection", "boolean", true)]
                                    )
                                ) . '
                            </div>
                        </div>
                    </div>
                    
                    <div class="entityTab" data-tab="users" v-bind:class="{showTab: dashboardTab === \'users\'}">
                        <div class="width100 entityDetails">
                            <div class="card-tile-100">
                                
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    
                    <div class="entityTab" data-tab="contacts" v-bind:class="{showTab: dashboardTab === \'contacts\'}">
                        <div class="width100 entityDetails">
                            <div class="card-tile-100">
                                <h4 class="account-page-subtitle">
                                    <span class="fas fa-id-card fas-large desktop-30px"></span>
                                    <span class="fas-large">Card Contacts</span>
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

                    <div class="entityTab" data-tab="groups" v-bind:class="{showTab: dashboardTab === \'groups\'}" style="display:none;">
                        <div class="width100 entityDetails">
                            <div class="card-tile-100">
                                <h4 class="account-page-subtitle">
                                    <span class="fas fa-layer-group fas-large desktop-25px"></span>
                                    <span class="fas-large">Card Groups</span>
                                    <span class="pointer addNewEntityButton entityButtonFixInTitle" v-on:click="addToCardGroup()" ></span>
                                </h4>
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
                                        <span class="fas-large">Payment Account</span></h4>
                                    <div id="payment-account-outer" class="entityDetailsInner ajax-loading-anim-inline">
                                        <component ref="dynPaymentAccountRef" :is="dynPaymentAccountComponent" :entity="entity"></component>
                                    </div>
                                </div>
                            </div>
                            <div class="width50">
                                <div class="card-tile-50">
                                    <h4 class="account-page-subtitle">History</h4>
                                    <div id="payment-history-outer" class="entityDetailsInner ajax-loading-anim-inline entityListActionColumn">
                                        <component ref="dynPaymentHistoryRef" :is="dynPaymentHistoryComponent" :entity="entity"></component>
                                    </div>
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