<?php

namespace Entities\Cards\Components\Vue\DigitalCardWidget\V2;

use App\Core\AppModel;
use App\Website\Vue\Classes\Base\VueComponent;

class DigitalCardMainWidget extends VueComponent
{
    protected $id = "4185dd32-d268-40cd-886d-47fe9f80075f";
    protected $title = "Digital Card";
    protected $endpointUriAbstract = "{card_num}";
    protected $noMount = false;

    public function __construct (?AppModel $entity = null)
    {
        parent::__construct($entity);

        $editorComponent = new DigitalCardPageWidget();
        $editorComponent->addParentId($this->getInstanceId(), ["edit"]);

        $this->addComponentsList($editorComponent->getDynamicComponentsForParent());
        $this->addComponent($editorComponent);
    }

    protected function renderComponentDataAssignments() : string
    {
        return "
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
                },';
    }

    protected function renderComponentHydrationScript() : string
    {
        return '
            let cardId = props.cardId;
            
            if (typeof this.activeCardId !== "undefined" && this.activeCardId != null)
            {
                cardId = this.activeCardId;
            }
            
            this.showSplashImage = true;
            
            let self = this;
            self.checkHanded();
            self.setAuth();
            
            self.loadCardDataById(cardId, function(data) {
                self.batchLoadPages();
                self.loadCardModules();
                self.registerHistory();
            });
        ';
    }

    protected function renderComponentMethods() : string
    {
        global $app;
        return 'loadCardDataById: function(id, callback) 
            {
                let self = this;
                const url = "api/v1/cards/get-card-by-uuid?uuid=" + id + "&pages=true&pageContent=false&addons=modules";
                ajax.Get(url, null, function(result)
                {
                    if (result.success === false || typeof result.response === "undefined") 
                    { 
                        return;
                    }
                    
                    self.entityFound = true;                    
                    self.entity = result.response.data.card;
                    self.mainCardColor = "#" + atob(getJsonSetting(self.entity.card_data, "style.card.color.main"));
                    
                    let vc = self.findVc(self);
                    self.$forceUpdate();
                                                                                                       
                    if (typeof callback === "function") { callback(result.response.data); }
                }, "GET");          
            },
            loadCardModules: function()
            {
                let self = this;
                setTimeout(function() 
                {
                    self.loadModules();
                }
                , 50);
            },
            buildModuleIds: function()
            {
                let moduleIds = [];
                for (let currModule of this.entity.Modules)
                {
                    moduleIds.push(currModule.instance_uuid);
                }
                
                return moduleIds.join("|");
            },
            loadModules: function(index)
            {
                let self = this;
                const url = "modules/widget/card-widget?id=" + self.entity.card_id + "&modules=" + this.buildModuleIds();
            
                ajax.Get(url, null, function(result) 
                {
                    // invisibleCardModule
                    console.log(result);
                });
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
                    console.log(result);
                });
                
            },
            batchLoadPages: function()
            {
                let self = this;
                setTimeout(function() 
                {
                    self.loadPage(0);
                }
                , 50);
            },
            loadPage: function(index)
            {
                let self = this;
                let pages = self.entity.Tabs;
                
                if (typeof pages[index] === "undefined") return;
                
                if (pages[index].hasDataLoaded === true)
                {
                    setTimeout(function() 
                    {
                        self.loadPage(index + 1);
                    }
                    ,20);
                    
                    return;
                }
                
                const url = "cards/card-data/get-card-page-data?card_tab_rel_id=" + pages[index].card_tab_rel_id + "&card_id=" + self.entity.card_id;
                pages[index].loadingPage = true;
                
                ajax.Get(url, null, function(result) 
                {
                    pages[index].loadingPage = false;
                    
                    try
                    {
                        pages[index].content = result.response.data.content;
                        pages[index].hasDataLoaded = true;
                    }
                    catch(err)
                    {
                        console.log(err);
                        console.log(result);
                        pages[index].content = "Error loading Page: " + err;
                    }
        
                    if ((index + 1 ) < pages.length)
                    {
                        setTimeout(function() 
                        {
                            self.loadPage(index + 1);
                        }
                        ,20);
                    }
                });
            },
            processConnectionRequest: function(connection)
            {
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
                window.open("'.$app->objCustomPlatform->getFullPublicDomain().'/api/v1/cards/download-vcard?card_id=" + this.entity.card_num, "_blank");
            },
            sendSms: function()
            {
                window.location = "sms:?body='.$app->objCustomPlatform->getFullPublicDomain().'/" + this.entity.card_num + "%20Let\'s%20connect%20with%20" + this.entity.card_owner_name + "!";
            },
            sendShare: function()
            {
                window.location = "https://optin.mobiniti.com/" + this.entity.card_num;
            },
            openCardPage: function(page)
            {
                this.menuOpen = false;
                '. $this->activateRegisteredComponentById(DigitalCardPageWidget::getStaticId(), "view", true, "this.entity", "[]", ["cardPage" => "page"], "this", "function(result) { 
                    console.log(result);
                }").'
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
            attemptLogin: function()
            {
                if(!this.validateUsername(this.loginUsername)) return;
                if(!this.validatePassword(this.loginPassword)) return;
                
                const url = "'.$app->objCustomPlatform->getFullPortalDomain().'/process/login/authenticate-login-request";
                console.log(url);
                
                let self = this;
                this.loginCardUser(
                    url,
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
                        
                        console.log(self.loggedInUser);
                        console.log(self.entity);
                        
                        let urlRedirect = "";
                        
                        if (self.loggedInUser.id === self.entity.card_user_uuid || self.loggedInUser.id === self.entity.card_owner_uuid)
                        {
                            urlRedirect = "'.$app->objCustomPlatform->getFullPortalDomain().'/account/cards/card-dashboard/" + self.entity.sys_row_id;
                        }
                        else
                        {
                            urlRedirect = "'.$app->objCustomPlatform->getFullPortalDomain().'/account/";
                        }
                        
                        console.log(urlRedirect); return;
                        
                        window.location = urlRedirect;
                        
                    }, 
                    function(error) 
                    {
                        console.log(error);
                    });
            },
            attemptMemberLogin: function()
            {
                if(!this.validateUsername(this.loginUsername)) return;
                if(!this.validatePassword(this.loginPassword)) return;
                
                let self = this;
                this.loginCardUser(
                    "'.$app->objCustomPlatform->getFullPortalDomain().'/api/v1/users/validate-existing-user-credentials",
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
                    return "'.$app->objCustomPlatform->getFullPortalDomain().'/_ez/images/users/no-user.jpg";
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
            loginCardUser: function(url, username, password, successCallback, errorCallback)
            {
                this.loggedInAttemptError = "";
                let newUser = {browserId: Cookie.get("me"),affiliate_id:"", first_name: "", last_name: "", email: "", phone: "", username: username,  password: password};
                
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
            ';
    }

    protected function renderTemplate(): string
    {
        return '
            <div v-if="entityFound == true" class="app-section app-template-2">
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
                    .guard-smart-global-logo {
                        background: url(/widgets/images/guard-smart/guard-smart-global-logo.png) no-repeat center center / contain;
                    }
                    .i360-takeoff-logo {
                        background: url(/widgets/images/360-takeoff/360-takeoff-logo.png) no-repeat center center / contain;
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
                </v-style>
                <div id="splash-shield" class="universal-float-shield vue-float-shield" style="position:absolute;z-index: 5;" v-bind:class="{hidden: showSplashImage === false}" v-on:click="hideSplashImage()">
                    <div class="vue-float-shield-inner app-card" v-bind:style="{\'background-image\': \'url(\' + entity.splash_cover + \')\' }" style="background: no-repeat center center, #fff; background-size:cover;">
                        <div class="floating-blocks">
                            <div class="floating-blocks-inner">
                                <div>{{ entity.card_user_name }}</div>
                                <div v-if="getJsonSetting(entity, \'card_data.card_user.title\') !== null">{{ getJsonSettingDecoded(entity, \'card_data.card_user.title\') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="cardView ===\'public\'" class="app-component app-main-component">
                    <header class="app-main-comp-header" v-bind:style="{ background: \'url(\' + entity.banner + \') no-repeat center center / cover\'}">
                        <div class="app-main-comp-header-logo">
                            <div class="app-main-comp-header-logo-image" v-bind:style="{ background: \'url(\' + entity.logo + \') no-repeat center center / contain\'}">
                            </div>
                        </div>
                    </header>
                    <div class="app-main-comp-body">
                        <nav class="app-main-comp-nav">
                            <ul>
                                <li v-for="currConnection in cardConnections" v-on:click="processConnectionRequest(currConnection)">
                                    <span class="app-main-comp-nav-item">
                                        <span v-bind:class="displayFontAwesome(currConnection)"></span>
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
                        <div class="app-main-comp-float social-media-float" v-bind:class="{active: socialMediaOpen === true}">
                            <div class="app-main-comp-float-modal">
                                <div class="app-main-comp-float-modal-tri"></div>
                                <div class="app-modal-title" style="margin-top: 0;">Social Media</div>
                                <ul class="social-media-list">
                                    <li class="pointer" v-for="currMedia in cardSocialMedia" v-on:click="gotoSocialLink(currMedia)"><span v-bind:class="currMedia.font_awesome"></span><span>@MyHandle</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <footer class="app-main-comp-footer">Powered By EZ Digital</footer>
                    <div class="app-kabob-float" v-bind:class="{active: menuOpen === true}">
                        <div class="app-kabob" v-on:click="openCardMenu">
                            <div class="app-kabob-handle">
                                <div class="app-kabab-circle" v-bind:style="{background: mainCardColor}"></div>
                                <div class="app-kabab-circle" v-bind:style="{background: mainCardColor}"></div>
                                <div class="app-kabab-circle" v-bind:style="{background: mainCardColor}"></div>
                            </div>
                        </div>
                        <div class="app-kabob-header">
                            <span class="app-kabob-hide" v-on:click="closeCardMenu"></span>
                            <span class="app-kabob-avatar" v-bind:style="{ background : \'url(\' + getUserAvatar() + \') no-repeat center center / 100% auto\' }"></span>
                            <span class="app-kabob-user-name" v-bind:style="{color: mainCardColor}">{{ entity.card_user_name }}</span>
                            <ul class="app-kabob-menu">
                                <li v-on:click="openSplashImage"><span><span class="fas fa-home"></span>Home</span></li>
                                <li v-on:click="openModules"><span><span class="fas fa-th-large"></span>Modules</span></li>
                                <li v-on:click="openShareSave"><span><span class="fas fa-qrcode"></span>Share & Save</span></li>
                                <li><span><span class="fas fa-thumbs-up"></span>Get Your Own Card</span></li>
                                <li v-on:click="openMembersAccess"><span><span class="fas fa-users"></span>Members</span></li>
                                <li v-on:click="openLogin"><span><span class="fas fa-sign-in-alt"></span>Login</span></li>
                            </ul>
                        </div>
                        <div class="app-kabob-footer">
                            <div>Hand Controls</div>
                            <table class="switchHandsWrapper" v-on:click="toggleHandControls">
                                <tr>
                                    <td>Left</td>
                                    <td><span class="switchHands"></span></td>
                                    <td>Right</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="app-modules-float app-modal-float" v-bind:class="{active: modulesOpen === true}">
                        <div class="app-modal">
                            <div class="app-modal-box">
                                <span class="app-modal-hide" v-on:click="closeModules"></span>
                                <div class="app-modal-title">Modules</div>
                                <div class="app-modal-body" style="margin-top:10px;">
                                    <ul class="modules-wrapper">
                                        <li class="modules-item pointer">
                                            <div class="modules-item-box pointer" v-on:click="toggleRealEstateModule()"><span v-bind:style="{color: mainCardColor}"><i class="fas fa-sign"></i>Real Estate</span></div>
                                            <div id="realEstateModule_box" style="display:none;">
                                                <ul class="modules-apps-wrapper">
                                                    <li v-on:click="goto360TakeoffDemo()" class="modules-wrapper-app"><span class="i360-takeoff-logo"></span></li>
                                                    <li v-on:click="gotoGuardSmartDemo()" class="modules-wrapper-app"><span class="guard-smart-global-logo"></span></li>
                                                </ul>
                                                <div class="getAdditionalTools"><i class="fas fa-plus-circle"></i> Get Addition Tools</div>
                                            </div>
                                        </li>
                                        <li class="modules-item">
                                            <div class="modules-item-box"><span v-bind:style="{color: mainCardColor}"><i class="fas fa-hammer"></i>Construction</span></div>
                                        </li>
                                        <li class="modules-item">
                                            <div class="modules-item-box"><span v-bind:style="{color: mainCardColor}"><i class="fas fa-house-damage"></i>Insurance</span></div>
                                        </li>
                                        <li class="modules-item">
                                            <div class="modules-item-box"><span v-bind:style="{color: mainCardColor}"><i class="fas fa-business-time"></i>Business</span></div>
                                        </li>
                                        <li class="modules-item pointer">
                                            <div class="getAdditionalTools"><span><i class="fas fa-plus-circle"></i> Get Additional Modules</span></div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="app-modules-float app-modal-float" v-bind:class="{active: takeOffOpen === true}">
                        <div class="app-modal">
                            <div class="app-modal-box">
                                <span class="app-modal-hide" v-on:click="closeTakeOff360"></span>
                                <div class="app-modal-title">360 Takeoff</div>
                                <div class="app-modal-body" style="margin-top:10px;">
                                    <div style="overflow: hidden;width: calc(100vw + 15px);height: 229px;max-width: 441px;">
                                        iFrame removed...
                                    </div>
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
                    <footer class="app-main-comp-portal-footer" >
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