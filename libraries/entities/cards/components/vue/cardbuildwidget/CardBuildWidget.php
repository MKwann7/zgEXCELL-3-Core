<?php

namespace Entities\Cards\Components\Vue\CardBuildWidget;

use App\Core\AppModel;
use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\Management\VueManageData;
use App\Website\Vue\Classes\VueProps;
use Entities\Cards\Components\Vue\CardWidget\ManageCardConnectionsListWidget;
use Entities\Cards\Components\Vue\CardWidget\ManageCardImageWidget;
use Entities\Cards\Components\Vue\CardWidget\ManageCardSocialMediaWidget;
use Entities\Cards\Components\Vue\CardWidget\SwapCardConnectionWidget;

class CardBuildWidget extends VueComponent
{
    protected string $id = "61631a8c-8f7a-4b11-ab62-d7429bc2a1e0";
    protected string $modalWidth = "750";
    protected ?VueComponent $manageDataWidget = null;

    public function __construct(?AppModel $entity = null, $name = "Card Build Widget", $props = [])
    {
        $this->loadProps($props);
        $this->name = $name;

        parent::__construct($entity);

        $this->manageDataWidget = $this->registerDynamicComponent(
            new VueManageData(),
            "view",
            [
                new VueProps("dataRow", "object", "currPage"),
                new VueProps("dataField", "string", "dataField"),
                new VueProps("dataType", "string", "dataType"),
                new VueProps("userId", "string", "userId")
        ]);

        $this->modalTitleForAddEntity = "Add " . $name;
        $this->modalTitleForEditEntity = "Edit " . $name;
        $this->modalTitleForDeleteEntity = "Delete " . $name;
        $this->modalTitleForRowEntity = "View " . $name;
    }

    protected function renderComponentDataAssignments() : string
    {
        return "
            buildEntity: null,
            originalEntity: null,
            formIsDirty: false,
            buildPage: 1,
            dynCardShareButtonsComponent: null,
            dynCardShareButtonsComponentInstance: null,
            dynCardSocialMediaComponent: null,
            dynCardSocialMediaComponentInstance: null,
        ";
    }

