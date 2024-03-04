<?php

namespace Entities\Companies\Components\Vue\PlatformWidget;

use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\VueProps;
use Entities\Cards\Components\Vue\CardWidget\ListCardWidget;
use Entities\Companies\Components\Vue\CardWidget\ListCustomPlatformCardWidget;
use Entities\Companies\Components\Vue\CustomerWidget\ListCustomPlatformCustomerWidget;
use Entities\Companies\Components\Vue\PackageWidget\ListCustomerPlatformPackageWidget;
use Entities\Companies\Components\Vue\UserWidget\ListCustomPlatformUserWidget;
use Entities\Companies\Models\CompanyModel;
use Entities\Modules\Components\Vue\AppsWidget\ListAppsWidget;
use Entities\Packages\Components\Vue\PackageWidget\ListPackageWidget;
use Entities\Users\Components\Vue\UserWidget\ListCustomerWidget;
use Entities\Users\Components\Vue\UserWidget\ListUserWidget;

class ManagePlatformWidget extends VueComponent
{
    protected string $id = "afc846ff-123f-4860-8d95-2cf650a694bf";
    protected string $title = "Custom Platform Dashboard";
    protected string $endpointUriAbstract = "platform-dashboard/{id}";

    public function __construct($defaultEntity = null, array $components = [])
    {
        if ($defaultEntity === null)
        {
            $defaultEntity = (new CompanyModel())
                ->setDefaultSortColumn("company_id", "DESC")
                ->setDisplayColumns(["platform", "company_name", "status", "portal_domain", "public_domain", "owner", "cards", "state", "country", "created_on", "last_updated"])
                ->setRenderColumns(["company_id","platform", "company_name", "status", "portal_domain", "public_domain", "owner", "owner_id", "cards", "state", "country", "created_on", "last_updated", "sys_row_id"]);
        }

        parent::__construct($defaultEntity, $components);

        $mainEntityList = new VueProps("mainEntityList", "array", "mainEntityList");
        $this->addProp($mainEntityList);

        $this->modalTitleForAddEntity = "Add Custom Platform";
        $this->modalTitleForEditEntity = "Edit Custom Platform";
        $this->modalTitleForDeleteEntity = "Delete Custom Platform";
        $this->modalTitleForRowEntity = "View Custom Platform";
    }

    protected function renderComponentDataAssignments() : string
    {
        return "
        dashboardTab: 'profilewidget',
        ";
    }

