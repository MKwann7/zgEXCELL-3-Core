<?php

namespace Entities\Cards\Components\Vue\ManagementHubWidget\V1;

use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cards\Models\CardModel;

class ManagementHubCardList extends VueComponent
{
    protected $id = "420fb313-9f83-43bd-a48d-e1d930f2511f";
    protected $title = "Card Hub List";
    protected $endpointUriAbstract = "myhub";
    protected $batchCount = 500;
    protected $batchLoadEndpoint = "cards/card-data/get-card-new-batches";

    public function __construct($defaultEntity = null, array $components = [])
    {
        if ($defaultEntity === null)
        {
            $defaultEntity = (new CardModel())
                ->setDefaultSortColumn("card_num", "DESC")
                ->setDisplayColumns(["card_name","card_num","card_vanity_url","card_owner_name","status"])
                ->setFilterColumns(["card_name","card_num","card_vanity_url","card_owner_name","status"])
                ->setRenderColumns(["card_id", "owner_id", "card_owner_name", "card_name", "card_num", "template_id", "card_vanity_url", "card_keyword", "product", "product_id", "card_contacts", "status", "order_line_id", "platform", "company_id", "banner", "favicon", "created_on", "last_updated", "sys_row_id",]);
        }

        parent::__construct($defaultEntity, $components);
    }

