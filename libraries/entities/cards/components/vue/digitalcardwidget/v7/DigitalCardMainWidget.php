<?php

namespace Entities\Cards\Components\Vue\DigitalCardWidget\V7;

use App\Core\AppModel;
use Entities\Cards\Components\Vue\DigitalCardWidget\Assets\AbstractDigitalSiteComponent;
use Entities\Cards\Components\Vue\DigitalCardWidget\Assets\SharedVueSiteMethods;

class DigitalCardMainWidget extends AbstractDigitalSiteComponent
{
    protected string $id = "22a8defb-1ba2-461f-a379-401faef7eb3d";
    protected string $cssPrefix = ".app-template-7";

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
            shareButtons: [1,2,3,4],
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
            },
            renderPersonaAvatar: function(entity) {
                if (!entity.Settings.avatar) {
                    return "url(/_ez/images/users/defaultAvatar.jpg) no-repeat center center / contain";
                }
                return "url(" + imageServerUrl() + entity.Settings.avatar + ") no-repeat center center / cover";
            },
            renderUserName: function(entity) {
                if (!entity.Settings.display_name) {
                    return "Person\'s Display Name";
                }
                return entity.Settings.display_name;
            },
            renderUserTitle: function(entity) {
                if (!entity.Settings.title) {
                    return "Person\'s User Title";
                }
                return entity.Settings.title;
            },
            hasUserTitle: function(entity) {
                if (!entity.Settings.user_title) {
                    false;
                }
                return true;
            },
            activateShare: function(label, value) {
                const numberPattern = /\d+/g;
                switch(label) {
                    case "sms":
                        window.location = "sms:" + value.match(numberPattern).join("");
                        break;
                    case "phone":
                        window.location = "tel:" + value.match(numberPattern).join("");
                        break;
                    case "email":
                        window.location = "mailto:" + value;
                        break;
                    case "website":
                        window.open(value, "_blank");
                        break;
                    default:
                        window.open(value, "_blank");
                        break;
                }
            },
            renderShareIcon: function(label) {
                
            },
            ';
    }

    protected function getMobileCss(): array
    {
        return [
            '.personaAvatar' => 'width: 100%; padding-top: 100%;',
            '.userShareButtonItem > a' => 'width: 98px;height: 75px;',
        ];
    }

    protected function getTabletCss(): array
    {
        return [
            '.app-main-comp-header' => 'height: 125px',
        ];
    }

    protected function renderTemplate(): string
    {
        return '                                                                                                                                                                                                                                                                                                
            <div id="excell-site-app-6" v-if="entityFound == true" class="app-section '.$this->cssPrefixClass().'">
            <v-style type="text/css">
                    '.$this->cssPrefix.' .personaAvatar {
                        width:250px;
                        height:250px;
                        border-bottom:5px solid #000000;
                    }
                    '.$this->cssPrefix.' .personaUserInfo {
                        text-align:center;
                        padding:10px;
                    }
                    '.$this->cssPrefix.' .personaUserDisplayName {
                        font-size:2rem;
                    }
                    '.$this->cssPrefix.' .personaUserTitle {
                        font-size:1.25rem;
                    }
                    '.$this->cssPrefix.' .userShareButtons {
                        display:flex;
                    }
                    '.$this->cssPrefix.' .userShareButtonItem > a {
                        display:flex;
                        padding: 10px 15px;
                        flex-direction:column;
                        background:#ff0000;
                    }
                    '.$this->renderMobileCss().'
                </v-style>
                <div v-if="cardView ===\'public\'" class="app-component app-main-component">
                    <div class="personaHeader">
                        <div class="personaAvatar" v-bind:style="{background: renderPersonaAvatar(entity)}">
                        </div>
                        <div class="personaUserInfo">
                            <h1 class="personaUserDisplayName">{{ renderUserName(entity) }}</h1>
                            <h2 v-if="hasUserTitle(entity)" class="personaUserTitle">{{ renderUserTitle(entity) }}</h2>
                        </div>
                        <div class="userShareButtons">
                            <div class="userShareButtonItem" v-for="currIndex in shareButtons">
                                <a v-on:click="activateShare(entity.Settings[\'shareButton_\' + currIndex + \'_label\'], entity.Settings[\'shareButton_\' + currIndex + \'_value\'])">
                                    <div v-style="renderShareIcon(entity.Settings[\'shareButton_\' + currIndex + \'_label\'])""></div>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    
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