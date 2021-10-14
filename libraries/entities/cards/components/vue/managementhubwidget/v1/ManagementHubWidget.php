<?php

namespace Entities\Cards\Components\Vue\ManagementHubWidget\V1;

use App\Core\AppModel;
use App\Website\Vue\Classes\Base\VueComponent;
use App\website\Vue\Classes\VueHub;
use App\Website\Vue\Classes\VueProps;
use Entities\Cards\Classes\Cards;
use Entities\Cards\Components\Vue\DigitalCardWidget\V1\DigitalCardMainWidget as V1Card;
use Entities\Cards\Components\Vue\DigitalCardWidget\V2\DigitalCardMainWidget as V2Card;
use Entities\Cards\Components\Vue\DigitalCardWidget\V3\DigitalCardMainWidget as V3Card;

class ManagementHubWidget extends VueComponent
{
    protected $id = "122160fe-9981-4d3d-8218-fabdd279713a";
    protected $title = "Management Hub V1";
    protected $activeCardId = "";
    protected $cardTemplateId = "";
    protected $cardList = null;
    protected $cardHub             = null;
    protected $activeLandingWidget = null;

    public function __construct (?AppModel $entity = null)
    {
        parent::__construct($entity);

        global $app;
        $props = null;
        if (!empty($app->objHttpRequest->Data->Params["props"])) {
            $props = json_decode(base64_decode(str_replace("_","=", $app->objHttpRequest->Data->Params["props"])), false);
        }

        $filterEntity = new VueProps("filterEntityId", "object", "filterEntityId");
        $this->addProp($filterEntity);

        if (!empty($props->cardId))
        {
            $this->processActiveCard($props->cardId);
            return;
        }

        $this->processHub();
    }

    protected function processActiveCard($cardId): void
    {
        $activeCardRequest = (new Cards())->getByUuid($cardId);

        if ($activeCardRequest->Result->Count === 1)
        {
            $this->activeCardId = $activeCardRequest->Data->First()->card_id;
            $this->activeCardUuid = $activeCardRequest->Data->First()->sys_row_id;
            $this->cardTemplateId = $activeCardRequest->Data->First()->template_id;

            $this->cardHub = new VueHub();

            $this->cardList = new ManagementHubCardList();
            $this->cardList->setNoMount(true);
            $this->cardList->setComponentsToNoMount(true);
            $this->cardList->setNoHydrate(true);
            $this->cardList->setComponentsToNoHydrate(true);
            $this->cardList->setParentId($this->cardHub->getInstanceId());
            $this->cardHub->addDynamicComponent($this->cardList, true, true);

            $this->activeLandingWidget = $this->getActiveTemplateRequest();

            if ($this->activeLandingWidget !== null)
            {
                $this->activeLandingWidget->setNoMount(true);
                $this->activeLandingWidget->setComponentsToNoMount(true);
                $this->activeLandingWidget->setNoHydrate(true);
                $this->activeLandingWidget->setComponentsToNoHydrate(true);
                $this->activeLandingWidget->setParentId($this->cardList->getInstanceId());
                $this->cardHub->addDynamicComponent($this->activeLandingWidget, true, true);
                $this->cardHub->addComponentsList($this->activeLandingWidget->getDynamicComponentsForParent(), true);
            }

            $this->addComponentsList($this->cardHub->getDynamicComponentsForParent(), false);
            $this->addDynamicComponent($this->cardHub);
        }
    }

    protected function processHub() : void
    {
        $this->cardHub = new VueHub();

        $this->cardList = new ManagementHubCardList();
        $this->activeLandingWidget = $this->cardList;

        $this->activeLandingWidget->setNoMount(true);
        $this->activeLandingWidget->setComponentsToNoMount(true);
        $this->activeLandingWidget->setNoHydrate(true);
        $this->activeLandingWidget->setComponentsToNoHydrate(true);
        $this->activeLandingWidget->setParentId($this->cardHub->getInstanceId());
        $this->cardHub->addDynamicComponent($this->activeLandingWidget, true, true);
        $this->cardHub->addDynamicComponent($this->activeLandingWidget, true, true);

        $this->addComponentsList($this->cardHub->getDynamicComponentsForParent(), false);
        $this->addDynamicComponent($this->cardHub);
    }

    protected function getActiveTemplateRequest() : ?VueComponent
    {
        switch($this->cardTemplateId)
        {
            case 1:
                return new V1Card();
            case 2:
                return new V2Card();
            case 3:
                return new V3Card();
        }
    }

