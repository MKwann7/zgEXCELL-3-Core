<?php

namespace Entities\Cards\Components\Vue\CardPageWidget;

use App\Core\AppModel;
use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Modules\Classes\ModuleApps;
use Entities\Users\Classes\Users;

class SelectNewCardPageWidget extends VueComponent
{
    protected string $id = "f5ca8b43-6b4e-4872-a9b5-e5a4cc662df6";

    public function __construct(?AppModel $entity = null, $name = "Card Page Widget", $props = [])
    {
        $this->loadProps($props);
        $this->name = $name;;

        parent::__construct($entity);

        $this->modalTitleForAddEntity = "Add " . $name;
        $this->modalTitleForEditEntity = "Edit " . $name;
        $this->modalTitleForDeleteEntity = "Delete " . $name;
        $this->modalTitleForRowEntity = "View " . $name;
    }

    protected function renderComponentDataAssignments() : string
    {
        return "
        widgetId: null,
        dynamicSearch: false,
        userId: null,
        title: '',
        userSearch: '',
        customerList: " . $this->renderUsersForSelection() . ",
        ";
    }

    protected function renderComponentMethods() : string
    {
        return '
            createNewPageWidget: function(result) 
            {
                let self = this;
                const url = "cards/card-page-app/create-and-add-to-page";
                let postData = {
                    widgetId: this.widgetId,
                    userId: this.userId,
                    widgetTitle: this.title
                };
                
                `modal.EngageFloatShield();`
                
                ajax.Post(url, postData, function(objCardResult) 
                {
                    let newEntity = objCardResult.data.cardPage;
                    self.entities.push(newEntity);    
                    self.$forceUpdate();
                    let app = self.findAppVc(self);
                    app.getComponentById("'. ListCardPageWidget::getStaticId().'").instance.editMainEntityWithWidget(newEntity);
                    modal.CloseFloatShield();
                });
            },
            engageDynamicSearch: function(user)
            {
                this.dynamicSearch = true;
            },
            hideDynamicSearch: function()
            {
                const self = this;
                setTimeout(function() {
                    self.dynamicSearch = false;
                }, 100);
            },
            keyMonitorCustomerList: function(event)
            {
                this.customerList = this.customerList;
                this.$forceUpdate();
            },
            parseUsersBySearch(customerList)
            {
                const self = this;
                let newUserList = [];
                
                if (typeof customerList.length !== "number" || customerList.length === 0)
                {
                    return newUserList;
                }
                
                let intTotalCount = 0;
                
                for (let currUser of customerList)
                {
                    if (intTotalCount > 25) { break; }
                    if (
                        currUser.first_name.toLowerCase().includes(self.userSearch.toLowerCase()) || 
                        currUser.last_name.toLowerCase().includes(self.userSearch.toLowerCase()) ||
                        (currUser.first_name.toLowerCase() + " " + currUser.last_name.toLowerCase()).includes(self.userSearch.toLowerCase()) ||
                        currUser.user_id.toString().toLowerCase().includes(self.userSearch.toLowerCase())
                    )
                    {
                        newUserList.push(currUser);
                        intTotalCount++;
                    }
                }
                
                return newUserList;
            },
            assignCustomer: function(user)
            {
                this.userSearch = user.first_name + " " + user.last_name;
                this.userId = user.user_id;
                this.dynamicSearch = false;
            },
        ';
    }

