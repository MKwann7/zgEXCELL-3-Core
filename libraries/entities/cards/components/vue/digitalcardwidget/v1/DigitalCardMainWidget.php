<?php

namespace Entities\Cards\Components\Vue\DigitalCardWidget\V1;

use App\Core\AppModel;
use App\Website\Vue\Classes\VueProps;
use Entities\Cards\Components\Vue\CardWidget\SwapCardConnectionWidget;
use Entities\Cards\Components\Vue\DigitalCardWidget\Assets\AbstractDigitalSiteComponent;
use Entities\Cards\Components\Vue\DigitalCardWidget\Assets\SharedVueSiteMethods;

class DigitalCardMainWidget extends AbstractDigitalSiteComponent
{
    protected string $id = "74db623b-2abd-41b4-b710-c9e1108ab608";
    protected string $cssPrefix = ".app-template-1";

    public function __construct (?AppModel $entity = null)
    {
        $this->cardPage = new DigitalCardPageWidget();
        parent::__construct($entity);
    }

    protected function renderComponentDataAssignments() : string
    {
        return SharedVueSiteMethods::dataAssignments($this, true) . "
            showSplashImage: true,
            entityFound: false,
            entity: null,
            menuOpen: false,
            modulesOpen: false,
            takeOffOpen: false,
            shareSaveOpen: false,
            loginOpen: false,
            membersAccessOpen: false,
            socialMediaOpen: false,
            handedType: 'right',
            loginUsername: '',
            loginPassword: '',
            loggedIn: false,
            loggedInUser: {},
            loggedInAttemptError: '',
            cardUserAssociation: 'vistior',
            cardView: 'public',
            learnMoreAboutShareSave_box: false,
            realEstateModule_box: false,
            emailCard_box: false,
            mainCardColor: '#ff0000',
            mainCardColorRgb: '',
            secondaryColor: '#ff0000',
            secondaryColorRgb: '',
            cardWidth: 400,
            pageHeight: 55,
            cardModulesByClass: [],
            cardPagePadding: 55,
            shareTypeList: [],
        ";
    }

    protected function renderComponentComputedValues() : string
    {
        return 'cardConnections: function()
                {
                    if (typeof this.entity !== "undefined") { return this.entity.Connections; }
                    return null;
                },
                cardSocialMedia: function()
                {
                    if (typeof this.entity !== "undefined") { return this.entity.SocialMedia; }
                    return null;
                },
                cardPages: function()
                {
                    if (typeof this.entity !== "undefined") { return this.entity.Tabs; }
                    return null;
                },
                cardModules: function()
                {
                    return this.cardModulesByClass;
                },
                cardPrimaryColor: function()
                {
                    return {
                        background: "#183470"
                    }
                    
                    if (this.mainCardColorRgb === "") return null
                    
                    const red = this.mainCardColorRgb.r;
                    const green = this.mainCardColorRgb.g;
                    const blue = this.mainCardColorRgb.b;
                    
                    let redDark = red - 50; if (redDark < 0) redDark = 0;
                    let greenDark = green - 50; if (greenDark < 0) greenDark = 0;
                    let blueDark = blue - 50; if (blueDark < 0) blueDark = 0;
                    
                    const backgroundImage = {
                        backgroundImage: "linear-gradient(180deg, rgba("+red+","+green+","+blue+",1.00) 0%, rgba("+redDark+","+greenDark+","+blueDark+",1.00) 100%)",
                    }
                    
                    return backgroundImage;
                },
                cardWidthPadding: function()
                {
                    return {
                        width: "100%",
                    };
                },
                pageTitlePadding: function()
                {
                    return {
                        paddingTop: (this.cardPagePadding/2) + "px",
                        paddingBottom: (this.cardPagePadding/2) + "px",
                    };
                },';
    }

