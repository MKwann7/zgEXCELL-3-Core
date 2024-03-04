<?php

namespace Entities\Cards\Components\Vue\DigitalCardWidget\V4;

use App\Core\AppModel;
use App\Website\Vue\Classes\VueProps;
use Entities\Cards\Components\Vue\CardWidget\SwapCardConnectionWidget;
use Entities\Cards\Components\Vue\DigitalCardWidget\Assets\AbstractDigitalSiteComponent;
use Entities\Cards\Components\Vue\DigitalCardWidget\Assets\SharedVueSiteMethods;
use Entities\Media\Components\Vue\Gallerywidget\ListLogoGalleryWidget;

class DigitalCardMainWidget extends AbstractDigitalSiteComponent
{
    protected string $id = "3293b847-8cbc-4372-8cb8-792412fb86d6";
    protected string $cssPrefix = ".app-template-4";

    public function __construct(?AppModel $entity = null)
    {
        $this->cardPage = new DigitalCardPageWidget();
        parent::__construct($entity);
    }

    protected function renderComponentDataAssignments(): string
    {
        return SharedVueSiteMethods::dataAssignments($this) . "
            socialMediaOpen: false,
            handedType: 'left',
            learnMoreAboutShareSave_box: false,
            realEstateModule_box: false,
            hideSplashTitle: false,
            emailCard_box: false,
            mainCardColor: '#ff0000',
            siteWidth: 1224,
            siteMobileWidth: 640,
            siteMobileBannerDisplay: 'flex',
            customShowcaseWidth: 850,
            customShowcaseHeight: 400,
            customShowcaseBannerHeight: 400,
            customShowcaseSidebarHeight: 400,
            sideBarShowcaseWidth: 374,
            showHomePageBanner: true,
            showDefaultPageBanner: false,
        ";
    }

    protected function renderComponentComputedValues(): string
    {
        return SharedVueSiteMethods::computed($this) . '
            cardConnections: function()
            {
                if (typeof this.entity !== "undefined") { return this.entity.Connections; }
                return null;
            },
            cardSocialMedia: function()
            {
                if (typeof this.entity !== "undefined") { return this.entity.SocialMedia; }
                return null;
            },
            cardModules: function()
            {
                return this.cardModulesByClass;
            },
            ';
    }

