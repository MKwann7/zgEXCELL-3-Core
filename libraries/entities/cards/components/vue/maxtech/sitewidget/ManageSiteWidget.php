<?php

namespace Entities\Cards\Components\Vue\Maxtech\Sitewidget;

use App\Core\App;
use App\Website\Constructs\Breadcrumb;
use App\Website\Constructs\SubPageLinks;
use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\Base\VueCustomMethods;
use App\website\Vue\Classes\VueHub;
use App\Website\Vue\Classes\VueProps;
use Entities\Cards\Components\Vue\CardBuildWidget\CardBuildWidget;
use Entities\Cards\Components\Vue\CardPaymentWidget\CardPaymentAccountWidget;
use Entities\Cards\Components\Vue\CardPaymentWidget\CardPaymentHistoryWidget;
use Entities\Cards\Components\Vue\CardWidget\ManageCardCustomizableImageWidget;
use Entities\Cards\Components\Vue\CardWidget\ManageCardImageWidget;
use Entities\Cards\Components\Vue\CardWidget\ManageCardMainColorWidget;
use Entities\Cards\Components\Vue\CardWidget\ManageCardProfileWidget;
use Entities\Cards\Components\Vue\CardWidget\ManageCardUserProfileWidget;
use Entities\Cards\Components\Vue\CardWidget\ManageSiteTemplateSettingsWidget;
use Entities\Cards\Components\Vue\CardWidget\SwapCardConnectionWidget;
use Entities\Cards\Components\Vue\DigitalCardWidget\V1\DigitalCardMainWidget as V1Card;
use Entities\Cards\Components\Vue\ManagementHubWidget\V1\ManagementHubCardListWidget;
use Entities\Cards\Models\CardModel;
use Entities\Directories\Components\Vue\Maxtech\Groupwidget\DirectoryManagementWidget;
use Entities\Directories\Components\Vue\Maxtech\Groupwidget\DirectoryOverviewWidget;
use Entities\Directories\Components\Vue\Maxtech\Groupwidget\DirectoryProfileWidget;
use Entities\Media\Components\Vue\GalleryWidget\ListImageGalleryWidget;
use Entities\Users\Components\Vue\ConnectionWidget\ManageUserConnectionsWidget;
use Entities\Users\Components\Vue\PersonaWidget\DirectoryRegistrationsWidget;
use Entities\Users\Components\Vue\SearchWidget\SearchUserWidget;

class ManageSiteWidget extends VueComponent
{
    protected string $id = "bca6eb5c-4789-40b9-bc09-706bcfe7dcad";
    protected string $title = "Max Site";
    protected string $endpointUriAbstract = "site-profile/{id}";

    protected int $activeCardId = 0;
    protected string $cardTemplateId = "";

    protected ?VueComponent $activeLandingWidget = null;
    protected ?ManagementHubCardListWidget $cardList = null;
    protected ?VueHub $cardHub = null;

    protected ManageCardProfileWidget $cardProfile;

    protected DirectoryProfileWidget $groupProfile;

    protected DirectoryOverviewWidget $directoryOverview;
    protected DirectoryManagementWidget $directoryManager;
    protected DirectoryRegistrationsWidget $directoryRegistrationManager;

    protected SearchUserWidget $searchUser;

    protected function loadBreadCrumbs(): VueComponent
    {
        $this->addBreadcrumb(new Breadcrumb("Admin","/account/admin/", "link"));
        $this->addSubPageLink(new SubPageLinks("Active","/account/my-sites"))
            ->addSubPageLink(new SubPageLinks("Inactive","/account/my-sites/inactive"))
            ->addSubPageLink(new SubPageLinks("Purchase","/account/my-sites/purchase"))
            ->addSubPageLink(new SubPageLinks("Add CRM","/account/my-sites/add-crm"));
        return $this;
    }

    public function __construct(array $components = [])
    {
        $this->cardHub  = new VueHub();
        $this->activeLandingWidget = $this->getActiveTemplateRequest();

        if ($this->activeLandingWidget !== null)
        {
            $this->activeLandingWidget->setMountType("no_mount");
            $this->activeLandingWidget->setComponentsMountType("no_mount");
            $this->activeLandingWidget->setNoHydrate(true);
            $this->activeLandingWidget->setComponentsToNoHydrate(true);
            $this->cardHub->addDynamicComponent($this->activeLandingWidget, true, true);
            $this->cardHub->addComponentsList($this->activeLandingWidget->getDynamicComponentsForParent(), true);
        }

        $this->addComponentsList($this->cardHub->getDynamicComponentsForParent(), false);
        $this->addDynamicComponent($this->cardHub);

        parent::__construct(new CardModel());

        $mainEntityList = new VueProps("mainEntityList", "array", "mainEntityList");
        $this->addProp($mainEntityList);

        $this->cardProfile = new ManageCardProfileWidget();
        $this->cardProfile->setMountType("no_mount");
        $this->cardProfile->setComponentsMountType("no_mount");
        $this->cardProfile->addParentId($this->getInstanceId());
        $this->addDynamicComponent($this->cardProfile,true, false);
        $this->addComponentsList($this->cardProfile->getDynamicComponentsForParent());
        $this->addComponent($this->cardProfile, false);

        $this->groupProfile = new DirectoryProfileWidget();
        $this->groupProfile->setMountType("no_mount");
        $this->groupProfile->setComponentsMountType("no_mount");
        $this->groupProfile->addParentId($this->getInstanceId());
        $this->addDynamicComponent($this->groupProfile,true, false);
        $this->addComponentsList($this->groupProfile->getDynamicComponentsForParent());
        $this->addComponent($this->groupProfile, false);


        $this->directoryRegistrationManager = new DirectoryRegistrationsWidget();
        $this->directoryRegistrationManager->setMountType("no_mount");
        $this->directoryRegistrationManager->setComponentsMountType("no_mount");
        $this->directoryRegistrationManager->addParentId($this->getInstanceId());
        $this->addDynamicComponent($this->directoryRegistrationManager,true, false);
        $this->addComponentsList($this->directoryRegistrationManager->getDynamicComponentsForParent());
        $this->addComponent($this->directoryRegistrationManager, false);

        $this->directoryManager = new DirectoryManagementWidget();
        $this->directoryManager->setMountType("no_mount");
        $this->directoryManager->setComponentsMountType("no_mount");
        $this->directoryManager->addParentId($this->getInstanceId());
        $this->addDynamicComponent($this->directoryManager,true, false);
        $this->addComponentsList($this->directoryManager->getDynamicComponentsForParent());
        $this->addComponent($this->directoryManager, false);

        $this->directoryOverview = new DirectoryOverviewWidget();
        $this->directoryOverview->setMountType("no_mount");
        $this->directoryOverview->setComponentsMountType("no_mount");
        $this->directoryOverview->addParentId($this->getInstanceId());
        $this->addDynamicComponent($this->directoryOverview,true, false);
        $this->addComponentsList($this->directoryOverview->getDynamicComponentsForParent());
        $this->addComponent($this->directoryOverview, false);

        $this->searchUser = new SearchUserWidget();
        $this->searchUser->setMountType("no_mount");
        $this->searchUser->setComponentsMountType("no_mount");
        $this->searchUser->addParentId($this->getInstanceId());
        $this->addDynamicComponent($this->searchUser,true, false);
        $this->addComponentsList($this->searchUser->getDynamicComponentsForParent());
        $this->addComponent($this->searchUser, false);

        $this->modalTitleForAddEntity = "Add Card Widget";
        $this->modalTitleForEditEntity = "Edit Card Widget";
        $this->modalTitleForDeleteEntity = "Delete Card Widget";
        $this->modalTitleForRowEntity = "View Card Widget";
    }