    protected function renderComponentMethods() : string
    {
        return '
                entityFound: function()
                {
                    return true;
                },
                entityNotFound: function()
                {
                    return false;
                },
                getCardNumUrl: function(result) {
                    
                },
                goToLiveCard: function(result) {
                    
                },
                setDashbaordTab: function(tabName) {
                    this.dashboardTab = tabName;
                    sessionStorage.setItem(\'custom-platform-dashboard-tab\', tabName);
                },
                impersonateCustomer: function(user_id) {
                    let strAuthUrl = "users/impersonate-user?user_id=" + user_id;
                    ajax.Post(strAuthUrl, null, function(objResult) {
                        if(objResult.success == false)
                        {
                            console.log(objResult.message);
                            return;
                        }
            
                        window.location.href = "/account";
                    });
                },
                loadFromUriAbstract: function(id) 
                {
                    this.engageComponentLoadingSpinner();
                    let self = this;
                    this.component_title = this.component_title_original;     
                    this.loadCustomPlatformDataById(id, function(data) {
                        self.disableComponentLoadingSpinner();
                        //self.loadCardPaymentData();
                    });
                },
                loadCustomPlatformDataById: function(id, callback) 
                {
                    let self = this;
                    const url = "api/v1/companies/get-custom-platform-by-uuid?uuid=" + id + "";                    
                    ajax.Get(url, null, function(result)
                    {
                        if (result.success === false || typeof result.response.data === "undefined" || result.response.data.length === 0) 
                        { 
                            self.entityNotFound = true;
                            self.showEntityNotFoundModal();
                            return;
                        }
                        
                        self.entity = result.response.data.customPlatform;
                        self.filterEntityId = self.entity.company_id;
                        self.component_title = self.component_title_original + ": " + self.entity.company_name;
                        
                        let vc = self.findVc(self);
                        vc.reloadComponents("'.$this->getInstanceId().'");
                        
                        self.$forceUpdate();  
                                                                                   
                        if (typeof callback === "function") { callback(result.response.data); }
                    });          
                },
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return '
            this.dashboardTab = sessionStorage.getItem(\'custom-platform-dashboard-tab\');
            
            if (this.dashboardTab === null || (
                this.dashboardTab !== "profilewidget" &&
                this.dashboardTab !== "cards" &&
                this.dashboardTab !== "customers" &&
                this.dashboardTab !== "packages" &&
                this.dashboardTab !== "users" &&
                this.dashboardTab !== "billing"
                )
            ) {
                this.dashboardTab = "profilewidget"; sessionStorage.setItem(\'custom-platform-dashboard-tab\', "profilewidget"); 
            }
            
            this.component_title = this.component_title_original;
            let self = this;
            
            if (this.entity && typeof this.entity.sys_row_id !== "undefined") 
            {
                this.loadCustomPlatformDataById(this.entity.sys_row_id, function(data)
                {                    
                    self.disableComponentLoadingSpinner();
                    //self.loadCardPaymentData();
                });
            }
            else
            {
                this.showNewSelection = true;
            }
        ';
    }

    protected function renderTemplate() : string
    {
        return '
            <div class="formwrapper-manage-entity">
                <!-- 404 here -->
                <div v-if="entity" class="entityDashboard">
                    <table class="table header-table" style="margin-bottom:0px;">
                        <tbody>
                        <tr>
                            <td class="mobile-to-table">
                                <h3 class="account-page-title">
                                <a v-show="hasParent" v-on:click="backToComponent()" id="back-to-entity-list" class="back-to-entity-list pointer"></a> 
                                {{ component_title }}
                                </h3>
                            </td>
                            <td class="mobile-to-table text-right page-count-display dashboard-tab-display" style="vertical-align: middle;">
                                <div data-block="profilewidget" v-on:click="setDashbaordTab(\'profilewidget\')" class="dashboard-tab fas fa-user-circle" v-bind:class="{active: dashboardTab === \'profilewidget\'}"><span>Profile</span></div>
                                <div data-block="users" v-on:click="setDashbaordTab(\'users\')" class="dashboard-tab fas fa-users" v-bind:class="{active: dashboardTab === \'users\'}"><span>Users</span></div>
                                <div data-block="cards" v-on:click="setDashbaordTab(\'cards\')" class="dashboard-tab fas fa-users" v-bind:class="{active: dashboardTab === \'cards\'}"><span>Cards</span></div>
                                <div data-block="customers" v-on:click="setDashbaordTab(\'customers\')" class="dashboard-tab fas fa-users" v-bind:class="{active: dashboardTab === \'customers\'}"><span>Customers</span></div>
                                <div data-block="packages" v-on:click="setDashbaordTab(\'packages\')" class="dashboard-tab fas fa-box-open" v-bind:class="{active: dashboardTab === \'packages\'}"><span>Packages</span></div>
                                <div data-block="billing" v-on:click="setDashbaordTab(\'billing\')" class="dashboard-tab fas fa-credit-card" v-bind:class="{active: dashboardTab === \'billing\'}"><span>Billing</span></div>
                                <div data-block="activity" v-on:click="setDashbaordTab(\'activity\')" class="dashboard-tab fas fa-credit-card" v-bind:class="{active: dashboardTab === \'activity\'}"><span>Activity</span></div>
                                
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="entityTab" data-tab="profilewidget" v-bind:class="{showTab: dashboardTab === \'profilewidget\'}">
                        <div class="width100 entityDetails">
                            <div class="width50">
                                <div class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-cloud fas-large desktop-30px"></span>
                                        <span class="fas-large">Profile</span>
                                        <span v-on:click="editCardProfile()" class="pointer editEntityButton entityButtonFixInTitle"></span></h4>
                                    <div class="entityDetailsInner cardProfile">
                                        <table>
                                            <tbody>
                                            <tr>
                                                <td>Name: </td>
                                                <td><strong>{{ entity.platform }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>Company: </td>
                                                <td>
                                                    <strong>
                                                        {{ entity.company_name }}
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr v-if="entity.public_domain === entity.portal_domain" class="highlighed-field btn-primary pointer">
                                                <td>Domain: </td>
                                                <td><strong>{{ entity.public_domain }}</strong></td>
                                            </tr>
                                            <tr v-if="entity.public_domain !== entity.portal_domain" class="highlighed-field btn-primary pointer">
                                                <td>Card Domain: </td>
                                                <td><strong>{{ entity.public_domain }}</strong></td>
                                            </tr>
                                            <tr v-if="entity.public_domain !== entity.portal_domain">
                                                <td>Card Portal: </td>
                                                <td><strong>{{ entity.portal_domain }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>Owner: </td>
                                                <td>
                                                    <strong id="entityOwner"><a style="text-decoration: underline;" class="pointer" v-on:click="goToCustomerProfile(entity.owner_id)">{{ entity.owner }}</a></strong>
                                                    <span v-on:click="impersonateCustomer(entity.owner_id)" class="pointer loginUserButton fas fa-sign-in-alt" style="top:-1px;left:3px;"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Status: </td>
                                                <td><strong>{{ entity.status }}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="width50">
                                <div class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-cogs fas-large desktop-30px"></span>
                                        <span class="fas-large">Domains</span>
                                        <span v-on:click="editMainImage()" class="pointer editEntityButton entityButtonFixInTitle"></span>
                                    </h4>
                                    <div class="entityDetailsInner" style="margin-top:5px;">
                                        <div class="divTable widthAuto mobile-to-100">
                                            <div class="divRow">
                                                <div class="divCell mobile-to-table mobile-text-center">
                                                        // Domains Here                                                
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="width100 entityDetails">
                            <div class="width50">
                                <div class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-credit-card fas-large desktop-25px"></span>
                                        <span class="fas-large">Financial Account</span>
                                    </h4>
                                    <div class="entityDetailsInner cardStyles" style="margin-top:5px;">
                                        <div class="divTable">
                                            <div class="divRow">
                                                <div class="divCell desktop-90px mobile-to-table mobile-text-center">
                                                // Here!
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="width50">
                                <div class="card-tile-50" style="min-height: 269px;">
                                    <h4>
                                        <span class="fas fa-cogs fas-large desktop-35px"></span>
                                        <span class="fas-user-circle">Users</span>
                                    </h4>
                                    <div class="entityDetailsInner cardStyles" style="margin-top:5px;">
                                        <div class="divTable">
                                            <div class="divRow">
                                                // List Users
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    <div class="entityTab" data-tab="users" v-bind:class="{showTab: dashboardTab === \'users\'}">
                        <div v-if="entity" class="width100 entityDetails">
                            <div class="card-tile-100">
                                ' . $this->renderRegisteredDynamicComponent(
                                    $this->registerDynamicComponentViaHub(
                                        ListCustomPlatformUserWidget::getStaticId(),
                                        "view",
                                        [
                                            new VueProps("filterEntityId", "object", "entity.company_id"),
                                            new VueProps("filterByEntityValue", "boolean", true),
                                            new VueProps("filterByEntityRefresh", "boolean", true)
                                        ])
                                ) . '
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    <div class="entityTab" data-tab="users" v-bind:class="{showTab: dashboardTab === \'cards\'}">
                        <div v-if="entity" class="width100 entityDetails">
                            <div class="card-tile-100">
                                ' . $this->renderRegisteredDynamicComponent(
                                    $this->registerDynamicComponentViaHub(
                                        ListCustomPlatformCardWidget::getStaticId(),
                                        "view",
                                        [
                                            new VueProps("filterEntityId", "object", "entity.company_id"),
                                            new VueProps("filterByEntityValue", "boolean", true),
                                            new VueProps("filterByEntityRefresh", "boolean", true)
                                        ])
                                ) . '
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    <div class="entityTab" data-tab="users" v-bind:class="{showTab: dashboardTab === \'customers\'}">
                        <div v-if="entity" class="width100 entityDetails">
                            <div class="card-tile-100">
                                ' . $this->renderRegisteredDynamicComponent(
                                    $this->registerDynamicComponentViaHub(
                                        ListCustomPlatformCustomerWidget::getStaticId(),
                                        "view",
                                        [
                                            new VueProps("filterEntityId", "object", "entity.company_id"),
                                            new VueProps("filterByEntityValue", "boolean", true),
                                            new VueProps("filterByEntityRefresh", "boolean", true)
                                        ])
                                ) . '
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    <div class="entityTab" data-tab="users" v-bind:class="{showTab: dashboardTab === \'packages\'}">
                        <div v-if="entity" class="width100 entityDetails">
                            <div class="card-tile-100">
                                ' . $this->renderRegisteredDynamicComponent(
                                    $this->registerDynamicComponentViaHub(
                                        ListCustomerPlatformPackageWidget::getStaticId(),
                                        "view",
                                        [
                                            new VueProps("filterEntityId", "object", "entity.company_id"),
                                            new VueProps("filterByEntityValue", "boolean", true),
                                            new VueProps("filterByEntityRefresh", "boolean", true)
                                        ])
                                ) . '
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                </div>
            </div>
        ';
    }
}