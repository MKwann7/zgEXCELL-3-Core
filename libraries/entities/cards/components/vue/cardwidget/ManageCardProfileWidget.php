<?php

namespace Entities\Cards\Components\Vue\CardWidget;

use App\Core\App;
use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\VueProps;
use Entities\Cards\Components\Vue\HelperWidget\DomainManagementWidget;
use Entities\Cards\Models\CardModel;
use Entities\Users\Components\Vue\SearchWidget\SearchUserWidget;

class ManageCardProfileWidget extends VueComponent
{
    protected string $id = "4c140efb-0aa5-4161-b9dc-9f0c4d4477dd";
    protected string $modalWidth = "750";
    protected string $title = "Manage Card Profile";
    protected string $modalTitleForAddEntity = "Manage Card Profile";
    protected string $updateButtonText = "Update";

    protected SearchUserWidget $searchUser;
    protected DomainManagementWidget $domainManagement;

    public function __construct (array $components = [])
    {
        $displayColumns = ["banner", "status"];

        if (userCan("manage-platforms"))
        {
            $displayColumns[] = "platform";
        }

        $displayColumns = array_merge($displayColumns, ["card_name", "card_num", "card_vanity_url", "card_owner_name", "card_contacts", "product", "created_on", "last_updated"]);

        $defaultEntity = (new CardModel())
            ->setDefaultSortColumn("card_num", "DESC")
            ->setDisplayColumns($displayColumns)
            ->setRenderColumns(["card_id", "owner_id", "card_owner_name", "card_name", "card_num", "card_vanity_url", "card_keyword", "product", "card_contacts", "status", "order_line_id", "platform", "company_id", "banner", "favicon", "created_on", "last_updated",]);

        parent::__construct($defaultEntity, $components);

        $this->searchUser = new SearchUserWidget();
        $this->searchUser->setMountType("no_mount");
        $this->searchUser->setComponentsMountType("no_mount");
        $this->searchUser->addParentId($this->getInstanceId());
        $this->addDynamicComponent($this->searchUser,true, false);
        $this->addComponentsList($this->searchUser->getDynamicComponentsForParent());
        $this->addComponent($this->searchUser, false);

        $this->domainManagement = new DomainManagementWidget();
        $this->domainManagement->setMountType("no_mount");
        $this->domainManagement->setComponentsMountType("no_mount");
        $this->domainManagement->addParentId($this->getInstanceId());
        $this->addDynamicComponent($this->domainManagement,true, false);
        $this->addComponentsList($this->domainManagement->getDynamicComponentsForParent());
        $this->addComponent($this->domainManagement, false);

        $this->modalTitleForAddEntity = "Add Card Profile";
        $this->modalTitleForEditEntity = "Edit Card Profile";
        $this->modalTitleForDeleteEntity = "Delete Card Profile";
        $this->modalTitleForRowEntity = "View Card Profile";
    }

    protected function renderComponentDataAssignments (): string
    {
        return '
            entityClone: false,
            templateList: [],
            profileType: "site",
            profileImageUploadUrl: "",
            objMyCropper: null,
            shareButtons: [1,2,3,4],
        ';
    }

    protected function renderComponentHydrationScript () : string
    {
        return parent::renderComponentHydrationScript() . '
        ';
    }

    protected function renderComponentMountedScript(): string
    {
        return '
            dispatch.register("rehydrate_site_in_editor", this, "hydrateCard");
        ';
    }

