<?php

namespace Entities\Dashboard\Components\Vue\DashboardWidget;

use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cards\Classes\Cards;
use Entities\Users\Models\UserModel;
use Entities\Visitors\Classes\Visitors;

class DashboardWidget extends VueComponent
{
    protected $id = "61be5055-f507-43f3-943c-528aa9869afa";
    protected $title = "Dashboard";
    protected $entityPageDisplayCount = 15;
    protected $customPlatformName;

    public function __construct(array $components = [])
    {
        $defaultEntity = (new UserModel())
            ->setDefaultSortColumn("user_id", "DESC");

        parent::__construct($defaultEntity, $components);

        $this->modalTitleForAddEntity = "View Dashboard";
        $this->modalTitleForEditEntity = "View Dashboard";
        $this->modalTitleForDeleteEntity = "View Dashboard";
        $this->modalTitleForRowEntity = "View Dashboard";
        $this->setDefaultAction("view");

        global $app;
        $this->customPlatformName = $app->objCustomPlatform->getPortalName();
    }

    protected function renderComponentHydrationScript (): string
    {
        return parent::renderComponentHydrationScript() . '
            let swiper = new Swiper(\'.swiper-container\', {
                slidesPerView: 2,
                spaceBetween: 15,
                observer: true,
                observeParents: true,
                navigation: {
                    nextEl: \'.swiper-button-next\',
                    prevEl: \'.swiper-button-prev\',
                },
                breakpoints: {   
                    650: {       
                        slidesPerView: 2,
                        spaceBetween: 15     
                    },  
                    1100: {       
                        slidesPerView: 3,       
                        spaceBetween: 15     
                    },
                    1500: {       
                        slidesPerView: 4,       
                        spaceBetween: 15     
                    } 
                
                } 
            });
            
            let myModuleData = this.getMyModuleData();
            
