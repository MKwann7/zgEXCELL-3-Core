<?php

namespace Entities\Communication\Components\Vue\CommunicationWidget;

use App\Website\Vue\Classes\Base\VueComponent;

class ShowCommunicationWidget extends VueComponent
{
    protected string $id = "188f21e3-8458-41e5-b16e-9eeb57ef4dd1";
    protected string $title = "Communication";

    protected function renderComponentDataAssignments() : string
    {
        return "
            dashboardTab: 'overview',
        ";
    }

    protected function renderComponentHydrationScript() : string
    {
        return parent::renderComponentDataAssignments() . "
            this.disableModalLoadingSpinner();
            google.charts.load('upcoming', {packages: ['corechart']}).then(this.drawChart);
        ";
    }

    protected function renderComponentMethods() : string
    {
        return '
            setDashbaordTab: function(tabName) {
                this.dashboardTab = tabName;
                sessionStorage.setItem(\'dashboard-tab\', tabName);
            },
            drawChart: function() {
                let data = new google.visualization.DataTable();
                data.addColumn("string", "Topping");
                data.addColumn("number", "Slices");
                data.addRows([
                  ["Mushrooms", 3],
                  ["Onions", 1],
                  ["Olives", 1],
                  ["Zucchini", 1],
                  ["Pepperoni", 2]
                ]);
        
                // Set chart options
                var options = {"title":"How Much Pizza I Ate Last Night",
                               "width":400,
                               "height":300};
        
                // Instantiate and draw our chart, passing in some options.
                const chart = new google.visualization.PieChart(document.getElementById("chart_div"));
                chart.draw(data, options);
            },
            sendMessage: function()
            {
                modal.EngageFloatShield();
                let data = {title: "Send Message?", html: "Are you sure you want to proceed?<br>Please confirm."};
                modal.EngagePopUpConfirmation(data, function() 
                {
                    modal.CloseFloatShield();
                }, 400, 115);
            },
        ';
    }

    public function renderTemplate() : string
    {
        return '
            <div class="formwrapper-manage-entity">
                <v-style type="text/css">
                    .quickMessageBox .selectCardDetails .width50:first-child {
                        width: calc(50% + 50px);
                        text-align:right;
                    }
                    .quickMessageBox .selectCardSchedule .width50:first-child {
                        text-align:right;
                        padding-right:10px;
                    }
                    .quickMessageBox .selectCardSchedule .width50:last-child {
                        text-align: right;
                    }
                    .quickMessageBox .selectCardSchedule .width50:last-child label {
                        text-align: right;
                    }
                    .quickMessageBox .selectCardSchedule .width50:first-child label {
                        display: inline-block;
                        width: 170px;
                    }
                    .quickMessageBox .selectCardSchedule .width50:first-child > input {
                        display: inline;
                        width: calc(100% - 185px);
                    }
                    .quickMessageBox .selectCardDetails .width50:first-child input {
                        display: inline;
                        width: calc(100% - 30px);
                    }
                    .quickMessageBox .selectCardSchedule .width50:first-child span,
                    .quickMessageBox .selectCardDetails .width50:first-child span {
                        display: inline;
                        width: 50px;
                        padding-right: 5px;
                    }
                    .quickMessageBox .selectCardDetails .width50:last-child {
                        width: calc(50% - 50px);
                    }
                    .quickMessageBox .selectCardSchedule .width50:last-child button {
                        width: 200px;
                    }
                    @media(max-width:700px) {
                        .quickMessageBox .width100,
                        .quickMessageBox .width100 select,
                        .quickMessageBox .width100 input,
                        .quickMessageBox .width50 {
                            width: 100% !important;
                        }
                        .quickMessageBox .width100 input,
                        .quickMessageBox .width100 select {
                            margin-bottom:10px;
                        }
                        .quickMessageBox .width100 textarea {
                            margin-top:-10px;
                        }
                        .quickMessageBox .width100.selectCardDetails .width50 span {
                            display:inline;
                            width:50px;
                        }
                        .quickMessageBox .width100.selectCardDetails .width50:first-child input {
                            display:inline;
                            width:calc(100% - 34px) !important;
                        }
                    }
                </v-style>'.'
                <div class="entityDashboard">
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
                                <div data-block="pending" v-on:click="setDashbaordTab(\'overview\')"  class="dashboard-tab fas fa-tachometer-alt" v-bind:class="{active: dashboardTab === \'overview\'}"><span>Overview</span></div>
                                <div data-block="pending" v-on:click="setDashbaordTab(\'pending\')"  class="dashboard-tab fas fa-clock" v-bind:class="{active: dashboardTab === \'pending\'}"><span>Pending</span></div>
                                <div data-block="sent" v-on:click="setDashbaordTab(\'sent\')"  class="dashboard-tab fas fa-calendar-alt" v-bind:class="{active: dashboardTab === \'sent\'}"><span>Sent</span></div>
                                <div data-block="campaigns" v-on:click="setDashbaordTab(\'campaigns\')"  class="dashboard-tab fas fa-bullhorn" v-bind:class="{active: dashboardTab === \'campaigns\'}"><span>Campaigns</span></div>
                                <div data-block="templates" v-on:click="setDashbaordTab(\'templates\')"  class="dashboard-tab fas fa-file-alt" v-bind:class="{active: dashboardTab === \'templates\'}"><span>Templates</span></div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="entityTab" data-tab="profilewidget" v-bind:class="{showTab: dashboardTab === \'overview\'}">
                        <div class="width100 entityDetails">
                            <div class="width50">
                                <div class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-envelope fas-large desktop-30px"></span>
                                        <span class="fas-large">Quick Message</span>
                                    </h4>
                                    <div class="quickMessageBox entityDetailsInner">
                                        <div class="width100">
                                            <div class="width50">
                                                <select class="form-control" style="width:calc(100% - 10px);">
                                                    <option>Send via...</option>
                                                    <option>Email</option>
                                                    <option>WhatsApp</option>
                                                    <option>Mobile Notification</option>
                                                </select>
                                            </div>
                                            <div class="width50">
                                                <select class="form-control">
                                                    <option>Select Recipient...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="width100" style="clear:both;height:10px;"></div>
                                        <div class="width100">
                                            <textarea class="form-control" style="height:110px;"></textarea>
                                        </div>
                                        <div class="width100" style="clear:both;height:10px;"></div>
                                        <div class="width100 selectCardDetails">
                                            <div class="width50">
                                                <span class="fas fa-info-circle"></span>
                                                <input placeholder="Select a card..." class="form-control selectCardNumber" />
                                            </div>
                                            <div class="width50">
                                                <input placeholder="Select a page..." class="form-control selectPageNumber" />
                                            </div>
                                        </div>
                                        <div class="width100" style="clear:both;height:10px;"></div>
                                        <div class="width100 selectCardSchedule">
                                            <div class="width50">
                                                <label for="message_future_schedule">
                                                    <input id="message_future_schedule" type="checkbox">
                                                    <span>Schedule Message</span>
                                                </label>
                                                <input class="form-control" />
                                            </div>
                                            <div class="width50">
                                                <button v-on:click="sendMessage" class="btn btn-primary">Send</button>
                                            </div>
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="width50">
                                <div class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-chart-pie fas-large desktop-30px"></span>
                                        <span class="fas-large">Messaging Stats</span>
                                    </h4>
                                    <div class="entityDetailsInner">
                                        <div id="chart_div"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="width100 entityDetails">
                            <div class="width50">
                                <div v-if="entity" class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-images fas-large desktop-30px"></span>
                                        <span class="fas-large">Pending Messages</span>
                                    </h4>
                                    <div class="entityDetailsInner">
                                        Here!!
                                    </div>
                                </div>
                            </div>

                            <div class="width50">
                                <div v-if="entity" class="card-tile-50">
                                    <h4>
                                        <span class="fas fa-users fas-large desktop-30px"></span>
                                        <span class="fas-large">Campaigns</span>
                                    </h4>
                                    <div class="entityDetailsInner">
                                        Here!
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    
                    <div class="entityTab" data-tab="pages" v-bind:class="{showTab: dashboardTab === \'pending\'}">
                        <div class="width100 entityDetails">
                            <div class="card-tile-100">
                                <h4 class="account-page-subtitle">
                                    <span class="fas fa-clock fas-large desktop-30px"></span>
                                    <span class="fas-large">Pending Messages</span>
                                    <span class="pointer addNewEntityButton entityButtonFixInTitle"  v-on:click="addCardPageItem()" ></span>
                                </h4>
                                '.date("Y-m-d").'
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    
                    <div class="entityTab" data-tab="groups" v-bind:class="{showTab: dashboardTab === \'sent\'}">
                        <div class="width100 entityDetails">
                            <div class="card-tile-100">
                                <h4 class="account-page-subtitle">
                                    <span class="fas fa-calendar-alt fas-large desktop-30px"></span>
                                    <span class="fas-large">Sent Messages</span>
                                </h4>
                               '.date("Y-m-d").'
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    
                    <div class="entityTab" data-tab="users" v-bind:class="{showTab: dashboardTab === \'campaigns\'}">
                        <div class="width100 entityDetails">
                            <div class="card-tile-100">
                                <h4 class="account-page-subtitle">
                                    <span class="fas fa-bullhorn fas-large desktop-35px"></span>
                                    <span class="fas-large">Campaigns</span>
                                </h4>
                                '.date("Y-m-d").'
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    
                    <div class="entityTab" data-tab="contacts" v-bind:class="{showTab: dashboardTab === \'templates\'}">
                        <div class="width100 entityDetails">
                            <div class="card-tile-100">
                                <h4 class="account-page-subtitle">
                                    <span class="fas fa-file-alt fas-large desktop-25px"></span>
                                    <span class="fas-large">Templates</span>
                                </h4>
                                
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                </div>
            </div>';
    }
}