    protected function renderComponentMethods (): string
    {
        /** @var App $app */
        global $app;

        return '
            hydrateCard: function(data)
            {
                if (data.card)
                {
                    let newCardId = data.card.card_id
                    let oldCardId = this.entityClone.card_id
                    this.entity = data.card
                    this.entityClone = _.clone(data.card)
                    this.hydrateDynamicComponents(this.entityClone, "entity")
                    if (newCardId !== oldCardId) {
                        this.loadMyCropper()
                        this.loadShareButtons()
                    }

                    if (this.templateList.length === 0)
                    {
                        let templateType = "site"
                        switch(this.entityClone.card_type_id) {
                            case 2: templateType = "persona"; break
                            case 3: templateType = "group"; break
                        }
                        this.profileType = templateType
                        this.hydrateSiteDataType("templates", this.templateList, "?type=" + templateType)
                    }
                }
            },
            updateSiteProfile: function()
            {
                let self = this
                
                const elVanity = document.getElementById("vanity_1603190947")
                const elKeyword = document.getElementById("keyword_1603190947")
                
                if (elVanity && elVanity.classList.contains("error-validation")) { return }
                if (elKeyword && elKeyword.classList.contains("error-validation")) { return }
                
                const url = "api/v1/cards/update-site-profile?card_id=" + this.entityClone.card_id
                
                let settings = {}
                for (let settingIndex in this.entityClone.Settings) {
                    ezLog(settingIndex, "settingIndex")
                    if (settingIndex !== "avatar") {
                        settings[settingIndex] = this.entityClone.Settings[settingIndex] ? this.entityClone.Settings[settingIndex] : "___REMOVE___"
                    }
                }

                const entityNew = {
                    card_id: this.entityClone.card_id,
                    owner_id: this.entityClone.owner_id,
                    card_user_id: this.entityClone.card_user_id,
                    card_name: this.entityClone.card_name,
                    card_domain: this.entityClone.card_domain,
                    status: this.entityClone.status,
                    card_vanity_url: this.entityClone.card_vanity_url,
                    card_keyword: this.entityClone.card_keyword,
                    template_id: this.entityClone.template_id,
                    settings: settings
                };
                
                modal.EngageFloatShield()
                 
                ajax.Post(url, entityNew, function(result)  {
                    if (result.success === false) {
                        return
                    }
                    
                    let templateChange = false
                    
                    if (self.entityClone.template_id != self.entity.template_id) {
                        templateChange = true
                    }

                    self.entity.card_name = self.entityClone.card_name
                    self.entity.owner_id = self.entityClone.owner_id
					self.entity.card_owner_name = result.response.data.card.card_owner_name
                    self.entity.card_keyword = self.entityClone.card_keyword
                    self.entity.card_vanity_url = self.entityClone.card_vanity_url
                    self.entity.card_domain = self.entityClone.card_domain
                    self.entity.template_id = self.entityClone.template_id
                    self.entity.template_name = self.getTemplateNameById(self.entityClone.template_id)
                    self.entity.status = self.entityClone.status
                    self.entity.Settings = self.entityClone.Settings
                    
                    Slim.save(self.objMyCropper, function() {
                    })
                    
                    dispatch.broadcast("rehydrate_site_in_editor", {card: self.entity})
                    dispatch.broadcast("reload_site_profile_in_editor", {card: self.entity, templateChange: templateChange})

                    let vue = self.findApp(self)
                    vue.$forceUpdate()
                    
                    setTimeout(function() {
                        modal.CloseFloatShield()
                    },500);
                });
            },
            getTemplateNameById: function(templateId)
            {
                for (currTemplate of this.templateList)
                {
                    if (currTemplate.card_template_id == templateId) { return currTemplate.name }
                }
            },
            hydrateSiteDataType: function(type, list, query)
            {
                let self = this;
                if (!query) query = ""
                let templateQuery = "/api/v1/cards/get-site-" + type + query
                ajax.Get(templateQuery, null, function(result) 
                {
                    if (result.success === false)
                    {
                        return
                    }

                    const templates = Object.entries(result.response.data.list)
                    
                    templates.forEach(function([id, currTemplate])
                    {
                        list.push(currTemplate)
                    });                    
                    self.$forceUpdate()
                });
            },
            loadShareButtons: function()
            {
                if (typeof this.entityClone.Settings === "undefined") return
                for(let currIndex of this.shareButtons) {
                    const indexLabel = \'shareButton_\' + currIndex + \'_label\'
                    if (!this.entityClone.Settings[indexLabel]) {
                        this.entityClone.Settings[indexLabel] = ""
                    }
                }
            },
            loadMyCropper: function()
            {
                const self = this;
                self.objMyCropper = document.getElementById("my-cropper-avatar")
                if (self.objMyCropper === null) {
                    setTimeout(function() {
                        self.loadMyCropper()
                    },10)
                    return
                }
    
                Slim.destroy(self.objMyCropper)
                this.loadMyCropperData(self.entityClone, self.objMyCropper)
            },
            loadMyCropperData: function(entity, objMyCropper)
            {
                const self = this
                let cropperChildren = objMyCropper.getElementsByTagName("img")
                if (typeof entity.Settings.avatar === "string" && entity.Settings.avatar !== "") {
                    const mediaArray = entity.Settings.avatar.split("|");
                    cropperChildren[0].src = imageServerUrl() + mediaArray[1]
                } else {
                    cropperChildren[0].removeAttribute("src")
                }
                
                self.setProfileImageUploadUrl()
    
                Slim.create(
                    objMyCropper,
                    Slim.getOptionsFromAttributes(objMyCropper, {browseButton: false, uploadButton: false, }),
                    {app: self, method: "updateEntityAvatar"},
                    {app: self, method: "removeEntityAvatar"}
                )
                
                Slim.setUploadUrl(objMyCropper, self.profileImageUploadUrl)
            },
            updateEntityAvatar: function(imageData)
            {
                let cropperChildren = this.objMyCropper.getElementsByTagName("img");
                const url = imageData.path
                if (cropperChildren[0]) {
                    cropperChildren[0].src = imageServerUrl() + url
                    this.entityClone.Settings.avatar = url
                    const postUrl = "/api/v1/cards/save-persona-avatar-url?persona=" + this.entityClone.card_id
                    const postData = {avatar_url: url}
                    ajax.PostExternal(postUrl, postData, true, function(result) {
                        ezLog(result, "updateEntityAvatar")
                    });
                }
            },
            setProfileImageUploadUrl: function()
            {
                this.profileImageUploadUrl = \'/api/v1/media/upload-image?entity_id=\' + this.entityClone.card_id + \'&user_id=\' + this.entityClone.owner_id + \'&uuid=\' + this.userId + \'&entity_name=persona&class=persona-avatar\';
            },
            removeEntityAvatar: function()
            {
                this.entityClone.Settings.avatar = "__remove__";
            },
        ';
    }

