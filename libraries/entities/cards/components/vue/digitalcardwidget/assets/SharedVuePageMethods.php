<?php

namespace Entities\Cards\Components\Vue\DigitalCardWidget\Assets;

use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cards\Components\Vue\CardWidget\ManageCardPagesWidget;
use Entities\Cards\Components\Vue\Maxtech\Sitewidget\ManageSitePageWidget;
use Entities\Media\Components\Vue\Gallerywidget\ListImageGalleryWidget;
use Entities\Media\Components\Vue\Gallerywidget\ListLogoGalleryWidget;

class SharedVuePageMethods
{
    public static function dataAssignments(VueComponent $vueComponent) : string
    {
        return "
            entityFound: false,
            page: null,
            pageContentHtml: '',
            editor: false,
            noTitle: false,
            customPage: false,
            dynPageWidgetComponent: null,
        ";
    }

    public static function computed(VueComponent $vueComponent) : string
    {
        return '
            renderCardContent: function() {
                return this.pageContentHtml;
            },
        ';
    }

    public static function methods(VueComponent $vueComponent) : string
    {
        global $app;
        return '
            activatePage: function(content, reload)
            {
                ezLog(this,"ACTIVATING PAGE");
                if (typeof this.page !== "undefined" && this.page !== null && this.page.card_tab_id === content.card_tab_id && reload === false) return;
                
                this.page = content
                
                if (typeof this.$parent.$parent.$parent.parentIsEditor !== "undefined") {
                    this.editor = true
                }
                
                if (this.page && this.page.__app) {
                   this.customPage = true;
                   this.loadPageWidget();
                } else {
                    this.customPage = false;
                    self.dynPageWidgetComponent = null;
                }
                
                ezLog(this.page,"ACTIVATING PAGE CONTENT");
                
                this.insertAndExecute()
            },
            loadPageWidget: function() {
                const self = this;
                let vc = this.findVc(this) 
                vc.loadComponent(this.uuidv4(), this.page.__app.app_uuid + "_" + this.page.__app.widget_page_id, null, "edit", "Loading...", this.page, this.entities, {site_id: this.page.card_id}, true, true, function(component) {
                    self.dynPageWidgetComponent = component.rawInstance;
                    self.hydratePageWidget();
                });
            },
            hydratePageWidget: function(content)
            {
                const self = this;
                if (typeof self.$refs.dynPageWidgetComponentRef === "undefined") {
                    setTimeout(function() {
                        self.hydratePageWidget();
                    },10);
                    return;
                }

                if (typeof self.$refs.dynPageWidgetComponentRef.hydrateComponent !== "function") return;
                
                const settingsRaw = self.page.__app.__settings
                const { __settings, ...tempApp } = self.page.__app
                let settings = {};
                if (this.isIterable(settingsRaw)) {
                    for (currSetting of settingsRaw) {
                        settings[currSetting.label] = currSetting.value;
                    }
                }
                
                const props = {
                    page: self.page, 
                    app: tempApp, 
                    settings: settings, 
                    user: {login: self.isLoggedIn, data: self.user, uuid: self.userId, id: self.userNum},
                    editor: self.editor
                };
                
                self.$refs.dynPageWidgetComponentRef.__props = props
                for (let currPropLabel in props) {
                    self.$refs.dynPageWidgetComponentRef[currPropLabel] = props[currPropLabel];
                }
                self.$refs.dynPageWidgetComponentRef.hydrateComponent(
                    props,
                    true, 
                    function(test) {
                });
            },
            renderPageContent: function(content)
            {
                try {
                    let newContent = atob(content);
                    if (newContent.includes("<p><\/p>")) {
                        const regex = new RegExp("<p><\/p>", "g");
                        newContent = newContent.replace(regex, "")
                    }
                    return newContent
                }
                catch(ex)
                {
                    console.log("base64 conversion error");
                    console.log(ex);
                    return "Error converting string.";
                }
            },
            insertAndExecute: function() 
            {
                if (typeof this.page === "undefined" || this.page.content === null) return;
                this.$forceUpdate()
                this.pageContentHtml = this.renderPageContent(this.page.content)
                
                ezLog(this.pageContentHtml, "this.pageContentHtml");
                
                this.assignContent();
            },
            assignContent: function() {
                const self = this;
                if (document.querySelector(".app-main-comp-body .app-page-content-inner") === null) {
                    setTimeout(function() {
                        self.assignContent();
                    },250);
                    return;
                }
                
                if ( typeof this.$parent.$parent.$parent.parentIsEditor !== "undefined" && typeof this.$parent.$parent.$parent.contentBuilder !== "undefined" && this.$parent.$parent.$parent.contentBuilder !== null) {
                    ezLog(this.pageContentHtml, "LOADING IN BUILDER")
                    this.$parent.$parent.$parent.contentBuilder.loadHtml(this.pageContentHtml);
                } else {
                    ezLog(this.pageContentHtml, "LOADING IN LIVE SITE")
                    ezLog(document.querySelector(".app-main-comp-body .app-page-content-inner"), ".app-card .app-page-content-inner")
                    document.querySelector(".app-main-comp-body .app-page-content-inner").innerHTML = this.pageContentHtml 
                }
            },
            siteIsLoadedInEditor: function() {
                return typeof this.$parent.$parent.$parent.parentIsEditor !== "undefined";
            },
            updatePageTitle: function() {
                if (!this.siteIsLoadedInEditor()) return;
                const url = "/api/v1/cards/update-site-page-rel-profile?site_id=" + this.entityClone.card_id + "&site_page_id=" + this.entityClone.card_tab_id + "&site_page_rel_id=" + this.entityClone.card_tab_rel_id;
                const sitePageRelData = { title: this.activePage.title }
                ajax.Post(url, sitePageRelData, function(result) {
                    ezLog(result,"sitePageRelDataResult");
                });
            },
            forceEditorUpdate: function() {
                if (!this.siteIsLoadedInEditor()) return;
                this.$parent.$parent.$parent.saveContentChanges(true);
            },
            isIterable: function(obj)
            {
                if (obj == null)
                {
                    return false;
                }
        
                return typeof obj[Symbol.iterator] === "function";
            },
            renderLoginText: function()
            {
                if (this.user.login === "active") {
                    return "My Account"
                }
                
                return "Login";
            },
        ';
    }

    public static function hydration(VueComponent $vueComponent) : string
    {
        return '
            this.page = props.cardPage;
            this.noTitle = props.noTitle ?? false;
            this.insertAndExecute();
        ';
    }
}