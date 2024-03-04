<?php

namespace Entities\Directories\Components\Vue\Maxtech\Directorywidget;

use App\Core\App;
use App\Website\Constructs\Breadcrumb;
use App\Website\Constructs\SubPageLinks;
use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\Base\VueCustomMethods;
use App\Website\Vue\Classes\VueProps;
use Entities\Directories\Components\Vue\Directorywidget\Helper\PackageManagementWidget;
use Entities\Directories\Components\Vue\Directorywidget\ManageDirectoryWidget;

class ManageMaxDirectoryWidget extends ManageDirectoryWidget
{
    protected string $id = "626c6d1b-f97e-45aa-ae4b-67af6204ba03";

    protected function loadBreadCrumbs(): VueComponent
    {
        $this->addBreadcrumb(new Breadcrumb("Admin","/account/admin/", "link"));
        $this->addSubPageLink(new SubPageLinks("Active","/account/max-directories", true))
            ->addSubPageLink(new SubPageLinks("Inactive","/account/max-directories/inactive"))
            ->addSubPageLink(new SubPageLinks("Purchase","/account/max-directories/purchase"));
        return $this;
    }

    protected function renderComponentDataAssignments(): string
    {
        return parent::renderComponentDataAssignments() . "
            subDirCount: 0,
            subEventCount: 0,
            activeEventCount: 0,
            attendeesCount: 0,
            upcomingEventsCount: 0,
            mainEntityColumns: ['status', 'avatar', 'name', 'phone', 'email'],
            mainEventsColumns: ['date', 'title', 'type', 'members'],
            mainDirectoryColumns: ['title', 'type', 'members'],
            searchMainQuery: '' ,
            orderKey: 'order',
            sortByType: true,
            source: 'widgetEditor',
            mainEntityPageDisplayCount: 15,
            mainEntityPageTotal: 1,
            mainEntityPageIndex: 1,
            batchEnd: true,
            listLayoutType: 'list',
        ";
    }