    protected function renderTemplate() : string
    {
        switch ($this->applicationType) {
            case "maxtech":
                $this->updateButtonText = "Update Site Profile";
                break;
            default:
                $this->updateButtonText = "Update Card Info";
        }

        return '
        <div class="editEntityProfile">
            <v-style type="text/css">
            
                .editEntityProfile .dynamic-search-list {
                    position: absolute;
                    width: calc(100% - 35px);
                    background: #fff;
                    margin-left: 5px;
                    z-index: 1000;
                    max-height:40vh;
                    overflow-y:auto;
                }
                .editEntityProfile .dynamic-search-list > table {
                    width: 100%;
                }
                .editEntityProfile .dynamic-search-list > table > thead {
                    box-shadow: rgba(0,0,0,0.2) 0px 2px 5px;
                    background-color: #007bff;
                    color: #fff !important;
                }
                .editEntityProfile .dynamic-search-list > table tr {
                    cursor:pointer;
                }
                .editEntityProfile .dynamic-search-list > table tr:hover {
                    background-color:#d5e9ff !important;
                }
            </v-style>
            <div v-if="entity">
                <div v-if="userAdminRole" class="augmented-form-items mb-3">
                    '. $this->registerAndRenderDynamicComponent(
                    $this->searchUser,
                    "view",
                    [
                        new VueProps("entityData", "object", "entity"),
                    ]
                ) .'
                </div>
                <div v-if="entityClone.Settings && entityClone.card_type_id == 2" class="augmented-form-items" style="margin-bottom: 15px;">
                    <table class="table" style="margin-bottom:2px;">
                        <tr>
                            <td style="width:300px;">
                                <div class="width300px">
                                    <div class="memberAvatarImage">
                                        <div class="slim" data-ratio="1:1" data-force-size="650,650" v-bind:data-service="profileImageUploadUrl" id="my-cropper-avatar" style="background-image: url(/_ez/images/users/defaultAvatar.jpg); background-size: cover;background-position: center;">
                                            <input type="file"/>
                                            <img width="300" height="300" alt="">
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="pop-up-dialog-sub-title-text ml-2 pl-1 mt-2">Persona Profile</span>
                                <table class="table" style="margin-bottom:2px;">
                                    <tr>
                                        <td style="width:117px;vertical-align: middle;">Name</td>
                                        <td style="position:relative;" colspan="3">
                                            <input v-model="entityClone.Settings.display_name" class="form-control">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width:117px;vertical-align: middle;">Title</td>
                                        <td style="position:relative;">
                                            <input v-model="entityClone.Settings.title" class="form-control">
                                        </td>
                                        <td style="width:117px;vertical-align: middle;">Company</td>
                                        <td style="position:relative;">
                                            <input v-model="entityClone.Settings.company" class="form-control">
                                        </td>
                                    </tr>
                                </table>
                                <span class="pop-up-dialog-sub-title-text ml-2 pl-1 mt-4">Persona Contact Information</span>
                                <table class="table" style="margin-bottom:2px;">
                                    <tr>
                                        <td style="width:117px;vertical-align: middle;">Email</td>
                                        <td style="position:relative;">
                                            <input v-model="entityClone.Settings.contact_email" class="form-control">
                                        </td>
                                        <td style="width:117px;vertical-align: middle;">Phone</td>
                                        <td style="position:relative;">
                                            <input v-model="entityClone.Settings.contact_phone" class="form-control">
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table class="table" style="margin-bottom:2px;">
                        <tr v-for="currIndex in shareButtons">
                            <td style="width:25%;">
                                <select v-model="entityClone.Settings[\'shareButton_\' + currIndex + \'_label\']" class="form-control">
                                    <option disabled value="">--Select Share Type--</option>
                                    <option value="phone">Phone</option>
                                    <option value="sms">SMS</option>
                                    <option value="email">Email</option>
                                    <option value="website">Website</option>
                                    <option value="facebook">Facebook</option>
                                    <option value="twitter">Twitter</option>
                                    <option value="instagram">Instagram</option>
                                    <option value="linkedin">LinkedIn</option>
                                    <option value="youtube">YouTube</option>
                                    <option value="tiktok">Tik Tok</option>
                                </select>
                            </td>
                            <td><input v-model="entityClone.Settings[\'shareButton_\' + currIndex + \'_value\']"  class="form-control"></td>
                        </tr>
                    </table>
                </div>
                <div v-if="entityClone.Settings && entityClone.card_type_id == 1">
                    <table class="table no-top-border">
                        <tbody>
                            <tr>
                                <td style="width:125px;vertical-align: middle;">Name</td>
                                <td><input v-model="entityClone.card_name" class="form-control" type="text" placeholder="Enter Name..."></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <span v-if="entityClone.Settings && entityClone.card_type_id == 2 && entityClone.template_id != 6" class="pop-up-dialog-main-title-text mb-2">Domain Information</span>
                <div v-if="entityClone.template_id != 6">
                    '. $this->registerAndRenderDynamicComponent(
                        $this->domainManagement,
                        "view",
                        [
                            new VueProps("entityData", "object", "entity"),
                        ]
                    ) .'
                </div>
                <span class="pop-up-dialog-main-title-text mb-2">Status</span>
                <div>
                    <table class="table no-top-border">
                        <tr v-if="userAdminRole">
                                    <td style="width:125px;vertical-align: middle;">Template</td>
                                    <td>
                                        <select v-model="entityClone.template_id" class="form-control site-template-select">
                                            <option value="">--Select Template--</option>
                                            <option v-for="currTemplate in templateList" v-bind:value="currTemplate.card_template_id" selected="">{{ currTemplate.name }}</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr v-if="userAdminRole">
                                    <td style="width:125px;vertical-align: middle;">Status</td>
                                    <td>
                                        <select v-model="entityClone.status" class="form-control">
                                            <option value="Pending">Pending</option>
                                            <option value="Active">Active</option>
                                            <option value="Build">Build</option>
                                            <option value="Inactive">Inactive</option>
                                            <option value="Cancelled">Cancelled</option>
                                            <option value="Disabled">Disabled</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                <button v-on:click="updateSiteProfile" class="buttonID9234597e456 btn btn-primary w-100">' . $this->updateButtonText .'</button>
            </div>
        </div>';
    }
}