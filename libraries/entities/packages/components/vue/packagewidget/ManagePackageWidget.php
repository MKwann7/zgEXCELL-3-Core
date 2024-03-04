<?php

namespace Entities\Packages\Components\Vue\PackageWidget;

use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\VueProps;
use Entities\Packages\Models\PackageModel;

class ManagePackageWidget extends VueComponent
{
    protected string $id = "e808a5d3-9c6d-4645-8803-b4110e4533cf";
    protected string $title = "Package Dashboard";
    protected $uriPath = "root/platform-dashboard/{id}";

    public function __construct(array $components = [])
    {
        $defaultEntity = (new PackageModel())
            ->setDefaultSortColumn("company_id", "DESC")
            ->setDisplayColumns(["platform", "company_name", "status", "portal_domain", "public_domain", "owner", "cards", "state", "country", "created_on", "last_updated"])
            ->setRenderColumns(["company_id","platform", "company_name", "status", "portal_domain", "public_domain", "owner", "owner_id", "cards", "state", "country", "created_on", "last_updated", "sys_row_id"]);

        parent::__construct($defaultEntity, $components);

        $mainEntityList = new VueProps("mainEntityList", "array", "mainEntityList");
        $this->addProp($mainEntityList);

        $this->modalTitleForAddEntity = "Add Package";
        $this->modalTitleForEditEntity = "Edit Package";
        $this->modalTitleForDeleteEntity = "Delete Package";
        $this->modalTitleForRowEntity = "View Package";
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
                    sessionStorage.setItem(\'dashboard-tab\', tabName);
                },
                impersonateCustomer: function(user_id) {
                    let strAuthUrl = "users/impersonate-user?user_id=" + user_id;
                    ajax.Post(strAuthUrl, null, function(objResult) {
                        if(objResult.success == false)
                        {
                            //alert(objResult.message);
                            console.log(objResult.message);
                            return;
                        }
            
                        window.location.href = "/account";
                    });
                }
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return '
                this.dashboardTab = sessionStorage.getItem(\'dashboard-tab\');
                if (this.dashboardTab === null) this.dashboardTab = "profilewidget"; sessionStorage.setItem(\'dashboard-tab\', "profilewidget");
                
                if (this.entity && typeof this.entity.card_tab_id !== "undefined") 
                {
        //            this.engageModalLoadingSpinner();
        //            let self = this;
        //
        //            ajax.Send("cards/card-data/get-card-tab?card_tab_id=" + this.entity.card_tab_id, null, function(result)
        //            {
        //                if (result.success === false || typeof result.response.data === "undefined" || result.response.data.length === 0) 
        //                { 
        //                    // Throw Error?
        //                }
        //                
        //                const cardData = {
        //                    cardPageId: result.response.data[0].card_tab_id, 
        //                    cardPageType: result.response.data[0].card_tab_type_id, 
        //                    title: result.response.data[0].title, 
        //                    html: atob(result.response.data[0].content), 
        //                    url: result.response.data[0].url, 
        //                    visibility: result.response.data[0].visibility, 
        //                    library: result.response.data[0].library_tab 
        //                };
        //                self.disableModalLoadingSpinner();  
        //            });
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
                                <div data-block="packages" v-on:click="setDashbaordTab(\'packages\')" class="dashboard-tab fas fa-box-open" v-bind:class="{active: dashboardTab === \'packages\'}"><span>Packages</span></div>
                                <div data-block="financial" v-on:click="setDashbaordTab(\'financial\')" class="dashboard-tab fas fa-credit-card" v-bind:class="{active: dashboardTab === \'financial\'}"><span>Financial</span></div>
                                
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
                </div>
            </div>
        ';
    }
}