    protected function renderComponentHydrationScript(): string
    {
        return SharedVueSiteMethods::hydration($this) . '
            if (this.entity) {
                const websiteView = sessionStorage.getItem(\'active_editor_siteView_\' + this.entity.card_id, "members")
                this.cardView = websiteView !== "members" ? "public" : "members";
                this.loadShareTypeList();
            }    
        ';
    }

    protected function renderComponentMethods() : string
    {
        global $app;
        return SharedVueSiteMethods::methods($this) . '
            loadSiteData: function(siteId) {
                const self = this
                if (this.siteIsLoadedInEditor()) {
                    // Card Is In Editor
                    self.editor = true
                    self.entity = self.$parent.$parent.entity
                    self.hydrateCard()
                    self.loadCardModules()
                    self.batchLoadPages(false)
                    self.registerHistory()
                } else {
                    // Card Is Live
                    self.loadCardDataById(siteId, function(data) {
                        self.loadCardModules()
                        self.batchLoadPages(false)
                        self.registerHistory()
                    });
                }
            }, 
            openSitePageLive: function(page) {
                if (this.editor) { return; }
                this.openSitePage(page);
            },
            openSitePageInEditor: function(page,) {
                if (!this.editor) { return; }
                this.openSitePage(page);
            },
            prePageAssignment: function(page, options, callback) {
                const dynamicContent = document.getElementById("dynamicPageComponent")
                slideUp(dynamicContent, 350, function() {
                    if (typeof callback === "function") callback(page)                
                })
            },
            postPageAssignment: function(page, options, callback) {
                const dynamicContent = document.getElementById("dynamicPageComponent")
                slideDown(dynamicContent, 350, function() {
                    if (typeof callback === "function") callback(page)                
                })
            },
            editConnectionRel: function(entity)
            {   
                let self = this;
                
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
            processConnectionRequest: function(connection)
            {
                if (this.editor) return;
                
                switch(connection.action)
                {
                    case "sms":
                        window.location = "sms:" + connection.connection_value;
                        break;
                    case "phone":
                        window.location = "tel:" + connection.connection_value;
                        break;
                    case "email":
                        window.location = "mailto:" + connection.connection_value;
                        break;
                    case "link":
                        window.open(connection.connection_value, "_blank");
                        break;
                }
            },
            loadCardIntoContacts: function()
            {
                window.open("'.$app->objCustomPlatform->getFullPublicDomainName().'/api/v1/cards/download-vcard?card_id=" + this.entity.card_num, "_blank");
            },
            sendSms: function()
            {
                const unqiueUrl = (this.entity.card_vanity_url !== "" ? this.entity.card_vanity_url : this.entity.card_num);
                window.location = "sms:?&body='.$app->objCustomPlatform->getFullPublicDomainName().'/" + unqiueUrl + "%20Click%20the%20link%20to%20connect%20with%20" + this.entity.card_owner_name + "!";
            },
            sendShare: function()
            {
                window.location = "https://optin.mobiniti.com/" + this.entity.card_num;
            },
            openCardMenu: function()
            {
                this.menuOpen = true;
            },
            closeCardMenu: function()
            {
                this.menuOpen = false;
            },
            hideSplashImage: function()
            {
                this.closeCardMenu();
                this.showSplashImage = false;
            },
            openSplashImage: function()
            {
                this.closeCardMenu();
                this.showSplashImage = true;
            },
            openModules: function()
            {
                this.modulesOpen = true;
                this.menuOpen = false;
            },
            openTakeOff360: function()
            {
                this.takeOffOpen = true;
                this.modulesOpen = false;
                this.menuOpen = false;
            },
            closeTakeOff360: function()
            {
                this.takeOffOpen = false;
                this.modulesOpen = true;
                this.menuOpen = false;
            },
            closeModules: function()
            {
                this.modulesOpen = false;
            },
            openShareSave: function()
            {
                this.shareSaveOpen = true;
                this.menuOpen = false;
            },
            openLogin: function()
            {
                this.loginOpen = true;
                this.menuOpen = false;
            },
            signOut: function()
            {
                modal.EngageFloatShield();
                
                let self = this;
                
                setTimeout(function() {
                    self.loginOpen = false;
                    self.menuOpen = false;
                    self.$parent.$parent.processSignOut()
                    modal.CloseFloatShield();
                }, 1000);
            },
            openMembersAccess: function()
            {
                if (this.loggedIn === true && this.cardOwnerLoggedIn === false)
                {
                    
                }
                else
                {
                    this.membersAccessOpen = true;
                    this.menuOpen = false;
                }
            },
            closeShareSave: function()
            {
                this.shareSaveOpen = false;
            },
            closeLogin: function()
            {
                this.loginOpen = false;
            },
            closeMembersAccess: function()
            {
                this.membersAccessOpen = false;
            },
            attemptMemberLogin: function()
            {
                
            },
            attemptLogin: function()
            {
                if(!this.validateUsername(this.loginUsername)) return;
                if(!this.validatePassword(this.loginPassword)) return;
                
                let self = this;
                
                this.$parent.$parent.processLoginAuthentication(this.loginUsername, this.loginPassword, this.entity.sys_row_id, function() {
                    self.loginOpen = false;
                });
            },
            attemptMemberLogin: function()
            {
                if(!this.validateUsername(this.loginUsername)) return;
                if(!this.validatePassword(this.loginPassword)) return;
                
                let self = this;
                this.loginCardUser(
                    "'.$app->objCustomPlatform->getFullPortalDomainName().'/api/v1/users/validate-existing-user-credentials",
                    this.loginUsername, 
                    this.loginPassword, 
                    function(result) 
                    {
                        if (result.response.success === false)
                        {
                            self.loggedIn = false;
                            self.loggedInAttemptError = result.response.message;
                            return;
                        }
                        
                        console.log(self.entity);
                        
                        self.loggedInUser = result.response.data.user;
                        self.loggedIn = true;
                        Cookie.erase("auth")
                        Cookie.set("auth", true)
                        console.log(Cookie.get("auth"))
                        self.cardView = "private";
                    }, 
                    function(error) 
                    {
                        console.log(error);
                    });
            },
            validateUsername: function()
            {
                return true;
            },
            validatePassword: function()
            {
                return true;
            },
            openSocialMedia: function()
            {
                this.socialMediaOpen = !this.socialMediaOpen;
            },
            toggleHandControls: function()
            {
                console.log(this.handedType);
                if (this.handedType === "left")
                {
                    this.setRightHanded();
                    this.closeCardMenu();
                    return;
                }
                
                this.setLeftHanded();
                this.closeCardMenu();
            },
            setLeftHanded: function()
            {
                console.log("setting left handed");
                const bodyEl = document.getElementsByTagName("body")[0];
                
                bodyEl.classList.remove("handed-right");
                bodyEl.classList.add("handed-left");
                
                this.handedType = "left";
                sessionStorage.setItem(\'card-hand-orientation\', "left");
            },
            setRightHanded: function()
            {
                const bodyEl = document.getElementsByTagName("body")[0];
                
                bodyEl.classList.remove("handed-left");
                bodyEl.classList.add("handed-right");
                
                this.handedType = "right";
                sessionStorage.setItem(\'card-hand-orientation\', "right");
            },
            setAuth: function()
            {
                this.isLoggedIn = this.$parent.$parent.isLoggedIn;
                this.authUserId = this.$parent.$parent.authUserId;
            },
            checkHanded: function()
            {
                this.handedType = sessionStorage.getItem(\'card-hand-orientation\');
                if (this.handedType === null) { this.handedType = "right"; sessionStorage.setItem(\'card-hand-orientation\', "right"); }
                
                if (this.handedType === "right")
                {
                    this.setRightHanded();
                    return;
                }
                
                this.setLeftHanded();
            },
            getUserAvatar: function()
            {
                if (typeof this.entity.user_avatar === "undefined")
                {
                    return "'.$app->objCustomPlatform->getFullPortalDomainName().'/_ez/images/users/no-user.jpg";
                }
                return this.entity.user_avatar;
            },
            displayFontAwesome: function(connection)
            {
                if (connection.connection_type_id === 1 && connection.action === "sms")
                {
                    return "fas fa-sms";
                }
                
                return connection.font_awesome;
            },
            toggleRealEstateModule: function()
            {
                let self = this;
                if (this.realEstateModule_box === false) { slideDown( elm("realEstateModule_box"), 250, function() {
                    self.realEstateModule_box = true;
                }); }
                if (this.realEstateModule_box === true) { slideUp( elm("realEstateModule_box"), 250, function() {
                    self.realEstateModule_box = false;
                }); }
            },
            learnMoreAboutShareSave: function()
            {
                let self = this;
                if (this.learnMoreAboutShareSave_box === false) { slideDown( elm("learnMoreAboutShareSave_box"), 250, function() {
                    self.learnMoreAboutShareSave_box = true;
                }); }
                if (this.learnMoreAboutShareSave_box === true) { slideUp( elm("learnMoreAboutShareSave_box"), 250, function() {
                    self.learnMoreAboutShareSave_box = false;
                }); }
            },
            emailCard: function()
            {
                let self = this;
                if (this.emailCard_box === false) { slideDown( elm("emailCard_box"), 250, function() {
                    self.emailCard_box = true;
                }); }
                if (this.emailCard_box === true) { slideUp( elm("emailCard_box"), 250, function() {
                    self.emailCard_box = false;
                }); }
            },
            goto360TakeoffDemo: function()
            {
                this.openTakeOff360();
            },
            gotoGuardSmartDemo: function()
            {
                const url = "https://www.figma.com/proto/g36rbE71irGy3snguMXztD/Dashboard?node-id=198%3A1044&scaling=scale-down";
                window.open(url, "_blank");
            },
            gotoSocialLink: function(social)
            {
                const url = social.connection_value;
                window.open(url, "_blank");
            },
            ucWords: function(str) {
                return str.replace("_"," ").replace(/\w\S*/g, function (txt) {
                    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                });
            },
            enableModuleTool: function(id) {
                
            },
            registerHistory: function()
            {                
                if (this.isLoggedIn !== "active") return;
                
                const url = "api/v1/cards/register-card-in-history";
                const postData = {
                    card_id: this.entity.card_id,
                    user_id: this.authUserId
                };
                
                ajax.Post(url, postData, function(result) 
                {
                });
                
            },
            hexToRgb: function(hex) {
              const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
              return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
              } : null;
            }
            ';
    }

    protected function renderTemplate(): string
    {
        return '
            <div v-if="entityFound == true" class="app-section app-template-1 app-section-scrollable">
                <v-style type="text/css">
                    .wrapper {
                        box-shadow:rgba(0,0,0,.3) 0 0 10px;
                    }
                    .mainButtons a li {
                        position:relative;
                    }
                    .mainPages, .mainPages > ul, .mainPages > ul > li, .mainPages > ul > li > div {
                        width:100%;
                        position:relative;
                    }
                    .app-section-scrollable {
                        overflow-y:auto;
                    }
                    .app-section-scrollable .app-main-component {
                        height: auto !important;
                    }
                    .tab-title-text {
                        position: absolute;
                        top: -40px;
                        left: -3px;
                        font-size: 13px;
                        width: calc(100% + 6px);
                        text-transform: uppercase;
                        -webkit-border-radius: 5px 5px 0 0;
                        -moz-border-radius: 5px 5px 0 0;
                        border-radius: 5px 5px 0 0;
                        height: 28px;
                        display: flex;
                        vertical-align: middle;
                        text-align: center !important;
                        justify-content: center;
                        flex-direction: column;
                    }
                    .social-media-button-outer {
                        position: absolute;
                        right:0;
                        top:90px;
                    }
                    .social-media-button-outer ul {
                        left: 34px;
                        position: relative;
                    }
                    .social-media-button-outer li {
                        margin-bottom:5px;
                        padding: 11px 12px 8px;
                        border-radius: 0 3px 3px 0;
                        text-align: center;
                        display:block;
                    }
                    .social-media-button-outer li i {
                        color:white;
                        width:10px;
                        display: inline-block;
                        position: relative;
                        left: -1px;
                    }
                    .mainButtons {
                        margin: 2.5% 0;
                    }
                    .mainButtons ul {
                        margin:0;
                        padding:0;
                    }
                    .mainButtons > ul > li:first-child {
                        margin-left: 3%;
                    }
                    .mainButtons > ul > li:last-child {
                        margin-right: 3%;
                    }
                    .mainButtons > ul > li {
                        background-color: #183470;
                        width: 21.25%;
                        float: left;
                        border-radius: 10px;
                        margin: 0 1.5%;
                        padding: 3% 3%;
                        position:relative;
                        list-style-type: none;
                    }
                    .mainButtons > ul > li > div {

                        text-align: center;
                        color: #fff;
                        font-size: 30px;
                        list-style-type:none;
                        position:relative;
                    }
                    .tabs {
                        margin: 1.5% 0 0 0;
                    }
                    .tabs ul {
                        margin:0;
                        padding:0;
                    }
                    .tabs li {
                        margin: 0;
                        padding: 0;
                        border: 0;
                        list-style-type:none;
                    }
                    .tabTitle {
                        color: #fff;
                        font-size: 19px;
                        padding: 15px 4px;
                        text-align: center;
                        cursor: pointer;
                    }
                    .mainImageHandler {
                        height:500px;
                    }
                    .app-main-component .mainButtons *:not(span.fas) , .app-main-component .tabTitle *:not(.fas) {
                        color: #fff !important;
                    }
                    
                    @media (max-width:600px){
            
                        .social-media-button-outer {
                            position: relative;
                            right:0;
                            left:0;
                            top:0;
                            width:100%;
                            display: table;
                        }
                        .social-media-button-outer ul {
                            left: 0;
                            display: table-row;
                        }
                        .social-media-button-outer li {
                            margin-bottom:0;
                            display: table-cell;
                            padding: 11px 12px 8px;
                            border-radius: 0;
                            display:inline-block;
                            width:100%;
                        }
                        .mainImageHandler {
                            height:100vw;
                        }
                        
                    }
                                        
                    .tabTitle, .tabTitleText {
                        position:relative;
                    }
                    .text-input-editor {
                        position: absolute;
                        font-size:14px;
                        color:black !important;
                        cursor:pointer;
                        background:white;
                        border-radius:25px;
                        padding:4px 2px 5px 6px;
                        box-shadow: 0 0 3px rgba(0,0,0,.4);
                        z-index:1002;
                    }
                    .text-input-editor-inner-center {
                        top: calc(50% - 15px);
                        left: calc(50% - 15px);
                    }
                    .text-input-editor-left {
                        top: 3px;
                        left:-33px;
                    }
                    .text-input-editor-inner-top-left {
                        top: 10px;
                        left:10px;
                    }
                    .text-input-editor-connection {
                        top: 40%;
                        left: -7px;
                    }
                    .media-hub-850  .text-input-editor-connection {
                        top: 36%;
                    } 
                    .media-hub-400 .text-input-editor-connection {
                        top: 30%;
                    } 
                    .text-input-editor-inner-top-right {
                        top: 10px;
                        right:10px;
                    }
                    .text-input-editor-navigation {
                        top: 10px;
                        left: 10px;
                    }
                    .text-input-editor > span { 
                        position: absolute;
                        top: 1px;
                        pointer-events:none;
                        left: 10px;
                        opacity: 0;
                        transition: all .1s ease-in-out;
                        white-space: nowrap;
                        padding:3px 5px;
                        font-size:13px;
                    }
                    .text-input-editor:hover > span {
                        display:block;
                        color:#000;
                        left: 30px;
                        top: 1px;
                        background:white;
                        border-radius:3px;
                        box-shadow: 0 0 3px rgba(0,0,0,.4);
                        opacity: 1;
                    }
                </v-style>
                <div v-if="cardView ===\'public\'" class="app-component app-main-component">
                    <div class="wrapper" v-bind:style="cardWidthPadding">
                        <div class="mainImage">
                            <div class="mainImageHandler" v-bind:style="renderBackgroundMedia(\'header-banner\')">
                                <span v-if="editor === true" class="fas fa-edit text-input-editor text-input-editor-inner-top-left" v-on:click="editImage(\'header-banner\')"><span>Edit Header Background</span></span>
                            </div>
                        </div>
                        <div class="mainButtons">
                            <ul>
                                <li v-for="currConnection in cardConnections"v-bind:style="cardPrimaryColor">
                                    <span v-if="editor === true" class="fas fa-edit text-input-editor text-input-editor-connection" v-on:click="editConnectionRel(currConnection)"><span>Edit Connection</span></span>
                                    <div v-on:click="processConnectionRequest(currConnection)">
                                        <i v-bind:class="displayFontAwesome(currConnection)"></i>
                                    </div>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="mainPages tabs app-main-comp-body">
                            <li v-if="editor === true">
                                <span v-if="editor === true" class="fas fa-edit text-input-editor text-input-editor-navigation" v-on:click="editPages()"><span>Edit Pages</span></span>
                            </li>
                            <ul>
                                <li v-for="currPage in cardPages" v-if="currPage.rel_visibility == true">
                                    <div class="tabTitle" v-on:click="event.preventDefault(); openSitePageLive(currPage)" v-bind:style="[cardPrimaryColor, pageTitlePadding]" v-bind:id="\'tab\' + currPage.card_tab_rel_id">
                                        <span class="tabTitleText"><span v-if="editor === true" class="fas fa-edit text-input-editor text-input-editor-left" v-on:click="editPage(currPage)"></span><span v-on:click="event.preventDefault(); openSitePageInEditor(currPage)">{{ renderMenuTitle(currPage) }}</span></span>
                                    </div>
                                    <div class="tabContent" v-bind:id="\'content_\' + currPage.card_tab_rel_id">
                                    </div>
                                </li>
                            </ul>
                            <div id="dynamicPageHolder" style="display:none;">
                                <div id="dynamicPageComponent">
                                ' . $this->registerAndRenderDynamicComponent(
                                    $this->cardPage,
                                    "view",
                                    [
                                        new VueProps("activeEntity", "object", "entity"),
                                        new VueProps("noTitle", "boolean", "true")
                                    ]
                                ) . '
                                </div>
                            </div>
                        </div>
                        <div class="footer" style="display:none;">
                            <div class="footerLeft" style="font-size: 11px; padding-top: 3px;">
                                Powered by <span style="font-size: 18px;">EZ Digital</span>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="app-modules-float app-modal-float" v-bind:class="{active: modulesOpen === true}">
                        <div class="app-modal">
                            <div class="app-modal-box">
                                <span class="app-modal-hide" v-on:click="closeModules"></span>
                                <div class="app-modal-title">Modules</div>
                                <div class="app-modal-body" style="margin-top:10px;">
                                    <ul class="modules-wrapper">
                                        <li v-for="currModule, index in cardModules" class="modules-item">
                                            <div class="modules-item-box pointer" v-on:click="toggleRealEstateModule()"><span v-bind:style="{color: mainCardColor}"><i v-bind:class="renderModuleCssClass(currModule.module_label)"></i>{{ currModule.module_label }}</span></div>
                                            <div id="realEstateModule_box" style="display:none;">
                                                <ul class="modules-apps-wrapper">
                                                    <li v-for="currTool, index in currModule.module_tools" v-on:click="enableModuleTool(currTool.id)" class="pointer modules-wrapper-app"><span v-bind:style="{\'background-image\': \'url(\' + currTool.logo + \')\'}"></span></li>
                                                </ul>
                                                <div class="getAdditionalTools"><i class="fas fa-plus-circle"></i> Get Addition Tools</div>
                                            </div>
                                        </li>
                                        <li class="modules-item pointer">
                                            <div class="getAdditionalTools"><span><i class="fas fa-plus-circle"></i> Get Additional Modules</span></div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="app-sharesave-float app-modal-float" v-bind:class="{active: shareSaveOpen === true}">
                        <div class="app-modal">
                            <div class="app-modal-box">
                                <span class="app-modal-hide" v-on:click="closeShareSave"></span>
                                <div class="app-modal-title">Share & Save</div>
                                <div class="app-modal-qr">
                                    <img v-bind:src="\'/api/v1/cards/generate-qr-code-for-card?id=\' + entity.card_num" alt="qr code" width="450" height="450" style="border:none;width:100%;height:auto;" />
                                </div>
                                <div class="app-modal-body">
                                    <h3 class="app-modal-subtitle">
                                        Share This Card <i v-on:click="learnMoreAboutShareSave()" class="pointer learnMoreAboutShareSave fa fa-question-circle"></i>
                                    </h3>
                                    <div id="learnMoreAboutShareSave_box" style="display:none;">
                                        <div style="margin-bottom:25px;padding: 25px;">
                                            <p>Ready to connect with others?</p>
                                            <ol>
                                                <li><b style="font-weight:bold;">Infini-Track:</b> Lets card owner stay in touch with everyone the card is shared with, permission-based.</li>
                                                <li><b style="font-weight:bold;">Direct Text:</b> Sends to someone in your phone\'s Contact Manager, or directly by phone number.</li>
                                                <li><b style="font-weight:bold;">Email:</b> Sends by email, with a customizable message.</li>
                                                <li><b style="font-weight:bold;">QR Code:</b> Scan QR Code and display card.</li>
                                            </ol>
                                        </div>
                                    </div>
                                    <ul class="app-modal-horz-list">
                                        <li v-on:click="sendShare()">Share</li>
                                        <li v-on:click="sendSms()">Text</li>
                                        <li v-on:click="emailCard()">Email</li>
                                    </ul>
                                    <div style="display:none;" class="emailCardDesktop" id="emailCard_box">
                                        <div style="margin:25px 25px 0px;">
                                            <p>Enter an email address and optional message below and click "Send"</p>
                                            <form name="emailCard" method="post" action="/cards/card-data/email-card-to-address">
                                                <input class="form-control" name="email" type="email" id="email" value=""><br>
                                                <textarea class="form-control" name="msg" id="msg" cols="32" rows="2" maxlength="64" placeholder="Optional message."></textarea><br>
                                                <button class="btn btn-primary" style="width:100%;">Send</button>
                                            </form>
                                        </div>
                                    </div>

                                    <div style="display:none;" id="emailCardSuccess_box">
                                        <div style="margin:25px 25px 0px;">
                                            <h5 style="text-align: center;">SUCCESS!</h5>
                                            <p>We\'ve emailed <span id="email"></span><br>a link to this card!</p>
                                        </div>
                                    </div>
                                    <div class="app-modal-emphasisc">Text {{ entity.card_keyword }} to 64600</div>
                                    <div class="app-modal-emphasisc-sub">To Share with a Group or on Social Media.</div>
                                    <h3 class="app-modal-subtitle">
                                        Save To Your Phone <i class="learnMoreAboutShareSave fa fa-question-circle"></i>
                                    </h3>
                                    <ul class="app-modal-horz-list">
                                        <li v-on:click="loadCardIntoContacts()">Contacts</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="app-login-float app-modal-float" v-bind:class="{active: membersAccessOpen === true}">
                        <div class="app-modal">
                            <div class="app-modal-box">
                                <span class="app-modal-hide" v-on:click="closeMembersAccess"></span>
                                <div class="app-modal-title">Member Access</div>
                                <div class="app-modal-body">
                                    '.'
                                    <div class="login-field-table">
                                        <div class="login-field-row">
                                            <div class="editor-label">
                                                <label for="Username">Username</label>
                                            </div>
                                            <div class="editor-field">
                                                <input name="username" type="text" v-model="loginUsername" class="form-control">
                                                <span class="field-validation-valid" data-valmsg-for="Username" data-valmsg-replace="true"></span>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="login-field-row">
                                            <div class="editor-label">
                                                <label for="Password">Password</label>
                                            </div>
                                            <div class="editor-field">
                                                <input name="password" type="password" v-model="loginPassword" class="form-control">
                                                <span class="field-validation-valid" data-valmsg-for="Password" data-valmsg-replace="true"></span>
                                            </div>
                                        </div>
                                        <div class="loginwidget-field-row">
                                            <div class="editor-label">
                                            </div>
                                            <div class="editor-field">
                                                <a class="small-capitalized-text reset-password-dialog pointer">Forgot Your Password?</a>
                                            </div>
                                        </div>
                                        <div class="clear editor-label login-button-box">
                                            <button type="button" v-on:click="attemptMemberLogin" class="btn btn-primary pointer width100">Log In</button>
                                        </div>
                                    </div>'.'
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="app-login-float app-modal-float" v-bind:class="{active: loginOpen === true}">
                        <div class="app-modal">
                            <div class="app-modal-box">
                                <span class="app-modal-hide" v-on:click="closeLogin"></span>
                                <div class="app-modal-title">Login</div>
                                <div class="app-modal-body">
                                    '.'
                                    <div class="login-field-table">
                                        <div class="login-field-row">
                                            <div class="editor-label">
                                                <label for="Username">Username</label>
                                            </div>
                                            <div class="editor-field">
                                                <input name="username"  type="text" v-model="loginUsername" class="form-control">
                                                <span class="field-validation-valid" data-valmsg-for="Username" data-valmsg-replace="true"></span>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="login-field-row">
                                            <div class="editor-label">
                                                <label for="Password">Password</label>
                                            </div>
                                            <div class="editor-field">
                                                <input name="password"  type="password" v-model="loginPassword" class="form-control">
                                                <span class="field-validation-valid" data-valmsg-for="Password" data-valmsg-replace="true"></span>
                                            </div>
                                        </div>
                                        <div v-if="loggedInAttemptError !== \'\'" class="login-field-row">
                                            <div class="editor-label">
                                                <label for="Password"></label>
                                            </div>
                                            <div class="editor-field">
                                                <span class="field-validation-valid">{{ loggedInAttemptError }}</span>
                                            </div>
                                        </div>
                                        <div class="login-field-row">
                                            <div class="editor-label">
                                            </div>
                                            <div class="editor-field">
                                                <a class="small-capitalized-text reset-password-dialog pointer">Forgot Your Password?</a>
                                            </div>
                                        </div>
                                        <div class="clear editor-label login-button-box">
                                            <button type="button" v-on:click="attemptLogin" class="btn btn-primary pointer width100">Log In</button>
                                        </div>
                                    </div>'.'
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="cardView ===\'private\'" class="app-component app-main-component">
                    <header class="app-main-comp-portal-header">
                        <span class="portal-header-salutation">Welcome,</span>
                        <span class="portal-header-name">{{ loggedInUser.first_name }}</span>
                    </header>
                    <div class="app-main-comp-body">
                        <nav class="app-main-comp-nav">
                            <ul>
                                <li v-for="currConnection in cardConnections" v-on:click="processConnectionRequest(currConnection)">
                                    <span class="app-main-comp-nav-item">
                                        <span v-bind:class="currConnection.font_awesome"></span>
                                    </span>
                                </li>
                                <li v-on:click="openSocialMedia">
                                    <span class="app-main-comp-nav-item">
                                        <span class="social-media-icon"></span>
                                    </span>
                                </li>
                            </ul>
                        </nav>
                        <article class="app-main-comp-pages">
                            <ul>
                                <li v-for="currPage in cardPages" v-on:click="openCardPage(currPage)">
                                    <span v-bind:class="{\'ajax-loading-anim-inner\': currPage.loadingPage === true } " class="app-main-comp-page-item">{{ currPage.title}}</span>
                                </li>
                            </ul>
                        </article>
                    </div>
                    <footer class="app-main-comp-portal-footer">
                        Here is the footer....
                    </footer>
                </div>
                <div v-if="cardView ===\'members\'" class="app-component app-main-component">
                    <header class="app-main-comp-portal-header">
                        <span class="portal-header-salutation">Welcome,</span>
                        <span class="portal-header-name">{{ loggedInUser.first_name }}</span>
                    </header>
                    <div class="app-main-comp-body">
                        <nav class="app-main-comp-nav">
                            <ul>
                                <li v-for="currConnection in cardConnections" v-on:click="processConnectionRequest(currConnection)">
                                    <span class="app-main-comp-nav-item">
                                        <span v-bind:class="currConnection.font_awesome"></span>
                                    </span>
                                </li>
                                <li v-on:click="openSocialMedia">
                                    <span class="app-main-comp-nav-item">
                                        <span class="social-media-icon"></span>
                                    </span>
                                </li>
                            </ul>
                        </nav>
                        <article class="app-main-comp-pages">
                            <ul>
                                <li v-for="currPage in cardPages" v-on:click="openCardPage(currPage)">
                                    <span v-bind:class="{\'ajax-loading-anim-inner\': currPage.loadingPage === true } " class="app-main-comp-page-item">{{ currPage.title}}</span>
                                </li>
                            </ul>
                        </article>
                    </div>
                    <footer class="app-main-comp-portal-footer" >
                        Here is the footer....
                    </footer>
                </div>
            </div>
                ';
    }
}