    protected function renderComponentDataAssignments() : string
    {
        return '
            batchLoadingUri: "' . $this->batchLoadEndpoint . '",
            batchOffset: 0,
            batchStart: false,
            batchEnd: false,
            mainEntityList: [],
            favorites: [],
            history: [],
            dashboardTab: "cards",
            loginUsername: "",
            loginPassword: "",
            loggedInAttemptError: "",
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return '
            this.dashboardTab = sessionStorage.getItem(\'hub-dashboard-tab\');
            
            if (this.dashboardTab === null || (
                this.dashboardTab !== "favorites" &&
                this.dashboardTab !== "history"
                )
            ) { 
                this.setDashboardTab("cards"); 
            }
            
            this.setAuth();
            this.batchLoadMainEntitiesAwait();
            this.loadHubData();
        ';
    }

    protected function renderComponentMethods() : string
    {
        global $app;
        return '
            jumpToCard: function(card)
            {
                this.$parent.$parent.jumpToCard(card)
            },
            attemptLogin: function()
            {
                if(!this.$parent.$parent.validateUsername(this.loginUsername)) return;
                if(!this.$parent.$parent.validatePassword(this.loginPassword)) return;
                
                const url = "'.$app->objCustomPlatform->getFullPublicDomain().'/process/login/authenticate-login-request";
                                
                let self = this;
                this.$parent.$parent.loginCardUser(
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

                        self.$parent.$parent.registerAuth(result.response.data);                        
                    }, 
                    function(error) 
                    {
                        console.log(error);
                    }
                );
            },
            setAuth: function()
            {
                this.isLoggedIn = this.$parent.$parent.isLoggedIn;
                this.authUserId = this.$parent.$parent.authUserId;
            },
            loadHubData: function()
            {
                const self = this;
                const url = "api/v1/cards/load-my-hub-data?user_id=" + this.authUserId;
                ajax.Get(url, 1, function(result)
                {             
                    if (result.success === false) return;
                    
                    self.favorites = result.response.data.favorites;
                    self.history = result.response.data.history;
                });
            },
            setTypeToFavorites: function()
            {
                this.setDashboardTab(\'favorites\');
            },
            batchLoadMainEntitiesAwait: function()
            {    
                const self = this;  
                   
                if (self.isLoggedIn === "inactive")
                {
                    setTimeout(function() {
                        self.batchLoadMainEntitiesAwait();
                    }, 250);
                    
                    return;
                }      
                
                self.batchLoadMainEntities();
            },
            batchLoadMainEntities: function()
            {
                const vc = this.findVc(this);
                
                if ( this.batchLoadingUri === "") { this.removeAjaxClass(); return; }
                
                this.batchOffset++;

                let self = this;
                
                setTimeout(function()
                {
                    let strBatchUrl = self.batchLoadingUri + "?batch=' . $this->batchCount . '&offset=" + self.batchOffset + "&fields=' . implode(",", $this->getEntity()->getRenderColumns()) . '";
                        
                    strBatchUrl += "&filterEntity=" + self.authUserId;
                    
                    ajax.Get(strBatchUrl, 1, function(result)
                    {             
                        for(let currEntityIndex in result.response.data.list)
                        {
                            self.mainEntityList.push(result.response.data.list[currEntityIndex]);
                        }
                        
                        setTimeout(function() { self.batchStart = true; } , 250);
                        self.mainEntityPageTotal = self.mainEntityList / self.mainEntityPageDisplayCount;
                        
                        if (result.response.end == "false")
                        {
                            self.batchLoadMainEntities();
                            return;
                        }
                        
                        self.batchEnd = true;
                    });
                },50);
            },
            setDashboardTab: function(label)
            {
                this.dashboardTab = label;
                sessionStorage.setItem(\'hub-dashboard-tab\', label);
            },
            renderCardUrl: function(card)
            {
                return (card.card_vanity_url) ? card.card_vanity_url : card.card_num;
            },
        ';
    }

    protected function renderComponentComputedValues (): string
    {
        return parent::renderComponentComputedValues() . '
            sortedHistory: function()
            {
                return _.orderBy(this.history, "last_updated", "desc");
            },
        ';
    }

    protected function renderTemplate(): string
    {
        global $app;
        return '<div class="hub-card-list">
            <v-style>
                .hub-card-list {
                    background:#ececec;
                }
                .hub-card-list-inner {
                    height: 100%;
                }
                .hub-card-list .cardListWrapper {
                    display: flex;
                    flex-direction: column;
                    text-align: center;  
                    height: calc(100% - 47px);
                    background: linear-gradient(to bottom, rgba(255,255,255,.5) 0, rgba(255,255,255,0) 50px);
                }
                .hub-card-list .cardListRowsWrapper {
                    display: block;
                    height: calc(100% - 47px);
                    background: linear-gradient(to bottom, rgba(255,255,255,.5) 0, rgba(255,255,255,0) 50px);
                }
                .hub-card-list .cardListInner {
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: space-between;
                    padding: 0 8px 15px;
                    overflow-y: auto;
                }
                .hub-card-list .cardListRowsInner {
                    display: block;
                    padding: 0 8px 15px;
                    overflow-y: auto;
                    height: 100%;
                }
                .hub-card-list .cardListRowsInner .cardListEl {
                    display: table;
                    width: calc(100% - 15px);
                }
                .hub-card-list .cardListRowsInner .cardListEl > div {
                    display: table-cell;
                    vertical-align:middle;
                }
                .hub-card-list .cardListEl {
                    cursor: pointer !important;
                    flex-direction: column;
                    display: flex;
                    flex: auto;
                    margin: 15px 7px 0px;
                    box-shadow: rgb(0 0 0 / 40%) 0 0 7px;
                    position: relative;
                    align-content: center;
                    align-items: center;
                    padding: 20px;
                    background: #fff;
                }
                .hub-card-list .cardBanner {
                    width: 150px;
                    height: 150px;
                    box-shadow: rgb(0 0 0 / 40%) 0 0 5px;
                }
                .hub-card-list .cardUrl {
                    font-weight: normal;
                    font-size: 14px;
                }
                .hub-card-list .cardAccessed {
                    font-weight: bold;
                    font-size: 14px;
                }
                .hub-card-list .cardListRowsInner .cardBanner {
                    width: 75px;
                    height: 75px;
                }
                .hub-card-list .cardListRowsInner .cardNumber {
                    padding-left:15px;
                }
                .hub-card-list .cardNumber {
                    font-size: 1.2em;
                    font-weight:bold;
                    max-width:175px;
                    margin-top: 5px;
                }
                
                .dashboard-tab {
                    display:flex;
                    padding:10px 15px;
                    border-top:1px solid #ffffff;
                    border-right: 1px solid #aaaaaa;
                    border-left: 1px solid #ffffff;
                    border-radius: 20px 20px 0 0 /90px 90px 0 0;
                    background: linear-gradient(to bottom, rgba(255,255,255,.5) 0%, rgba(255,255,255,0) 100%);
                    z-index:2;
                    position: relative;
                    cursor:pointer;
                }
                
                .dashboard-tab.active {
                    padding:10px 15px;
                    border-top:1px solid #ffffff;
                    border-right: 1px solid #aaaaaa;
                    border-left: 1px solid #ffffff;
                    border-radius: 20px 20px 0 0 /90px 90px 0 0;
                    background: linear-gradient(to bottom, rgba(255,255,255,.8) 50%, rgba(255,255,255,.5) 100%);
                    z-index:3;
                    cursor:default;
                }
                .dashboard-tab-display .fas span {
                    font-size: 12px;
                    top: 2px;
                    position: relative;
                    margin-left: 5px;
                    font-family: \'Montserrat\', sans-serif;
                    font-weight: normal;
                }
                .dashboard-tab-display.mobile-to-table {
                    padding-top:10px;
                    display: flex;
                }
                
                .login-block-wrapper {
                    display: flex; place-content: center; overflow-y: auto; height:100%;
                }
                .login-block-inner {
                    display: flex; align-items: center; justify-content: center; flex-direction: column;height:100% max-height: 100vh; max-height: -webkit-fill-available;
                }
                .login-block-wrapper .login-block {
                    text-align: center;background: #fff;box-shadow: rgba(0,0,0,.2) 0 0 7px;
                }
                .login-block-wrapper .portalLogo {
                    width:150px;height:150px;display: inline-block;margin-top: -54%;margin-bottom: 42px;
                }
                
                @media (max-width: 600px) {
                    .hub-card-list .cardBanner {
                        width: 30vw;
                        height: 30vw;
                        box-shadow: rgb(0 0 0 / 40%) 0 0 5px;
                    }
                    .hub-card-list .cardNumber {
                        max-width:25vw;
                        font-size: 3vw;
                    }
                    .dashboard-tab {
                        width: 35%;
                        text-align: center;
                        border-radius: 0px !important;
                        justify-content: center;
                        margin: 0;
                    }
                    .login-block-wrapper .login-block {
                        width:90%;
                    }
                    .login-block-wrapper .login-block .portalLogo {
                        width: 100px;
                        height: 100px;
                        margin-top: -40%;
                        margin-bottom: -15px;
                    }
                    .login-block-wrapper .login-block .app-modal-title {
                        margin-top: 0px;
                    }
                    .cp_0 .portalLogo {
                        margin-top: -6px;
                    }
                }
            </v-style>
            <div class="hub-card-list-inner" v-if="isLoggedIn == \'active\'">
                <div class="dashboard-tab-display mobile-to-table">
                    <div v-on:click="setDashboardTab(\'cards\')" class="dashboard-tab fas fa-id-card" v-bind:class="{active: dashboardTab === \'cards\'}"><span>My Cards</span></div>
                    <div v-on:click="setDashboardTab(\'favorites\')" class="dashboard-tab fas fa-heart" v-bind:class="{active: dashboardTab === \'favorites\'}"><span>Favorites</span></div>
                    <div v-on:click="setDashboardTab(\'history\')" class="dashboard-tab fas fa-calendar-alt" v-bind:class="{active: dashboardTab === \'history\'}"><span>History</span></div>
                </div>
                <div class="cardListWrapper" v-show="dashboardTab === \'cards\'">
                    <div class="cardListInner">
                        <div v-for="currCard in mainEntityList" v-on:click="jumpToCard(currCard)" class="cardListEl">
                            <div class="cardBanner" v-bind:style="{background: \'url(\' + currCard.banner +\') no-repeat center center / cover\'}"></div>
                            <div class="cardNumber">{{ currCard.card_name }}</div>
                            <div class="cardstatus">{{ currCard.card_num }} | {{ ucwords(currCard.status) }}</div>
                        </div>
                    </div>
                </div>
                <div class="cardListWrapper" v-show="dashboardTab === \'favorites\'">
                    <div class="cardListInner">
                        <div v-for="currCard in favorites" v-on:click="jumpToCard(currCard)" class="cardListEl">
                            <div class="cardBanner" v-bind:style="{background: \'url(\' + currCard.banner +\') no-repeat center center / cover\'}"></div>
                            <div class="cardNumber">{{ currCard.card_name }}</div>
                        </div>
                    </div>
                </div>
                <div class="cardListRowsWrapper" v-show="dashboardTab === \'history\'">
                    <div class="cardListRowsInner">
                        <div v-for="currCard in sortedHistory" v-on:click="jumpToCard(currCard)" class="cardListEl">
                            <div class="cardBanner" v-bind:style="{background: \'url(\' + currCard.banner +\') no-repeat center center / cover\'}"></div>
                            <div class="cardNumber">
                                {{ currCard.card_name }}
                                <div class="cardUrl">'.$app->objCustomPlatform->getPublicDomain().'/{{ renderCardUrl(currCard) }}</div>
                                <div class="cardAccessed">{{ formatDateForDisplay(currCard.last_updated) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="isLoggedIn == \'inactive\'">
                <div class="login-block-wrapper">
                    <div class="login-block-inner">
                        <div class="login-block">
                            <div class="portalLogo"></div>
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
        </div>';
    }

    private function recursiveWidgetCall($directory, &$objActiveAppEntities, $debug = false) : void
    {
        $arModuleWidgetPaths = glob($directory . "/*");

        foreach($arModuleWidgetPaths as $currModuleWidgetPath)
        {
            if ( is_file($currModuleWidgetPath))
            {

                if ($debug === true)
                {
                    echo $currModuleWidgetPath. PHP_EOL;
                }

                [$currClassIndex, $objClassInstanceName] = getClassData($currModuleWidgetPath);

                if ($objClassInstanceName === false)
                {
                    continue;
                }

                /** @var VueComponent $objClassInstance */
                try
                {
                    $objClassInstance = new $objClassInstanceName();

                    if (property_exists(get_class($objClassInstance), "isNotDynamic"))
                    {
                        if ($objClassInstance->isNotDynamic === true)
                        {
                            continue;
                        }
                    }

                    if ($debug === true)
                    {
                        echo $objClassInstanceName. PHP_EOL;
                    }

                    $objActiveAppEntities[$objClassInstance->getId()] = $objClassInstanceName;
                }
                catch (ArgumentCountError $ex)
                {
                    // Silent exit.
                    // If we cant instantiate it, we don't have to worry about hydrating it.
                }
            }
            elseif (is_dir($currModuleWidgetPath))
            {
                $this->recursiveWidgetCall($currModuleWidgetPath, $objActiveAppEntities, $debug);
            }
        }
    }
}