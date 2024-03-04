<?php

namespace Entities\Cards\Components\Vue\DigitalCardWidget\Assets;

use Entities\Cards\Components\Vue\Cardwidget\Footer\ManagePageHighlightWidget;
use Entities\Cards\Components\Vue\Cardwidget\Footer\ManagePagesShowcaseWidget;
use Entities\Cards\Components\Vue\LoginWidget\MemberLoginWidget;
use Entities\Media\Components\Vue\Logowidget\LogoProfileWidget;
use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cards\Components\Vue\CardWidget\ManageCardPagesWidget;
use Entities\Cards\Components\Vue\Maxtech\Sitewidget\ManageSitePageWidget;
use Entities\Media\Components\Vue\BackgroundWidget\BackgroundProfileWidget;

class SharedVueSiteMethods
{
    public static function dataAssignments(
        VueComponent $vueComponent,
        bool $multiPage = false
    ) : string
    {
        return "
            webDomEl: null,
            entityFound: false,
            entity: null,
            editor: false,
            activePage: null,
            activeTitle: null,
            activeUrl: null,
            pageRootPathIndex: 0,
            requestUriPath: '',
            requestUriFullPath: '',
            requestUri: [],
            menuOpen: false,
            loginOpen: false,
            shareSaveOpen: false,
            membersAccessOpen: false,
            modulesOpen: false,
            loginUsername: '',
            loginPassword: '',
            loggedIn: false,
            loggedInUser: {},
            loggedInAttemptError: '',
            cardUserAssociation: 'vistior',
            cardView: 'public',
            initialHydration: true,
            siteMedia: [],
            siteLogos: [],
            siteSettings: [],
            cardModulesByClass: [],
            deviceWidth: 'desktop',
            pageDisplayMultiStyle: ". ($multiPage ? "true" : "false") . ",
        ";
    }

    public static function computed(VueComponent $vueComponent) : string
    {
        return '
            cardPages: function()
            {
                if (typeof this.entity !== "undefined") { return this.entity.Tabs; }
                return null;
            }, 
            renderHighlightTitle: function() {
                let cardPages = this.cardPages
                if (cardPages === null) return "" 
                ezLog(cardPages, "cardPages")
                for (const currPage in cardPages) {
                    if (cardPages[currPage].rel_sort_order > 1) {
                        return cardPages[currPage].title
                    }
                }
                return "No Page Title Found"
            },
            renderHighlightPageTitle: function() {
                let cardPages = this.cardPages
                if (cardPages === null) return ""
                for (const currPage in cardPages) {
                    if (cardPages[currPage].rel_sort_order > 1) {
                        return cardPages[currPage].title
                    }
                }
                return "No Page Title Found"
            },
            renderHighlightPageDesc: function() {
                let cardPages = this.cardPages
                if (cardPages === null) return ""
                for (const currPage in cardPages) {
                    if (cardPages[currPage].rel_sort_order > 1) {
                        let div = document.createElement("div");
                        div.innerHTML = atob(cardPages[currPage].content);
                        const summaryText = (div.textContent || div.innerText || "").substr(0,165);
                        return summaryText != "" ? (summaryText + "...") : "No Summary Text Found"
                    }
                }
                return "No Page Title Found"
            },
            renderHighlightPageUrl: function() {
                let cardPages = this.cardPages
                if (cardPages === null) return ""
                for (const currPage in cardPages) {
                    if (cardPages[currPage].rel_sort_order > 1) {
                        return cardPages[currPage].url
                    }
                }
                return "/"
            },
        ';
    }