    protected function renderComponentMethods() : string
    {
        global $app;
        $loggedInUser = $app->getActiveLoggedInUser();
        return '
           checkForDuplicateVanityUrl: function(entity)
            {
                const el = document.getElementById("vanity_56456456456");
                
                if (entity.card_vanity_url === "") 
                {    
                    el.classList.remove("pass-validation");
                    el.classList.remove("error-validation");
                    return
                }
                
                const url = "/api/v1/cards/check-vanity-url?vanity_url=" + entity.card_vanity_url + "&card_id=" + entity.card_id;

                ajax.Get(url, null, function(result) 
                {
                    if (result.match === true) 
                    {
                        el.classList.remove("pass-validation");
                        el.classList.add("error-validation");
                        return;
                    }
                    
                    el.classList.add("pass-validation");
                    el.classList.remove("error-validation");
                });
            },
            checkForDuplicateKeyword: function(entity)
            {
                const el = document.getElementById("keyword_56456456456");
                
                if (entity.card_keyword === "") 
                {    
                    el.classList.remove("pass-validation");
                    el.classList.remove("error-validation");
                    return
                }
                
                const url = "api/v1/cards/check-keyword?keyword=" + entity.card_keyword + "&card_id=" + entity.card_id;
                
                ajax.Get(url, null, function(result) 
                {
                    if (result.match === true) 
                    {
                        el.classList.remove("pass-validation");
                        el.classList.add("error-validation");
                        return;
                    }
                    
                    el.classList.add("pass-validation");
                    el.classList.remove("error-validation");
                });
            },
            loadCardShareButtonsComponent: function(component)
            {
                let self = this;
                if (typeof self.$refs.dynCardShareButtonsRef === "undefined")
                {
                    setTimeout(function() {
                        self.loadCardShareButtonsComponent(component);
                    }, 50);
                    return;
                }
                
                self.dynCardShareButtonsComponentInstance = self.$refs.dynCardShareButtonsRef;
                self.dynCardShareButtonsComponentInstance
                    .setModalComponentInstance(component.instanceId, true)
                    .injectDefaultData(component.instanceId, component.parentInstanceId, "edit", self.buildEntity, self.mainEntityList, {}, {}, \''.$loggedInUser->sys_row_id.'\', \''.$loggedInUser->user_id.'\')
                    .hydrateComponent({card: self.buildEntity, createNew: true, editConnection: true, deleteConnection: true}, true, function(result) {
                });
            },
            loadCardSocialMediaComponent: function(component)
            {
                let self = this;
                if (typeof self.$refs.dynCardSocialMediaRef === "undefined")
                {
                    setTimeout(function() {
                        self.loadCardSocialMediaComponent(component);
                    }, 50);
                    return;
                }
                
                self.dynCardSocialMediaComponentInstance = self.$refs.dynCardSocialMediaRef;
                self.dynCardSocialMediaComponentInstance
                    .setModalComponentInstance(component.instanceId, true)
                    .injectDefaultData(component.instanceId, component.parentInstanceId, "edit", self.buildEntity, self.mainEntityList, {}, {}, \''.$loggedInUser->sys_row_id.'\', \''.$loggedInUser->user_id.'\')
                    .hydrateComponent({card: self.buildEntity, createNew: false, editConnection: false, deleteConnection: true}, true, function(result) {
                });
            },
            loadCardConnectionsWidget: function()
            {
                let self = this;
                ' . $this->activateDynamicComponentById(ManageCardConnectionsListWidget::getStaticId(), "", "edit", "self.buildEntity", "self.mainEntityList", null, "this", false,"function(component) {
                    self.dynCardShareButtonsComponent = component.rawInstance;                            
                    self.loadCardShareButtonsComponent(component);
                }", false) . '
            },
            loadCardSocialMediaWidget: function()
            {
                let self = this;
                ' . $this->activateDynamicComponentById(ManageCardSocialMediaWidget::getStaticId(), "", "edit", "self.buildEntity", "self.mainEntityList", null, "this", false,"function(component) {
                    self.dynCardSocialMediaComponent = component.rawInstance;                            
                    self.loadCardSocialMediaComponent(component);
                }", false) . '
            },
            addCardSocialMedia: function()
            {
                let self = this;
                let socialMedia = this.buildEntity.SocialMedia;
                let connectionList = [];
                let swapType = "socialmedia";
                let ownerId = this.buildEntity.owner_id;
                let pseudoElement = {card_id: this.buildEntity.card_id};
                
                '. $this->activateDynamicComponentByIdInModal(SwapCardConnectionWidget::getStaticId(),"", "add", "pseudoElement", "socialMedia", ["ownerId"=> "ownerId", "swapType" => "swapType", "functionType" =>"'save new'", "createNew" => "true"], "this", true,"function(component) {
                    let modal = self.findModal(self);
                    modal.vc.setTitle('Add Social Media Link');
                }") . '
            },
            addCardPageItem: function()
            {
                appCart.openPackagesByClass("card page", {id: this.entity.card_id, type: "card"}, this.entity.owner_id, this.entity.owner_id)
                    .registerEntityListAndManager("", "' . self::getStaticId() . '");
            },
            editCardImage: function(entity, type, imageClass, field, imageSize)
            {
                ' . $this->activateDynamicComponentByIdInModal(ManageCardImageWidget::getStaticId(), "", "edit", "this.entity", "this.mainEntityList", ["imageType" => "type", 'imageClass'=> 'imageClass', 'entityField'=> 'field',  'imageSize'=> "imageSize"], "this", true,"function(component) {
                    //console.log(component);
                }") . '
            },
            showErrorImage: function(entity, label)
            {
                entity[label] = "'.$app->objCustomPlatform->getFullPortalDomainName().'/_ez/images/no-image.jpg";
            },
            goBackToCard: function(page) 
            {
                this.autoSaveBuildForm();
                if (this.buildPage > page) this.buildPage = page;
            },
            goToNextStep: function() 
            {
                this.autoSaveBuildForm();
                ++this.buildPage;
            },
            submitBuildForm: function() 
            {
                let self = this;
                const url = "/api/v1/cards/submit-build-form-data";
                modal.EngageFloatShield();
                this.autoSaveBuildForm(function(){
                    ajax.Post(url, {card_id: self.buildEntity.card_id}, function(result) {
                        modal.CloseFloatShield(function() { self.originalEntity.status = result.response.data.card.status; }, 2500);
                    });
                });
            },
            startAutoSave: function()
            {
                let self = this;
                setTimeout(function() {
                    self.autoSaveBuildForm();
                    self.startAutoSave();
                }, 10000);
            },
            autoSaveBuildForm: function(callback)
            {
                if (globalClassList("error-validation").length > 0)
                {
                    ezLog("Error in Validation. Unable to Save");
                    return;
                }
                
                let self = this;
                const url = "/api/v1/cards/auto-save-build-form-data";
                const formData = {
                    card_id: this.buildEntity.card_id,
                    card_name: this.buildEntity.card_name, 
                    card_vanity_url: this.buildEntity.card_vanity_url, 
                    card_keyword: this.buildEntity.card_keyword, 
                    main_color: this.buildEntity.mainColor, 
                    secondary_color: this.buildEntity.secondaryColor, 
                    pages: this.getPagesFromBuilder()
                };
                
                ajax.Post(url, formData, function(result)
                {
                    if (result.success === false) 
                    {
                        return;
                    }
                    
                    self.formIsDirty = false;  
                    if (typeof callback === "function") callback();     
                });               
                
            },
            getPagesFromBuilder: function()
            {
                let pages = [];
                for(let currPage of this.editableCardPages)
                {
                    if (typeof currPage.element === "undefined")
                    {
                        continue;
                    }
                    
                    const pageData = {
                        page_id: currPage.card_tab_id,
                        page_rel_id: currPage.card_tab_rel_id,
                        title: currPage.title,
                        header: currPage.header,
                        content: btoa(this.getHtmlCode(currPage.element)),
                    };
                    
                    pages.push(pageData);
                }
                
                return pages;
            },
            getHtmlCode: function(editor)
            {
                if (editor.froalaEditor("codeView.isActive"))
                {
                    return editor.froalaEditor("codeView.get").replace(/[\t\n]+/, "");
                }
                
                return editor.froalaEditor("html.get").replace(/[\t\n]+/, "");
            },
            getAutoSaveForBuildForm: function()
            {
                let self = this;
                const url = "/api/v1/cards/get-auto-save-for-build-form?card_id=" + this.buildEntity.card_id;
                ajax.Get(url, function(result)
                {
                    if (result.success === false) 
                    {
                        return;
                    }
                    
                    self.buildEntity.card_name = result.response.data.card.card_name;
                    self.buildEntity.card_keyword = result.response.data.card.card_keyword;
                    self.buildEntity.card_vanity_url = result.response.data.card.card_vanity_url;
                    self.buildEntity.mainColor = getJsonSettingDecoded(result.response.data.card.card_data, "style.card.color.main", "ff0000");
                    self.buildEntity.secondaryColor = getJsonSettingDecoded(result.response.data.card.card_data, "style.card.color.secondary", "ff0000");
                    
                    if (typeof result.response.data.card.pages === "undefined" || result.response.data.card.pages.length === 0)
                    {
                        return;
                    }
                    
                    setTimeout(function() 
                    {
                        for(let currTabIndex in self.buildEntity.Tabs)
                        {
                            for(let currPage of result.response.data.card.pages)
                            {
                                if (currPage.card_page_id === self.buildEntity.Tabs[currTabIndex].card_tab_id)
                                {
                                    self.buildEntity.Tabs[currTabIndex].title = currPage.title;
                                    self.buildEntity.Tabs[currTabIndex].element.froalaEditor("html.set", atob(currPage.content));
                                    self.buildEntity.Tabs[currTabIndex].header = getJsonSettingDecoded(currPage.card_tab_data, "custom.header");
                                    
                                    for(let currManager of self.$refs.pageManageDataWidget)
                                    {
                                        currManager.$forceUpdate();
                                    }
                                }
                            }
                        }
                        self.$forceUpdate();
                    },500);
                });
            },
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return '
            let self = this;

            this.buildEntity = _.clone(props.entity);
            this.originalEntity = props.entity;
            
            this.loadCardConnectionsWidget();
            this.loadCardSocialMediaWidget();
            
            this.buildEntity.mainColor = getJsonSettingDecoded(this.buildEntity.card_data, "style.card.color.main", "ff0000");
            this.buildEntity.secondaryColor = getJsonSettingDecoded(this.buildEntity.card_data, "style.card.color.secondary", "ff0000");
            
            if (this.originalEntity.status === "Build")
            {
                this.getAutoSaveForBuildForm();
                this.startAutoSave();
            }
        ';
    }

    protected function renderComponentComputedValues() : string
    {
        return '
            editableCardPages: function()
            {
                let self = this;
                if (typeof self.buildEntity === "undefined" || self.buildEntity === null || typeof self.buildEntity.Tabs === "undefined") { return []; }
                
                let filteredCardPages = [];
                
                for (let currPage of self.buildEntity.Tabs)
                {
                    if(currPage.card_tab_rel_type != \'mirror\' && currPage.library_tab != 1)
                    {
                        filteredCardPages.push(currPage);
                    }
                }
                
                return filteredCardPages;
            },
        ';
    }

    //

    protected function renderTemplate() : string
    {
        global $app;
        return '
        <div class="cardBuildWidget" style="margin-top: 25px;">
            <v-style type="text/css">
                .cardBuildWidget .entityDetailsInner ul li {
                    width: 100%;
                    display: flex;
                    flex-direction: row;
                    align-content: center;
                    align-items: center;
                    margin-bottom:10px;
                }
                .cardBuildWidget .entityDetailsInner ul li p {
                    display:block;
                }
                .cardBuildWidget .entityDetailsInner ul li .divCell:first-child {
                    width: 200px;
                    display: flex;
                }
                .cardBuildWidget .entityDetailsInner ul li .divCell:last-child {
                    width: 100%;
                    vertical-align: middle;
                    display: flex;
                }
                .cardBuildWidget .entityDetailsInner ul li .divCell:last-child input {
                    width: 100%;
                }
                .cardBuildWidget .entityDetailsInner ul li .cellInformation.divCell {
                    font-size: 13px;
                }
                .cardBuildWidget .entityDetailsInner ul li .cellInformation.divCell span {
                    top: 3px;
                    position: relative;
                    font-size: 16px;
                    margin-right: 5px;
                }
                .cardBuildWidget .entityDetailsInner td {
                    border-top:0 transparent;
                }
                .cardBuildWidget .sharebuttons .entityDetailsInner {
                    width: 100%;
                    position: relative;
                }
                .cardBuildWidget .cardPages ul {
                    width: 100%;
                }
                .cardBuildWidget .cardBuildRibbon {
                    display: flex;
                    flex-direction:row;
                    margin: auto 100px;
                }
                .cardBuildWidget .cardBuildRibbonItem {
                    display: flex;
                }
                .cardBuildWidget .cardBuildRibbonItem span {
                    text-align:center;
                    padding: 25px;
                    background: url(/media/images/tracker-arrow-outline-right.png) no-repeat right center / auto 100%, #fff;
                    position:relative;
                    margin: auto;
                    width: 100%;
                    border-top:2px solid #bbb;
                    border-bottom:2px solid #bbb;
                    text-indent:-10px;
                    height: 75px;
                }
                .cardBuildWidget .cardBuildRibbonItem:last-child span {
                    border-right:2px solid #bbb;
                    background: #fff;
                }
                .cardBuildWidget .cardBuildRibbonItem:first-child span {
                    border-left:2px solid #bbb;
                    background: url(/media/images/tracker-arrow-outline-right.png) no-repeat right center / auto 100%, #fff;
                }
                .cardBuildWidget .cardBuildRibbonItem.active {
                    margin-left: -20px;
                    margin-right: -20px;
                    position: relative;
                    z-index: 50;
                }
                .cardBuildWidget .cardBuildRibbonItem.active span {
                    background: url(/media/images/tracker-arrow-mask-left.png) no-repeat left center / auto 100%, url(/media/images/tracker-arrow-mask-right.png) no-repeat right center / auto 100%, #007bff;
                    color: #fff !important;
                }
                .cardBuildWidget .cardBuildRibbonItem.active:first-child span {
                    background: url(/media/images/tracker-arrow-mask-right.png) no-repeat right center / auto 100%, #007bff;
                    color: #fff !important;
                }
                .cardBuildWidget .cardBuildRibbonItem.active:last-child span {
                    background: url(/media/images/tracker-arrow-mask-left.png) no-repeat left center / auto 100%, #007bff;
                    color: #fff !important;
                }
                .cardBuildWidget .cardBuildRibbonItem:first-child span {
                    border-radius: 50px 0 0 50px;
                }
                .cardBuildWidget .cardBuildRibbonItem:last-child span {
                    border-radius: 0 50px 50px 0;
                }
                .cardBuildWidget .cardBuildRibbonItem:last-child span:after {
                    right: 0;
                    border-width: 0;
                    border-color: transparent transparent transparent transparent;
                }
                .cardBuildWidget .cardBuildRibbonItem:first-child span:before {
                    left: 0;
                    border-width: 0;
                    border-color: transparent transparent transparent transparent;
                }
                body .cardBuildWidget .btn-secondary {
                    color:#fff !important;
                }
                .cardBuildWidget .entityDetailsInner > button {
                    margin-top:20px;
                }
                .cardBuildWidget .cardColors .cardColorsWrapper {
                    display: block;width:100%;
                }
                .cardBuildWidget .cardColors .cardItemLabel {
                    width:100%;
                }
                .cardBuildWidget .cardColors .cardItemCell {
                    overflow:visible !important;
                    max-width: 100% !important;      
                }
                .cardBuildWidget .cardColors .cardItemCell .flex-column {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    height: 100%;
                    background: #eee;
                    border-radius: 10px;
                    padding: 15px;     
                }
                .cardBuildWidget .cardColors .cardItemCellTitle {
                    width:225px;
                    text-align:center;
                }
                .cardBuildWidget .pageBlockWrapper {
                    background: #eee;
                    border-radius: 10px;
                    padding: 15px;
                    width: 100%;
                }
                .cardBuildWidget .cardPages {
                    align-items: stretch !important;
                }
                .cardBuildWidget h4 {
                    margin-bottom: .5rem;
                }
                @media (max-width:1150px)
                {
                    .cardBuildWidget .cardBuildRibbon {
                        margin: auto 35px;
                    }
                    .cardBuildWidget .cardBuildRibbon * {
                        font-size:13px;
                    }
                }
                @media (max-width:750px)
                {
                    .cardBuildWidget .cardBuildRibbon {
                        margin: auto 0;
                    }
                    .cardBuildWidget .cardBuildRibbon * {
                        font-size:11px;
                    }
                }
            </v-style>'.'
            <div v-if="buildEntity !== null && ( originalEntity.status === \'Build\' || originalEntity.status === \'BuildComplete\')">
                <div class="entityDetailsInner" style="margin-top:5px;">'.'
                    <ul class="cardBuildRibbon">
                        <li class="cardBuildRibbonItem" v-bind:class="{active: buildPage === 1, pointer: buildPage > 1}" @click="goBackToCard(1)"><span>Card Profile</span></li>
                        <li class="cardBuildRibbonItem" v-bind:class="{active: buildPage === 2, pointer: buildPage > 2}" @click="goBackToCard(2)"><span>Share/Social Media</span></li>
                        <li class="cardBuildRibbonItem" v-bind:class="{active: buildPage === 3, pointer: buildPage > 3}" @click="goBackToCard(3)"><span>Graphics/Images</span></li>
                        <li class="cardBuildRibbonItem" v-bind:class="{active: buildPage === 4, pointer: buildPage > 4}" @click="goBackToCard(4)"><span>Card Pages</span></li>
                        <li class="cardBuildRibbonItem" v-bind:class="{active: buildPage === 5}" ><span>Submit</span></li>
                    </ul>
                    <ul v-show="buildPage === 1">
                        <li>
                            <div class="divCell" style="display:block;margin-top: 15px;">
                                <h3>Let\'s get your card built!</h3><p>Below you\'ll find the basic profilewidget information for your new '.$app->objCustomPlatform->getPortalName().' card. <b>Let\'s get started!</b></p>
                                <p><span style="background:#17a2b8;padding:5px 10px;border-radius: 5px;color:#fff !important;display:inline-block;">IMPORTANT: Also, we\'re going to auto-save this form for you, so you don\'t have to worry about losing any of it.</span></p>
                            </div>
                        </li>
                        <li>
                            <div class="divCell" style="display:block;">
                                <div style="background-color: #6c757d;width:100%;height:10px;margin-bottom: 15px;"></div>
                            </div>
                        </li>
                        <li>
                            <div class="divCell">Card Name</div>
                            <div class="divCell"><input v-model="buildEntity.card_name" class="form-control" type="text" placeholder="Enter Your Card\'s Name"></div>
                        </li>
                        <li>
                            <div class="divCell">Unique Card Vanity Url</div>
                            <div class="divCell"><input id="vanity_56456456456" v-on:blur="checkForDuplicateVanityUrl(buildEntity)"  v-model="buildEntity.card_vanity_url" class="form-control" type="text" placeholder="(Optional)"></div>
                        </li>
                        <li>
                            <div class="divCell"></div>
                            <div class="divCell cellInformation"><span class="fas fa-info-circle"></span> (Optional) A custom vanity URL extension. e.g. ' . $app->objCustomPlatform->getFullPublicDomainName() . '/your-custom-url </div>
                        </li>
                        <li>
                            <div class="divCell">Unique Card Keyword</div>
                            <div class="divCell"><input id="keyword_56456456456" v-on:blur="checkForDuplicateKeyword(buildEntity)"  v-model="buildEntity.card_keyword" class="form-control" type="text" placeholder="(Optional)"></div>
                        </li>
                        <li>
                            <div class="divCell"></div>
                            <div class="divCell cellInformation"><span class="fas fa-info-circle"></span> (Optional) A keyword for communication inside ' . $app->objCustomPlatform->getPortalName() . '\'s community and beyond.</div>
                        </li>
                        <li style="display: none;">
                            <div class="divCell">Card User</div>
                            <div class="divCell"><input v-model="buildEntity.card_user_id" class="form-control" type="text" placeholder="(Optional)"></div>
                        </li>
                        <li style="display: none;">
                            <div class="divCell"></div>
                            <div class="divCell cellInformation"><span class="fas fa-info-circle"></span> (Optional) If different than owner.</div>
                        </li>
                    </ul>'.'
                    <ul v-show="buildPage === 2">
                        <li>
                            <div class="divCell" style="display:block;margin-top: 15px;">
                                <p>Alright. <b>Next we will setup your social links!</b> The share buttons on your card let visitors connect with you immediately! From calling your business line to texting your cell number, a quick email to you, or your company website, adding your card\'s share buttons is easy!</p>
                                <p><span style="background:#17a2b8;padding:5px 10px;border-radius: 5px;color:#fff !important;display:inline-block;">NOTE: Double click on a share button spot to assign an action to it! You can create these actions inside the pop-up modal.</span></p>
                                <p>Lastly, don\'t forget your social media profilewidget links! They\'re easy to add and offer additional resources for your visitors to connect with you via your new digital card!</p>
                            </div>
                        </li>
                        <li>
                            <div class="divCell" style="display:block;">
                                <div style="background-color: #6c757d;width:100%;height:10px;margin-bottom: 15px;"></div>
                            </div>
                        </li>
                        <li>
                            <div style="display: block;width:100%;">
                                <div style="width:100%;"><h4>Share Buttons</h4></div>
                                <div class="sharebuttons" style="width:100%;">
                                   <component ref="dynCardShareButtonsRef" :is="dynCardShareButtonsComponent" :card="buildEntity"></component>
                               </div>
                               <div style="position:relative; top:-10px;"><span class="fas fa-info-circle"></span> Set your card share buttons! Either select existing connections registered with your account or create new ones!</div>
                           </div>
                        </li>
                        <li>
                            <div style="display: block;width:100%;margin-top:15px;">
                                <div style="width:100%;"><h4>Social Media <button class="btn btn-sm btn-primary" v-on:click="addCardSocialMedia()" style="margin-left: 5px;margin-top: -4px;">Add</button></h4></div>
                                <div class="sharebuttons" style="width:100%;">
                                   <component ref="dynCardSocialMediaRef" :is="dynCardSocialMediaComponent" :card="buildEntity"></component>
                               </div>
                               <div style="position:relative; top:-10px;"><span class="fas fa-info-circle"></span> Social media is a must. Help others connect with you online by registering links to your social media accounts.</div>
                           </div>
                        </li>
                    </ul>'.'
                    <ul v-show="buildPage === 3">
                        <li>
                            <div class="divCell" style="display:block;margin-top: 15px;">
                                <p>Time to make your new '.$app->objCustomPlatform->getPortalName().' card reflect you! From specific company branding to a photo of you, this area allows you to select your card\'s theme colors, and assign its images.</p>
                                <p><span style="background:#17a2b8;padding:5px 10px;border-radius: 5px;color:#fff !important;display:inline-block;">IMPORTANT: If you don\'t have all your stuff together, you can always come back later to complete this form!</span></p>
                            </div>
                        </li>
                        <li>
                            <div class="divCell" style="display:block;">
                                <div style="background-color: #6c757d;width:100%;height:10px;margin-bottom: 15px;"></div>
                            </div>
                        </li>
                        <li class="cardColors">
                            <div class="cardColorsWrapper">
                                <div class="cardItemLabel"><h4>Colors</h4></div>
                                <table class="table w-50">
                                    <tr>
                                        <td class="w-50 cardItemCell">
                                            <component :is="dyn' . str_replace("-", "", $this->manageDataWidget->getInstanceId()) . 'Component" :dataRow="buildEntity" dataField="mainColor" dataType="color-picker" :userId="userId"></component>
                                            <div class="cardItemCellTitle">Primary Color</div>
                                        </td> 
                                        <td class="w-50 cardItemCell">
                                            <component :is="dyn' . str_replace("-", "", $this->manageDataWidget->getInstanceId()) . 'Component" :dataRow="buildEntity" dataField="secondaryColor" dataType="color-picker" :userId="userId"></component>
                                            <div class="cardItemCellTitle">Secondary Color</div>
                                        </td>    
                                    </tr>
                                </table>
                            </div>
                        </li>'.'
                        <li class="cardColors">
                            <div class="cardColorsWrapper">
                                <div class="cardItemLabel"><h4>Images</h4></div>
                                <table v-if="entity.template_id == 1" class="table w-100">
                                    <tr>
                                        <td class="w-50 cardItemCell">
                                            <div class="flex-column">
                                                <img v-on:click="editCardImage(entity, \'banner\', \'main-image\', \'banner\', \'650,650\')" v-bind:src="buildEntity.banner" @error="showErrorImage(buildEntity,\'banner\')" class="pointer mobile-to-75 mobile-to-block mobile-vertical-margins-15 mobile-to-heightAuto mobile-center" width="160" height="160" style="max-width:300px;width:100%;height:auto;"/>
                                                <div>Card Main Banner</div>
                                            </div>
                                        </td>  
                                        <td class="w-50 cardItemCell">
                                            <div class="flex-column">
                                                <img v-on:click="editCardImage(entity, \'favicon\', \'favicon-image\', \'favicon\', \'180,180\')" v-bind:src="buildEntity.favicon" @error="showErrorImage(buildEntity,\'favicon\')" class="pointer" width="64" height="64" style="max-width:64px;width:64px;height:64px;"/>
                                                <div >Card Favicon</div>
                                            </div>
                                        </td>    
                                    </tr>
                                </table>'.'
                                <table v-if="entity.template_id == 2" class="table w-100">
                                    <tr>
                                        <td class="w-50 cardItemCell">
                                            <div style="display: flex;align-items: center;" class="flex-column">
                                                <img v-on:click="editCardImage(entity, \'banner\', \'main-image\', \'banner\', \'750,446\')" v-bind:src="buildEntity.banner" @error="showErrorImage(buildEntity,\'banner\')" class="pointer mobile-to-75 mobile-to-block mobile-vertical-margins-15 mobile-to-heightAuto mobile-center" id="entityMainImage" width="300" height="160" style="max-width:284px;width:100%;height:auto;"/>
                                                <div>Card Main Banner</div>
                                            </div>
                                        </td>  
                                        <td class="w-50 cardItemCell">
                                            <div style="display: flex;align-items: center;" class="flex-column">
                                                <img v-on:click="editCardImage(entity, \'splash_cover\', \'splash-cover-image\', \'splash_cover\', \'750,1334\')" v-bind:src="buildEntity.splash_cover" @error="showErrorImage(buildEntity,\'splash_cover\')" class="pointer" width="90" height="160" style="max-width:160px;width:160px;height:284px;"/>
                                                <div>Splash Cover</div>
                                            </div>
                                        </td> 
                                        <td class="w-50 cardItemCell">
                                            <div style="display: flex;align-items: center;" class="flex-column">
                                                <img v-on:click="editCardImage(entity, \'logo\', \'logo-image\', \'logo\', \'250,250\')" v-bind:src="buildEntity.logo" @error="showErrorImage(buildEntity,\'logo\')" class="pointer" width="90" height="90" style="max-width:90px;width:90px;height:90px;"/>
                                                <div>Logo</div>
                                            </div>
                                        </td> 
                                        <td class="w-50 cardItemCell">
                                            <div style="display: flex;align-items: center;" class="flex-column">
                                                <img v-on:click="editCardImage(entity, \'favicon\', \'favicon-image\', \'favicon\', \'180,180\')" v-bind:src="buildEntity.favicon" @error="showErrorImage(buildEntity,\'favicon\')" class="pointer" width="64" height="64" style="max-width:64px;width:64px;height:64px;"/>
                                                <div>Card Favicon</div>
                                            </div>
                                        </td>    
                                    </tr>
                                </table>
                            </div>
                        </li>
                    </ul>'.'
                    <ul v-show="buildPage === 4">
                        <li>
                            <div class="divCell" style="display:block;margin-top: 15px;">
                                <p>Now let\'s get specific. You have {{ editableCardPages.length }} pages on your new '.$app->objCustomPlatform->getPortalName().' card, and we need to know how to build them for you. This means we need some ideas!</p>
                                <p><span style="background:#17a2b8;padding:5px 10px;border-radius: 5px;color:#fff !important;display:inline-block;">NOTE: Put down exactly what you want, or write down direction for our designers. Drag in images from your computer or mobile device!</span></p>
                                <p>Also, we\'re saving this as you go along, so don\'t worry about losing it!</p>
                            </div>
                        </li>
                        <li>
                            <div class="divCell" style="display:block;">
                                <div style="background-color: #6c757d;width:100%;height:10px;margin-bottom: 15px;"></div>
                            </div>
                        </li>
                        <li class="cardPages">
                            <div class="cardColorsWrapper w-75">
                                <div class="cardItemLabel"><h4>Card Pages <button class="btn btn-sm btn-primary" v-on:click="addCardPageItem()" style="margin-left: 5px;margin-top: -4px;">Purchase Additional Page</button></h4></div>
                                <ul v-if="buildEntity && buildEntity.Tabs">
                                    <li v-for="currPage, index in editableCardPages">
                                        <div class="pageBlockWrapper">
                                            <h5>Page {{ (index + 1) }}</h5>
                                            <component ref="pageManageDataWidget" :is="dyn' . str_replace("-", "", $this->manageDataWidget->getInstanceId()) . 'Component" :dataRow="currPage" :rowIndex="index" :userId="userId"></component>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="w-25" style="padding-left:25px;">
                                <h4>More Info</h4>
                                <img src="https://www.cognitoforms.com/file/OB7vefQRD-qszRuJEHV6WI7b-M00JQbVlSuoXzN8etIpk-yiCpYfKIiGii0qP1M8?id=F-jM$ochcMAYaFseufoMuge!&name=body.png&ct=image%2Fpng&size=240453" />
                            </div>
                        </li>
                    </ul>'.'
                    <ul v-show="buildPage === 5">
                        <li>
                            <div class="divCell" style="display:block;margin-top: 15px;">
                                <p><b>Once last thing!</b> Submitting this form sends it to our team to review so we can put your card together, so if you\'re not ready yet, take your time and go back to the previous steps until you are completely finished with it.</p>
                                <p><span style="background:#17a2b8;padding:5px 10px;border-radius: 5px;color:#fff !important;display:inline-block;">IMPORTANT: Submitting this form closes out the form build process on your end and sends it to us!</span></p>
                                <p>Once we have it we will be in contact with you shortly to review your form. <b>We look forward to contacting you!</b></p>
                            </div>
                        </li>
                        <li>
                            <div class="divCell" style="display:block;">
                                <div style="background-color: #6c757d;width:100%;height:10px;margin-bottom: 15px;"></div>
                            </div>
                        </li>
                    </ul>
                    <button v-if="buildPage < 5" class="btn btn-secondary w-100" @click="goToNextStep">Continue to Step {{ (buildPage + 1) }}</button>
                    <button v-if="buildPage === 5" class="btn btn-primary w-100" @click="submitBuildForm">Submit Build Form</button>
                </div>
            </div>
            <div v-if="buildEntity !== null && originalEntity.status === \'BuildComplete\'">
                Here is where we will create the admin version of the BuildComplete Form
            </div>
        <div>
        ';
    }
}