    protected function renderComponentMethods() : string
    {
        global $app;
        return VueCustomMethods::renderSortMethods() . '
            ' . $this->renderPaginationMethods() . '
        loadDataById: function(id, callback) 
        {
            let self = this;
            const url = "api/v1/directories/get-directory-by-uuid?uuid=" + id + "&addons=paymentAccount|paymentHistory";                    
            ajax.Get(url, null, function(result) {
                ezLog(result,"result")
                if (result.success === false || result.response.success === false || typeof result.response.data === "undefined" || result.response.data.length === 0) { 
                    self.entityNotFound = true;
                    self.showEntityNotFoundModal();
                    return;
                }
                
                self.entity = result.response.data.directory;
                self.filterEntityId = self.entity.directory_id;
                self.component_title = self.component_title_original + ": " + self.entity.title;
                
                let vc = self.findVc(self);
                vc.reloadComponents("'.$this->getInstanceId().'");
                
                self.$forceUpdate();  
                                                                           
                if (typeof callback === "function") { callback(result.response.data); }
            });          
        },
        loadFromUriAbstract: function(id) 
        {
            this.engageComponentLoadingSpinner();
            let self = this;
            this.component_title = this.component_title_original;
              
            this.loadDataById(id, function(data) {
                self.disableComponentLoadingSpinner();
            });
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
                let data = {title: "Directory Not Found!", html: "Oops. That directory cannot be accessed or doesn\'t exist."};
                
                modal.EngagePopUpAlert(data, function() {
                    modal.CloseFloatShield();
                }, 500, 115, true);
            }, 500);

        },
        setDashboardTab: function(tabName) 
        {
            this.dashboardTab = sessionStorage.getItem(\'directory-dashboard-tab\');
        
            if (this.dashboardTab === null || (
                this.dashboardTab !== "overview" &&
                this.dashboardTab !== "profile" &&
                this.dashboardTab !== "events" &&
                this.dashboardTab !== "sub-directories" &&
                this.dashboardTab !== "billing"
                )
            ) { 
                this.dashboardTab = "overview"; sessionStorage.setItem(\'directory-dashboard-tab\', "overview"); 
            }
        },
        setDashboardTabByTag: function(tabName) 
        {
            this.dashboardTab = tabName;
            sessionStorage.setItem(\'directory-dashboard-tab\', tabName);
        },
        canUserViewCard: function()
        {
            ezLog(this.parentData,"this.parentData");
            if (this.parentData.singleEntity == true && typeof this.parentData.loggedInUser !== "undefined" && !this.userIdMatchesCardUser(this.parentData.loggedInUser.user_id)) return false;
            return true;
        },
        userIdMatchesCardUser: function (userId)
        {
            if (userId == this.entity.owner_id) return true;
            if (userId == this.entity.card_user_id) return true;
            
            return false;
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
        toggleLayoutGrid: function() {
            this.listLayoutType = "grid";
        },
        toggleLayoutList: function() {
            this.listLayoutType = "list";
        },
        goToCardDashboard: function(member) {
            this.listLayoutType = "list";
        },
        deleteMainEntity: function(member) {
            this.listLayoutType = "list";
        },
        renderMemberAvatar: function(member) {
            if (member && member.persona && member.persona.Media && member.persona.Media.avatar) {
                return "url(" + member.persona.Media.avatar + ") no-repeat center center / contain";
            }
            return "url(/_ez/images/users/defaultAvatar.jpg) no-repeat center center / contain";
        },
        editRegistration: function() {
            const self = this
            modal.EngageFloatShield(function(shield) {
                let data = {};
                data.title = "Edit Directory Package"
                let editComponent = self.getComponent("registerForDirectoryComponent","registerForDirectoryComponent", "main", "add", "Loading...", {}, this.directoryMembers, {directoryId: this.directoryId, directoryMembers: this.directoryMembers});
                    modal.EngagePopUpDialog(data, 850, 250, true, "default", true, editComponent, self, function(widget) {
                });
            });
        },
        deleteRegistration: function() {
            
        },
        editFreeRegistration: function() {
            const self = this
            modal.EngageFloatShield(function(shield) {
                let data = {};
                data.title = "Edit Free Directory Package"
                let editComponent = self.getComponent("registerForDirectoryComponent","registerForDirectoryComponent", "main", "add", "Loading...", {}, this.directoryMembers, {directoryId: this.directoryId, directoryMembers: this.directoryMembers});
                    modal.EngagePopUpDialog(data, 850, 250, true, "default", true, editComponent, self, function(widget) {
                });
            });
        },
        addDirectoryPackage: function() {
            const self = this
            modal.EngageFloatShield(function(shield) {
                let data = {};
                data.title = "Add  Directory Package"
                let editComponent = self.getComponent("registerForDirectoryComponent","registerForDirectoryComponent", "main", "add", "Loading...", {}, this.directoryMembers, {directoryId: this.directoryId, directoryMembers: this.directoryMembers});
                    modal.EngagePopUpDialog(data, 850, 250, true, "default", true, editComponent, self, function(widget) {
                });
            });
        },
        addNewEvent: function() {
            const self = this
            modal.EngageFloatShield(function(shield) {
                let data = {};
                data.title = "Add New Event"
                let editComponent = self.getComponent("registerForDirectoryComponent","registerForDirectoryComponent", "main", "add", "Loading...", {}, this.directoryMembers, {directoryId: this.directoryId, directoryMembers: this.directoryMembers});
                    modal.EngagePopUpDialog(data, 850, 250, true, "default", true, editComponent, self, function(widget) {
                });
            });
        },
        addNewDirectory: function() {
            const self = this
            modal.EngageFloatShield(function(shield) {
                let data = {};
                data.title = "Add New Directory"
                let editComponent = self.getComponent("registerForDirectoryComponent","registerForDirectoryComponent", "main", "add", "Loading...", {}, this.directoryMembers, {directoryId: this.directoryId, directoryMembers: this.directoryMembers});
                    modal.EngagePopUpDialog(data, 850, 250, true, "default", true, editComponent, self, function(widget) {
                });
            });
        },
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return '
        this.disableModalLoadingSpinner();
        this.setDashboardTab();
        
        this.component_title = this.component_title_original;
        let self = this;
        
        if (this.entity && typeof self.entity.instance_uuid !== "undefined") 
        {
            this.loadDataById(self.entity.instance_uuid, function(data)
            {
                
            });
        }
        else
        {
        
        }
        ';
    }

    protected function renderTemplate() : string
    {
        /** @var App $app */
        global $app;
        $portalThemeMainColor = $app->objCustomPlatform->getCompanySettings()->FindEntityByValue("label","portal_theme_main_color")->value ?? "006666";
        return '<div class="manangeDirectoryWidget">
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
            </v-style>
            <div class="entityDashboard">
                <table class="table header-table">
                    <tbody>
                    <tr>
                        <td class="mobile-to-table">
                            <h3 class="account-page-title">
                            <a v-show="hasParent" v-on:click="backToComponent()" id="back-to-entity-list" class="fa back-to-entity-list pointer"></a> 
                            {{ component_title }}
                            </h3>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="entityTab flex-column" data-tab="profile">
                    <div class="left-hand-column">
                        <div class="main-site-menu">
                            <div v-for="currMainMenu in mainMenu" class="pointer" v-bind:class="{activeMenuItem: dashboardTab == currMainMenu.tag}" v-on:click="setDashboardTabByTag(currMainMenu.tag)"><span v-bind:class="currMainMenu.icon"></span>{{ currMainMenu.title }}</div>
                        </div>
                    </div>
                    <div class="middle-hand-column">
                        <div class="siteContainer" style="width:100%;height:100%;">
                            <div v-show="dashboardTab === \'overview\'" style="width:100%;height:100%;position:relative;">
                                <div class="pl-3 pr-3">
                                    <span class="pop-up-dialog-main-title-text">Overview</span>
                                    <div class="row mt-3">
                                        <div class="col-sm-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">Directory Membership</h5>
                                                    <p class="card-text">Your directory currently has {{ totalDirectoryMembers }} total members.</p>
                                                    <ul class="list-group">
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            Attached directories:
                                                            <span class="badge badge-primary badge-pill" style="color:white !important;">{{ subDirCount }}</span>
                                                        </li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            Associated events:
                                                            <span class="badge badge-primary badge-pill" style="color:white !important;">{{ subEventCount }}</span>
                                                        </li>
                                                    </ul>
                                                    <div class="mt-3">
                                                        <a href="#" class="card-link">View All Directories</a> <a href="#" class="card-link">View All Events</a> 
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">Active Events</h5>
                                                    <p class="card-text">Your directory currently has {{ activeEventCount }} active events.</p>
                                                    <ul class="list-group">
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            Attending registrants:
                                                            <span class="badge badge-primary badge-pill" style="color:white !important;">{{ attendeesCount }}</span>
                                                        </li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                             Upcoming events:
                                                            <span class="badge badge-primary badge-pill" style="color:white !important;">{{ upcomingEventsCount }}</span>
                                                        </li>
                                                    </ul>
                                                    <div class="mt-3">
                                                        <a href="#" class="card-link">View All Directories</a> <a href="#" class="card-link">View All Events</a> 
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-sm-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">Active Members</h5>
                                                    <p class="card-text">Your directory currently has {{ totalActiveDirectoryMembers }} active members, with {{ totalDirectoryMembers }} total members.</p>
                                                    <div class="fformwrapper-header">
                                                        <table class="entity-list-header-wrapper table header-table" style="margin-bottom:0;margin-left:0;">
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <div class="form-search-box" v-cloak>
                                                                            <table>
                                                                                <tr>
                                                                                    <td>
                                                                                        <select id="entity-search-filter" class="form-control">
                                                                                            <option value="status">Status</option>
                                                                                            <option value="name">Name</option>
                                                                                            <option value="phone">Phone</option>
                                                                                            <option value="email">Email</option>
                                                                                            <option value="everything" selected>Everything</option>
                                                                                        </select>
                                                                                    </td>
                                                                                    <td>
                                                                                        <input id="entity-search-input" v-model="searchMainQuery" class="form-control ml-3" type="text" placeholder="Search..."/>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-right page-count-display" style="vertical-align: middle;">
                                                                        <button v-on:click="prevMainEntityPage()" class="btn prev-btn" :disabled="mainEntityPageIndex == 1">Prev</button>
                                                                        <span class="page-count-display-data">
                                                                            <span>{{ mainEntityPageIndex }}</span> / <span>{{ totalDirectoryMembers }}</span>
                                                                        </span>
                                                                        <button v-on:click="nextMainEntityPage()" class="btn" :disabled="mainEntityPageIndex == totalDirectoryMembers">Next</button>
                                                                        <span>
                                                                            <span v-bind:class="{active: listLayoutType === \'grid\'}" v-on:click="toggleLayoutGrid" class="fas fa-th pointer"></span>
                                                                            <span v-bind:class="{active: listLayoutType === \'list\'}" v-on:click="toggleLayoutList" class="fas fa-list pointer"></span>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="entityListOuter" v-bind:class="{tableGridLayout: listLayoutType === \'grid\'}">
                                                        <table class="card-list-outer table table-striped entityList">
                                                            <thead>
                                                            <th v-for="mainEntityColumn in mainEntityColumns">
                                                                <a v-on:click="orderByColumn(mainEntityColumn)" v-bind:class="{ active : orderKey == mainEntityColumn, sortasc : sortByType == true, sortdesc : sortByType == false }">
                                                                    {{ mainEntityColumn | ucWords }}
                                                                </a>
                                                            </th>
                                                            <th class="text-right">
                                                                Actions
                                                            </th>
                                                            </thead>
                                                            <tbody v-if="orderDirectoryPersonas.length > 0">
                                                            <tr v-for="mainEntity in orderDirectoryPersonas">
                                                                <td>{{ mainEntity.status }}</td>
                                                                <td><div v-bind:style="{background: renderMemberAvatar(mainEntity)}" style="width:35px;height:35px"></td>
                                                                <td>{{ mainEntity.persona.Settings.display_name }}</td>
                                                                <td>{{ mainEntity.persona.Settings.contact_phone }}</td>
                                                                <td>{{ mainEntity.persona.Settings.contact_email }}</td>
                                                                <td class="text-right">
                                                                    <span v-on:click="goToCardDashboard(mainEntity)" class="pointer editEntityButton"></span>
                                                                    <span v-on:click="deleteMainEntity(mainEntity)" class="pointer deleteEntityButton"></span>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                            <tbody v-if="orderDirectoryPersonas.length == 0 && batchEnd == true">
                                                                <tr><td colspan="100"><span><span class="fas fa-exclamation-triangle"></span> No members!</span></td></tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-show="dashboardTab === \'profile\'" style="width:100%;height:100%;position:relative;">
                                <div class="pl-3 pr-3">
                                    <span class="pop-up-dialog-main-title-text">Profile</span>
                                    <div class="row mt-3">
                                        <div class="col-sm-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">Directory Information</h5>
                                                    <p class="card-text">Setup your directory here! Control identification information, settings, and restrictions.</p>
                                                    <div>
                                                        <table class="table no-top-border">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="width: 125px; vertical-align: middle;">Title</td> 
                                                                    <td style="width: calc(50% - 125px)">
                                                                        <input v-model="entity.title" type="text" class="form-control">
                                                                    </td> 
                                                                    <td style="width: 125px; vertical-align: middle;">Description</td> 
                                                                    <td style="width: calc(50% - 125px)">
                                                                        <input type="text" class="form-control">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 125px; vertical-align: middle;">Member Approval</td> 
                                                                    <td style="width: calc(50% - 125px)">
                                                                        <select type="text" class="form-control">
                                                                            <option value="display_asc">Automatic</option> 
                                                                            <option value="display_desc">Manual</option> 
                                                                            <option value="company_asc">Manual | Widget Override</option>
                                                                        </select>
                                                                    </td>
                                                                    <td style="width: 125px; vertical-align: middle;">Member Limit</td> 
                                                                    <td style="width: calc(50% - 125px)">
                                                                        <input type="number" class="form-control">
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-sm-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">Packages</h5>
                                                    <p class="card-text">Give your directory some exclusivity!</p>
                                                    <div>
                                                    ' . $this->registerAndRenderDynamicComponent(
                                                        new PackageManagementWidget(),
                                                        "view",
                                                        [
                                                            new VueProps("mainEntity", "object", "entity"),
                                                            new VueProps("filterEntityId", "object", "entity.directory_id"),
                                                            new VueProps("filterByEntityValue", "boolean", true),
                                                            new VueProps("filterByEntityRefresh", "boolean", true)
                                                        ]
                                                    ) . '
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-show="dashboardTab === \'events\'" style="width:100%;height:100%;position:relative;">
                                <div class="pl-3 pr-3">
                                    <div class="d-flex">
                                        <span class="pop-up-dialog-main-title-text">Events</span>
                                        <button class="btn btn-primary ml-3" style="height:35px;margin-top:7px;" v-on:click="addNewEvent">Add New Event</button>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-sm-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">New Registrations</h5>
                                                    <p class="card-text">You have 0 new registrations.</p>
                                                    <div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">Archived</h5>
                                                    <p class="card-text">You have 0 archived events.</p>
                                                    <div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-sm-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">All Events</h5>
                                                    <p class="card-text">You currently have 0 events, with 0 total registrations.</p>
                                                    <div class="fformwrapper-header">
                                                        <table class="entity-list-header-wrapper table header-table" style="margin-bottom:0;margin-left:0;">
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <div class="form-search-box" v-cloak>
                                                                            <table>
                                                                                <tr>
                                                                                    <td>
                                                                                        <select id="entity-search-filter" class="form-control">
                                                                                            <option value="status">Status</option>
                                                                                            <option value="name">Name</option>
                                                                                            <option value="phone">Phone</option>
                                                                                            <option value="email">Email</option>
                                                                                            <option value="everything" selected>Everything</option>
                                                                                        </select>
                                                                                    </td>
                                                                                    <td>
                                                                                        <input id="entity-search-input" v-model="searchMainQuery" class="form-control ml-3" type="text" placeholder="Search..."/>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-right page-count-display" style="vertical-align: middle;">
                                                                        <button v-on:click="prevMainEntityPage()" class="btn prev-btn" :disabled="mainEntityPageIndex == 1">Prev</button>
                                                                        <span class="page-count-display-data">
                                                                            <span>{{ mainEntityPageIndex }}</span> / <span>{{ totalDirectoryMembers }}</span>
                                                                        </span>
                                                                        <button v-on:click="nextMainEntityPage()" class="btn" :disabled="mainEntityPageIndex == totalDirectoryMembers">Next</button>
                                                                        <span>
                                                                            <span v-bind:class="{active: listLayoutType === \'grid\'}" v-on:click="toggleLayoutGrid" class="fas fa-th pointer"></span>
                                                                            <span v-bind:class="{active: listLayoutType === \'list\'}" v-on:click="toggleLayoutList" class="fas fa-list pointer"></span>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="entityListOuter" v-bind:class="{tableGridLayout: listLayoutType === \'grid\'}">
                                                        <table class="card-list-outer table table-striped entityList">
                                                            <thead>
                                                            <th v-for="mainEventsColumn in mainEventsColumns">
                                                                <a v-on:click="orderByColumn(mainEventsColumn)" v-bind:class="{ active : orderKey == mainEventsColumn, sortasc : sortByType == true, sortdesc : sortByType == false }">
                                                                    {{ mainEventsColumn | ucWords }}
                                                                </a>
                                                            </th>
                                                            <th class="text-right">
                                                                Actions
                                                            </th>
                                                            </thead>
                                                            <tbody v-if="orderDirectoryEvents.length > 0">
                                                            <tr v-for="mainEntity in orderDirectoryEvents">
                                                                <td>{{ mainEntity.status }}</td>
                                                                <td><div v-bind:style="{background: renderMemberAvatar(mainEntity)}" style="width:35px;height:35px"></td>
                                                                <td>{{ mainEntity.persona.Settings.display_name }}</td>
                                                                <td>{{ mainEntity.persona.Settings.contact_phone }}</td>
                                                                <td>{{ mainEntity.persona.Settings.contact_email }}</td>
                                                                <td class="text-right">
                                                                    <span v-on:click="goToCardDashboard(mainEntity)" class="pointer editEntityButton"></span>
                                                                    <span v-on:click="deleteMainEntity(mainEntity)" class="pointer deleteEntityButton"></span>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                            <tbody v-if="orderDirectoryEvents.length == 0 && batchEnd == true">
                                                                <tr><td colspan="100"><span><span class="fas fa-exclamation-triangle"></span> No events!</span></td></tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-show="dashboardTab === \'directories\'" style="width:100%;height:100%;position:relative;">
                                <div class="pl-3 pr-3">
                                    <div class="d-flex">
                                        <span class="pop-up-dialog-main-title-text">Directories</span>
                                        <button class="btn btn-primary ml-3" style="height:35px;margin-top:7px;" v-on:click="addNewDirectory">Add New Directory</button>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-sm-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">New Members</h5>
                                                    <p class="card-text">You have 0 new members. Also, you have 0 new member requests.</p>
                                                    <div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">Archived</h5>
                                                    <p class="card-text">You have 0 archived directories.</p>
                                                    <div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-sm-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">All Sub Directories</h5>
                                                    <p class="card-text">Your have 0 sub directories, with 0 total members.</p>
                                                    <div class="fformwrapper-header">
                                                        <table class="entity-list-header-wrapper table header-table" style="margin-bottom:0;margin-left:0;">
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <div class="form-search-box" v-cloak>
                                                                            <table>
                                                                                <tr>
                                                                                    <td>
                                                                                        <select id="entity-search-filter" class="form-control">
                                                                                            <option value="status">Status</option>
                                                                                            <option value="name">Name</option>
                                                                                            <option value="phone">Phone</option>
                                                                                            <option value="email">Email</option>
                                                                                            <option value="everything" selected>Everything</option>
                                                                                        </select>
                                                                                    </td>
                                                                                    <td>
                                                                                        <input id="entity-search-input" v-model="searchMainQuery" class="form-control ml-3" type="text" placeholder="Search..."/>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-right page-count-display" style="vertical-align: middle;">
                                                                        <button v-on:click="prevMainEntityPage()" class="btn prev-btn" :disabled="mainEntityPageIndex == 1">Prev</button>
                                                                        <span class="page-count-display-data">
                                                                            <span>{{ mainEntityPageIndex }}</span> / <span>{{ totalDirectoryMembers }}</span>
                                                                        </span>
                                                                        <button v-on:click="nextMainEntityPage()" class="btn" :disabled="mainEntityPageIndex == totalDirectoryMembers">Next</button>
                                                                        <span>
                                                                            <span v-bind:class="{active: listLayoutType === \'grid\'}" v-on:click="toggleLayoutGrid" class="fas fa-th pointer"></span>
                                                                            <span v-bind:class="{active: listLayoutType === \'list\'}" v-on:click="toggleLayoutList" class="fas fa-list pointer"></span>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="entityListOuter" v-bind:class="{tableGridLayout: listLayoutType === \'grid\'}">
                                                        <table class="card-list-outer table table-striped entityList">
                                                            <thead>
                                                            <th v-for="mainDirectoryColumn in mainDirectoryColumns">
                                                                <a v-on:click="orderByColumn(mainDirectoryColumn)" v-bind:class="{ active : orderKey == mainDirectoryColumn, sortasc : sortByType == true, sortdesc : sortByType == false }">
                                                                    {{ mainDirectoryColumn | ucWords }}
                                                                </a>
                                                            </th>
                                                            <th class="text-right">
                                                                Actions
                                                            </th>
                                                            </thead>
                                                            <tbody v-if="orderDirectoryEvents.length > 0">
                                                            <tr v-for="mainEntity in orderDirectoryEvents">
                                                                <td>{{ mainEntity.status }}</td>
                                                                <td><div v-bind:style="{background: renderMemberAvatar(mainEntity)}" style="width:35px;height:35px"></td>
                                                                <td>{{ mainEntity.persona.Settings.display_name }}</td>
                                                                <td>{{ mainEntity.persona.Settings.contact_phone }}</td>
                                                                <td>{{ mainEntity.persona.Settings.contact_email }}</td>
                                                                <td class="text-right">
                                                                    <span v-on:click="goToCardDashboard(mainEntity)" class="pointer editEntityButton"></span>
                                                                    <span v-on:click="deleteMainEntity(mainEntity)" class="pointer deleteEntityButton"></span>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                            <tbody v-if="orderDirectoryEvents.length == 0 && batchEnd == true">
                                                                <tr><td colspan="100"><span><span class="fas fa-exclamation-triangle"></span> No events!</span></td></tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-show="dashboardTab === \'billing\'" style="width:100%;height:100%;position:relative;">
                                <div class="pl-3 pr-3">
                                    <span class="pop-up-dialog-main-title-text">Billing</span>
                                    <p>Manage your payment account below and review your billing history.</p>
                                    <hr>
                                    <h4>
                                        <span class="fas fa-credit-card fas-large desktop-30px"></span>
                                        <span class="fas-large">Payment Account</span>
                                    </h4>
                                    <h4 class="account-page-subtitle" style="margin-top: 2rem;">History</h4>
                                </div>
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
        return 'totalDirectoryMembers: function()
                {
                    return (this.entity && this.entity.personas && this.entity.personas.length > 1 ) ? this.entity.personas.length : 1;
                },
                totalActiveDirectoryMembers: function()
                {
                    let activeMembers = 0;
                    if (this.entity && this.entity.personas && this.entity.personas.length >= 1 ) {    
                        for (let currEntity of this.entity.personas) {
                            if (currEntity.status.toLowerCase() === "active") activeMembers++
                        }
                    }
                    
                    return activeMembers
                },
                ' . $this->renderOrderedMainEntityListComputedMethod();
    }

    protected function renderOrderedMainEntityListComputedMethod() : string
    {
        return '
                orderDirectoryPersonas: function()
                {
                    var self = this;

                    let objSorted = this.sortedEntity(this.searchMainQuery, (this.entity && this.entity.personas ? this.entity.personas : []), this.orderKey, this.sortByType, this.mainEntityPageIndex,  this.mainEntityPageDisplayCount, this.mainEntityPageTotal, function(data) {
                        self.mainEntityPageTotal = data.pageTotal;
                        self.mainEntityPageIndex = data.pageIndex;
                    });
                    
                    return objSorted;
                },
                orderDirectoryEvents: function()
                {
                    var self = this;

                    let objSorted = this.sortedEntity(this.searchMainQuery, (this.entity && this.entity.events ? this.entity.events : []), this.orderKey, this.sortByType, this.mainEntityPageIndex,  this.mainEntityPageDisplayCount, this.mainEntityPageTotal, function(data) {
                        self.mainEntityPageTotal = data.pageTotal;
                        self.mainEntityPageIndex = data.pageIndex;
                    });
                    
                    return objSorted;
                },
        ';
    }

    protected function renderPaginationMethods() : string
    {
        return '
            orderByColumn: function(column)
            {
                this.sortByType = ( this.orderKey == column ) ? ! this.sortByType : this.sortByType;
                this.orderKey = column;
            },
            prevMainEntityPage: function()
            {
                this.mainEntityPageIndex--;
                this.entity.personas = this.entity.personas;
            },
            nextMainEntityPage: function()
            {
                this.mainEntityPageIndex++;
                this.entity.personas = this.entity.personas;
            },
        ';
    }
}