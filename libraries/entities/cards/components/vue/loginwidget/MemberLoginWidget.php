<?php


namespace Entities\Cards\Components\Vue\LoginWidget;

use App\Website\Vue\Classes\Base\VueComponent;
use App\website\vue\classes\VueComponentListTable;
use App\website\vue\classes\VueComponentSortableList;
use Entities\Cards\Models\CardModel;

class MemberLoginWidget extends VueComponent
{
    protected string $id = "28d911b7-daee-434b-b7b7-ff6aa544dc9f";
    protected string $modalWidth = "550";
    protected string $mountType = "no_mount";
    protected string $title = "Member Login";

    public function __construct ($props = [], ?VueComponentListTable $listTable = null, ?VueComponentSortableList $sortableList = null)
    {
        parent::__construct((new CardModel()), $listTable, $sortableList, $props);

        $this->modalTitleForAddEntity = "Account Login";
        $this->modalTitleForEditEntity = $this->modalTitleForAddEntity;
        $this->modalTitleForDeleteEntity = $this->modalTitleForAddEntity;
        $this->modalTitleForRowEntity = $this->modalTitleForAddEntity;
    }

    protected function renderComponentDataAssignments() : string
    {
        return '
            entity: {}
        ';
    }

    protected function renderComponentComputedValues() : string
    {
        return '';
    }

    protected function renderComponentHydrationScript() : string
    {
        return parent::renderComponentHydrationScript() . '
        ';
    }

    protected function renderTemplate() : string
    {
        return '
            <div class="entityDetailsInner memberLoginWidget">
                <v-style>
                </v-style>
                <p>Please login to access your account.</p>
                <div>
                    <input name="username" class="form-control mt-4" type="text" placeholder="Username or Email" v-model="entity.emailOnUsername">
                    <input name="password" class="form-control mt-2" type="password" placeholder="Password" v-model="entity.password2">
                    <button class="w-100 btn btn-primary mt-3" v-on:click="loginToMemberAccount">Login</button>
                    <div style="text-align:center" class="mt-3"><b>-- OR --</b></div>
                    <div style="text-align:center"><button class="btn btn-secondary mt-3" v-on:click="createNewAccountScreen">Create a New Account Here</button></div>
                </div>
            </div>';
    }

    protected function renderComponentMethods() : string
    {
        return '
            loginToMemberAccount: function() {
                const self = this;
                modal.EngageFloatShield();
                ezLog(Cookie.get("instance"), "Cookie.get(instance)")
                const loginUser = {browserId: Cookie.get("instance"), username: self.entity.emailOnUsername,  password: self.entity.password2};
                ajax.PostExternal("/api/v1/users/log-user-into-core", loginUser, true, function(loginResult) {
                    modal.CloseFloatShield(function() {
                        if (loginResult.success === false) {
                            self.throwErrors(loginResult)
                            return
                        }
    
                        const userData = {
                            userId: loginResult.response.data.user.id,
                            userNum: loginResult.response.data.user.user_id,
                            user: JSON.stringify(loginResult.response.data.user),
                            isLoggedIn: "active",
                            authUserId: loginResult.response.data.user.id
                        }
    
                        Cookie.set("user", JSON.stringify(loginResult.response.data.user))
                        dispatch.broadcast("user_auth", userData)
                        dispatch.broadcast("move_into_portal")
                    }, 1000)
                });
            },
            throwErrors: function(result) {
                
            },
            createNewAccountScreen: function() {
                
            },
        ';
    }
}