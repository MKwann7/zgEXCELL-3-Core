<?php

namespace Entities\Modules\Components\Vue\AppsWidget;

use App\Website\Constructs\Breadcrumb;
use App\Website\Vue\Classes\VueComponentEntityList;
use Entities\Modules\Models\ModuleMainModel;

class ListModuleAppsWidget extends VueComponentEntityList
{
    protected $id = "834a5a03-d5e2-4c9b-8af9-a877a79f356a";
    protected $title = "My Modules";
    //protected $batchLoadEndpoint = "modules/module-data/get-module-batches";

    public function __construct(array $components = [])
    {
        $displayColumns = ["banner", "status"];

        global $app;

        if ($app->userAuthentication() && userCan("manage-platforms"))
        {
            $displayColumns[] = "platform";
        }

        $displayColumns = array_merge($displayColumns, ["card_name", "card_num", "card_vanity_url", "card_owner_name", "card_contacts", "product", "created_on", "last_updated"]);

        $defaultEntity = (new ModuleMainModel())
            ->setDefaultSortColumn("card_num", "DESC")
            ->setDisplayColumns($displayColumns)
            ->setFilterColumns(["card_name","card_num","card_vanity_url","card_owner_name","status"])
            ->setRenderColumns(["card_id", "owner_id", "card_owner_name", "card_name", "card_num", "card_vanity_url", "card_keyword", "product", "card_contacts", "status", "order_line_id", "platform", "company_id", "banner", "favicon", "created_on", "last_updated", "sys_row_id",]);

        parent::__construct($defaultEntity, $components);

        $this->addBreadcrumb(new Breadcrumb("Admin","/account/admin/", "link"));

        $editorComponent = new ManageModuleAppsWidget();
        $editorComponent->addParentId($this->getInstanceId(), ["edit"]);

        $this->addComponentsList($editorComponent->getDynamicComponentsForParent());
        $this->addComponent($editorComponent);

        $this->modalTitleForAddEntity = "View Apps";
        $this->modalTitleForEditEntity = "View Apps";
        $this->modalTitleForDeleteEntity = "View Apps";
        $this->modalTitleForRowEntity = "View Apps";
        $this->setDefaultAction("view");
    }

    protected function renderTemplate() : string
    {
        return '<div class="formwrapper-control list-cards-main-wrapper">
                    <v-style type="text/css">
                    .module-wrapper-list {
                        display:flex;
                        flex-direction: row;
                        justify-content: left; 
                        padding: 8px 0;
                    }
                    .module-wrapper-list li {
                        display: flex;
                        align-items: center;
                    }
                    .module-wrapper-list li div {
                        width: 100%;
                        display: block;
                        text-align: center;
                        margin: 0 5px;
                        padding: 10px 20px;
                        border-radius: 4px;
                        cursor:pointer;
                        background:#fff;
                    }
                    .app-wrapper-list {
                        display:flex;
                        flex-direction: row;
                        justify-content: space-between; 
                        padding: 8px 0;
                        flex-wrap: wrap;
                    }
                    .app-wrapper-list li {
                        display: flex;
                        align-items: center;
                        margin-bottom:15px;
                    }
                    .app-wrapper-list li div {
                        width: 100%;
                        display: block;
                        text-align: center;
                        margin: 0 5px;
                        padding: 10px 20px;
                        border-radius: 4px;
                        cursor:pointer;
                        background:#fff;
                    }
                    .module-wrapper-list li div,
                    .module-wrapper-list li div i {
                        color: #ff0000 !important;
                        font-size: 20px;
                    }
                    .BodyContentBox .list-cards-main-wrapper .form-control {
                        position: relative;
                        top: -1px;
                        font-size: 13px;
                        padding: .100rem .75rem .150rem;
                        width: 140px;
                        line-height: 1.1;
                        height: calc(1.55rem + 2px);
                    }
                    .fformwrapper-header .form-search-box {
                        display: inline-block;
                        top: 7px;
                        position: relative;
                        max-width: 500px;
                        left: 5px;
                    }
                    .i360-takeoff-logo {
                        background: url(/widgets/images/360-takeoff/360-takeoff-logo.png) no-repeat center center / contain;
                    }
                    .guard-smart-global-logo {
                        background: url(/widgets/images/guard-smart/guard-smart-global-logo.png) no-repeat center center / contain;
                    }
                    .modules-wrapper-app {
                        width: 150px !important;
                        height: 150px;
                        flex-direction: column;
                    }
                    .modules-wrapper-app span {
                        width: 100%;
                        height: 100%;
                        display: inline-block;
                    }
                    </v-style>
                    <div class="entityListOuter">
                        <div class="width100 entityDetails">
                            <div class="card-tile-100">
                                <ul class="module-wrapper-list">
                                    <li><div><i class="fas fa-sign"></i> Real Estate</div></li>
                                    <li><div><i class="fas fa-hammer"></i> Construction</div></li>
                                    <li><div><i class="fas fa-house-damage"></i> Insurance</div></li>
                                    <li><div><i class="fas fa-business-time"></i> Business</div></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="fformwrapper-header">
                        <table class="table header-table" style="margin-bottom:0px;">
                            <tbody>
                            <tr>
                                <td>
                                    <h4>
                                        <span class="fas fa-tablet-alt fas-large desktop-25px"></span>
                                        <span class="fas-large">My Tools</span>
                                        <span v-on:click="editCardProfile(entity)" class="pointer editEntityButton entityButtonFixInTitle"></span>
                                        <div class="form-search-box" v-cloak>
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input id="entity-search-input" v-model="searchMainQuery" class="form-control" type="text" placeholder="Search..."/>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary" v-on:click="openCartPackageSelection()" style="margin-left: 5px;margin-top: -4px;">Purchase Tool</button>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </h4>
                                </td>
                                <td class="text-right page-count-display" style="vertical-align: middle;">
                                    <span class="page-count-display-data">
                                        Current: <span>{{ mainEntityPageIndex }}</span>
                                        Pages: <span>{{ totalMainEntityPages }}</span>
                                    </span>
                                    <button v-on:click="prevMainEntityPage()" class="btn prev-btn" :disabled="mainEntityPageIndex == 1">Prev</button>
                                    <button v-on:click="nextMainEntityPage()" class="btn" :disabled="mainEntityPageIndex == totalMainEntityPages">Next</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="entityListOuter">
                        <div>
                            <ul class="app-wrapper-list">
                                <li><div class="modules-wrapper-app"><span class="i360-takeoff-logo"></span></div></li>
                                <li><div class="modules-wrapper-app"><span class="guard-smart-global-logo"></span></div></li>
                                <li><div class="modules-wrapper-app">Tool 3</div></li>
                                <li><div class="modules-wrapper-app">Tool 4</div></li>
                                <li><div class="modules-wrapper-app">Tool 5</div></li>
                                <li><div class="modules-wrapper-app">Tool 6</div></li>
                            </ul>
                        </div>
                    </div>
                </div>
        ';
    }
}