    protected function renderComponentMethods(): string
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
            sendSms: function()
            {
                const unqiueUrl = (this.entity.card_vanity_url !== "" ? this.entity.card_vanity_url : this.entity.card_num);
                window.location = "sms:?&body=' . $app->objCustomPlatform->getFullPublicDomainName() . '/" + unqiueUrl + "%20Click%20the%20link%20to%20connect%20with%20" + this.entity.card_owner_name + "!";
            },
            sendShare: function()
            {
                window.location = "https://optin.mobiniti.com/" + this.entity.card_num;
            },
            openModules: function()
            {
                this.modulesOpen = true;
                this.menuOpen = false;
            },
            openShareSave: function()
            {
                this.shareSaveOpen = true;
                this.menuOpen = false;
            },
            closeShareSave: function()
            {
                this.shareSaveOpen = false;
            },
            attemptMemberLogin: function()
            {
                if(!this.validateUsername(this.loginUsername)) return;
                if(!this.validatePassword(this.loginPassword)) return;
                
                let self = this;
                this.loginCardUser(
                    "' . $app->objCustomPlatform->getFullPortalDomainName() . '/api/v1/users/validate-existing-user-credentials",
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
                        
                        self.loggedInUser = result.response.data.user;
                        self.loggedIn = true;
                        Cookie.erase("auth")
                        Cookie.set("auth", true)
                        console.log(Cookie.get("auth"))
                        self.cardView = "members";
                        sessionStorage.setItem(\'active_editor_siteView_\' + self.entity.card_id, "members");
                    }, 
                    function(error) 
                    {
                        console.log(error);
                    });
            },
            openSocialMedia: function()
            {
                this.socialMediaOpen = !this.socialMediaOpen;
            },
            setAuth: function()
            {
                this.isLoggedIn = this.$parent.$parent.isLoggedIn;
                this.authUserId = this.$parent.$parent.authUserId;
            },
            displayFontAwesome: function(connection)
            {
                if (connection.connection_type_id === 1 && connection.action === "sms")
                {
                    return "fas fa-sms";
                }
                
                return connection.font_awesome;
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
            gotoSocialLink: function(social)
            {
                const url = social.connection_value;
                window.open(url, "_blank");
            },
            addSocialMedia: function(social)
            {
                let self = this;
                let socialMedia = this.entity.SocialMedia;
                let connectionList = [];
                let swapType = "socialmedia";
                let ownerId = this.entity.owner_id;
                
                ' . $this->activateDynamicComponentByIdInModal(SwapCardConnectionWidget::getStaticId(), "", "add", "this.entity", "socialMedia", ["ownerId" => "ownerId", "connectionList" => "connectionList", "swapType" => "swapType", "functionType" => "'save new'"], "this", true, "function(component) {
                    let modal = self.findModal(self);
                    modal.vc.setTitle('Add Social Media Link');
                }") . '
            },
            enableModuleTool: function(id) {
                
            },
            renderSiteBanner: function(entity) {
                if (typeof entity.banner === "undefined" || entity.banner === "") return "https://www.micahzak.com/images/header-static-back.jpg";
                return entity.banner;
            },
            editShowcase: function(page) {
                let cardPages = []
                ' . $this->activateDynamicComponentByIdInModal(ListLogoGalleryWidget::getStaticId(), "", "edit", "this.entity", "cardPages", ["type" => "'logos'"], "this", true) . '
                return;
            },
            renderCustomSliderWidth: function() {
                if (this.webDomEl === null) return "850px"
                const actualWidth = this.webDomEl.offsetWidth;
                const fullWidth = (actualWidth > this.siteWidth ? this.siteWidth : actualWidth)
                const ratio = 850/this.siteWidth;
                const newWidth = fullWidth * ratio

                if (actualWidth > this.siteMobileWidth) {
                    this.customShowcaseWidth = newWidth
                    this.siteMobileBannerDisplay = "flex"
                } else {
                    this.customShowcaseWidth = actualWidth
                    this.siteMobileBannerDisplay = "block"
                }
            },
            renderCustomSliderHeight: function() {
                if (this.webDomEl === null) return "400px"
                const actualWidth = this.webDomEl.offsetWidth;
                const fullWidth = (actualWidth > this.siteWidth ? this.siteWidth : actualWidth)
                const ratio = 400/this.siteWidth;
                const newWidth = fullWidth * ratio
                const fullNewWidth = fullWidth * (400/850);
                const fullNewBannerWidth = fullWidth * (400/850);
                const fullNewSidebarWidth = fullWidth * (400/374);
                
                if (actualWidth > this.siteMobileWidth) {
                    this.customShowcaseHeight = newWidth
                    this.customShowcaseBannerHeight = newWidth
                    this.customShowcaseSidebarHeight = newWidth
                } else {
                    this.customShowcaseHeight = fullNewBannerWidth + fullNewSidebarWidth
                    this.customShowcaseBannerHeight = fullNewBannerWidth
                    this.customShowcaseSidebarHeight = fullNewSidebarWidth
                }
            },
            renderSidebarWidth: function() {
                if (this.webDomEl === null) return "374px"
                const actualWidth = this.webDomEl.offsetWidth;
                const fullWidth = (actualWidth > this.siteWidth ? this.siteWidth : actualWidth)
                const ratio = 374/this.siteWidth;
                const newWidth = fullWidth * ratio
                if (actualWidth > this.siteMobileWidth) {
                    this.sideBarShowcaseWidth = newWidth
                } else {
                    this.sideBarShowcaseWidth = actualWidth
                }
            },
            prePageAssignment: function(page, options, callback) {
                if (options.reload || this.siteIsLoadedInEditor()) {
                    if (typeof callback === "function") callback(page);
                    return;
                }
                if (options.firstLoad === true) {
                    if (page.rel_sort_order == 1) { 
                        this.showHomePageBanner = true
                        this.showDefaultPageBanner = false
                    } else {
                        this.showDefaultPageBanner = true
                        this.showHomePageBanner = false
                    }
                    if (typeof callback === "function") callback(page);
                    return;
                }
                if (page.rel_sort_order == 1) {
                    if (this.activePage.rel_sort_order !== page.rel_sort_order) {
                        this.easeElement("app-main-comp-page-title", "close", 0, 500, function() {
                            this.showDefaultPageBanner = false
                            this.showHomePageBanner = false
                            if (typeof callback === "function") callback(page);
                        });
                        return;
                    }
                    if (typeof callback === "function") callback(page);
                } else {
                    if (this.activePage.rel_sort_order === 1) {
                        this.easeElement("app-main-comp-page-title", "close", 0, 500, function() {
                            this.showDefaultPageBanner = false
                            this.showHomePageBanner = false
                            if (typeof callback === "function") callback(page);
                        });
                        return;
                    }
                    if (typeof callback === "function") callback(page);
                }
            },
            postPageAssignment: function(page, options, callback) {
                if (options.reload || this.siteIsLoadedInEditor()) {
                    if (page.rel_sort_order == 1) { 
                        this.showHomePageBanner = true
                        this.showDefaultPageBanner = false
                    } else {
                        this.showDefaultPageBanner = true
                        this.showHomePageBanner = false
                    }
                    if (typeof callback === "function") callback(page);
                    return;
                }
                if (page.rel_sort_order == 1) {
                    this.showHomePageBanner = true
                    this.showDefaultPageBanner = false
                    this.easeElement("app-main-comp-page-title", "open", this.customShowcaseHeight, 500, function() {
                        if (typeof callback === "function") callback(page);
                    });
                } else {
                    this.showHomePageBanner = false
                    this.showDefaultPageBanner = true
                    this.easeElement("app-main-comp-page-title", "open", 225, 500, function() {
                        if (typeof callback === "function") callback(page);
                    });
                }
                if (typeof callback === "function") callback(page);
            },
            easeElement: function(className, direction, amount, speed, callback) {
                const self = this;
                self.classList(className, function(elm) { elm.style.overflowY = "hidden"; });
                const newHeight = (!amount.toString().includes("%") ? ((direction === "open" ? amount : 0) + "px") : amount);
                anime({
                    targets: "." +className,
                    height: newHeight,
                    duration: speed,
                    easing: "easeInOutSine",
                });
                setTimeout(function() {
                    self.classList(className, function(elm) { elm.style.overflowY = "visible"; });
                    if (typeof callback === "function") callback();
                }, speed);
            },
            ';
    }

    protected function getMobileCss(): array
    {
        return [
            '.app-main-comp-header' => 'height: 100%',
            '.app-main-comp-header .app-main-comp-header-inner,
             .app-main-comp-header .app-main-comp-footer-inner,
             .app-main-comp-header .app-main-comp-header-flex,
             .app-main-comp-header .app-main-comp-footer-flex' => 'height: 100%',
            '.app-main-comp-page-title' => 'height:150px;',
            '.app-main-comp-nav .nav-buttons' => 'display:none;',
            '.app-kabob-float:not(.active) .app-kabob' => 'display:block;',
            '.toggle_menu' => 'display:flex;'
        ];
    }

    protected function getTabletCss(): array
    {
        return [
            '.app-main-comp-header' => 'height: 100%',
            '.app-main-comp-header .app-main-comp-header-inner,
             .app-main-comp-header .app-main-comp-footer-inner,
             .app-main-comp-header .app-main-comp-header-flex,
            .app-main-comp-header .app-main-comp-footer-flex' => 'height: 100%',
            '.app-main-comp-page-title' => 'height:200px;',
            '.app-main-comp-nav .nav-buttons' => 'display:none;',
            '.app-kabob-float:not(.active) .app-kabob' => 'display:block;',
            '.toggle_menu' => 'display:flex;'
        ];
    }

    protected function renderTemplate(): string
    {
        return '                                                                                                                                                                                                                                                                                                
            <div id="excell-site-app-4" v-if="entityFound == true" class="app-section app-template-4">
                <v-style type="text/css">
                    .login-field-table {
                        display:flex;
                        flex-direction:column;
                    }
                    .login-field-table .login-field-row {
                        display:flex;
                        width: 100%;
                        height: 50px;
                    }
                    .login-field-table .login-field-row .editor-label {
                        width: 115px;
                    }
                    .login-field-table .login-field-row .editor-field {
                        width: calc(100% - 35px);
                    }
                    .login-field-table {
                        padding: 15px 25px 0;
                    }
                    .login-field-table .width100 {
                        width: 100%;
                    }
                    .modules-wrapper-app span {
                        background-repeat: no-repeat;
                        background-position: center center;
                        background-size: contain;
                    }
                    .field-validation-valid {
                        color:#ff0000;
                    }
                    .getAdditionalTools {
                        text-align: center;
                        margin-bottom: 23px;
                    }
                    .app-kabob-footer > div {
                        color: #aaa;
                        font-size: 12px;
                        padding-bottom: 5px;
                    }
                    .switchHandsWrapper {
                        margin: 0 30px 12px 40px;
                        width: calc(100% - 75px) !important;
                    }
                    .switchHands {
                        background: url(/_ez/templates/2/images/switch-hands.png) no-repeat center center / contain;
                        width:17px;
                        height:17px;
                        display: inline-block;
                    }
                    .app-template-4 {
                        position:relative;
                    }
                    #pageTitleMirror {
                        position: absolute;
                        top: 0;
                        left: 0;
                        visibility: hidden;
                        height: 0;
                        white-space: pre;
                        box-sizing: content-box;
                        font-family: Saira;
                        font-size: 27px;
                        padding: 4px 0;
                        text-transform: uppercase;
                    }
                    .customSlider-inner {
                        width:inherit;
                        height:inherit;
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
                        top: 9px;
                        left:-15px;
                    }
                    .text-input-editor-inner-top-left {
                        top: 10px;
                        left:10px;
                    }
                    .text-input-editor-inner-top-right {
                        top: 10px;
                        right:10px;
                    }
                    .text-input-editor-navigation {
                        top: 10px;
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
                    .nav-search * {
                        color:white;
                    }
                    .app-page-editor-input-large {
                        text-align: center; 
                        box-sizing: content-box;
                    }
                    '.$this->cssPrefix.' .app-main-comp-nav ul li {
                        white-space: nowrap;
                    }
                    '.$this->cssPrefix.' .footer-widgets .footer-widget-inner {
                        display:block;
                        position:relative;
                    }
                    '.$this->cssPrefix.' li.footer-widgets {
                        width:33.33%
                    }
                    '.$this->cssPrefix.' .footer-widgets .footer-title,
                    '.$this->cssPrefix.' .footer-widgets .footer-title a,
                    '.$this->cssPrefix.' .footer-widgets .about-page-summary a,
                    '.$this->cssPrefix.' .footer-widgets .footer-service-title a,
                    '.$this->cssPrefix.' .footer-widgets .footer-services-excerpt,
                    '.$this->cssPrefix.' .footer-widgets .legal-disclaimer-links a {
                        color: #fff !important;
                    }
                    '.$this->cssPrefix.' .footer-widgets .about-page-summary a {
                        font-size: 14px;
                    }
                    '.$this->cssPrefix.' .app-main-comp-footer-inner {
                        padding: 15px 0;
                    }
                    '.$this->cssPrefix.' .footer-widgets .footer-about-page-thmb {
                        margin: 5px 20px 13px 0;
                        padding: 10px;
                        border: 1px solid #E2E3E4;
                        border-radius: 6px;
                        box-shadow: 0 0 2px rgb(0 0 0 / 20%);
                        background-color: #ffffff;
                    }
                    '.$this->cssPrefix.' .footer-widgets h4.footer-title a {
                        font-size: 20px;
                        color: #000000;
                        margin-bottom: 5px;
                        padding-bottom: 4px;
                        padding-top: 5px;
                        font-weight: lighter;
                        text-decoration: none;
                    }
                    '.$this->cssPrefix.' .hideDefaultBanner,
                    '.$this->cssPrefix.' .hideHomeBanner {
                        display:none !important;
                    }
                    '.$this->cssPrefix.' .app-kabob-float:not(.active),
                    '.$this->cssPrefix.' .app-kabob-float:not(.active) {
                        opacity:0 !important;
                    }
                    '.$this->cssPrefix.' .app-kabob-float.active {
                        left: 0;
                        opacity:1;
                    }
                    '.$this->cssPrefix.' .app-kabob-float {
                        position: absolute;
                        left: -300px;
                        top: 0px;
                        bottom: 0;
                        width: 300px;
                        background: linear-gradient(to bottom,  #4c4c4c 0%,#131313 100%);
                        border: 0;
                        z-index: 1001;
                        box-shadow:0 0 5px rgba(0,0,0,.5)
                        opacity:0;
                    }
                    '.$this->cssPrefix.' .nav-buttons > li.mobile-nav-buttons {
                        padding: 10px 15px;
                        background: linear-gradient(to bottom, #404040 0%,#5a5a5a 100%);
                        border-top: 1px solid #555;
                        border-bottom: 1px solid #333;
                    }
                    '.$this->cssPrefix.' .toggle_menu {
                        cursor: pointer;
                        display: none;
                        height: 55px;
                        visibility: visible;
                        width: 60px;
                        z-index: 1001;
                        position: relative;
                    }
                    '.$this->cssPrefix.' .app-kabob,
                    '.$this->cssPrefix.' .toggle_menu {
                        display:none;
                    }
                    '.$this->cssPrefix.' .app-component-public {
                        transform: rotate3d(0, 1, 0, 90deg);
                        perspective: 5.5cm;
                    }
                    '.$this->cssPrefix.' .app-component-members {
                        transform: rotate3d(0, 1, 0, 90deg);
                        perspective: 5.5cm;
                    }
                    '.$this->cssPrefix.' .app-component-members.inAccount {
                        transform: rotate3d(0, 1, 0, 0deg);
                        perspective: 5.5cm;
                    }
                    '.$this->cssPrefix.' .app-component-public.onPublicSite {
                        transform: rotate3d(0, 1, 0, 0deg);
                    }
                    '.$this->cssPrefix.' .app-main-component {
                        transition: transform .5s ease-in-out;
                        perspective: 5.5cm;
                    }
                    '.$this->cssPrefix.' .ui-btn-up-b {
                        border: 1px solid #044062;
                        background: #396b9e;
                        font-weight: 700;
                        color: #fff;
                        text-shadow: 0 1px 0 #194b7e;
                        background-image: linear-gradient(#5f9cc5,#396b9e);
                    }
                    '.$this->cssPrefix.' .log-out {
                        color: #ffffff !important;
                        font-weight: normal;
                        text-shadow: none;
                        font-size: 13px !important;
                        border-radius: 10px 10px 10px 10px;
                        padding: 2px 10px;
                    }
                    '.$this->cssPrefix.' .pop-up-dialog-main-title-text {
                        font-family: inherit;
                        font-size: 1.75rem;
                        display: inline-block;
                        margin-top: 5px;
                    }
                    '.$this->cssPrefix.' .zgpopup-dialog-header {
                        border-radius: 0px 0px 0 0;
                        margin-bottom: -7px;
                        margin-top: -5px;
                        padding: 15px 20px 7px;
                    }
                    .theme_shade_light '.$this->cssPrefix.' .nav-buttons > li.mobile-nav-buttons > span {
                        color:white;
                    }
                    '.$this->renderMobileCss().'
                </v-style>
                <div v-show="cardView === \'public\'" class="app-component app-main-component app-component-public" v-bind:class="{onPublicSite: siteIsPublic()}">
                    <header class="app-main-comp-header" v-bind:style="renderBackgroundMedia(\'header-banner\')">
                        <span v-if="editor === true" class="fas fa-edit text-input-editor text-input-editor-inner-top-left" v-on:click="editImage(\'header-banner\')"><span>Edit Header Background</span></span>
                        <div class="app-main-comp-header-inner" v-bind:style="{ maxWidth: siteWidth + \'px\'}">
                            <div class="app-main-comp-header-flex">
                                <div class="mainImageLeftHeader position-relative" v-bind:style="renderMicroSiteLogoCss(\'main-logo\')" >
                                    <span v-if="editor === true" class="fas fa-edit text-input-editor text-input-editor-inner-top-left" v-on:click="editLogo(\'main-logo\')" style="top: calc(50% - 15px);left: auto;right: 15px;z-index: 100"><span>Edit Site Logo</span></span>
                                    <img class="mainSiteLogo hover-effect-small pointer" v-on:click="openSitePageByRel(1)" v-bind:src="renderMicroSiteLogo(\'main-logo\')" v-bind:width="renderMicroSiteLogoStyle(\'main-logo\', \'width\')" v-bind:height="renderMicroSiteLogoStyle(\'main-logo\', \'height\')"  title="The Maxr App" alt="The Maxr App">
                                    <div style="display:none;" class="mainImageHandler" v-bind:style="{background: \'url(\' + entity.banner + \') no-repeat center center / auto 100%\'}"></div>
                                </div>
                                <div class="floatRightHeader">
                                </div>
                            </div>
                        </div>
                    </header>
                    <nav class="app-main-comp-nav" v-bind:style="renderBackgroundMedia(\'navigation-background\', {background: \'#aaa\'})">
                        <span v-if="editor === true" class="fas fa-edit text-input-editor text-input-editor-inner-top-left" v-on:click="editImage(\'navigation-background\')"><span>Edit Navigation Background</span></span>
                        <div class="app-main-comp-nav-inner position-relative" v-bind:style="{ maxWidth: siteWidth + \'px\'}">
                            <span class="toggle_menu" v-on:click="openCardMenu"><span></span></span>
                            <ul class="nav-buttons">
                                <li v-for="currPage in cardPages" v-on:click="event.preventDefault(); openSitePage(currPage)" v-if="currPage.rel_visibility == true">
                                    <a class="app-main-comp-page-item" v-bind:href="currPage.url">{{ renderMenuTitle(currPage) }}</a>
                                </li>
                                <li v-if="editor === true">
                                    <span v-if="editor === true" class="fas fa-edit text-input-editor text-input-editor-navigation" v-on:click="editPages()" style="top: 10px;"><span>Edit Pages</span></span>
                                </li>
                            </ul>
                            <ul class="nav-search">
                                <li v-on:click="toggleSearch">Search</li>
                                <li v-if="isLoggedIn && isLoggedIn !== \'active\'" v-on:click="toggleLogin"class="my-account-login">Login</li>
                                <li v-if="isLoggedIn && isLoggedIn === \'active\'" v-on:click="goToAccount" class="my-account-portal">My Account</li>
                                <li style="display:none" v-on:click="toggleCart">Purchase</li>
                            </ul>
                        </div>
                    </nav>
                    <section v-if="activePage" class="app-main-comp-page-title" v-bind:class="{hideDefaultBanner: !showDefaultPageBanner}" v-bind:style="renderBackgroundMedia(\'bread-crumb-banner-other\')">
                        <span v-if="editor === true" class="fas fa-edit text-input-editor text-input-editor-inner-top-left" v-on:click="editImage(\'bread-crumb-banner-other\')"><span>Edit Non-Home Breadcrumb Background</span></span>
                        <div class="app-main-comp-showcase-inner container" v-bind:style="{ maxWidth: siteWidth + \'px\'}">
                            <div class="row">
                                <div class="text-center" style="position: relative;">
                                    <h2 v-if="editor === false" class="app-page-title app-page-editor-text-transparent" style="text-align: center;">{{ activeTitle }}</h2>
                                    <h2 v-if="editor === true"><div id="pageTitleMirror"></div><span v-if="editor === true" class="fas fa-edit text-input-editor text-input-editor-left" v-on:click="editPage(activePage)"></span><input id="app-page-editor-title-transparent" placeholder="Title" v-model="activeTitle" v-on:change="updatePageMenuTitle" v-on:keyup="updatePageMenuTitle" v-on:keydown="resizeMe" v-on:blur="updatePageTitle" style="width:114px" class="app-page-title app-page-editor-text-transparent app-page-editor-input-large" /></h2>
                                    <div class="breadcrumb-wrapper">
                                        <ol class="breadcrumb" style="text-align: center;">
                                            <li class="first-item">
                                                <a>Home</a>
                                            </li>
                                            <li class="last-item">{{ renderMenuTitle(activePage) }}</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section v-if="activePage" class="app-main-comp-page-title" v-bind:class="{hideHomeBanner: !showHomePageBanner}" v-bind:style="renderBackgroundMedia(\'bread-crumb-banner\', {height: customShowcaseHeight + \'px\'})">
                        <span v-if="editor === true" class="fas fa-edit text-input-editor text-input-editor-inner-top-left" v-on:click="editImage(\'bread-crumb-banner\')"><span>Edit Home Breadcrumb Background</span></span>
                        <div class="container app-main-comp-showcase-inner" v-bind:style="{display: siteMobileBannerDisplay, maxWidth: siteWidth + \'px\'}" style="padding-left:0;padding-right:0;">
                            <div class="customSlider" v-bind:style="{width: customShowcaseWidth + \'px\', height: customShowcaseBannerHeight + \'px\'}" style="background: url(/website/images/background-banner-image.jpg) no-repeat center center;background-size:cover;">
                                <div class="customSlider-inner position-relative">
                                    <span v-if="editor === true" class="fas fa-edit text-input-editor text-input-editor-inner-center" v-on:click="editShowcase(\'main-page-showcase\')"><span>Edit Main Page Showcase</span></span>
                                </div>
                            </div>
                            <div class="banner-sidebar-outer" v-bind:style="{width: sideBarShowcaseWidth + \'px\', height: customShowcaseSidebarHeight + \'px\'}" style="background-color:white;">
                                <div class="banner-sidebar-inner" style="height:100%;">
                                    <div style="display:flex;align-items: center;justify-content: center;height:100%;">
                                        <div style="display:flex;">
                                            <img src="/website/images/maxr-app-logo-smaller.png" width="300" height="270" style="margin:0;"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <div class="app-main-comp-body">
                        <div class="app-main-comp-body-inner" v-bind:style="{ maxWidth: siteWidth + \'px\'}">
                            <article class="app-main-comp-pages">
                            ' . $this->registerAndRenderDynamicComponent(
                                $this->cardPage,
                                "view",
                                [
                                    new VueProps("activeEntity", "object", "entity"),
                                    new VueProps("noTitle", "boolean", "true")
                                ]
                            ) . '
                            </article>
                        </div>
                    </div>
                    <footer class="app-main-comp-footer" v-bind:style="renderBackgroundMedia(\'footer-banner\',{background: \'#aaa\'})">
                        <span v-if="editor === true" class="fas fa-edit text-input-editor text-input-editor-inner-top-left" v-on:click="editImage(\'footer-banner\')"><span>Edit Footer Background</span></span>
                        <div class="app-main-comp-footer-inner" v-bind:style="{ maxWidth: siteWidth + \'px\'}">
                            <ul class="app-main-comp-footer-menu">
                                <li class="footer-widgets footer-widget-1">
                                    <div class="footer-widget-inner">
                                        <span v-if="editor === true" class="fas fa-edit text-input-editor text-input-editor-inner-top-right" v-on:click="editFooterHighlightPage()"><span>Edit Highlight Page</span></span>
                                        <h3 class="footer-title">{{ renderHighlightTitle }}</h3>
                                        <div class="text-widget">
                                            <a id="text-widget-about-img" v-bind:href="renderHighlightPageUrl" @click="openHighlightPage"><img class="footer-about-page-thmb float-left" width="77" height="77" src="http://www.micahzak.com/XtDsXed/?src=website/uploads/page_thumbs/pages/page_2.jpg&amp;w=77&amp;h=77&amp;zc=1&amp;s=1&amp;q=95&amp;a=t" alt="About Micah Zak" title="About Micah Zak"></a>
                                            <h4 class="footer-title"><a v-bind:href="renderHighlightPageUrl" @click="openHighlightPage">{{ renderHighlightPageTitle }}</a></h4>
                                            <div class="about-page-summary">
                                                <a v-bind:href="renderHighlightPageUrl" @click="openHighlightPage">{{ renderHighlightPageDesc }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="footer-widgets footer-widget-2">
                                    <div class="footer-widget-inner">
                                        <span v-if="editor === true" class="fas fa-edit text-input-editor text-input-editor-inner-top-right" v-on:click="editFooterShowcasePage()"><span>Edit Showcase Page</span></span>
                                        <h3 class="footer-title">Showcase</h3>
                                        <div class="text-widget">
                                            <div v-for="currPage in cardPages" v-on:click="event.preventDefault(); openSitePage(currPage)" v-if="currPage.rel_visibility == true && currPage.rel_sort_order > 2" class="footer-service-title">
                                                <a v-bind:title="currPage.title" v-bind:href="currPage.url" v-on:click="event.preventDefault(); openSitePage(currPage)">{{ currPage.title }}</a>
                                                <br>
                                                <span class="footer-services-excerpt">{{ renderPageContentSnipIt(currPage) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="footer-widgets footer-widget-3">
                                    <div class="footer-widget-inner">
                                        <span v-if="editor === true" class="fas fa-edit text-input-editor text-input-editor-inner-top-right" v-on:click="editFooterShowcasePage()"><span>Edit Contact Information</span></span>
                                        <h3 class="footer-title">Contact Info</h3>
                                        <div class="legal-disclaimer-links">
                                            <a href="/disclaimer/">Disclaimer</a><br>
                                            <a href="/privacy-policy/">Privacy Policy</a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </footer>
                    <div class="app-kabob-float" v-bind:class="{active: menuOpen === true}">
                        <div class="app-kabob-header">
                            <span class="app-kabob-hide" v-on:click="closeCardMenu"></span>
                            <div class="text-center pt-1 pb-1"><img class="mobielNavLogo hover-effect-small" src="http://www.micahzak.com/website/uploads/zggraphics/micahzakcom-logo.svg" height="40"></div>
                            <ul class="nav-buttons">
                                <li v-for="currPage in cardPages" @click="event.preventDefault(); openSitePage(currPage)" class="mobile-nav-buttons">
                                    <a class="app-main-comp-page-item" v-bind:href="currPage.url">{{ currPage.title}}</a>
                                </li>
                            </ul>
                            <ul class="app-kabob-menu">
                                <li v-on:click="openMembersAccess"><span><span class="fas fa-users"></span>Members</span></li>
                                <li v-if="(entity.card_owner_uuid !== authUserId && entity.card_user_uuid !== authUserId)" v-on:click="openLogin"><span><span class="fas fa-sign-in-alt"></span>Login</span></li>
                                <li v-if="(entity.card_owner_uuid === authUserId || entity.card_user_uuid === authUserId)" v-on:click="logMemberOutModal"><span><span class="fas fa-sign-out-alt"></span>Log Out</span></li>
                            </ul>
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
                </div>
                <div v-if="cardView ===\'members\'" class="app-component app-main-component app-component-members" v-bind:class="{inAccount: siteIsPortal()}">
                    <header class="app-main-comp-portal-header" v-bind:style="renderBackgroundMedia(\'header-portal-banner\')">
                        <span v-if="editor === true" class="fas fa-edit text-input-editor text-input-editor-inner-top-left" v-on:click="editImage(\'header-portal-banner\')"><span>Edit Header Background</span></span>
                        <div class="app-main-comp-header-inner" v-bind:style="{ maxWidth: siteWidth + \'px\'}">
                            <div class="app-main-comp-header-flex">
                                <div class="mainImageLeftHeader position-relative" v-bind:style="renderMicroSiteLogoCss(\'main-logo\')" >
                                    <span v-if="editor === true" class="fas fa-edit text-input-editor text-input-editor-inner-top-left" v-on:click="editLogo(\'main-logo\')" style="top: calc(50% - 15px);left: auto;right: 15px;z-index: 100"><span>Edit Site Logo</span></span>
                                    <img class="mainSiteLogo hover-effect-small pointer" @click="goToSite" v-bind:src="renderMicroSiteLogo(\'main-logo\')" v-bind:width="renderMicroSiteLogoStyle(\'main-logo\', \'width\')" v-bind:height="renderMicroSiteLogoStyle(\'main-logo\', \'height\')"  title="The Maxr App" alt="The Maxr App">
                                    <div style="display:none;" class="mainImageHandler" v-bind:style="{background: \'url(\' + entity.banner + \') no-repeat center center / auto 100%\'}"></div>
                                </div>
                                <div class="floatRightHeader">
                                    <table style="text-align: right;">
                                        <tr>
                                            <td>
                                                <div class="welcome-name mb-2">
                                                    <span class="portal-header-salutation">Welcome,</span>
                                                    <span v-if="user" class="portal-header-name">{{ user.data.first_name }}</span>
                                                </div>
                                                <span class="log-out ui-btn-up-b ui-shadow pointer" v-on:click="logMemberOutModal">Log Out</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </header>
                    <div class="app-main-comp-body">
                        <div class="app-main-comp-body-inner" v-bind:style="{ maxWidth: siteWidth + \'px\'}">
                            <article class="app-main-comp-pages">
                                {{ user }}
                            </article>
                        </div>
                    </div>
                    <footer class="app-main-comp-portal-footer">
                        <div class="app-main-comp-footer-inner" v-bind:style="{ maxWidth: siteWidth + \'px\'}">
                            Here is the footer....
                        </div>
                    </footer>
                </div>
            </div>
                ';
    }

    protected function renderComponentHydrationScript(): string
    {
        return SharedVueSiteMethods::hydration($this) . '  
            if (this.entity) {
                const websiteView = sessionStorage.getItem(\'active_editor_siteView_\' + this.entity.card_id, "members")
                this.cardView = websiteView !== "members" ? "public" : "members";
            }    
        ';
    }
}