            var d = new Date();
            this.currentYear = d.getFullYear();
        ';
    }

    protected function renderComponentDataAssignments() : string
    {
        return '
            dashboardWidgetData: {},
            currentYear: {},
            socket: null,
        ';
    }

    protected function renderComponentMethods() : string
    {
        return '
            getMyModuleData: function(result) {
                const self = this;
                
                const url = "/api/v1/dashboard/get-module-data?uuid=" + this.userId + "&widgets=transactions|contacts|shares|visitors";
                ajax.GetExternal(url, true, function(result) {
                    self.dashboardWidgetData = result.response.data.widgets;
                });
            },
            getDashboardData: function(id, parameter, defaultText)
            {
                if (typeof this.dashboardWidgetData[id] === "undefined" || typeof this.dashboardWidgetData[id][parameter] === "undefined") { return defaultText; }
                
                return this.dashboardWidgetData[id][parameter];
            },
            renderMoney: function(num) 
            {                
                return "$" + this.numberWithCommas(parseFloat(this.renderCartCurrency(num)).toFixed(2),1);
            },
            numberWithCommas: function(x, isMoney) {
            
                if (typeof x === "undefined" || x === null || isNaN(x)) { if(!isMoney) {return 0;} else {return "0.00"}};
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            },
            renderCartCurrency: function(num) 
            {
               return num;
            },
        ';
    }

    protected function renderTemplate() : string
    {
        global $app;
        if ($app->objCustomPlatform->getCompanySettings()->FindEntityByValue("label","portal_theme")->value == 2)
        {
            return $this->oldDashboard();
        }

        return $this->newDashboard();
    }

    protected function oldDashboard(): string
    {
        global $app;
        $lstUserCards = (new Cards())->GetByUserId($app->intActiveUserId);
        $cardsCount = $lstUserCards->Result->Count;

        $lstCardsTraffic = (new Visitors())->getWhereIn("card_id", $lstUserCards->Data->FieldsToArray(["card_id"]));
        $visitorCount = number_format($lstCardsTraffic->Result->Count,0,".",",");

        return '
            <div class="dashboardWidgetsWrapper">
                <div class="fformwrapper-header">
                    <table class="entity-list-header-wrapper table header-table" style="margin-bottom:0px;">
                        <tbody>
                        <tr>
                            <td>
                                <h3 class="account-page-title">My Dashboard</h3>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="main-body-wrapper" style="padding:5px 15px;">
                    <div class="width100 entityDetails">
                        <div class="width50">
                            <div class="card-tile-50">
                                <h4>My ' . $this->customPlatformName . ' Success Journey</h4>
                                <!--<iframe src="https://player.vimeo.com/video/384218503?autoplay=1&color=f3f3f3&title=0&byline=0&portrait=0" width="800" height="450" frameborder="0" style="width:100%;margin-top:15px;" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>-->
                            </div>
                        </div>
                        <div class="width50">
                            <div class="card-tile-50">
                                <h4>My ' . $this->customPlatformName . ' Account Overview</h4>
                                <div class="entityDetailsInner cardProfile" style="margin-top:20px;">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td style="width:150px;"><a href="/account/cards">My Card</a>:</td>
                                                <td>' . $cardsCount . '</td>
                                            </tr>
                                            <tr>
                                                <td>My Visitors:</td>
                                                <td>' . $visitorCount . '</td>
                                            </tr>
                                            <tr>
                                                <td>My Points:</td>
                                                <td>TBA</td>
                                            </tr>
                                            <tr>
                                                <td>My Commission Level:</td>
                                                <td>TBA</td>
                                            </tr>
                                            <tr>
                                                <td>Founder Level:</td>
                                                <td>TBA</td>
                                            </tr>
                                            <tr>
                                                <td>Game Changers:</td>
                                                <td>TBA</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-tile-50" style="margin-top:15px;">
                                <h4>My Messages</h4>
                                <div class="entityDetailsInner cardProfile" style="margin-top:20px;">
                                    Coming Soon.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ';
    }

    protected function newDashboard(): string
    {
        global $app;
        $portalThemeMainColor = $app->objCustomPlatform->getCompanySettings()->FindEntityByValue("label","portal_theme_main_color")->value ?? "006666";
        return '<div class="dashboardWidgetsWrapper">
                <v-style>
                    .dashboardWidgetsBox {
                        overflow: hidden !important;
                        padding: 0 10px 15px;
                        margin: 0 -10px;
                    }
                    .dashboardWidgetsBox ul {
                        display:flex;
                        flex-direction: row;
                        justify-content: space-between; 
                    }
                    .dashboardWidgetsBox ul li {
                        display: flex;
                        padding-top: 25px;
                    }
                    .dashboardWidgetsBox ul li.cardWidth25 {
                        width: calc(25% - 10px);
                    }
                    .dashboardWidgetsBox ul li.cardWidth75 {
                        width: calc(75% - 5px);
                    }
                    .dashboardWidgetsBox ul > li > div {
                        width: 100%;
                        position:relative;
                        border-radius:5px;
                        box-shadow: rgba(0,0,0,.3) 2px 2px 7px;
                    }
                    .dashboardWidgetsBox ul > li > div.primaryColor {
                        background: #'.$portalThemeMainColor.';
                    }
                    .theme_shade_light .dashboardWidgetsBox ul > li > div.shadowColor {
                        background: #fff;
                    }
                    .theme_shade_dark .dashboardWidgetsBox ul > li > div.shadowColor {
                        background: #333;
                    }
                    .dashboardWidgetsBox ul > li > div > span.showcaseIcon {
                        display: flex;
                        width: 130px;
                        height: 115px;
                        background: #ff6c00;
                        color: #fff;
                        justify-content: center;
                        line-height: 115px;
                        font-size: 70px;
                        position:absolute;
                        top: -15px;
                        left: 15px;
                        box-shadow: rgba(0,0,0,.5) 5px 5px 7px;
                    }
                    .dashboardWidgetsBox ul > li > div > div.showcaseTitle {
                        margin-left: 160px;
                        color: #fff;
                        font-family: \'Montserrat\', sans-serif;
                        font-size:20px;
                        padding-top: 13px;
                        display:block;
                        font-weight: 300;
                        padding-bottom: 30px;
                    }
                    .dashboardWidgetsBox ul > li > div > div.showcaseTitle > span.showcaseTitleValue {
                        font-size: 35px;
                        display: block;
                        color: #fff;
                        line-height: 35px;
                        font-weight: 500;
                    }
                    .dashboardWidgetsBox div.showcaseBody table tr > td {
                        color: #fff;
                    }
                    .dashboardWidgetsBox div.showcaseBody table tr > td:first-child {
                        width: 160px;
                        text-align: right;
                        padding-right: 14px;
                    }
                    .dashboardWidgetsBox div.showcaseBody {
                        padding-bottom: 35px;
                    }
                    .dashboardWidgetsBox div.widgetBoxInner {
                        padding:15px 25px;
                    }
                    .dashboardWidgetsBox div.widgetBoxInner .widgetBoxTitle {
                        font-family: \'Montserrat\', sans-serif;
                        font-size: 19px;
                    }
                    .swiper-button-next.swiper-button-disabled, .swiper-button-prev.swiper-button-disabled {
                        opacity: .25;
                    }
                    .dashboardWidgetsBox .stackableCards {
                        flex-wrap: wrap;
                    }
                    
                    @media (max-width:750px) {
                        .dashboardWidgetsBox .cardWidth25 {
                            width: calc(33% - 5px) !important;
                        }
                        .dashboardWidgetsBox .cardWidth75 {
                            width: 100% !important;
                        }
                        .dashboardWidgetsBox div.widgetBoxInner {
                            padding: 10px 10px;
                        }
                        
                        .dashboardWidgetsBox div.widgetBoxInner .widgetBoxTitle {
                            font-size: 13px;
                        }
                    }
                    
                    @media (max-width:550px) {
                        .dashboardWidgetsBox .cardWidth25 {
                            width: calc(50% - 5px) !important;
                        }
                    }
                </v-style>
                <div class="dashboardWidgetsBox swiper-container">
                    <ul class="swiper-wrapper">
                        <li class="swiper-slide">
                            <div class="primaryColor">
                                <span class="showcaseIcon fas fa-receipt"></span>   
                                <div class="showcaseTitle">
                                    {{ currentYear }} Transactions
                                    <span class="showcaseTitleValue">{{ renderMoney(getDashboardData("transactions", "gross_month", "0.00")) }}</Span>
                                </div>
                                <div class="showcaseBody">
                                    <table>
                                        <tbody>
                                            <tr><td>This Month:</td><td>{{ renderMoney(getDashboardData("transactions", "last_month", "0.00")) }}</td></tr>    
                                            <tr><td>Monthly Avg:</td><td>{{ renderMoney(getDashboardData("transactions", "avg_month", "0.00")) }}</td></tr>    
                                        </tbody>        
                                    </table>
                                </div> 
                            </div>    
                        </li>
                        <li class="swiper-slide">
                            <div class="primaryColor">
                                <span style="background:#07aaff;" class="showcaseIcon fas fa-users"></span>   
                                <div class="showcaseTitle">
                                    Total Contacts
                                    <span class="showcaseTitleValue">{{ numberWithCommas(getDashboardData("contacts", "total_contacts", "0")) }}</Span>
                                </div>
                                <div class="showcaseBody">
                                    <table>
                                        <tbody>
                                            <tr><td>Metric #1:</td><td>200</td></tr>    
                                            <tr><td>Metric #2:</td><td>950</td></tr>    
                                        </tbody>        
                                    </table>
                                </div> 
                            </div>    
                        </li>
                        <li class="swiper-slide">
                            <div class="primaryColor">
                                <span style="background:#d71fc8;" class="showcaseIcon fas fa-share-alt"></span>   
                                <div class="showcaseTitle">
                                    Shares
                                    <span class="showcaseTitleValue">4,500</Span>
                                </div>
                                <div class="showcaseBody">
                                    <table>
                                        <tbody>
                                            <tr><td>Metric #1:</td><td>200</td></tr>    
                                            <tr><td>Metric #2:</td><td>950</td></tr>    
                                        </tbody>        
                                    </table>
                                </div> 
                            </div>    
                        </li>
                        <li class="swiper-slide">
                            <div class="primaryColor">
                                <span style="background:#1abb1d;" class="showcaseIcon fas fa-eye"></span>   
                                <div class="showcaseTitle">
                                    Visitors
                                    <span class="showcaseTitleValue">1,500</Span>
                                </div>
                                <div class="showcaseBody">
                                    <table>
                                        <tbody>
                                            <tr><td>Metric #1:</td><td>200</td></tr>    
                                            <tr><td>Metric #2:</td><td>950</td></tr>    
                                        </tbody>        
                                    </table>
                                </div> 
                            </div>    
                        </li>
                    </ul>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
                <div class="dashboardWidgetsBox">
                    <ul class="stackableCards">
                        <li class="cardWidth75">
                            <div class="shadowColor widgetBoxInner">
                                <div class="widgetBoxTitle">My Modules</div>
                            </div>
                        </li>
                        <li class="cardWidth25">
                            <div class="shadowColor widgetBoxInner">
                                <div class="widgetBoxTitle">Analytics</div>
                            </div>
                        </li>
                        <li class="cardWidth25">
                            <div class="shadowColor widgetBoxInner">
                                <div class="widgetBoxTitle">Marketing Calendar</div>
                            </div>
                        </li>
                        <li class="cardWidth25">
                            <div class="shadowColor widgetBoxInner">
                                <div class="widgetBoxTitle">Virtual Meeting</div>
                            </div>
                        </li>
                        <li class="cardWidth25">
                            <div class="shadowColor widgetBoxInner">
                                <div class="widgetBoxTitle">Customer Service Tickets</div>
                            </div>
                        </li>
                        <li class="cardWidth25">
                            <div class="shadowColor widgetBoxInner">
                                <div class="widgetBoxTitle">Company Communication</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>';
    }
}