<?php

namespace Entities\Cards\Components\Vue\DigitalCardWidget\V6;

use App\Core\AppModel;
use Entities\Cards\Components\Vue\DigitalCardWidget\Assets\AbstractDigitalSiteComponent;
use Entities\Cards\Components\Vue\DigitalCardWidget\Assets\SharedVueSiteMethods;

class DigitalCardMainWidget extends AbstractDigitalSiteComponent
{
    protected string $id = "22a8defb-1ba2-461f-a379-401faef7eb3d";
    protected string $cssPrefix = ".app-template-6";

    public function __construct (?AppModel $entity = null)
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
            tempCard: null,
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
            },';
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
                    self.batchLoadPages()
                    self.registerHistory()                 
                } else {
                    // Card Is Live
                    self.loadCardDataById(siteId, function(data) {
                        self.loadCardModules()
                        self.batchLoadPages()
                        self.registerHistory()
                    });
                }
            },
            prePageAssignment: function(page, options, callback) {
                if (typeof callback === "function") callback(page)
            },
            postPageAssignment: function(page, options, callback) {
                if (typeof callback === "function") callback(page)
            },
            sendSms: function()
            {
                const unqiueUrl = (this.entity.card_vanity_url !== "" ? this.entity.card_vanity_url : this.entity.card_num);
                window.location = "sms:?&body=' . $app->objCustomPlatform->getFullPublicDomainName() . '/" + unqiueUrl + "%20Click%20the%20link%20to%20connect%20with%20" + this.entity.card_owner_name + "!";
            },
            sendShare: function()
            {
                window.location = "https://optin.mobiniti.com/" + this.entity.card_num
            },
            openModules: function()
            {
                this.modulesOpen = true
                this.menuOpen = false
            },
            openShareSave: function()
            {
                this.shareSaveOpen = true
                this.menuOpen = false
            },
            closeShareSave: function()
            {
                this.shareSaveOpen = false;
            },
            openSocialMedia: function()
            {
                this.socialMediaOpen = !this.socialMediaOpen
            },
            setAuth: function()
            {
                this.isLoggedIn = this.$parent.$parent.isLoggedIn
                this.authUserId = this.$parent.$parent.authUserId
            },
            emailCard: function()
            {
                let self = this;
                if (this.emailCard_box === false) { slideDown( elm("emailCard_box"), 250, function() {
                    self.emailCard_box = true
                }); }
                if (this.emailCard_box === true) { slideUp( elm("emailCard_box"), 250, function() {
                    self.emailCard_box = false
                }); }
            },
            gotoSocialLink: function(social)
            {
                const url = social.connection_value;
                window.open(url, "_blank");
            },
            renderSiteBanner: function(entity) {
                if (typeof entity.banner === "undefined" || entity.banner === "") return "https://www.micahzak.com/images/header-static-back.jpg";
                return entity.banner
            },
            renderCustomSliderWidth: function() {
                if (this.webDomEl === null) return "850px"
                const actualWidth = this.webDomEl.offsetWidth;
                const fullWidth = (actualWidth > this.siteWidth ? this.siteWidth : actualWidth)
                const ratio = 850/this.siteWidth
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
            }
            ';
    }

    protected function getMobileCss(): array
    {
        return [
            '.app-main-comp-header' => 'height: 100px',
            '.app-main-comp-header .app-main-comp-header-inner,
             .app-main-comp-header .mainImageLeftHeader, 
             .app-main-comp-header .app-main-comp-header-flex, 
             .app-main-comp-header .mainSiteLogo' => 'height: 100%',
            '.app-main-comp-page-title' => 'height:150px;',
            '.app-main-comp-nav .nav-buttons' => 'display:none;',
            '.app-kabob-float:not(.active) .app-kabob' => 'display:block;',
            '.toggle_menu' => 'display:flex;'
        ];
    }

    protected function getTabletCss(): array
    {
        return [
            '.app-main-comp-header' => 'height: 125px',
            '.app-main-comp-header .app-main-comp-header-inner, 
            .app-main-comp-header .mainImageLeftHeader, 
            .app-main-comp-header .app-main-comp-header-flex, 
            .app-main-comp-header .mainSiteLogo' => 'height: 100%',
            '.app-main-comp-page-title' => 'height:200px;',
            '.app-main-comp-nav .nav-buttons' => 'display:none;',
            '.app-kabob-float:not(.active) .app-kabob' => 'display:block;',
            '.toggle_menu' => 'display:flex;'
        ];
    }

    protected function renderTemplate(): string
    {
        return '                                                                                                                                                                                                                                                                                                
            <div id="excell-site-app-6" v-if="entityFound == true" class="app-section '.$this->cssPrefixClass().'">
            <v-style type="text/css">
                    .loginwidget-field-table {
                        display:flex;
                        flex-direction:column;
                    }
                    .loginwidget-field-table .loginwidget-field-row {
                        display:flex;
                        width: 100%;
                        height: 50px;
                    }
                    .loginwidget-field-table .loginwidget-field-row .editor-label {
                        width: 115px;
                    }
                    .loginwidget-field-table .loginwidget-field-row .editor-field {
                        width: calc(100% - 35px);
                    }
                    .loginwidget-field-table {
                        padding: 15px 25px 0;
                    }
                    .loginwidget-field-table .width100 {
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
                        z-index:500;
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
                    .theme_shade_light '.$this->cssPrefix.' .nav-buttons > li.mobile-nav-buttons > span {
                        color:white;
                    }
                    '.$this->renderMobileCss().'
                </v-style>
                <div v-if="cardView ===\'public\'" class="app-component app-main-component">
                    {{ entity.Settings }}
                </div>
            </div>
        ';
    }

    protected function renderComponentHydrationScript(): string
    {
        return SharedVueSiteMethods::hydration($this) . '  
        ';
    }
}