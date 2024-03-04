<?php

namespace Entities\Cards\Components\Vue\Maxtech\Sitewidget;

use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cards\Models\CardPageModel;

class ManageSitePageWidget extends VueComponent
{
    protected string $id = "5f050afc-de4c-4aa4-b090-f2ebf0d0f01a";
    protected string $modalWidth = "750";
    protected $htmlComponent;

    public function __construct(array $components = [])
    {
        $defaultEntity = (new CardPageModel())
            ->setDefaultSortColumn("card_tab_id", "DESC")
            ->setDisplayColumns(["card_tab_id", "title", "type", "card_count", "created_on", "last_updated"])
            ->setRenderColumns(["card_tab_id", "title", "user_id", "card_count", "type", "created_on", "last_updated", "__app"]);

        parent::__construct($defaultEntity, $components);

        $this->modalTitleForAddEntity = "Add Page Info";
        $this->modalTitleForEditEntity = "Edit Page Info";
        $this->modalTitleForDeleteEntity = "Delete Page Info";
        $this->modalTitleForRowEntity = "View Page Info";
    }

    protected function renderComponentDataAssignments(): string
    {
        return "
            entityClone: {},
            modules: [],
            widgetList: [],
            selectedWidget: null,
            selectedWidgetOriginal: null,
            pageDisplayMultiStyle: false,
        ";
    }

    protected function renderComponentMethods() : string
    {
        return '
            getModules: function() {
                return this.modules
            },
            setModules: function(modules) {
                this.modules = modules
                return this
            },
            loadUserModules: function() {
                let url = "/api/v1/modules/get-user-modules?user_id=" + this.userNum
                ajax.Get(url, null, function(result) {
                    ezLog(result,"Load User Modules");
                    
                });
            },
            submitUpdate: function() {
                const self = this;
                const url = "/api/v1/cards/update-site-page-rel-profile?site_id=" + this.entityClone.card_id + "&site_page_id=" + this.entityClone.card_tab_id + "&site_page_rel_id=" + this.entityClone.card_tab_rel_id;
                const sitePageRelData = {
                    title: this.entityClone.card_tab_rel_title,
                    menu: this.entityClone.card_tab_rel_menu_title,
                    url: this.entityClone.card_tab_rel_url,
                    widget: this.selectedWidget
                }
                modal.EngageFloatShield();
                ajax.Post(url, sitePageRelData, function(result) {
                    self.entity.card_tab_rel_title = self.entityClone.card_tab_rel_title
                    self.entity.card_tab_rel_menu_title = self.entityClone.card_tab_rel_menu_title
                    self.entity.card_tab_rel_url = self.entityClone.card_tab_rel_url
                    dispatch.broadcast("reload_active_page_title", {title: self.entityClone.card_tab_rel_title});
                    if (self.selectedWidgetOriginal !== self.selectedWidget) {
                        dispatch.broadcast("reload_active_page_widget", {widget: self.selectedWidget});
                    }
                    modal.CloseFloatShield(function() {
                        dispatch.broadcast("close_modal");
                    }, 200);
                });
            },
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return '
        this.disableModalLoadingSpinner();
        this.loadUserModules();

        if (this.entity && typeof this.entity.card_tab_id !== "undefined") {
            this.entityClone = _.clone(this.entity);
            if (!this.entityClone.card_tab_rel_title || this.entityClone.card_tab_rel_title === "") {
                this.entityClone.card_tab_rel_title = this.entityClone.title
            }
            if (!this.entityClone.card_tab_rel_menu_title || this.entityClone.card_tab_rel_menu_title === "") {
                this.entityClone.card_tab_rel_menu_title = this.entityClone.menu_title
            }
            if (!this.entityClone.card_tab_rel_url|| this.entityClone.card_tab_rel_url === "") {
                this.entityClone.card_tab_rel_url = this.entityClone.url
            }
            let modules = props.siteModules;
            this.pageDisplayMultiStyle = props.pageDisplayMultiStyle;
            let modulesByWidget = [];
            let currAppId = 0;
            if (modules) {
                for (currModule of modules) {
                    if (currAppId !== currModule.app_id) {
                        currAppId = currModule.app_id
                        let moduleWidgets = [];
                        for(currModuleWidget of modules) {
                            if (currModuleWidget.app_id = currModule.app_id) {
                                moduleWidgets.push({
                                    app_id: currModuleWidget.app_id,
                                    app_instance_id: currModuleWidget.app_instance_id,
                                    app_widget_id: currModuleWidget.app_widget_id,
                                    app_name: currModuleWidget.app_name,
                                    widget_name: currModuleWidget.widget_name,
                                    widget_class: currModuleWidget.widget_class
                                });
                            }
                        }
                        modulesByWidget.push({name: currModule.app_name, app_id:currModule.app_id, widgets: moduleWidgets});
                    }           
                }
            }
            this.widgetList = modulesByWidget;
            if (this.entityClone.__app) {
                this.selectedWidget = this.entityClone.__app.app_instance_id + "_" +this.entityClone.__app.widget_page_id
            } else {
                this.selectedWidget = "contentBuilder";
            }
            this.selectedWidgetOriginal = this.selectedWidget
        } else {
        
        }
        ';
    }

    protected function renderTemplate() : string
    {
        return '<div class="manangeSitePageWidget container">
            <v-style type="text/css">
                .modal-sub-title {
                    font-size: 20px;
                    border-bottom: 1px solid #ccc;
                    padding-bottom: 5px;
                }
            </v-style>
            <div v-if="entityClone">
                <div class="row mb-2">
                    <div class="col-12">
                        <h2 class="modal-sub-title">Page Profile</h2>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-12">
                        <input placeholder="Page Title" class="form-control" v-model="entityClone.card_tab_rel_title">
                    </div>
                </div>
                <div v-if="!pageDisplayMultiStyle" class="row mb-2">
                    <div class="col-6"><input placeholder="Menu Name" class="form-control" v-model="entityClone.card_tab_rel_menu_title"></div>
                    <div class="col-6"><input placeholder="Uri Path" class="form-control" v-model="entityClone.card_tab_rel_url"></div>
                </div>
                <div v-if="pageDisplayMultiStyle" class="row mb-2">
                    <div class="col-12"><input placeholder="Uri Path" class="form-control" v-model="entityClone.card_tab_rel_url"></div>
                </div>
                <div class="row mb-2 mt-4">
                    <div class="col-12">
                        <h2 class="modal-sub-title">Page Widget</h2>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-12">
                        <select class="form-control" v-model="selectedWidget">
                            <option value="">--Select Widget--</option>
                            <optgroup label="Drag & Drop">
                                <option value="contentBuilder">Content Builder Widget</option>
                            </optgroup>
                            <optgroup v-for="currModule in widgetList" v-bind:label="currModule.name + \' App\'">
                                <option v-for="currWidget in currModule.widgets" v-bind:value="currWidget.app_instance_id + \'_\' +currWidget.app_widget_id" selected="">{{ currWidget.widget_class }} | {{ currWidget.widget_name }}</option>
                            </optgroup>
                        </select>
                    </div>
                </div>
                <div class="row mb-2 mt-4">
                    <div class="col-12">
                        <h2 class="modal-sub-title">Page Thumbnail</h2>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-12">
                        <p>This is often seen when referencing the page from another link or widget.</p>
                        <div></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button v-on:click="submitUpdate" class="buttonID9234597e456 btn btn-primary w-100">Update Page Profile</button>
                    </div>
                </div>
            </div>
        </div>
        ';
    }
}