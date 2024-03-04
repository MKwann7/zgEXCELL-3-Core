<?php

namespace Entities\Users\Components\Vue\ProfileWidget;

use App\Core\AppModel;
use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\VueProps;
use Entities\Cards\Components\Vue\CardWidget\ManageCardPagesWidget;
use Entities\Users\Components\Vue\UserWidget\ManageCustomerProfileWidget;

class UserProfileWidget extends VueComponent
{
    protected string $id = "e09b204a-559f-4df8-9577-6db7401cce81";
    protected string $title = "Manage My Profile";

    public function __construct(?AppModel $entity = null)
    {
        parent::__construct($entity);

        $this->modalTitleForAddEntity = "My Profile";
        $this->modalTitleForEditEntity = "My Profile";
        $this->modalTitleForDeleteEntity = "My Profile";
        $this->modalTitleForRowEntity = "My Profile";
    }

    protected function renderComponentMethods() : string
    {
        return parent::renderComponentMethods() . '
            toggleTab: function(type)
            {
                this.display = type;
            },
            logout: function()
            {
                app.Logout()
            },
        ';
    }

    protected function renderComponentDataAssignments() : string
    {
        return "
        display: 'personal',
        ";
    }

    protected function renderTemplate() : string
    {
        return '
        <div class="manageMyProfile">
            <v-style type="text/css">
            .manageMyProfile .account-links * {
                color:white !important;
            }
            </v-style>
            <div class="container p-1">
                <div class="row">
                    <div class="col col-3 pl-0">
                        <div class="container">
                            <div class="row account-links">
                                <button v-on:click="toggleTab(\'personal\')" v-bind:class="{\'btn-primary\': display == \'personal\',\'btn-secondary\': display != \'personal\'}" class="btn btn-block text-left">
                                  <span class="fa fa-user desktop-25px"></span> Personal Information
                                </button>
                                <button v-on:click="toggleTab(\'security\')" v-bind:class="{\'btn-primary\': display == \'security\',\'btn-secondary\': display != \'security\'}" class="btn btn-block text-left">
                                  <span class="fas fa-lock desktop-25px"></span> Account &amp; Security
                                </button>
                                <button v-on:click="toggleTab(\'billing\')" v-bind:class="{\'btn-primary\': display == \'billing\',\'btn-secondary\': display != \'billing\'}" class="btn btn-block text-left">
                                  <span class="fas fa-money-bill desktop-25px"></span> Billing
                                </button>
                                <button class="btn btn-danger btn-block text-left" v-on:click="logout"><span class="fas fa-sign-out-alt desktop-25px"></span>Logout</button>
                            </div>
                        </div>
                    </div>
                    <div class="col col-9 pr-0">
                        <div v-show="display == \'personal\'">
                        <h2>Personal Information</h2>
                            ' . $this->registerAndRenderDynamicComponent(
                                new ManageCustomerProfileWidget(),
                                "view",
                                []
                            ) . '
                        </div>
                        <div v-show="display == \'account\'">
                        
                        </div>
                        <div v-show="display == \'billing\'">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }
}