    protected function getActiveTemplateRequest() : ?VueComponent
    {
        $newCartTemplate = "\Entities\Cards\Components\Vue\DigitalCardWidget\V{$this->cardTemplateId}\DigitalCardMainWidget";
        if (class_exists($newCartTemplate)) {
            return new $newCartTemplate();
        }

        return new V1Card();
    }

    protected function renderComponentDataAssignments() : string
    {
        return "
        dashboardTab: '',
        entityNotFound: false,
        singleEntity: false,
        activeCardId: '',
        cardComponentIds: " . json_encode($this->getCardComponentIds()) . ",
        editorScreenWidth: 'desktop',
        shareTypeList: [],
        activePage: null,
        activePageUrl: null,
        saveChangeCountDown: 0,
        saveChangeCountDownTrigger: false,
        themeList: [],
        contentBuilder: null,
        parentIsEditor: true,
        shouldLoadContentBuilder: true,
        noFloatScreen: false,
        
        mainMenu: [
            {title:'Editor',tag:'editor',icon:'fas fa-edit'},   
            {title:'Profile',tag:'profile',icon:'fa fa-id-card'},   
            {title:'Themes',tag:'templates',icon:'fa fa-file'},
            {title:'Share',tag:'share',icon:'fa fa-share-alt'},   
            {title:'Users',tag:'users',icon:'fa fa-users'},   
            {title:'Billing',tag:'billing',icon:'fa fa-credit-card'},   
        ],
        
        personaMenu: [
            {title:'Profile',tag:'profile',icon:'fa fa-id-card'},   
            {title:'Directories',tag:'my-directories',icon:'fa fa-sitemap', template: []},
            {title:'Showcase',tag:'showcase',icon:'fas fa-eye'},
            {title:'Editor',tag:'editor',icon:'fas fa-edit'},   
            {title:'Themes',tag:'templates',icon:'fa fa-file'},
            {title:'Share',tag:'share',icon:'fa fa-share-alt'},
            {title:'Users',tag:'users',icon:'fa fa-users'},   
            {title:'Billing',tag:'billing',icon:'fa fa-credit-card'},   
        ],
        
        groupMenu: [
            {title:'Overview',tag:'overview',icon:'fa fa-search'},   
            {title:'Profile',tag:'group-profile',icon:'fa fa-id-card'},   
            {title:'Editor',tag:'editor',icon:'fas fa-edit'},   
            {title:'Directories',tag:'directories',icon:'fa fa-sitemap', template: []},
            {title:'Events',tag:'events',icon:'fa fa-calendar-alt', template: []},
            {title:'Themes',tag:'templates',icon:'fa fa-file'},
            {title:'Share',tag:'share',icon:'fa fa-share-alt'},
            {title:'Users',tag:'users',icon:'fa fa-users'},   
            {title:'Billing',tag:'billing',icon:'fa fa-credit-card'},   
        ],

        dynCardBuildComponent: null,
        dynPaymentAccountComponent: null,
        dynPaymentHistoryComponent: null,
        ";
    }

