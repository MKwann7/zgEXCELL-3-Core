<?php

namespace Entities\Directories\Components\Vue\Maxtech\Groupwidget;

use App\Core\App;
use App\Core\AppModel;
use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\VueProps;
use Entities\Users\Components\Vue\SearchWidget\SearchUserWidget;

class DirectoryProfileWidget extends VueComponent
{
    protected string $id =  "71b89e7c-07d9-4427-b366-ad96b587228e";
    protected string $title = "Directory Profile";
    protected string $modalTitleForAddEntity = "Directory Profile";

    protected SearchUserWidget $searchUser;

    public function __construct(?AppModel $entity = null)
    {
        parent::__construct($entity);

        $this->searchUser = new SearchUserWidget();
        $this->searchUser->setMountType("no_mount");
        $this->searchUser->setComponentsMountType("no_mount");
        $this->searchUser->addParentId($this->getInstanceId());
        $this->addDynamicComponent($this->searchUser,true, false);
        $this->addComponentsList($this->searchUser->getDynamicComponentsForParent());
        $this->addComponent($this->searchUser, false);
    }

    protected function renderComponentDataAssignments (): string
    {
        return '
            entityClone: false,
            templateList: [],
            profileType: "site",
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
        ';
    }

    protected function renderTemplate() : string
    {
        return '<div class="directoryManagementWidget">
            <v-style type="text/css">
            </v-style>
            <div v-if="entity" class="directoryManagementInner">
                <div class="row mt-3">
                    <div class="col-sm-12">
                       <div v-if="userAdminRole" class="augmented-form-items mb-3">
                            '. $this->registerAndRenderDynamicComponent(
                                $this->searchUser,
                                "view",
                                [
                                    new VueProps("entityData", "object", "entity"),
                                ]
                            ) .'
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Directory Information</h5>
                                <p class="card-text">Setup your directory here! Control identification information, settings, and restrictions.</p>
                                <div>
                                    <table class="table no-top-border">
                                        <tbody>
                                            <tr>
                                                <td style="width: 125px; vertical-align: middle;">Title</td> 
                                                <td style="width: calc(50% - 125px)">
                                                    <input v-model="entity.title" type="text" class="form-control">
                                                </td> 
                                                <td style="width: 125px; vertical-align: middle;">Description</td> 
                                                <td style="width: calc(50% - 125px)">
                                                    <input type="text" class="form-control">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 125px; vertical-align: middle;">Member Approval</td> 
                                                <td style="width: calc(50% - 125px)">
                                                    <select type="text" class="form-control">
                                                        <option value="display_asc">Automatic</option> 
                                                        <option value="display_desc">Manual</option> 
                                                        <option value="company_asc">Manual | Widget Override</option>
                                                    </select>
                                                </td>
                                                <td style="width: 125px; vertical-align: middle;">Member Limit</td> 
                                                <td style="width: calc(50% - 125px)">
                                                    <input type="number" class="form-control">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ';
    }
}