    protected function renderTemplate() : string
    {
        return '
        <div class="addPageWidget" style="margin-top: 5px;">
            <v-style type="text/css">
            
                .addPageWidget .dynamic-search-list {
                    position: absolute;
                    width: calc(100% - 135px);
                    background: #fff;
                    margin-left: 5px;
                    z-index: 1000;
                    max-height:40vh;
                    overflow-y:auto;
                }
                .addPageWidget .dynamic-search-list > table {
                    width: 100%;
                }
                .addPageWidget .dynamic-search-list > table > thead {
                    box-shadow: rgba(0,0,0,0.2) 0px 2px 5px;
                    background-color: #007bff;
                    color: #fff !important;
                }
                .addPageWidget .dynamic-search-list > table tr {
                    cursor:pointer;
                }
                .addPageWidget .dynamic-search-list > table tr:hover {
                    background-color:#d5e9ff !important;
                }
                .addPageWidget .dynamic-search .inputpicker-arrow {
                    position: absolute;
                    top: 22px;
                    right: 21px;
                    width: 20px;
                    height: 20px;
                    cursor: pointer;
                }
                .addPageWidget .dynamic-search .inputpicker-arrow b {
                    border-color: #888 transparent transparent;
                    border-style: solid;
                    border-width: 5px 4px 0;
                    height: 0;
                    left: 50%;
                    top: 50%;
                    margin-left: -4px;
                    margin-top: -2px;
                    position: absolute;
                    width: 0;
                    font-weight: 700;
                }
            </v-style>
            <div class="divTable">
                <div class="divRow">
                    <div class="divCell" style="width:125px;vertical-align: middle;">Module Widget</div>
                    <div class="divCell">
                        <select v-model="widgetId" class="form-control" style="margin: 5px 0;">
                            <option value="">--Select Module Widget--</option>
                            ' . $this->renderModulesForSelection() . '
                        </select>
                    </div>
                </div>
                <div class="divRow">
                    <div class="divCell" style="width:125px;vertical-align: middle;">User Id</div>
                    <div class="divCell">
                        <div class="dynamic-search">
                            <span class="inputpicker-arrow">
                                <b></b>
                            </span>
                            <input v-on:focus="engageDynamicSearch" style="margin: 5px 0;" v-on:blur="hideDynamicSearch" v-model="userSearch" v-on:keyup="keyMonitorCustomerList" autocomplete="off" value="" placeholder="Start Typing..." class="form-control ui-autocomplete-input">
                            <div class="dynamic-search-list" style="position:absolute;" v-if="dynamicSearch === true && userSearch !== \'\'">
                                <table class="table">
                                    <thead>
                                        <th>User Id</th>
                                        <th>Name</th>
                                    </thead>
                                    <tbody>
                                        <tr v-for="currUser in cartUserSearchList">
                                            <td @click="assignCustomer(currUser)">{{currUser.user_id}}</td>
                                            <td @click="assignCustomer(currUser)">{{currUser.first_name}} {{currUser.last_name}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="divRow">
                    <div class="divCell" style="width:125px;vertical-align: middle;">Module Title</div>
                    <div class="divCell">
                        <input  v-model="title" placeholder="Enter a tab title..."  style="margin: 5px 0;" class="form-control" />
                    </div>
                </div>
            </div>
            <button style="margin-top:10px;" class="buttonID23542445 btn btn-primary w-100" v-on:click="createNewPageWidget()">Create New Card Page Widget</button>
        <div>
        ';
    }

    protected function renderModulesForSelection()
    {
        $objModulesResult = (new ModuleApps())->getLatestModuleWidgetsByNameAsc();
        $colModules = $objModulesResult->getData();
        $modules = [];

        foreach($colModules as $currModule)
        {
            $moduleName = $currModule->module_name . " | " . $currModule->name . " " . $currModule->version;
            $modules[] = '<option value="' . $currModule->module_app_id . '">'. $moduleName . '</option>';
        }

        return implode(PHP_EOL, $modules);
    }

    protected function renderUsersForSelection()
    {
        global $app;
        $objUsersResult = (new Users())->getWhere(["company_id" => $app->objCustomPlatform->getCompanyId(), "status" => "Active"]);
        $colUsers = $objUsersResult->getData();

        return $colUsers->ConvertToJavaScriptArray([
            "user_id",
            "first_name",
            "last_name",
        ]);
    }

    protected function renderComponentComputedValues() : string
    {
        return '
            cartUserSearchList: function()
            {
                return this.parseUsersBySearch(this.customerList);
            },
        ';
    }
}