    protected function renderComponentDismissalScript() : string
    {
        return parent::renderComponentDismissalScript() . '
            const snippetList = document.getElementById("divSnippetList");
            if (snippetList === null) return;
            snippetList.style.display = "none";
        ';
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
                hydrateCard: function(data)
                {
                    if (data.card) {
                        this.entity = data.card;
                        this.entityClone = _.clone(data.card);
                        
                        dispatch.broadcast("update_card_entityList_with_record", data);
                        this.reloadMainList();
                    }
                },
                reloadCard: function()
                {
                    this.refreshCard(false);
                },
                reloadCardProfile: function(data)
                {
                    const self = this;
                    if  (!data || !data.card) return;
                    if (data.templateChange === false) {
                        this.entity.card_name = data.card.card_name
                        this.entity.owner_id = data.card.owner_id
                        this.entity.card_vanity_url = data.card.card_vanity_url
                        this.entity.card_keyword = data.card.card_keyword
                        this.entity.card_domain = data.card.card_domain
                        this.entity.card_domain_ssl = data.card.card_domain_ssl
                        this.entity.status = data.card.status
                        this.component_title = this.component_title_original + ": " + this.buildCardTitleDisplay()
                        this.$forceUpdate()
                    } else {
                        this.noFloatScreen = true;
                        this.refreshCard(false, function() {
                            self.noFloatScreen = false;
                        });
                    }
                },
                addCardPageItem: function()
                {
                    appCart.openPackagesByClass("card page", {id: this.entity.card_id, type: "card"}, this.entity.owner_id, this.entity.owner_id)
                        .registerEntityListAndManager("", "' . self::getStaticId() . '");
                },
                addCardApp: function()
                {
                    appCart.openPackagesByClass("card app", {id: this.entity.card_id, type: "card"}, this.entity.owner_id, this.entity.owner_id)
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
                setDashboardTabByTag: function(tabName) 
                {
                    this.dashboardTab = tabName;
                    sessionStorage.setItem(\'site-dashboard-tab-\' + this.entity.card_type_id, tabName);
                    this.toggleSnippetList();
                    
                    if (tabName === "editor") {
                        const self = this;
                        setTimeout(function() {
                            self.setEditorWidth(self.editorScreenWidth)
                        },10);
                    }
                    
                    if (this.dashboardTab === "buildoutcomplete") 
                    {
                        this.dynCardBuildComponent = this.dynCardBuildComponent;
                    }
                },
                refreshCard: function(reloadBuilder, callback)
                {
                    const self = this;
                    this.loadCardDataById(this.entity.sys_row_id, function(data) 
                    {
                        if (!self.canUserViewCard()) {
                            self.backToComponent();
                        }
                        if (reloadBuilder) self.loadContentBuilder()
                        self.checkForCardBuildOut();
                        self.loadCardPaymentData();
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
                        self.loadContentBuilder();   
                        self.checkForCardBuildOut();
                        self.loadCardPaymentData();
                    });
                },
                jumpToCard: function(card, recursive)
                {
                    const self = this;
                    const vc = this.findChildVc(this);
                    if (vc === null && (typeof recursive === "undefined" || recursive < 10)) {
                        setTimeout(function() {
                            self.jumpToCard(card, (recursive ? recursive++ : 1))    
                        },20)
                        return;
                    }
                    const componentId = this.cardComponentIds[card.template_id];
                    const cardComponent = vc.getComponentById(componentId);
                    
                    if (!this.noFloatScreen) modal.EngageFloatShield();
                    
                    if (cardComponent === null)
                    {
                        vc.setUserId(self.userId).loadComponentByStaticId(componentId, "", "view", {}, this.mainEntityList, {cardId: card.sys_row_id}, true, true, function(component){ 
                            if (!self.noFloatScreen) modal.CloseFloatShield()
                        });
                        return;
                    }
                    
                    vc.loadComponent(cardComponent.instanceId, cardComponent.id, cardComponent.parentInstanceId, "view", "Digital Card", {}, this.mainEntityList, {cardId: card.sys_row_id}, true, true, function(component) { 
                        setTimeout(function() {
                            if (!self.noFloatScreen) modal.CloseFloatShield()
                        }, 500);
                    });
                },
                loadCardTemplateCss: function() {
                    let headTag = document.querySelectorAll("head")[0];
                    let floatShieldNode = createNode("link", ["href=/app/css/template.min.css?","#card-template-css","type=text/css","rel=stylesheet"]);
                    appendToNode(headTag, floatShieldNode);
                },
                loadCardTemplate: function() {
                    const self = this
                    let isLoaded = true
                   
                    if (elm("card-template-css") === null) {
                        self.loadCardTemplateCss()
                        isLoaded = false
                    }

                    if ( isLoaded === false ) {
                        setTimeout(function() {
                            self.loadCardTemplate();
                        }, 200);
                    }
                },'.'
                loadCardDataById: function(id, callback) 
                {
                    let self = this;
                    const url = "api/v1/cards/get-card-by-uuid?uuid=" + id + "&addons=paymentAccount|paymentHistory|availablePublicModules";
                    
                    self.loadCardTemplate();
                                  
                    ajax.Get(url, null, function(result)
                    {
                        if (result.success === false || result.response.success === false || typeof result.response.data === "undefined" || result.response.data.length === 0) 
                        { 
                            self.entityNotFound = true;
                            self.showEntityNotFoundModal();
                            return;
                        }
  
                        self.entity = result.response.data.card
                        self.filterEntityId = self.entity.card_id
                        self.component_title = self.component_title_original + ": " + self.buildCardTitleDisplay()
                        
                        self.jumpToCard(self.entity)
                        
                        dispatch.broadcast("rehydrate_site_in_editor", {card: self.entity});

                        let vc = self.findVc(self);
                        vc.reloadComponents("'.$this->getInstanceId().'");
                        
                        self.$forceUpdate();
                                                                                   
                        if (typeof callback === "function") { callback(result.response.data); }
                    });          
                },
                buildCardTitleDisplay: function() {
                    if (this.entity.card_domain) {
                        return this.entity.card_domain
                    }
                    
                    return this.entity.card_num
                },
                buildCardDisplay: function() {
                    if (this.entity.card_domain) {
                        return (this.entity.card_domain_ssl == true ? "https://" : "http://") + this.entity.card_domain
                    }
                    
                    return this.entity.card_num
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
                   ' . $this->activateDynamicComponentByIdInModal(ManageSiteTemplateSettingsWidget::getStaticId(), "", "edit", "this.entity", "this.mainEntityList", null, "this", true,"function(component) {
                        //console.log(component);
                    }") . '
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
                    entity[label] = "'.$app->objCustomPlatform->getFullPortalDomainName().'/_ez/images/no-image.jpg";
                },
                showErrorUser: function(entity, label)
                {
                    entity[label] = "'.$app->objCustomPlatform->getFullPortalDomainName().'/_ez//images/users/no-user.jpg";
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
                loadContentBuilderJs: function() {
                    let headTag = document.querySelectorAll("head")[0];
                    let floatShieldNode = createNode("script", ["src=/widgets/scripts/content-builder.js","#content-builder-js"]);
                    appendToNode(headTag, floatShieldNode);
                },
                loadContentBuilderCss: function() {
                    let headTag = document.querySelectorAll("head")[0];
                    let floatShieldNode = createNode("link", ["href=/widgets/scripts/content-builder.css","#content-builder-css","rel=stylesheet"]);
                    appendToNode(headTag, floatShieldNode);
                },
                loadContentBlocksCss: function() {
                    let headTag = document.querySelectorAll("head")[0];
                    let floatShieldNode = createNode("link", ["href=/widgets/scripts/assets/minimalist-blocks/content.css","#content-blocks-css","rel=stylesheet"]);
                    appendToNode(headTag, floatShieldNode);
                },
                loadContentBuilder: function() {
                    if (this.shouldLoadContentBuilder === false) return;
                    const self = this
                    let isLoaded = true
                    
                    if (elm("content-builder-js") == null) {
                        self.loadContentBuilderJs()
                        isLoaded = false
                    }
                    
                    if (elm("content-builder-css") === null) {
                        self.loadContentBuilderCss()
                        isLoaded = false
                    }
                    
                    if (elm("content-blocks-css") === null) {
                        self.loadContentBlocksCss()
                        isLoaded = false
                    }

                    if ( isLoaded === false ) {
                        setTimeout(function() {
                            self.loadContentBuilder();
                        }, 200);
                        return;
                    }
                    
                    if (typeof ContentBuilder === "undefined") {
                        setTimeout(function() {
                            self.loadContentBuilder();
                        }, 200);
                        return;
                    }
                    
                    const contentBuilderData = {
                        container: \'.app-main-comp-body .app-page-content-inner\',
                        imageSelect: null,
                        fileSelect: null,
                        onImageBrowseClick: function(data) {
                            let cardPages = []
                            let activeImage = self.contentBuilder.activeImage
                            ' . $this->activateDynamicComponentByIdInModal(ListImageGalleryWidget::getStaticId(), "", "edit", "this.entity", "cardPages", ["label" => "'content-builder-image'", "media" => "[]", "type" => "'images'", "imageSaveCallback" => "function(url) {
                                activeImage.src = url
                                self.saveContentHtmlFunc()
                            }"], "self") . '
                        },
                        onImageSelectClick: function(data) {
                            let cardPages = []
                            ' . $this->activateDynamicComponentByIdInModal(ListImageGalleryWidget::getStaticId(), "", "edit", "this.entity", "cardPages", ["label" => "'content-builder-image'", "media" => "[]", "type" => "'images'"], "self") . '
                        },
                        onFileSelectClick: function(data) {
                            ezLog(data, "onFileSelectClick")
                        },
                        assetPath: \'/widgets/scripts/assets/\',
                        fontAssetPath: \'/widgets/scripts/assets/fonts/\',
                        modulePath: \'/widgets/scripts/assets/modules/\',
                        pluginPath: \'/widgets/scripts/\',
                        snippetUrl: \'/widgets/scripts/assets/minimalist-blocks/content.js\',
                        snippetPath: \'/widgets/scripts/assets/minimalist-blocks/\',
                        snippetPathReplace: [\'assets/minimalist-blocks/\', \'/widgets/scripts/assets/minimalist-blocks/\'],
                        snippetOpen:(this.dashboardTab === "editor"),
                        sidePanel:\'right\',
                        plugins: [
                            { name: \'preview\', showInMainToolbar: true, showInElementToolbar: true },
                            { name: \'wordcount\', showInMainToolbar: true, showInElementToolbar: true },
                            { name: \'symbols\', showInMainToolbar: true, showInElementToolbar: false },
                            { name: \'buttoneditor\', showInMainToolbar: false, showInElementToolbar: false },
                        ],
                        onChange: function() { 
                            self.saveContentChanges() 
                        },
                    }

                    this.contentBuilder = new ContentBuilder(contentBuilderData);
                    this.contentBuilder.loadSnippets(\'/widgets/scripts/assets/minimalist-blocks/content.js\');
                    this.toggleSnippetList();
                },
                updateSitePagesFromCard: function(pages) {
                    for (let currTabIndex in this.entity.Tabs) {
                        for (let currPageIndex in pages) {
                            if (this.entity.Tabs[currTabIndex].card_tab_id === pages[currPageIndex].card_tab_id) {
                                this.entity.Tabs[currTabIndex] = pages[currPageIndex];
                            }
                        }
                    }
                },
                updateActivePageContent: function(html) {
                    if (typeof this.activePage === "undefined") return;
                
                    for (let currPageIndex in this.entity.Tabs) {
                        if (this.entity.Tabs[currPageIndex].card_tab_id === this.activePage.card_tab_id) {
                            const vc = this.findChildVc(this);
                            const componentId = this.cardComponentIds[this.entity.template_id];
                            const cardComponent = vc.getComponentById(componentId);                            
                            this.entity.Tabs[currPageIndex].content = btoa(html);
                            this.activePage.content = btoa(html)
                             
                            cardComponent.instance.updatePageData(this.entity.Tabs[currPageIndex])
                        }
                    }
                },
                saveContentChanges: function(force) {
                    this.saveChangeCountDown = 4;
                    
                    if (force === true) {
                        this.saveChangeCountDown = 1;
                    }

                    if (this.saveChangeCountDownTrigger === false || force === true) {
                        this.saveChangeCountDownTrigger = true
                        this.callSaveChangesWhenReady()
                    }
                },
                callSaveChangesWhenReady: function() {
                    const self = this;
                    if (this.saveChangeCountDown > 0) {
                        setTimeout(function() {
                            self.saveChangeCountDown--;
                            self.callSaveChangesWhenReady()
                        }, 250)
                        return;
                    }
                    
                    this.saveContentChangesFunc()
                },
                saveContentChangesFunc: function() {
                    const self = this;
                    const url = "/api/v1/media/upload-base64-image?entity_id=" + this.entity.card_id + "&user_id=" + this.entity.owner_id + "&uuid=" + this.userId + "&entity_name=card&class=images";
                    this.contentBuilder.saveImages(
                        "", 
                        function() {
                            self.saveContentHtmlFunc()
                        },
                        function(image, base64, filename) {
                            const imageData = {
                                image: "data:image/png;base64," + base64, 
                                filename: filename
                            };
                            ajax.PostExternal(url, imageData, true, function(result) {
                                if (result.response.success === false) {
                                    return;
                                }
                                image.setAttribute("src", imageServerUrl() + result.response.data.path)
                            })
                        }
                    )
                },
                saveContentHtmlFunc: function() {
                    if (this.contentBuilder === null || this.activePage === null) return;
                    this.saveChangeCountDownTrigger = false
                    this.saveChangeCountDown = 0
                    
                    const url = "/cards/card-data/save-card-page-app-content?id=" + this.activePage.card_tab_id;
                    const self = this;
                    const htmlData = this.contentBuilder.html().replace(/[\t\n]+/, "")
                    
                    if (typeof this.activePage === "undefined" || btoa(this.activePage.content) === htmlData) return;
                    
                    this.updateActivePageContent(htmlData)
                    
                    const htmlFroalaObject = {title: this.activePage.title, content: htmlData, card_id: this.entity.card_id,  card_page_id: this.activePage.card_tab_id, action: this.action};
                    
                    ajax.PostExternal(url, htmlFroalaObject, true, function(result) {
                        if (result.success === false) {
                            let data = {title: "Widget Error", html: "Oh no! There was an error saving the data for this widget: " + objResult.message };
                            modal.EngagePopUpAlert(data, function() {
                                modal.CloseFloatShield();
                            }, 350, 115, true);
                            return;
                        }
                        
                        if (self.action === "create") {
                            self.entities.push(result.response.data.card);
                        } else {
                            self.entities.forEach(function (currEntity, currIndex) {
                                if (self.entity.card_tab_id === currEntity.card_tab_id && typeof this.activePage !== "undefined" && this.activePage !== null) {
                                    self.entities[currIndex].title = this.activePage.title;
                                }
                            });
                        }
                    });
                },
                isNotSiteTypeTab: function() {
                    if (this.isPersona()) {
                        return this.evaluateDashboardTab(this.personaMenu) 
                    } else if (this.isGroup()) {
                        return this.evaluateDashboardTab(this.groupMenu) 
                    } else {
                        return this.evaluateDashboardTab(this.mainMenu) 
                    }
                },
                evaluateDashboardTab: function(menu) {
                    let foundMatch = false
                    for (const menuIndex in menu) {
                        if (menu[menuIndex].tag === this.dashboardTab) {
                            foundMatch = true
                        }
                    }
                    return foundMatch
                },
                setDashboardTab: function() {
                    const self = this;
                    
                    if (!this.entity || typeof this.entity.card_type_id === "undefined") {
                        setTimeout(function() {
                            self.setDashboardTab()
                        }, 20)
                        return;
                    }
                        
                    this.dashboardTab = sessionStorage.getItem(\'site-dashboard-tab-\' + this.entity.card_type_id);
                    
                    if (!this.isNotSiteTypeTab()) {
                        if (this.isPersona()) {
                            this.dashboardTab = "profile"; sessionStorage.setItem(\'site-dashboard-tab-\' + this.entity.card_type_id, "profile"); 
                        } else if (this.isGroup()) {
                            this.dashboardTab = "overview"; sessionStorage.setItem(\'site-dashboard-tab-\' + this.entity.card_type_id, "overview"); 
                        } else {
                            this.dashboardTab = "editor"; sessionStorage.setItem(\'site-dashboard-tab-\' + this.entity.card_type_id, "editor"); 
                        }
                    }
                    switch(this.entity.card_type_id) {
                        case 1:
                            break;
                    }
            
                    if (this.dashboardTab === null || (
                        this.dashboardTab !== "profile" &&
                        this.dashboardTab !== "editor" &&
                        this.dashboardTab !== "overview" &&
                        this.dashboardTab !== "directories" &&
                        this.dashboardTab !== "events" &&
                        this.dashboardTab !== "my-directories" &&
                        this.dashboardTab !== "showcase" &&
                        this.dashboardTab !== "template" &&
                        this.dashboardTab !== "config" &&
                        this.dashboardTab !== "pages" &&
                        this.dashboardTab !== "share" &&
                        this.dashboardTab !== "users" &&
                        this.dashboardTab !== "billing"
                        )
                    ) {                         
                        
                    }
                                 
                    this.toggleSnippetList();                  
                },
                toggleSnippetList: function() {
                    const self = this
                    sessionStorage.setItem("content-builder-active", true)
                    let vc = this.findVc(this)

                    vc.resizeContentBuilder(50, function(snippetList) {
                        if (snippetList === null) return;
                        
                        if (self.dashboardTab === "editor") {
                            snippetList.style.display = "block";
                        } else {
                            snippetList.style.display = "none";
                        }
                    })
                },
                setEditorScreenWidth: function() {
                    this.editorScreenWidth = sessionStorage.getItem(\'site-editor-screen-width\');
            
                    if (this.editorScreenWidth === null) { 
                        this.editorScreenWidth = "desktop"; sessionStorage.setItem(\'site-editor-screen-width\', "desktop"); 
                    }
                    this.updateEditorWidth();
                },
                setEditorWidth: function(type) {
                    this.editorScreenWidth = type; sessionStorage.setItem(\'site-editor-screen-width\', type);        
                    this.updateEditorWidth();
                },
                updateEditorWidth: function() {
                    let appCard = document.getElementsByClassName("app-card")[0]
                    
                    if (!appCard) return;
                    
                    appCard.classList.remove("media-hub-400")
                    appCard.classList.remove("app-width-mobile")
                    appCard.classList.remove("media-hub-850")
                    appCard.classList.remove("app-width-tablet")
                    screenSize = appCard.offsetWidth
                    switch(this.editorScreenWidth) {
                        case "mobile":
                            appCard.classList.add("media-hub-400")
                            appCard.classList.add("app-width-mobile")
                            screenSize = 400
                            break;
                        case "tablet":
                            appCard.classList.add("media-hub-850")
                            appCard.classList.add("app-width-tablet")
                            screenSize = 850
                            break;
                    }
                    dispatch.broadcast("screen_resize", {width: screenSize})
                },
                editConnectionRel: function(entity)
                {   
                    let self = this;
                    
                    console.log(entity);
                    
                    this.loadShareTypeList(function() 
                    {
                        let cardConnections = self.entity.Connections;
                        let ownerId = self.entity.owner_id;
                        let connectionList = self.shareTypeList;
                        let swapType = "shares";
                        let createNew = true;
         
                        '. $this->activateDynamicComponentByIdInModal(SwapCardConnectionWidget::getStaticId(),"", "edit", "entity", "cardConnections", ["ownerId"=> "ownerId", "connectionList" => "connectionList", "swapType" => "swapType", "functionType" =>"'update'", "createNew" => "createNew"], "self", true,"function(component) {
                                let modal = self.findModal(self);
                                modal.vc.setTitle('Swap Share Button Link');
                        }") . '
                    });
                },
                loadShareTypeList: function(callback)
                {
                    if (this.shareTypeList.length > 0) {
                        if (typeof callback === "function") callback();
                    }
                    let self = this;
                    const url = "/api/v1/users/get-connection-types";
                    ajax.Get(url, null, function(result)
                    {
                        self.shareTypeList = result.response.data.list;
                        self.$forceUpdate();
                        if (typeof callback === "function") callback();
                    });
                },
                updateActivePage: function(data)
                {
                    this.activePage = data.activePage;
                    
                    const vc = this.findChildVc(this);
                    const componentId = this.cardComponentIds[this.entity.template_id];
                    const cardComponent = vc.getComponentById(componentId);

                    if (data.activePage !== null && (data.activePage.rel_sort_order > 1 || cardComponent.instance.pageDisplayMultiStyle !== "true")) {
                        this.activePageUrl = data.activePage.card_tab_rel_url ? data.activePage.card_tab_rel_url : data.activePage.url;
                    } else {
                        this.activePageUrl = ""
                    }
                },
                reloadMainList: function()
                {
                    const self = this;
                    const mainVc = self.findVc(self);
                    const mainComponent = mainVc.getComponentByInstanceId(self.parentId)
                    const mainList = document.getElementById(mainComponent.instanceId);
                    const innerTable = mainList.querySelector(".entityListOuter");
                    const innerTableHeader = innerTable.querySelector("thead th a.active");
                    innerTableHeader.click(); innerTableHeader.click();
                },
                hydrateSiteDataType: function(type, list)
                {
                    let self = this;
                    ajax.Get("/api/v1/cards/get-site-" + type, null, function(result) {
                        if (result.success === false || !result.response.data) {
                            return;
                        }
    
                        const templates = Object.entries(result.response.data.list);
                        
                        templates.forEach(function([id, currTemplate]) {
                            list.push(currTemplate);
                        });
                        
                        self.$forceUpdate();
                    });
                },
                renderDomainName: function() 
                {
                    if (typeof this.entity.card_domain !== "undefined" && this.entity.card_domain !== null && this.entity.card_domain !== "") return this.buildCardDisplay()+"/"
                    return "' . $app->objCustomPlatform->getFullPublicDomainName() . '/" + this.entity.card_num + "/"
                },'.'
                renderDomainNameWidthPage: function()
                {
                    const domain = this.renderDomainName();
                    return domain + this.activePageUrl;
                },
                assignThemeToSite: function()
                {
                    modal.EngageFloatShield();
                    var data = {title: "Assign Theme?", html: "Are you sure?<br>This will remove the current one and it\'s settings and install this one."};
                    modal.EngagePopUpConfirmation(data, function() {
                        modal.CloseFloatShield(function() {
                            modal.CloseFloatShield();
                        });
                    }, 400, 115);
                },
                assignImageToSite: function(data)
                {
                    const self = this;
                    const imageData = {label: "background___" + data.label, type: "image", card_id: self.entity.card_id, value: data.image.url, options: JSON.stringify(data.options ?? {})}
                    ajax.Post("/api/v1/cards/assign-media-to-site", imageData, function(result) {
                        if (result.success === false) {
                            return;
                        }
                        self.entity.Media[data.label] = "image|" + data.image.url + "|" + JSON.stringify(data.options ?? {})
                        dispatch.broadcast("reload_site_media");
                        self.$forceUpdate();
                    });
                },
                assignLogoToSite: function(data)
                {
                    const self = this;
                    const imageData = {label: "logo___" + data.label, type: "image", card_id: self.entity.card_id, value: data.image.url, options: JSON.stringify(data.options ?? {})}
                    ajax.Post("/api/v1/cards/assign-media-to-site", imageData, function(result) {
                        if (result.success === false) {
                            return;
                        }
                        if (!self.entity.Logos) self.entity.Logos = []
                        self.entity.Logos[data.label] = "image|" + data.image.url + "|" + JSON.stringify(data.options ?? {})
                        dispatch.broadcast("reload_site_logos");
                        self.$forceUpdate();
                    });
                },
                assignColorToSite: function(data)
                {
                    let self = this;
                    let imageData = {label: "background___" + data.label, type: "color", card_id: self.entity.card_id, value: data.color, options: JSON.stringify(data.options ?? {})}
                    ajax.Post("/api/v1/cards/assign-media-to-site", imageData, function(result) {
                        if (result.success === false) {
                            return;
                        }
                        self.entity.Media[data.label] = "color|" + data.color + "|" + JSON.stringify(data.options ?? {})
                        dispatch.broadcast("reload_site_media");
                        self.$forceUpdate();
                    });
                },
                assignGradientToSite: function(data)
                {
                    let self = this;
                    let imageData = {label: "background___" + data.label, type: "gradient", card_id: self.entity.card_id, value: data.gradient, options: JSON.stringify(data.options ?? {})}
                    ajax.Post("/api/v1/cards/assign-media-to-site", imageData, function(result) {
                        if (result.success === false) {
                            return;
                        }
                        self.entity.Media[data.label] = "gradient|" + data.gradient + "|" + JSON.stringify(data.options ?? {})
                        dispatch.broadcast("reload_site_media");
                        self.$forceUpdate();
                    });
                },
                openThemeConfigruation: function()
                {
                    let self = this
                    let themeSettings = this.entity.Template.data
                    let configSettings = this.entity.Settings.theme_config ? this.entity.Settings.theme_config : []
                   ' . $this->activateDynamicComponentByIdInModal(ManageSiteTemplateSettingsWidget::getStaticId(), "", "edit", "this.entity", "this.mainEntityList", ["configSettings" => "configSettings", "themeSettings" => "themeSettings"], "this", true,"function(component) {
                        //console.log(component);
                    }") . '
                },
                isMicroSite: function()
                {
                    return this.entity && this.entity.card_type_id === 1
                },
                isPersona: function()
                {
                    return this.entity && this.entity.card_type_id === 2
                },
                isGroup: function()
                {
                    return this.entity && this.entity.card_type_id === 3
                },
                ' . VueCustomMethods::renderSortMethods() . '
        ';
    }

    protected function renderComponentMountedScript(): string
    {
        return '
            dispatch.register("update_active_page_in_card_editor", this, "updateActivePage");
            dispatch.register("rehydrate_site_in_editor", this, "hydrateCard");
            dispatch.register("reload_site_in_editor", this, "reloadCard");
            dispatch.register("reload_site_profile_in_editor", this, "reloadCardProfile");
            dispatch.register("assign_image_to_site", this, "assignImageToSite");
            dispatch.register("assign_logo_to_site", this, "assignLogoToSite");
            dispatch.register("assign_color_to_site", this, "assignColorToSite");
            dispatch.register("assign_gradient_to_site", this, "assignGradientToSite");
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return '
            this.setEditorScreenWidth();
            this.component_title = this.component_title_original;
            let self = this;
            
            // let cardHub = document.getElementById("'.$this->cardHub->getInstanceId().'");
            // let cardHubShadow = cardHub.createShadowRoot();
            
            if (this.entity && typeof this.entity.sys_row_id !== "undefined") 
            {
                this.loadCardDataById(this.entity.sys_row_id, function(data)
                {
                    if (!self.canUserViewCard()) {
                        self.backToComponent();
                    }
                    self.setDashboardTab();
                    self.loadContentBuilder(false);
                    
                    self.disableComponentLoadingSpinner();
                    self.checkForCardBuildOut();
                    self.loadCardPaymentData();
                    modal.CloseFloatShield();
                });
            }
            else
            {
                self.setDashboardTab();
                this.showNewSelection = true;
            }

            this.themeList = []
            this.hydrateSiteDataType("themes", this.themeList);
            this.loadShareTypeList();
        ';
    }

    protected function renderTemplate() : string
    {
        /** @var App $app */
        global $app;
        $portalThemeMainColor = $app->objCustomPlatform->getCompanySettings()->FindEntityByValue("label","portal_theme_main_color")->value ?? "006666";
        return '
            <div class="formwrapper-manage-entity">
                <v-style type="text/css">
                    .flex-column {
                        display:flex !important;
                        flex-direction:row !important;
                    }
                    .right-hand-column,
                    .left-hand-column,
                    .middle-hand-column {
                        display:flex;
                    }
                    .left-hand-column {
                        flex:0 0 150px;
                        background: #ccc;
                    }
                    .right-hand-column {
                        flex:0 0 225px;
                        background: #ccc;
                    }
                    .middle-hand-column {
                        flex:1 1 calc(100% - 400px);
                        overflow-y:auto;
                    }
                    .middle-hand-column .siteContainer > div:not(.editor) {
                        padding-bottom: 25px;
                    }
                    .entityDashboard table.header-table {
                        padding-bottom:10px;
                        margin-left:15px;
                        margin-bottom:12px;
                        width: calc(100% - 15px);
                    }
                    .main-site-menu {
                        display:flex;
                        flex-direction:column;
                        width: 100%;
                    }
                    .main-site-menu > div {
                        display:flex;
                        padding: 10px 15px;
                        transition: all 0.2s ease-in-out 0s;
                        position:relative;
                        left:0;
                    }
                    .main-site-menu > div.activeMenuItem {
                        display:flex;
                        padding: 10px 15px;
                        left:-2px;
                        border-right: 4px solid #000;
                        margin-right: -2px;
                        background: #'.$portalThemeMainColor.';
                        color:white;
                    }
                    .main-site-menu > div > span {
                        position: relative;
                        top: 2px;
                        margin-right: 6px;
                        width:22px;
                        text-align:center;
                    }
                    .main-site-menu > div.activeMenuItem > span {
                        color:white;
                    }
                    .entityDashboard .entityTab {
                        height:calc(100% - 47px);
                    }
                    .entityDashboard .editor-screen-width {
                        height:35px;background-color:#ccc;display:flex;flex-direction:column;align-items:center;
                    }
                    .entityDashboard .editor-screen-width-inner {
                        display:flex;flex:1 1 auto;align-items: center;
                    }
                    .entityDashboard .editor-screen-width-inner > div {
                        display:flex;padding: 0 5px;
                        cursor:pointer;
                        text-align:center;
                    }
                    .entityDashboard .editor-site-workbench {
                        position: relative;
                        overflow-y:auto;
                        width:100%;
                        height:calc(100% - 35px);
                    }
                    .portal_theme_1 .entityDashboard .editor-site-workbench {
                        height:calc(100% - 83px);
                    }
                    .entityDashboard .editor-site-workbench #app-vue.app-card {
                        transition: width 0.2s ease-in-out;
                    }
                    .editor-site-workbench .app-card {
                        width:100%;
                        margin:auto;
                    }
                    .editor-site-workbench .app-card.media-hub-400 {
                        width:400px;
                    }
                    .editor-site-workbench .app-card.media-hub-850 {
                        width:850px;
                    }
                    .screenWidthToggle {
                        width:30px;
                        height:28px;
                        display:inline-block;
                        padding: 3px 7px 3px !important;
                        margin: -5px 0 -5px;
                    }
                    .activeScreenWidth {
                        background-color:#444;
                        border-radius: 6px;
                    }
                    .activeScreenWidth span {
                        color:#fff !important;
                    }
                    body #_cbhtml #divSnippetList.micah-test {
                        top: var(--cb-top) !important;
                        bottom: var(--cb-bottom) !important;
                        left: var(--cb-left) !important;
                        right: var(--cb-right) !important;
                        width: var(--cb-width) !important;
                        height: var(--cb-height) !important;
                        transition: none !important;
                    }
                    body #_cbhtml #divSnippetHandle {
                        display:none !important;
                    }
                    body #_cbhtml .is-design-list > div,
                    body #_cbhtml .is-ui .is-design-list > div {
                        width: calc(100% - 40px) !important;
                    }
                </v-style>
                <div v-if="entityNotFound" class="entityDashboard">
                    <!-- 404 here -->
                </div>
                <div class="entityDashboard">
                    <table class="table header-table">
                        <tbody>
                        <tr>
                            <td class="mobile-to-table">
                                <h3 class="account-page-title">
                                <a v-show="hasParent" v-on:click="backToComponent()" id="back-to-entity-list" class="fa back-to-entity-list app-in-editor pointer"></a> 
                                {{ component_title }}
                                </h3>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="cardPromoBanner" v-if="entity && entity.product_id === 1100">
                        <div><span class="fas fa-exclamation-triangle fas-large desktop-30px"></span>This Is A Promo Card</div>
                    </div>
                    <div v-show="!cardIsBuildOut(entity) || userAdminRole === true " class="entityTab flex-column" data-tab="profile">
                        <div class="left-hand-column">
                            <div v-if="isMicroSite()" class="main-site-menu">
                                <div v-for="currMainMenu in mainMenu" class="pointer" v-bind:class="{activeMenuItem: dashboardTab == currMainMenu.tag}" v-on:click="setDashboardTabByTag(currMainMenu.tag)"><span v-bind:class="currMainMenu.icon"></span>{{ currMainMenu.title }}</div>
                            </div>
                            <div v-if="isPersona()" class="main-site-menu">
                                <div v-for="currMainMenu in personaMenu" class="pointer" v-bind:class="{activeMenuItem: dashboardTab == currMainMenu.tag}" v-on:click="setDashboardTabByTag(currMainMenu.tag)"><span v-bind:class="currMainMenu.icon"></span>{{ currMainMenu.title }}</div>
                            </div>
                            <div v-if="isGroup()" class="main-site-menu">
                                <div v-for="currMainMenu in groupMenu" class="pointer" v-bind:class="{activeMenuItem: dashboardTab == currMainMenu.tag}" v-on:click="setDashboardTabByTag(currMainMenu.tag)"><span v-bind:class="currMainMenu.icon"></span>{{ currMainMenu.title }}</div>
                            </div>
                        </div>
                        <div class="middle-hand-column">
                            <div class="siteContainer" style="width:100%;height:100%;">
                                <div v-show="dashboardTab === \'editor\'" class="editor" style="width:100%;height:100%;position:relative;">
                                    <div class="editor-screen-width">
                                        <div class="editor-screen-width-inner">
                                            <div v-on:click="setEditorWidth(\'desktop\')"><span class="screenWidthToggle" v-bind:class="{activeScreenWidth: editorScreenWidth == \'desktop\'}"><span class="fa fa-desktop"></span></span></div>
                                            <div v-on:click="setEditorWidth(\'tablet\')"><span class="screenWidthToggle" v-bind:class="{activeScreenWidth: editorScreenWidth == \'tablet\'}"><span class="fa fa-tablet-alt"></span></span></div>
                                            <div v-on:click="setEditorWidth(\'mobile\')"><span class="screenWidthToggle" v-bind:class="{activeScreenWidth: editorScreenWidth == \'mobile\'}"><span class="fa fa-mobile-alt"></span></span></div>
                                        </div>
                                    </div>
                                    <div v-if="entity" style="position: absolute;top:5px;left:12px;"><a v-bind:href="renderDomainNameWidthPage()" target="_blank">{{ renderDomainName() }}</a><input v-if="activePage" class="app-page-editor-text-transparent" v-model="activePageUrl"></div>
                                    <div class="editor-site-workbench">
                                        <div id="editor-site-workbench-box" style="display: block;width:100%;position: absolute;background:url(\'/portal/images/workshop-background-01.png\') repeat center center, linear-gradient(rgb(80 80 80) 0%, rgb(199 208 255) 50%, rgb(80 80 80) 100%); background-size: 25%;">
                                            <div class="app-wrapper app-in-editor" style="overflow:hidden;margin:auto;height: inherit;">
                                                <div class="app-wrapper-inner" style="height:inherit;">
                                                    <div id="app-vue" class="app-card" style="height:inherit;background-color:#fff;box-shadow: 0 0 15px rgba(0,0,0,.3);">
                                            ' . $this->renderRegisteredDynamicComponent(
                                                $this->registerDynamicComponentViaRegisteredHub(
                                                    $this->cardHub,
                                                    $this->activeLandingWidget,
                                                    "view",
                                                    [
                                                        new VueProps("activeCardId", "object", "activeCardId")
                                                    ])
                                            ) . '
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-show="dashboardTab === \'overview\'">
                                    <div v-if="isGroup()" class="pl-3 pr-3">
                                        <span class="pop-up-dialog-main-title-text">Overview</span>
                                        '. $this->registerAndRenderDynamicComponent(
                                            $this->directoryOverview,
                                            "view"
                                        ) .'
                                    </div>
                                </div>
                                <div v-show="dashboardTab === \'profile\'">
                                    <div class="pl-3 pr-3">
                                        <span class="pop-up-dialog-main-title-text">Profile</span>
                                        ' . $this->registerAndRenderDynamicComponent(
                                            $this->cardProfile,
                                            "view"
                                        ) . '
                                    </div>
                                </div>
                                <div v-show="dashboardTab === \'group-profile\'">
                                    <div class="pl-3 pr-3">
                                        <span class="pop-up-dialog-main-title-text">Profile</span>
                                        ' . $this->registerAndRenderDynamicComponent(
                                            $this->groupProfile,
                                            "view"
                                        ) . '
                                    </div>
                                </div>
                                <div v-show="dashboardTab === \'my-directories\'">
                                    <div v-if="isPersona()" class="pl-3 pr-3">
                                        <span class="pop-up-dialog-main-title-text">My Directories</span>
                                        '. $this->registerAndRenderDynamicComponent(
                                            $this->directoryRegistrationManager,
                                            "view"
                                        ) .'
                                    </div>
                                </div>
                                <div v-show="dashboardTab === \'directories\'">
                                    <div v-if="isGroup()" class="pl-3 pr-3">
                                        <span class="pop-up-dialog-main-title-text">Directories</span>
                                        '. $this->registerAndRenderDynamicComponent(
                                            $this->directoryManager,
                                            "view"
                                        ) .'
                                    </div>
                                </div>
                                <div v-show="dashboardTab === \'events\'">
                                    <div v-if="isGroup()" class="pl-3 pr-3">
                                        <span class="pop-up-dialog-main-title-text">Events</span>
                                        '. $this->registerAndRenderDynamicComponent(
                                            $this->directoryManager,
                                            "view"
                                        ) .'
                                    </div>
                                </div>
                                <div v-show="dashboardTab === \'showcase\'">
                                    <div v-if="isPersona()" class="pl-3 pr-3">
                                        <span class="pop-up-dialog-main-title-text">Showcase</span>
                                        <p>Showcase information this persona is displaying? One for the main Persona page and the other for the directories?</p>
                                    </div>
                                </div>
                                <div v-show="dashboardTab === \'templates\'">
                                    <div class="pl-3 pr-3">
                                        <span class="pop-up-dialog-main-title-text">Themes</span>
                                        <p>Change the look and feel of your site!</p>
                                        <hr>
                                        <div class="theme-outer">
                                            <ul v-if="themeList" class="d-flex flex-column flex-wrap justify-content-between">
                                                <li v-for="currTheme in themeList" class="col-lg-4 col-md-3 col-sm-2">
                                                    <div class="card">
                                                        <img class="card-img-top" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22286%22%20height%3D%22180%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20286%20180%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_18026f9ee11%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A14pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_18026f9ee11%22%3E%3Crect%20width%3D%22286%22%20height%3D%22180%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22107.203125%22%20y%3D%2296.3%22%3E286x180%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" alt="Card image cap">
                                                        <div class="card-body">
                                                            <h5 class="card-title">{{ currTheme.title }}</h5>
                                                            <p class="card-text">{{ currTheme.description }}</p>
                                                            <a v-on:click="assignThemeToSite(currTheme)" class="btn btn-primary w-100">Assign Theme</a>
                                                        </div>
                                                    </div>
                                                </li>    
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div v-show="dashboardTab === \'share\'">
                                    <div class="pl-3 pr-3">
                                        <span class="pop-up-dialog-main-title-text">Share</span>
                                    </div>
                                </div>
                                <div v-show="dashboardTab === \'users\'">
                                    <div class="pl-3 pr-3">
                                        <span class="pop-up-dialog-main-title-text">Users</span>
                                        <p>Need helping hands? You can link other accounts to help you manage your workflow!</p>
                                        <hr>
                                    </div>
                                </div>
                                <div v-show="dashboardTab === \'billing\'">
                                    <div class="pl-3 pr-3">
                                        <span class="pop-up-dialog-main-title-text">Billing</span>
                                        <p>Manage your payment account below and review your billing history.</p>
                                        <hr>
                                        <h4>
                                            <span class="fas fa-credit-card fas-large desktop-30px"></span>
                                            <span class="fas-large">Payment Account</span></h4>
                                        <div id="payment-account-outer" class="entityDetailsInner ajax-loading-anim-inline">
                                            <component ref="dynPaymentAccountRef" :is="dynPaymentAccountComponent" :entity="entity"></component>
                                        </div>
                                        <h4 class="account-page-subtitle" style="margin-top: 2rem;">History</h4>
                                        <div id="payment-history-outer" class="entityDetailsInner ajax-loading-anim-inline entityListActionColumn">
                                            <component ref="dynPaymentHistoryRef" :is="dynPaymentHistoryComponent" :entity="entity"></component>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="right-hand-column" v-show="dashboardTab === \'editor\'">
                            <div class="editor-content-builder" style="position:relative; width:100%">
                                <div v-on:click="openThemeConfigruation" style="pointer" style="position: absolute; top: 5px; right: 12px; white-space:nowrap; cursor:pointer">
                                    <span class="fa fa-cog"></span> Theme Settings
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

    private function getCardComponentIds() : array
    {
        $digitalCardDirectories = glob(APP_ENTITIES . "/cards/components/vue/digitalcardwidget/*" , GLOB_ONLYDIR);

        $objActiveAppEntities = [];

        foreach( $digitalCardDirectories as $currDigitalCardDirectory)
        {
            $templateVersionId = substr($currDigitalCardDirectory, -1);
            $templateUuid = null;

            $arModuleWidgetPaths = glob($currDigitalCardDirectory . "/*");

            foreach($arModuleWidgetPaths as $currModuleWidgetPath)
            {
                if ( is_file($currModuleWidgetPath) && strpos($currModuleWidgetPath, "DigitalCardMainWidget.php") !== false)
                {
                    [$currClassIndex, $objClassInstanceName] = getClassData($currModuleWidgetPath);

                    if ($objClassInstanceName === false)
                    {
                        continue;
                    }

                    /** @var VueComponent $objClassInstance */
                    try
                    {
                        $objClassInstance = new $objClassInstanceName();
                        $templateUuid = $objClassInstance->getId();
                        break;
                    }
                    catch (ArgumentCountError $ex)
                    {
                        // Silent exit.
                        // If we cant instantiate it, we don't have to worry about hydrating it.
                    }
                }
            }

            $objActiveAppEntities[$templateVersionId] = $templateUuid;
        }

        return $objActiveAppEntities;
    }
}