    protected function renderComponentDataAssignments() : string
    {
        return "
            activeCardId: " . ($this->activeCardUuid ? "'" . $this->activeCardUuid . "'" : "null") . ",
            cards: [],
            cardComponentIds: " . json_encode($this->getCardComponentIds()) . ",
            notifications: [],
            authentication: null,
            talk2MeChat: [],
            hubAtRoot: true,
            showChat: false,
            showModules: false,
            showNotify: false,
            showSearch: false,
            loginUsername: '',
            loginPassword: '',
            loggedInAttemptError: '',
            cardSearch: '',
            cardSearchResult: [],
            cardSearchDelay: 0,
            cardSearchDelayFunc: false,
        ";
    }

    protected function renderComponentMethods() : string
    {
        global $app;
        return '
            jumpToCard: function(card)
            {
                ezLog(card,"card")
                const vc = this.findChildVc(this);
                const componentId = this.cardComponentIds[card.template_id];
                const cardComponent = vc.getComponentById(componentId);
                
                const self = this;
                
                modal.EngageFloatShield();
                
                if (cardComponent === null)
                {
                    vc.setUserId(self.authUserId).loadComponentByStaticId(componentId, \''.$this->cardList->getInstanceId().'\', "view", {}, this.mainEntityList, {cardId: card.sys_row_id}, true, true, function(component){ 
                        self.updateCommandBar()
                        modal.CloseFloatShield()
                        self.closeSearch()
                    });
                    return;
                }
  
                vc.loadComponent(cardComponent.instanceId, cardComponent.id, cardComponent.parentInstanceId, "view", "Digital Card", {}, this.mainEntityList, {cardId: card.sys_row_id}, true, true, function(component){ 
                    self.updateCommandBar()
                    setTimeout(function() {
                        modal.CloseFloatShield()
                        self.closeSearch()
                    }, 500);
                });
            },
            authenticate: function()
            {
                this.authentication = new ExcellAuthentication(this);                
                this.authentication.validate();
            },
            registerAuth: function(auth)
            {
                const self = this;
                modal.EngageFloatShield();
                
                self.authentication.registerAuth(auth);
                
                setTimeout(function() {
                    self.authentication.validate();
                    const vueC = self.findChildVc(self)
                    vueC.getCurrentComponent().instance.loadHubData()
                    modal.CloseFloatShield()
                }, 2000);
            },
            loginCardUser: function(url, username, password, successCallback, errorCallback)
            {
                let newUser = {browserId: Cookie.get("instance"), affiliate_id:"", first_name: "", last_name: "", email: "", phone: "", username: username,  password: password};
                
                const self = this;
                
                ajax.PostExternal(url, newUser, true, function(result) 
                {
                    if (result.success === false)
                    {                        
                        if (typeof errorCallback === "function") 
                        {
                            errorCallback(result);
                        }
                        return;
                    }
                    
                    if (typeof successCallback === "function") 
                    {
                        successCallback(result);
                    }
                });
            },
            processSignOut: function()
            { 
                this.authentication.clearAuth();
            },
            connectToWebSocket: function()
            {
                let self = this;
                
                try 
                {
                    self.socket = new WebSocket("wss://ws.ezdigital.com/mh?auth=" + Cookie.get("me"))
    
                    self.socket.onopen = function(e) {
                        self.socket.send("My name is John")
                    };
                    
                    self.socket.onmessage = function(event) {
                        console.log(`[message] Data received from server: ${event.data}`)
                    };
                    
                    self.socket.onclose = function(event) {
                        if (event.wasClean) {
                            console.log(`[close] Connection closed cleanly, code=${event.code} reason=${event.reason}`)
                        } else {
                            console.log("[close] Connection died")
                        }
                    };
                    
                    self.socket.onerror = function(error) {
                        //console.log(`[error] ${error.message}`)
                    };
                }
                catch(err)
                {
                    ezLog(err,"Socket Error")
                }
            },
            sendSocketMessage: function(text)
            {
                this.socket.send(text)
            },
            accessProfile: function() 
            {
                 
            },
            backToCardList: function() 
            {
                 const vueC = this.findChildVc(this)
                 vueC.backToComponent()
                 this.updateCommandBar()
            },
            updateCommandBar: function() 
            {
                const vueC = this.findChildVc(this)
                
                if (vueC.getCurrentComponentId() === "'.$this->cardList->getInstanceId().'")
                {
                    this.hubAtRoot = true
                    vueC.getCurrentComponent().instance.loadHubData()
                }
                else
                {
                    this.hubAtRoot = false
                }
            },
            toggleFriends: function() 
            {
                // Load into where?
            },
            toggleModules: function() 
            {
                this.showModules = !this.showModules;
            },
            closeModules: function() 
            {
                this.showModules = false;
            },
            toggleSearch: function() 
            {
                this.showSearch = !this.showSearch;
            },
            closeSearch: function() 
            {
                this.showSearch = false;
                this.cardSearchResult = [];
                this.cardSearch = \'\';
            },
            toggleNotify: function() 
            {
                this.showNotify = !this.showNotify;
            },
            closeNotify: function() 
            {
                this.showNotify = false;
            },
            toggleChat: function() 
            {
                this.showChat = !this.showChat;
            },
            closeChat: function() 
            {
                this.showChat = false;
            },
            toggleFavorites: function() 
            {
                // Load into where?
            },
            loadTalk2MeWidget: function() 
            {
                // Load into where?
            },
            toggleFavoriteActiveCard: function() 
            {
                if (this.isLoggedIn === \'inactive\') {
                    this.backToCardList();
                    return;
                }
                
                let self = this;
                const authUrl = "api/v1/cards/add-to-user-favorites";
                ajax.Post(authUrl, {card_id: this.activeCardId, user_id: Cookie.get(\'user\')}, function(result) {
                    if (result.success === false || result.response.success === false) { return; }
                    self.setCardListTypeToFavorites();
                    self.backToCardList();
                });
            },
            setCardListTypeToFavorites: function()
            {
                const vueC = this.findChildVc(this)
                const hubList = vueC.getComponentByInstanceId("'.$this->cardList->getInstanceId().'");
                hubList.instance.setTypeToFavorites();
            },
            searchForCard: function()
            {
                let self = this;
                self.cardSearchDelay = 3
                
                if (this.cardSearchDelayFunc === false)
                {
                    this.cardSearchDelayFunc = true
                    self.searchForCardFunc()          
                    self.searchForCardTimer()
                }
            },
            searchForCardTimer: function()
            {
                if (this.cardSearchDelay <= 0) { this.cardSearchDelay = 0; this.cardSearchDelayFunc = false; return; }
                
                let self = this;
                
                setTimeout(function() 
                {
                    self.cardSearchDelay--;
                    self.searchForCardTimer()
                }, 150);
            },
            searchForCardFunc: function()
            {
                let self = this;
                if (this.cardSearchDelay > 0) 
                {
                    setTimeout(function() {
                        self.searchForCardFunc()
                    }, 150);

                    return;
                }
                
                this.cardSearchDelayFunc = false;
                this.cardSearchDelay = 0;
                
                const url = "'.$app->objCustomPlatform->getFullPortalDomain().'/api/v1/cards/search-cards";
                const searchData = {
                    text: self.cardSearch
                };

                ajax.PostExternal(url, searchData, true, function(result) 
                {
                    if (result.success === false)
                    {                        
                        return;
                    }
                    
                    self.cardSearchResult = result.response.data.cards;
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
            renderCardUrl: function(card)
            {
                return (card.card_vanity_url) ? card.card_vanity_url : card.card_num;
            },
        ';
    }

    protected function renderComponentMountedScript() : string
    {
        return parent::renderComponentMountedScript() . "
            this.authenticate()
        ";
    }

    protected function renderComponentHydrationScript() : string
    {
        return '
            if (this.activeCardId !== null)
            {
                this.hubAtRoot = false;
            }
            
            this.connectToWebSocket()
            this.loadTalk2MeWidget()
        ';
    }


    protected function renderTemplate(): string
    {
        global $app;
        $portalLogoDark = $app->objCustomPlatform->getCompanySettings()->FindEntityByValue("label","portal_logo_dark")->value ?? "/website/logos/logo-dark.svg";
        $portalLogoLight = $app->objCustomPlatform->getCompanySettings()->FindEntityByValue("label","portal_logo_light")->value ?? "/website/logos/logo-light.svg";
        return '
            <div class="app-section" v-bind:class="{\'management-hub\': isLoggedIn == \'active\' }">
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
                    .management-hub .app-hub-comp-wrapper .vue-app-body-component {
                        height: 797px;
                    }
                    .app-hub-comp-wrapper .app-main-comp-nav ul.app-hub-comp-footer-menu li {
                        min-height: 45px;
                        height: 45px;
                    }
                    .management-hub .app-hub-comp-wrapper .app-card {
                        height: 797px;
                    }
                    .management-hub .app-hub-float {
                        background:rgba(0,0,0,.2);
                        z-index:15000;
                    }
                    .right-float-menu-outer .fa-bell {
                        font-size: 23px;
                        position: relative;
                        top: 2px;
                    }
                    .management-hub .app-hub-float.hub-float-base {
                        align-items: flex-end;
                    } 
                    .management-hub .app-hub-float.hub-float-base .app-modal-box {
                        border-radius: 10px 10px 0 0;
                    }
                    .app-section .ajax-loading-anim-inner:after {
                        z-index: 4;
                    }
                    .app-section .vue-float-shield-inner {
                        max-height:890px;
                    }
                    .management-hub .app-section .vue-float-shield-inner {
                        max-height:797px;
                    }
                    
                    /**----------------Portal Header------------- **/

                    .labelBreadcrumb {
                        display:none;
                    }
                    .breadCrumbsInner {
                        margin: 2px 0 0 0;
                        padding:0;
                        display:block;
                        white-space:nowrap;
                    }
                    .homeBreadcrumb {
                        top: 3px;
                        position: relative;
                        margin-right: 5px;
                    }
                    .breadCrumbsInner li:not(.labelBreadcrumb) {
                        list-style-type: none;
                        display:inline-block;
                        white-space:nowrap;
                    }
                    .breadCrumbsInner li > * {
                        font-size:20px;
                    }
                    .breadCrumbsInner li > *:not(.fas) {
                        font-family: \'Montserrat\', sans-serif;
                    }
                    .theme_shade_dark .breadCrumbsInner li > * {
                        color: #fff !important;
                    }
                    .theme_shade_light .breadCrumbsInner li > * {
                        color: #000 !important;
                    }
                    .portal-body {
                        position:relative;
                        margin-left:250px;
                        padding:15px 0 15px 0;
                        z-index:3;
                        height:100vh;
                    }
                    .portal-body .divCell {
                        margin:0;
                    }
                    .theme_shade_dark .portal-body {
                        background-color:#252525;
                    }
                    .theme_shade_light .portal-body {
                        background-color:#dadada;
                    }
                    .portal-body .breadCrumbsInner a.breadCrumbHomeImageLink img {
                        width:25px;
                        height:22px;
                    }
                    header.portal-header {
                        border-radius:5px;
                        padding: 11px 19px;
                        position:relative;
                        width: calc(100% - 300px);
                        box-shadow: rgba(0,0,0,.3) 0 0 7px;
                    }
                    .shoppingCartIcon {
                        width: 23px;
                        height: 19px;
                        display:inline-block;
                    }
                    .divRow {
                        display:table-row;
                        width:100%;
                        clear:both;
                        vertical-align:top;
                    }
                    .divCell {
                        display:table-cell;
                        width:auto;
                        vertical-align:top;
                    }
                    header.portal-header .divCell {
                        display: flex !important;
                    }
                    header.portal-header {
                        width: 100%;
                        border-radius: 0;
                        z-index: 15;
                        display:flex !important;
                    }
                    header.portal-header .divRow {
                        width: 100% !important;
                        display:flex !important;
                    }
                    header.portal-header .divCell {
                        display:flex !important;
                    }
                    .portal-header .divRow .divCell:first-child {
                        width: calc(50% - 20px);
                    }
                    .portal-header .divRow .divCell:last-child {
                        width: calc(50% - 20px);
                        justify-content: flex-end;
                    }
                    .right-float-menu-outer > div {
                        display: inline-block;
                    }
                    .app-hub-header .right-float-menu ul, .portal-body .desktop-account-access ul {
                        display: block;
                        margin: 0;
                    }
                    .app-hub-header .right-float-menu ul li {
                        padding: 0 7px;
                    }
                    .app-hub-header .right-float-menu ul li, .portal-body .desktop-account-access ul li {
                        list-style-type: none;
                        display: inline-block;
                    }
                    .app-hub-comp-wrapper {
                        z-index:4;
                        position:relative;
                    }
                    .app-hub-header, .app-hub-comp-footer {
                        z-index:13000;
                        box-shadow:rgba(0,0,0,.2) 0 0 5px;
                    }
                    .vueAppWrapper .pointer {
                        cursor: pointer;
                    }
                    .theme_shade_dark .portalLogo {
                        background: url('.$portalLogoDark.') no-repeat center center / 100% auto;
                    }
                    .portalLogo {
                        width: 40px;
                        height: 40px;
                        margin-bottom: -10px;
                        margin-top: -10px;
                        display:inline-block;
                    }
                    .portalLogo {
                        background: url('.$portalLogoLight.') no-repeat center center / 100% auto;
                    }
                    .myHubTitle {
                        font-size: 17px;
                        font-weight: bold;
                        font-family: \'Montserrat\', sans-serif;
                    }
                    .app-hub-comp-footer {
                        width: 100%;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 45px;
                    }
                    .app-hub-comp-footer-menu {
                        display: flex;
                        flex-direction: row;
                        justify-content: space-between;
                        width: 100%;
                        height: 100%;
                        margin: 0;
                        padding: 0;
                    }
                    .app-hub-comp-footer-menu li {
                        display: flex;
                        width: 100%;
                        height: 100%;
                        align-items: center;
                        justify-content: center;
                        list-style-type: none;
                        font-size: 16px;
                        cursor: pointer;
                    }
                    .app-hub-comp-footer-menu li > span {
                        width: 65px;
                        text-align: center;
                    }
                    .app-hub-comp-footer-menu li span span {
                        width: 65px;
                        display: inline-block;
                        font-size: 28px;
                    }
                    .app-hub-card-favorite-toggle {
                        position: absolute;
                        top: 0;
                        right: 0px;
                        z-index: 100001;
                        border-top: #FFF 20px solid;
                        border-right: #FFF 20px solid;
                        border-left: transparent 20px solid;
                        border-bottom: transparent 20px solid;
                        height: 0;
                        width: 0;
                        background: transparent;
                    }
                    .app-hub-card-favorite-toggle span {
                        position: absolute;
                        top: -15px;
                    }
                    
                    .hub-card-search-list .cardListRowsWrapper {
                        display: block;
                        padding: 0 8px;
                        background: linear-gradient(to bottom, rgba(255,255,255,.5) 0, rgba(255,255,255,0) 50px);
                    }
                    .hub-card-search-list .cardListRowsInner {
                        display: block;
                        padding:0;
                    }
                    .hub-card-search-list .cardListRowsInner .cardListEl {
                        display: table;
                        width: 100% ;
                    }
                    .hub-card-search-list .cardListRowsInner .cardListEl > div {
                        display: table-cell;
                        vertical-align:middle;
                    }
                    .hub-card-search-list .cardListEl {
                        cursor: pointer !important;
                        flex-direction: column;
                        display: flex;
                        flex: auto;
                        margin: 10px 0 0;
                        box-shadow: rgb(0 0 0 / 40%) 0 0 7px;
                        position: relative;
                        align-content: center;
                        align-items: center;
                        padding: 10px;
                        background: #fff;
                    }
                    .hub-card-search-list .cardBanner {
                        width: 175px;
                        height: 175px;
                        box-shadow: rgb(0 0 0 / 40%) 0 0 5px;
                    }
                    .hub-card-search-list .cardUrl {
                        font-weight: normal;
                        font-size: 14px;
                    }
                    .hub-card-search-list .cardAccessed {
                        font-weight: bold;
                        font-size: 14px;
                    }
                    .hub-card-search-list .cardListRowsInner .cardBanner {
                        width: 50px;
                        height: 50px;
                    }
                    .hub-card-search-list .cardListRowsInner .cardNumber {
                        padding-left:15px;
                    }
                    .hub-card-search-list .cardNumber {
                        font-size: 1.2em;
                        font-weight:bold;
                        max-width:175px;
                        margin-top: 5px;
                    }
                    
                    @media (max-width: 600px) {
                        .management-hub .app-hub-comp-wrapper .vue-app-body-component {
                            height: var(--vhw1);
                        }
                        
                        .management-hub .app-hub-float {
                            top: 48px;
                        }
                        .management-hub .app-hub-comp-wrapper .app-card {
                            height: var(--vhw1);
                        }
                        .app-section .vue-float-shield-inner {
                            max-height: 100vh;
                            max-height: -webkit-fill-available;
                        }
                    }
                </v-style>
                <div class="app-component app-main-component">
                    <header v-if="isLoggedIn == \'active\'" class="app-hub-header portal-header divTable">
                        <div class="divRow">
                            <div v-on:click="backToCardList()" class="divCell myHubTitle">
                                My Hub
                            </div>
                            <div class="site-logo divCell showOnMobile">
                                <a href="/account" class="leftHeaderLogoLink">
                                    <span class="portalLogo"></span>
                                </a>
                            </div>
                            <div class="divCell right-float-menu-outer">
                                <div class="right-float-menu">
                                    <ul>
                                        <li v-on:click="toggleSearch"><span class="fas fa-search pointer"></span></li>
                                        <li v-on:click="toggleNotify"><span class="far fa-bell pointer"></span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </header>
                    <div class="app-hub-comp-wrapper">
                        ' . $this->renderRegisteredDynamicComponent(
                            $this->registerDynamicComponentViaRegisteredHub(
                                $this->cardHub,
                                $this->activeLandingWidget,
                                "view",
                                [
                                    new VueProps("activeCardId", "object", "activeCardId")
                                ])
                        ) . '
                        <div v-show="hubAtRoot === false" class="app-hub-card-favorite-toggle" v-on:click="toggleFavoriteActiveCard"><span class="fas fa-heart"></span></div>
                    </div>
                    <footer v-if="isLoggedIn == \'active\'"  class="app-hub-comp-footer">
                        <ul class="app-hub-comp-footer-menu">
                            <li v-on:click="backToCardList()"><span><span class="fas" v-bind:class="{\'fa-arrow-left\': hubAtRoot === false, \'fa-bars\': hubAtRoot === true}"></span></span></li>
                            <li v-on:click="toggleFriends()"><span><span class="fas fa-users"></span></span></li>
                            <li v-on:click="toggleModules()"><span><span class="fas fa-th-large"></span></span></li>
                            <li v-on:click="toggleChat()"><span><span class="fas fa-comments"></span></span></li>
                            <li v-on:click="toggleFavorites()"><span><span class="fas fa-heart"></span></span></li>
                        </ul>
                    </footer>
                    <div class="app-hub-float hub-float-base app-login-float app-modal-float" v-if="showModules === true" v-bind:class="{active: showModules === true}">
                        <div class="app-modal">
                            <div class="app-modal-box">
                                <span class="app-modal-hide" v-on:click="closeModules()"></span>
                                <div class="app-modal-title">Modules</div>
                                <div class="app-modal-body">
                                    This thing!!
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="app-hub-float hub-float-base app-login-float app-modal-float" v-if="showChat === true" v-bind:class="{active: showChat === true}">
                        <div class="app-modal">
                            <div class="app-modal-box">
                                <span class="app-modal-hide" v-on:click="closeChat()"></span>
                                <div class="app-modal-title">Chat</div>
                                <div class="app-modal-body">
                                    This thing!!
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="app-hub-float app-login-float app-modal-float" v-if="showSearch === true" v-bind:class="{active: showSearch === true}">
                        <div class="app-modal">
                            <div class="app-modal-box">
                                <span class="app-modal-hide" v-on:click="closeSearch()"></span>
                                <div class="app-modal-title">Search</div>
                                <div class="app-modal-body" style="padding:0 25px;">
                                    <input v-on:keyup="searchForCard()" v-model="cardSearch" name="searchText" type="text" class="form-control" placeholder="Search for a '.$app->objCustomPlatform->getCompany()->platform_name.' card!">
                                    <div v-if="cardSearchResult.length === 0 && cardSearch != \'\'">No Results</div>
                                    <div class="hub-card-search-list " v-if="cardSearchResult.length > 0 && cardSearch != \'\'">
                                        <ul class="cardListRowsInner">
                                            <li v-for="cardSearchItem in cardSearchResult" v-on:click="jumpToCard(cardSearchItem)" class="cardListEl">
                                                <div class="cardBanner" v-bind:style="{background: \'url(\' + cardSearchItem.banner +\') no-repeat center center / cover\'}"></div>
                                                <div class="cardNumber">
                                                    {{ cardSearchItem.card_name }}
                                                    <div class="cardUrl">'.$app->objCustomPlatform->getPublicDomain().'/{{ renderCardUrl(cardSearchItem) }}</div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="app-hub-float app-login-float app-modal-float" v-if="showNotify === true" v-bind:class="{active: showNotify === true}">
                        <div class="app-modal">
                            <div class="app-modal-box">
                                <span class="app-modal-hide" v-on:click="closeNotify()"></span>
                                <div class="app-modal-title">Alerts</div>
                                <div class="app-modal-body">
                                    This thing!!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                ';
    }

    private function getCardComponentIds() : array
    {
        $digitalCardDirectories = glob(AppEntities . "/cards/components/vue/digitalcardwidget/*" , GLOB_ONLYDIR);

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