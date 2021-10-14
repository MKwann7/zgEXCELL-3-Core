<?php

namespace Entities\Companies\Components\Vue\PlatformWidget;

use App\Core\App;
use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Companies\Models\CompanyModel;

class RegistrationWidget extends VueComponent
{
    protected $id = "cb3bae5e-8378-4784-90e1-b6c8a60c5e4c";

    public function __construct(array $components = [])
    {
        $defaultEntity = (new CompanyModel())
            ->setDefaultSortColumn("company_id", "DESC")
            ->setDisplayColumns(["platform", "company_name", "status", "portal_domain", "public_domain", "owner", "cards", "state", "country", "created_on", "last_updated"])
            ->setRenderColumns(["company_id","platform", "company_name", "status", "portal_domain", "public_domain", "owner", "owner_id", "cards", "state", "country", "created_on", "last_updated", "sys_row_id"]);

        parent::__construct($defaultEntity, $components);

        $componentTitle = "Manage Custom Platform";
        $this->modalTitleForAddEntity = $componentTitle;
        $this->modalTitleForEditEntity = $componentTitle;
        $this->modalTitleForDeleteEntity = $componentTitle;
        $this->modalTitleForRowEntity = $componentTitle;

        $this->setDefaultAction("view");
    }

    protected function renderComponentMethods() : string
    {
        /** @var App $app */
        global $app;

        return '
            myMethod: function(name, public)
            {
                const self = this;
                const vc = this.findVc(this);
            },
        ';
    }

    protected function renderComponentComputedValues() : string
    {
        return '
            computedValue: function()
            {
                return "";
            },
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return "this.disableModalLoadingSpinner();";
    }

    protected function renderComponentDataAssignments() : string
    {
        return '
            entity: {},
            directoryColumns: [],
            profileImageUploadUrl: "",
            submitButtonTitle: "Submit Registration",
        ';
    }

    protected function renderTemplate() : string
    {
        return '
        <div class="appCustomPlatformWrapper">
            <v-style type="text/css">
                
            </v-style>
            <h4 style="margin-top: 4px;"><span class="fas fa-user-circle fas-large desktop-25px"></span> 
                <span class="fas-large">My Custom Platform Registration</span>
            </h4>
            <div style="background:#ddd;padding: 15px 17px 2px;border-radius:5px;box-shadow:rgba(0,0,0,.2) 0 0 10px inset;margin-top:10px; ">
                <div class="width250px">
                    <div class="memberAvatarImage">
                        <div class="slim" data-ratio="1:1" data-force-size="650,650" v-bind:data-service="profileImageUploadUrl" id="my-cropper" style="background-image: url(/_ez/images/users/defaultAvatar.jpg); background-size:100%;">
                            <input type="file"/>
                            <img width="250" height="250" alt="">
                        </div>
                    </div>
                </div>
                <div class="widthAutoTo250px">    
                    <table class="table no-top-border">
                        <tbody>
                            <tr>
                                <td style="width:100px;vertical-align: middle;">First Name</td>
                                <td>
                                    <input name="card_vanity_url" v-model="entity.first_name" class="form-control" type="text" placeholder="" value="">
                                </td>
                            </tr>
                            <tr>
                                <td style="width:100px;vertical-align: middle;">Last Name</td>
                                <td>
                                    <input name="card_vanity_url" v-model="entity.last_name"class="form-control" type="text" placeholder="" value="">
                                </td>
                            </tr>
                            <tr>
                                <td style="width:100px;vertical-align: middle;">Phone</td>
                                <td>
                                    <input name="card_vanity_url" v-model="entity.mobile_phone" class="form-control" type="text" placeholder="" value="">
                                </td>
                            </tr>
                            <tr>
                                <td style="width:100px;vertical-align: middle;">Email</td>
                                <td>
                                    <input name="card_vanity_url" v-model="entity.email" class="form-control" type="text" placeholder="" value="">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="clear:both;"></div>
            </div>
            <h4 style="margin-top: 14px;"><span class="fas fa-puzzle-piece fas-large desktop-25px"></span> <span class="fas-large">Custom Fields</span></h4>
            <table id="editMemberComponentCustomFields" class="table no-top-border" style="min-height: 50px;">
                <tbody>
                    <tr v-for="currColumn in directoryColumns">
                        <td style="width:109px;vertical-align: middle;">{{ currColumn.name }}</td>
                        <td style="padding: 5px .75rem;">
                            <component :is="memberDataSelectorComponent" :member-column="currColumn"></component>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button class="buttonID9234597e456 btn btn-primary w-100" @click="saveMember()">{{ submitButtonTitle }}</button>
        </div>';
    }
}