    public static function methods(VueComponent $vueComponent) : string
    {
        global $app;
        return '
            siteIsLoadedInEditor: function() {
                return typeof this.$parent.$parent.parentIsEditor !== "undefined";
            },
            reloadActivePageTitle: function(data) {
                if (data.title) {
                    this.activeTitle = data.title
                    this.resizeElementByName("app-page-editor-title-transparent")
                }
            },
            reloadCardProfile: function(data) {
                if (!data || !data.card || data.templateChange === true) return;
                this.entity.card_name = data.card.card_name
                this.entity.owner_id = data.card.owner_id
                this.entity.card_vanity_url = data.card.card_vanity_url
                this.entity.card_keyword = data.card.card_keyword
                this.entity.card_domain = data.card.card_domain
                this.entity.card_domain_ssl = data.card.card_domain_ssl
                this.entity.status = data.card.status
                this.$forceUpdate()
            },
            reloadActivePageWidget: function(data) 
            {
                const self = this;
                if (data.widget) { 
                    for (currPageIndex in self.entity.Tabs) {
                        if (self.entity.Tabs[currPageIndex].card_tab_id == this.activePage.card_tab_id) {
                            this.loadPage(currPageIndex, false, function(page) {
                                self.openSitePage(page)
                            });
                        }
                    }
                }
            },
            loadCardDataById: function(id, callback) 
            {
                let self = this;
                const url = "api/v1/cards/get-card-by-uuid?uuid=" + id + "&pages=true&pageContent=false&addons=modules";
                ajax.Get(url, null, function(result) {
                    if (result.success === false || typeof result.response === "undefined") { 
                        return;
                    }
                                
                    self.entity = result.response.data.card;
                    self.hydrateCard();
                                                                                                   
                    if (typeof callback === "function") { callback(result.response.data); }
                }, "GET");          
            },
            hydrateCard: function()
            {
                this.entityFound = true
                this.reloadSiteConfiguration()
                this.reloadSiteMedia()
                this.reloadSiteLogos()
                this.registerDomEl()
            },
            reloadSiteMedia: function(data) {
                this.reloadMedia(this.entity.Media, "siteMedia")
            },
            reloadSiteLogos: function(data) {
                this.reloadMedia(this.entity.Logos, "siteLogos")
            },
            reloadMedia: function(media, label)
            {
                if (!this[label]) this[label] = []
                let defaultSettings = _.clone(this.entity.Template.data ? this.entity.Template.data : [])
                if (label === "siteLogos") {
                    for (const currSettingIndex in defaultSettings) {
                        if (defaultSettings[currSettingIndex].type === "logo") {
                            this[label][currSettingIndex] = {}
                            let settingOption = {}
                            for (const currElIndex in defaultSettings[currSettingIndex].elements) {
                                const dataEl = defaultSettings[currSettingIndex].elements[currElIndex].data
                                if (dataEl.label === "url") {
                                    this[label][currSettingIndex][dataEl.label] = dataEl.default
                                } else {
                                    settingOption[dataEl.label] = {value: dataEl.default, responsive: dataEl.responsive}
                                }
                            }
                            this[label][currSettingIndex].type = "image"
                            this[label][currSettingIndex].options = settingOption
                        }
                    }
                }
                if (media) {
                    for (let currMediaIndex in media) {
                        const mediaArray = media[currMediaIndex].split("|");
                        if (mediaArray[0] === "image") {
                            let imageUrl = mediaArray[1]
                            if (mediaArray[1].substr(0,5) === "/cdn/") {
                                imageUrl = imageServerUrl() + mediaArray[1]
                            }
                            let imageSettings = {}
                            if (defaultSettings[currMediaIndex]) {
                                const defaultImageSettings = defaultSettings[currMediaIndex]
                                for (currSetting in defaultImageSettings.elements) {
                                    const settingData = defaultImageSettings.elements[currSetting].data
                                    imageSettings[settingData.label] = {value: settingData.default, responsive: settingData.responsive}
                                }
                            }
                            let customSettings = JSON.parse(mediaArray[2])
                            for (currSettingIndex in imageSettings){
                                if (customSettings[currSettingIndex]) {
                                    imageSettings[currSettingIndex].value = customSettings[currSettingIndex]
                                }
                            }
                            ezLog(imageSettings, "imageSettings")
                            this[label][currMediaIndex] = {
                                url: imageUrl,
                                type: mediaArray[0],
                                options: imageSettings
                            }
                        } else if (mediaArray[0] === "color") {
                            let color = mediaArray[1]
                            this[label][currMediaIndex] = {
                                color: color,
                                type: mediaArray[0],
                                options: JSON.parse(mediaArray[2])
                            }
                        } else if (mediaArray[0] === "gradient") {
                            let gradient = mediaArray[1]                            
                            this[label][currMediaIndex] = {
                                gradient: gradient,
                                type: mediaArray[0],
                                options: JSON.parse(mediaArray[2])
                            }
                        }
                    }
                }
                this.$forceUpdate()
            },
            registerDomEl: function() {
                this.webDomEl = document.getElementById("excell-site-app-4");
                if (this.webDomEl == null) {
                    const self = this;
                    setTimeout(function() {
                        self.registerDomEl()
                    },20);
                    return;
                }
                this.screenSizeUpdate({width: document.getElementsByClassName("app-card")[0].offsetWidth})
            },
            openLogin: function()
            {
                this.loginOpen = true;
                this.menuOpen = false;
            },
            loadCardModules: function()
            {
                let self = this;
                
                setTimeout(function() {
                    self.loadModuleClasses();
                    self.loadModules();
                }
                , 50);
            },
            loadModuleClasses: function()
            {
                let modulesByClass = [];
                this.cardModulesByClass = [];

                if (typeof this.entity.Modules === "undefined" || !his.entity.Modules) {
                    return;
                }
                
                for (let currModule of this.entity.Modules) {
                    if (typeof modulesByClass[currModule.module_class] === "undefined") { modulesByClass[currModule.module_class] = {module_label: this.ucWords(currModule.module_class), module_tools: []}};
                    modulesByClass[currModule.module_class].module_tools.push({id:currModule.instance_uuid, logo: currModule.logo});
                }

                for (let currModuleIndex in modulesByClass) {
                    this.cardModulesByClass.push(modulesByClass[currModuleIndex]);
                }
                
                this.$forceUpdate();
            },
            renderModuleCssClass: function(className)
            {
                switch(className) {
                    case "Communication": return "fas fa-comments";
                    case "Real Estate": return "fas fa-sign";
                    case "Construction": return "fas fa-hammer";
                    case "Insurance": return "fas fa-house-damage";
                    case "Business": return "fas fa-business-time";
                }
            },
            buildModuleIds: function()
            {
                if (typeof this.entity.Modules === "undefined" || !his.entity.Modules) {
                    return;
                }
                
                let moduleIds = [];
                for (let currModule of this.entity.Modules) {
                    moduleIds.push(currModule.instance_uuid);
                }
                
                return moduleIds.join("|");
            },
            loadModules: function(index)
            {
                if (typeof this.entity.Modules === "undefined" || !his.entity.Modules) {
                    return;
                }
                
                let self = this;
                const url = "modules/widget/card-widget?id=" + self.entity.card_id + "&modules=" + this.buildModuleIds();
                
                return;
            
                ajax.Get(url, null, function(result) {
                    for (let currToolIndex in result.response.data) {
                        for (let currModuleIndex in self.cardModulesByClass) {
                            for (let currModuleToolIndex in self.cardModulesByClass[currModuleIndex].module_tools) {
                                if (currToolIndex === self.cardModulesByClass[currModuleIndex].module_tools[currModuleToolIndex].id) {
                                    //self.cardModulesByClass[currModuleIndex].module_tools[currModuleToolIndex].
                                }
                            }
                        }
                    }
                    // invisibleCardModule

                    self.$forceUpdate();
                });
            },
            updatePageData: function(page) {
                for (let currPageIndex in this.entity.Tabs) {
                    if (this.entity.Tabs[currPageIndex].card_tab_id === page.card_tab_id) {
                        this.entity.Tabs[currPageIndex] = page;
                    }
                }
            },
            isCustomDomain: function() 
            {
                if (typeof this.entity.card_domain !== "undefined" && this.entity.card_domain !== null && this.entity.card_domain !== "") {
                    return window.location.hostname === this.entity.card_domain
                }
                return false
            },'.'
            loadActivePage: function(useModal)
            {
                const self = this;
                this.pageRootPathIndex = self.isCustomDomain() ? 0 : 1;
                this.requestUri = window.location.pathname.split("/")
                this.requestUri.shift()
                for (let pageIndex = 0; pageIndex < this.pageRootPathIndex; pageIndex++) {
                    this.requestUri.shift()
                }
                this.requestUriPath = this.requestUri[0]
                this.requestUriFullPath = this.requestUri.join("/")
                const pages = self.entity.Tabs;
                
                if (!self.siteIsLoadedInEditor()) {
                    if (typeof this.requestUriPath !== "undefined" && this.requestUriPath !== "") {
                        for (currPageIndex in self.entity.Tabs) {
                            if (pages[currPageIndex].card_tab_rel_url === this.requestUriPath || pages[currPageIndex].url === this.requestUriPath) {
                                this.loadPage(currPageIndex, false, function(page) {
                                    self.openSitePage(page, useModal)
                                });
                            }
                        }
                    } else {
                        if (self.pageDisplayMultiStyle !== true) {
                            this.loadPage(0, false, function(page) {
                                self.openSitePage(page, useModal)
                            });
                        }
                    }
                } else {
                    let activeEditorPageId = sessionStorage.getItem(\'active_editor_page_\' + this.entity.card_id);
                    ezLog(\'active_editor_page_\' + this.entity.card_id,"CardID");
                    ezLog(activeEditorPageId, "activeEditorPageId");
                    activeEditorPageId = activeEditorPageId ? activeEditorPageId : self.entity.Tabs[0].card_tab_id;
                    for (currPageIndex in self.entity.Tabs) {
                        if (pages[currPageIndex].card_tab_id == activeEditorPageId) {

                            this.loadPage(currPageIndex, false, function(page) {
                                self.openSitePage(page, useModal)
                            });
                        }
                    }
                }
            },
            loadPage: function(index, recursive, callback) {
                const self = this;
                let page = self.entity.Tabs[index];
                ezLog(page, "loadPage");
                if (!page) return;
                
                const url = "cards/card-data/get-card-page-data?card_tab_rel_id=" + page.card_tab_rel_id + "&card_id=" + self.entity.card_id;
                self.entity.Tabs[index].loadingPage = true;
                
                ajax.Get(url, null, function(result) {
                    self.entity.Tabs[index].loadingPage = false;
                    try {
                        self.entity.Tabs[index].content = result.response.data.content;
                        self.entity.Tabs[index].hasDataLoaded = true;
                    } catch(err) {
                        console.log(err)
                        console.log(result)
                        self.entity.Tabs[index].content = "Error loading Page: " + err;
                    }
        
                    if (recursive && (index + 1 ) < self.entity.Tabs.length) {
                        setTimeout(function()  {
                            self.loadPage(index + 1, recursive, callback);
                        } ,20)
                        return
                    }
                    
                    if (typeof callback === "function") {
                        callback(self.entity.Tabs[index])
                    }
                });
            },
            batchLoadPages: function(useModal)
            {
                let self = this;
                this.loadActivePage(useModal);
                setTimeout(function() {
                    self.loadPageBatchFunc(0, function() {
                        if (self.siteIsLoadedInEditor()) {
                            self.$parent.$parent.updateSitePagesFromCard(self.entity.Tabs)
                        }
                    });
                }
                , 50);
            },
            loadPageBatchFunc: function(index, callback)
            {
                let self = this;
                if (typeof self.entity.Tabs[index] === "undefined") return;
                
                if (self.entity.Tabs[index].hasDataLoaded === true || typeof self.entity.Tabs[index].loadingPage !== "undefined")
                {
                    setTimeout(function() 
                    {
                        self.loadPage(index + 1, true, callback);
                    } ,20);
                    
                    return;
                }
                
                this.loadPage(index, true, callback);
            },
            updatePageMenuTitle: function(e) {
                if (!this.siteIsLoadedInEditor()) return;
                if (this.activePage.card_tab_rel_title) {
                    if (this.activePage.card_tab_rel_menu_title === this.activePage.card_tab_rel_title) {
                        this.activePage.card_tab_rel_menu_title = this.activeTitle;
                    }
                    this.activePage.card_tab_rel_title = this.activeTitle;
                } else {
                    if (this.activePage.menu_title === this.activePage.title) {
                        this.activePage.menu_title = this.activeTitle;
                    }
                    this.activePage.title = this.activeTitle;
                }
                this.resizeMe(e);
            },
            updatePageTitle: function() {
                if (!this.siteIsLoadedInEditor()) return;
                const url = "/api/v1/cards/update-site-page-rel-profile?site_id=" + this.entity.card_id + "&site_page_id=" + this.entity.card_tab_id + "&site_page_rel_id=" + this.entity.card_tab_rel_id;
                const sitePageRelData = { title: this.activePage.title }
                ajax.Post(url, sitePageRelData, function(result) {
                    ezLog(result,"sitePageRelDataResult");
                });
            },
            forceEditorUpdate: function() {
                if (!this.siteIsLoadedInEditor()) return;
                this.$parent.$parent.saveContentChanges(true);
            },
            loadCardIntoContacts: function()
            {
                window.open("' . $app->objCustomPlatform->getFullPublicDomainName() . '/api/v1/cards/download-vcard?card_id=" + this.entity.card_num, "_blank");
            },
            openSitePageByRel: function(num) {
                for (let currPageIndex in this.cardPages) {
                    if (this.cardPages[currPageIndex].rel_sort_order === num) this.openSitePage(this.cardPages[currPageIndex])
                }
            },
            openSitePage: function(page, useModal)
            {
                const self = this
                self.menuOpen = false
                const firstLoad = self.activePage ? false : true;
                const reload = self.activePage ? page.card_tab_id == self.activePage.card_tab_id : false;
                
                if (reload && this.pageDisplayMultiStyle) {
                    this.closeSitePage(page, useModal)                    
                    return;
                }
                
                const reloadOptions = {reload: reload, firstLoad: firstLoad};
                if ((typeof useModal === "undefined" || useModal === true) && this.pageDisplayMultiStyle !== true) {
                    modal.EngageFloatShield(function() {
                        self.prePageAssignment(page, reloadOptions, function(prePage) {
                            self.assignPageData(prePage, reloadOptions, function(postPage) {
                                self.postPageAssignment(postPage, reloadOptions, function() {
                                    modal.CloseFloatShield();
                                });
                            }, 1);
                        });
                    });
                } else {
                    self.prePageAssignment(page, reloadOptions, function(prePage) {
                        self.assignPageData(prePage, reloadOptions, function(postPage) {
                            self.postPageAssignment(postPage, reloadOptions, function() {
                            });
                        }, 1);
                    });
                }
            },
            closeSitePage: function(page, useModal)
            {
                const self = this
                this.prePageAssignment(page, {}, function(prePage) {
                    self.activePage = null;
                })
                dispatch.broadcast("update_active_page_in_card_editor", {activePage: null});
                if (!self.siteIsLoadedInEditor()) {
                    if (!self.isCustomDomain()) {
                        appHistory.pushState("/" +self.entity.card_num, "website-page", null, page.card_id);
                    } else {
                        appHistory.pushState("/", "website-page", null, page.card_id);
                    }
                } else {
                    sessionStorage.setItem(\'active_editor_page_\' + self.entity.card_id, null);
                }
            },
            assignPageData: function(page, options, callback, attempt)
            {
                const self = this
                const cardPage = self.$refs.' . $vueComponent->getCardPage()->getDynRef() . '
                
                if ( typeof cardPage === "undefined" && attempt < 5) {
                    attempt++;
                    setTimeout(function() {
                        self.assignPageData(page, options, callback, attempt)
                    },10)
                    return
                } 
                
                if (attempt >= 5) {
                    if (typeof callback === "function") callback(page);
                    return;
                }
                
                self.activePage = page
                self.activeTitle = page.card_tab_rel_title ? page.card_tab_rel_title : page.title
                self.activeUrl = page.card_tab_rel_url ? page.card_tab_rel_url : page.url
                
                if (self.pageDisplayMultiStyle === true) {
                    const dynamicContent = document.getElementById("dynamicPageComponent")
                    const dynamicPageSlot = document.getElementById("content_" + page.card_tab_rel_id)
                    dynamicPageSlot.appendChild(dynamicContent)
                    dynamicContent.style.display = "block"
                }
                
                cardPage.activatePage(page, options.reload)
                
                dispatch.broadcast("update_active_page_in_card_editor", {activePage: page});
                self.resizeElementByName("app-page-editor-title-transparent")
                self.screenSizeUpdate({width: document.getElementsByClassName("app-card")[0].offsetWidth});
                if (!options.reload) {
                    if (!self.siteIsLoadedInEditor()) {
                        let activeUrl = page.rel_sort_order > 1 ? self.activeUrl : ""
                        
                        if (self.pageDisplayMultiStyle === true) {
                            activeUrl = self.activeUrl
                        }
                        
                        if (!self.isCustomDomain()) {
                            if (self.pageDisplayMultiStyle !== true) {
                                activeUrl = page.rel_sort_order > 1 ? self.entity.card_num + "/" + activeUrl : self.entity.card_num
                            } else {
                                activeUrl = self.entity.card_num + "/" + activeUrl
                            }
                        }
                        activeUrl = "/" + activeUrl;                        
                        appHistory.pushState(activeUrl, "website-page", page.card_tab_rel_id, page.card_id);
                    } else {
                        sessionStorage.setItem(\'active_editor_page_\' + self.entity.card_id, page.card_tab_id);
                    }
                }
                if (typeof callback === "function") callback(page);
            },
            openCardMenu: function()
            {
                this.menuOpen = true;
            },
            closeCardMenu: function()
            {
                this.menuOpen = false;
            },
            openLogin: function()
            {
                this.loginOpen = true;
                this.menuOpen = false;
            },
            classList: function(element, callback)
            {
                if (typeof callback === "function") {
                    let elm = document.getElementsByClassName(element);
                    for (let currElm of Array.from(elm)) {
                        callback(currElm);
                    }
                    return elm;
                }
                return document.getElementsByClassName(element);
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
            closeModules: function()
            {
                this.modulesOpen = false;
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
                
                let self = this;
                
                this.$parent.$parent.processLoginAuthentication(this.loginUsername, this.loginPassword, this.entity.sys_row_id, function() {
                    self.loginOpen = false;
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
            screenSizeUpdate: function(data)
            {
                this.updateDeviceWidth(data)
                if (typeof this.renderCustomSliderWidth === "function") this.renderCustomSliderWidth()
                if (typeof this.renderCustomSliderHeight === "function") this.renderCustomSliderHeight()
                if (typeof this.renderSidebarWidth === "function") this.renderSidebarWidth()
            },
            updateDeviceWidth: function(data) {
                this.deviceWidth = "desktop"
                if (data.width <= 400) {
                    this.deviceWidth = "mobile"
                } else if (data.width <= 850) {
                    this.deviceWidth = "tablet"
                } 
            },
            getUserAvatar: function()
            {
                if (typeof this.entity.user_avatar === "undefined")
                {
                    return "' . $app->objCustomPlatform->getFullPortalDomainName() . '/_ez/images/users/no-user.jpg";
                }
                return this.entity.user_avatar;
            },
            ucWords: function(str) {
                return str.replace("_"," ").replace(/\w\S*/g, function (txt) {
                    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                });
            },
            registerHistory: function() {                
                if (this.isLoggedIn !== "active") return;
                
                const url = "api/v1/cards/register-card-in-history";
                const postData = {
                    card_id: this.entity.card_id,
                    user_id: this.authUserId
                };
                
                ajax.Post(url, postData, function(result) {
                });
            },
            toggleSearch: function() {
                if (typeof this.$parent.$parent.toggleSearch !== "function") return;
                this.$parent.$parent.toggleSearch()
            },
            toggleLogin: function() {
                this.memberLoginModel()
            },
            goToAccount: function() {
                const self = this;
                if (typeof this.$parent.$parent.toggleLogin !== "function") { 
                    const views = globalClassList("app-component-public")
                    views[0].classList.remove("onPublicSite")
                    setTimeout(function() {
                        ezLog(self, "digitalCard")
                        self.openAccountView()
                    }, 500);
                    return;
                }
                this.$parent.$parent.toggleLogin()
            },
            openAccountView: function() {
                const self = this;
                self.cardView = "members"
                setTimeout(function() {
                    const viewMember = globalClassList("app-component-members")
                    if (typeof viewMember[0] === "undefined") {
                        self.openAccountView()
                        return;
                    }
                    viewMember[0].classList.add("inAccount")
                    if (!self.siteIsLoadedInEditor()) {
                        let activeUrl = "account"
                        if (!self.isCustomDomain()) {
                            activeUrl = self.entity.card_num + "/" + activeUrl
                        }
                        activeUrl = "/" + activeUrl;
                        appHistory.pushState(activeUrl, "website-page", 0, self.entity.card_id);
                    } else {
                        sessionStorage.setItem(\'active_editor_siteView_\' + self.entity.card_id, "members");
                    }
                }, 50);
            },
            goToSite: function() {
                const self = this;
                if (typeof this.$parent.$parent.toggleLogin !== "function") { 
                    const viewMember = globalClassList("app-component-members")
                    viewMember[0].classList.remove("inAccount")
                    setTimeout(function() {
                        self.cardView = "public"
                        setTimeout(function() {
                            const views = globalClassList("app-component-public")
                            views[0].classList.add("onPublicSite")
                            self.openSitePage(self.activePage, false)
                            if (!self.siteIsLoadedInEditor()) {
                                let activeUrl = self.activePage.rel_sort_order > 1 ? self.activeUrl : ""
                                if (self.pageDisplayMultiStyle === true) {
                                    activeUrl = self.activeUrl
                                }
                                
                                if (!self.isCustomDomain()) {
                                    if (self.pageDisplayMultiStyle !== true) {
                                        activeUrl = page.rel_sort_order > 1 ? self.entity.card_num + "/" + activeUrl : self.entity.card_num
                                    } else {
                                        activeUrl = self.entity.card_num + "/" + activeUrl
                                    }
                                }
                                activeUrl = "/" + activeUrl;
                                appHistory.pushState(activeUrl, "website-page", self.activePage.card_tab_rel_id, self.entity.card_id);
                            } else {
                                sessionStorage.setItem(\'active_editor_siteView_\' + self.entity.card_id, "public");
                            }
                        }, 20);
                    }, 500);
                    return;
                }
            },
            siteIsPublic: function() {
                const self = this
                return sessionStorage.getItem(\'active_editor_siteView_\' + self.entity.card_id) !== "members"
            },
            siteIsPortal: function() {
                const self = this
                return sessionStorage.getItem(\'active_editor_siteView_\' + self.entity.card_id) === "members"
            },
            toggleCart: function() {
                if (typeof this.$parent.$parent.toggleCart !== "function") return;
                this.$parent.$parent.toggleCart()
            },
            resizeMe: function(e) {
                this.resizeElement(e.target)
            },
            resizeElementByName: function(name) {
                const self = this;
                
                if (document.getElementById(name) === null) {
                    setTimeout(function() {
                        self.resizeElementByName(name)
                    }, 20)
                } 
                
                this.resizeElement(document.getElementById(name))
            },
            resizeElement: function(el) {
                if (el === null) return;
                setTimeout(function() {
                    const mirror = document.getElementById("pageTitleMirror")
                    if (mirror === null) return;
                    mirror.setAttribute("aria-hidden", "true")
                    mirror.innerHTML = el.value.replace(/ /g,"&nbsp;")
                    el.style.width = ((mirror.offsetWidth > 114 ? mirror.offsetWidth : 114) +4) + "px"
                    el.style.paddingLeft = ((mirror.offsetWidth > 114 ? 15 : mirror.offsetWidth/7.6)) + "px"
                    el.style.paddingRight = ((mirror.offsetWidth > 114 ? 15 : mirror.offsetWidth/7.6)) + "px"           
                }, 20)
            },
            editPages: function(page) {
                let cardPages = []
                let pageDisplayMultiStyle = this.pageDisplayMultiStyle
                ' . $vueComponent->activateDynamicComponentByIdInModal(ManageCardPagesWidget::getStaticId(), "", "edit", "this.entity", "cardPages", ["pageDisplayMultiStyle" => "pageDisplayMultiStyle"], "this", true) . '
                return
            },
            editPage: function(page) {
                let cardPages = []
                let mainSiteModules = this.entity.AvailablePublicModules ? this.entity.AvailablePublicModules : [];
                let pageDisplayMultiStyle = this.pageDisplayMultiStyle
                ' . $vueComponent->activateDynamicComponentByIdInModal(ManageSitePageWidget::getStaticId(), "", "edit", "page", "cardPages", ["siteModules" => "mainSiteModules", "pageDisplayMultiStyle" => "pageDisplayMultiStyle"], "this", true) . '
                return
            },
            editImage: function(page) {
                let cardPages = []
                ' . $vueComponent->activateDynamicComponentByIdInModal(BackgroundProfileWidget::getStaticId(), "", "edit", "this.entity", "cardPages", ["label" => "page", "color" => "true", "gradient" => "true", "images" => "true"], "this", true) . '
                return
            },
            editLogo: function(page) {
                let cardPages = []
                ' . $vueComponent->activateDynamicComponentByIdInModal(LogoProfileWidget::getStaticId(), "", "edit", "this.entity", "cardPages", ["type" => "'logos'", "label" => "page"], "this", true) . '
                return
            },
            editFooterHighlightPage: function(page) {
                let cardPages = []
                ' . $vueComponent->activateDynamicComponentByIdInModal(ManagePageHighlightWidget::getStaticId(), "", "edit", "this.entity", "cardPages", ["type" => "'logos'", "label" => "page"], "this", true) . '
                return
            },
            editFooterShowcasePage: function(page) {
                let cardPages = []
                ' . $vueComponent->activateDynamicComponentByIdInModal(ManagePagesShowcaseWidget::getStaticId(), "", "edit", "this.entity", "cardPages", ["type" => "'logos'", "label" => "page"], "this", true) . '
                return
            },
            renderMicroSiteLogo: function(label) 
            {
                if (typeof this.siteLogos[label] !== "undefined" && this.siteLogos[label] !== null) {
                    switch(this.siteLogos[label].type) {
                        case "image":
                            return this.siteLogos[label].url
                    }
                }
                
                return "/website/images/maxr-app-logo-title.png"
            },
            renderMicroSiteLogoStyle: function(label, cssType) 
            {
                if (typeof this.siteLogos[label] !== "undefined" && this.siteLogos[label] !== null) {
                    switch(this.siteLogos[label].type) {
                        case "image":
                            if (this.siteLogos[label].options[cssType] && this.siteLogos[label].options[cssType].responsive[this.deviceWidth]) {
                                return this.siteLogos[label].options[cssType].responsive[this.deviceWidth]
                            }
                            return this.siteLogos[label].options[cssType] ? this.siteLogos[label].options[cssType].value : "auto"
                    }
                }
                
                return "auto"
            },
            renderMicroSiteLogoCss: function(label) 
            {
                if (typeof this.siteLogos[label] !== "undefined" && this.siteLogos[label] !== null) {
                    switch(this.siteLogos[label].type) {
                        case "image":
                            let logoOptions = {}
                            if (this.siteLogos[label].options) {
                                for (currOptionIndex in this.siteLogos[label].options) {
                                    if (["width","height","top","left"].includes(currOptionIndex)) {
                                        if (this.siteLogos[label].options[currOptionIndex] && this.siteLogos[label].options[currOptionIndex].responsive[this.deviceWidth]) {
                                            logoOptions[currOptionIndex] = this.siteLogos[label].options[currOptionIndex].responsive[this.deviceWidth] + "px"
                                        } else {
                                            logoOptions[currOptionIndex] = this.siteLogos[label].options[currOptionIndex].value + "px"
                                        }
                                    }
                                }
                            }
                            ezLog(logoOptions, "logoOptions")
                            return logoOptions
                    }
                }   
                             
                return {}
            },
            reloadSiteConfiguration: function(data) {
                let defaultSettings = _.clone(this.entity.Template.data ? this.entity.Template.data : [])
                let customSettings = _.clone(this.entity.Settings.theme_config ? this.entity.Settings.theme_config : [])
                this.siteSettings = []
                
                for (currSettingIndex in defaultSettings) {
                    if (defaultSettings[currSettingIndex].type !== "theme") continue
                    if (customSettings[currSettingIndex] && customSettings[currSettingIndex].elements) {
                        for (currSettingElementIndex in defaultSettings[currSettingIndex].elements) {
                            const currElement = customSettings[currSettingIndex].elements[currSettingElementIndex]
                            if (currElement.data.default) {
                                defaultSettings[currSettingIndex].elements[currSettingElementIndex].data.default = currElement.data.default
                            }
                            if (currElement.data.responsive.tablet) {
                                defaultSettings[currSettingIndex].elements[currSettingElementIndex].data.responsive.tablet = currElement.data.responsive.tablet
                            }
                            if (currElement.data.responsive.mobile) {
                                defaultSettings[currSettingIndex].elements[currSettingElementIndex].data.responsive.mobile = currElement.data.responsive.mobile
                            }
                        }
                    }
                }
                
                for (currSettingIndex in defaultSettings) {
                    if (defaultSettings[currSettingIndex].type !== "theme") continue
                    if (defaultSettings[currSettingIndex] && defaultSettings[currSettingIndex].elements) {
                        for (currSettingElementIndex in defaultSettings[currSettingIndex].elements) {
                            let currEl = defaultSettings[currSettingIndex].elements[currSettingElementIndex]
                            if (currEl.data.default) {
                                if (!this.siteSettings[currEl.label]) {
                                    this.siteSettings[currEl.label] = []
                                }
                                this.siteSettings[currEl.label][currEl.data.label]  = {label: currEl.data.label, value: currEl.data.default, responsive: currEl.data.responsive}
                            }
                        }
                    }
                }
            },
            getThemeSettingStyles: function(image) 
            {
                if (!this.siteSettings[image]) return {}
                let elementSetting = {}
                
                for (currCss in this.siteSettings[image]) {
                    if (this.siteSettings[image][currCss].responsive && this.siteSettings[image][currCss].responsive[this.deviceWidth]) {
                        elementSetting[currCss] = this.siteSettings[image][currCss].responsive[this.deviceWidth] + "px"
                    } else {
                        elementSetting[currCss] = this.siteSettings[image][currCss].value + "px"
                    }
                }

                return elementSetting
            },
            renderBackgroundMedia: function(image, otherStyles, defaultStyle) 
            {
                if (!defaultStyle) defaultStyle = {}
                if (!otherStyles) otherStyles = {}
                
                otherStyles = Object.assign(this.getThemeSettingStyles(image), otherStyles, defaultStyle)
                
                if (typeof this.siteMedia[image] !== "undefined" && this.siteMedia[image] !== null) {
                    console.log(this.siteMedia[image]);
                    switch(this.siteMedia[image].type) {
                        case "image":
                            otherStyles.backgroundPositionX = "center"
                            otherStyles.backgroundPositionY = "center"
                            otherStyles.backgroundRepeat = "repeat"
                            otherStyles.backgroundSize = "cover"
                            
                            if (this.siteMedia[image].options.imagePositionX) {
                                otherStyles.backgroundPositionX = this.siteMedia[image].options.imagePositionX.toLowerCase()
                            }
                            if (this.siteMedia[image].options.imagePositionY) {
                                otherStyles.backgroundPositionY = this.siteMedia[image].options.imagePositionY.toLowerCase()
                            }
                            if (this.siteMedia[image].options.imageRepeat) {
                                otherStyles.backgroundRepeat = this.siteMedia[image].options.imageRepeat.toLowerCase()
                            }
                            if (this.siteMedia[image].options.imageBackgroundBlend) {
                                otherStyles.backgroundBlendMode = this.siteMedia[image].options.imageBackgroundBlend.toLowerCase()
                            }
                            if (this.siteMedia[image].options.imageBackgroundSize) {
                                otherStyles.backgroundSize = this.siteMedia[image].options.imageBackgroundSize.toLowerCase()
                            }
                        
                            return Object.assign(
                                otherStyles,
                                {background: "url(" + this.siteMedia[image].url + ")"}
                            )
                        case "color":
                            return Object.assign(
                                otherStyles,
                                {backgroundColor: this.siteMedia[image].color}
                            )
                        case "gradient":
                            return Object.assign(
                                otherStyles,
                                {backgroundImage: this.siteMedia[image].gradient}
                            )
                    }
                }
                return otherStyles ?? {}
            },
            renderMenuTitle: function(page) {
                if (this.pageDisplayMultiStyle === true) { 
                    return page.card_tab_rel_title ? page.card_tab_rel_title : page.title
                } else if (page.rel_sort_order > 1) { 
                    return page.card_tab_rel_menu_title ? page.card_tab_rel_menu_title : page.menu_title  
                }
                return "Home"
            },
            openHighlightPage: function(event) {
                event.preventDefault()
                let cardPages = this.cardPages
                if (cardPages === null) return false
                for (const currPage in cardPages) {
                    if (cardPages[currPage].rel_sort_order > 1) {
                        this.openSitePage(cardPages[currPage])
                        return false
                    }
                }
                return false
            },
            renderPageContentSnipIt: function(pageData) {
                if (!pageData.content) return ""
                let div = document.createElement("div");
                div.innerHTML = atob(pageData.content);
                const summaryText = (div.textContent || div.innerText || "").substr(0,100);
                return summaryText != "" ? (summaryText + "...") : "No Summary Text Found"
            },
            memberLoginModel: function()
            {
                const self = this
                modal.EngageFloatShield(function(shield) {
                    let data = {};
                    data.title = "Member Login"
                    self.getComponentByStaticId("' . MemberLoginWidget::getStaticId() . '","", "view", {}, [], {}, true, true, function(editComponent) {
                        modal.EngagePopUpDialog(data, 550, 250, true, "default", true, editComponent, self, function(widget) {
                        })
                    })
                })
            },
            moveIntoPortal: function(retry)
            {
                const self = this
                setTimeout(function() {
                    const accessPortal = globalClassList("my-account-portal")
                    if (typeof accessPortal[0] === "undefined" && (typeof retry === "undefined" || retry < 10 )) {
                        self.moveIntoPortal(retry ? retry++ : 1);
                        return;
                    }
                    accessPortal[0].click()
                    setTimeout(function() {
                        modal.CloseFloatShield();
                    }, 750);
                }, 50);
            },
            logMemberOutModal: function()
            {
                const self = this
                modal.EngageFloatShield()
                let data = {}
                data.title = "Confirm Log Out"
                data.html = "Do you wish to continue?"
                modal.EngagePopUpConfirmation(data, function() {
                    self.logMemberOut()
                }, 400, 115)
            },
            logMemberOut: function()
            {
                let self = this;
                modal.EngageFloatShield()
                setTimeout(function() {
                    self.$parent.$parent.processSignOut(true)
                    if (!self.siteIsLoadedInEditor()) {
                        let activeUrl = ""
                        if (!self.isCustomDomain()) {
                            activeUrl = self.entity.card_num + "/" + activeUrl
                        }
                        activeUrl = "/" + activeUrl;
                        appHistory.pushState(activeUrl, "website-page", 0, self.entity.card_id);
                    } else {
                        sessionStorage.setItem(\'active_editor_page_\' + self.entity.card_id, page.card_tab_id);
                    }
                    self.goToSite()
                    modal.CloseFloatShield(function() {
                        modal.CloseFloatShield()
                    }, 750);
                }, 500);
            },
        ';
    }

    public static function hydration(VueComponent $vueComponent) : string
    {
        return '
            if (this.initialHydration === false && !this.siteIsLoadedInEditor()) { 
                return; 
            }
            this.initialHydration = false;
            
            let siteId = props.cardId;
            
            if (typeof props.cardId === "undefined") {
                if(typeof this.activeCardId !== "undefined" && this.activeCardId != null) {
                    siteId = this.activeCardId;
                }
            } else {
                this.activeCardId = siteId;
            }
            
            let self = this;
            self.setAuth();
            self.loadSiteData(siteId);
        ';
    }
}