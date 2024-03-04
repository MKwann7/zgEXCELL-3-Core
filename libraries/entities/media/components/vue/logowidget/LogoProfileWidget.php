<?php

namespace Entities\Media\Components\Vue\Logowidget;

use App\Core\App;
use App\Core\AppModel;
use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Media\Components\Vue\GalleryWidget\ListLogoGalleryWidget;
use Entities\Media\Components\Vue\GalleryWidget\ManageImageWidget;

class LogoProfileWidget extends VueComponent
{
    protected string $id = "2c2115f1-b5f8-4ffa-880a-0528a05caf42";
    protected string $modalWidth = "500";
    protected string $title = "Logo Profile";

    public function __construct(?AppModel $entity = null)
    {
        parent::__construct($entity);

        $editorComponent = $this->getImageManager();
        $editorComponent->addParentId($this->getInstanceId(), ["edit"]);

        $imageComponent = $this->getImageEditor();
        $imageComponent->addParentId($this->getInstanceId(), ["edit"]);

        $this->addComponentsList($editorComponent->getDynamicComponentsForParent());
        $this->addComponentsList($imageComponent->getDynamicComponentsForParent());

        $this->addComponent($editorComponent);
        $this->addComponent($imageComponent);

        $this->modalTitleForAddEntity = "Logo Profile";
        $this->modalTitleForEditEntity = "Logo Profile";
        $this->modalTitleForDeleteEntity = "Logo Profile";
        $this->modalTitleForRowEntity = "Logo Profile";
        $this->setDefaultAction("view");
    }

    protected function getImageManager() : ?VueComponent
    {
        return new ListLogoGalleryWidget();
    }

    protected function getImageManagerStaticId() : string
    {
        return ListLogoGalleryWidget::getStaticId();
    }

    protected function getImageEditor() : ?VueComponent
    {
        return new ManageImageWidget();
    }

    protected function getImageEditorStaticId() : string
    {
        return ManageImageWidget::getStaticId();
    }

    protected function renderComponentMountedScript(): string
    {
        return '
            dispatch.register("reload_site_logos", this, "reloadSiteLogos");
        ';
    }

    protected function renderComponentDataAssignments() : string
    {
        return parent::renderComponentDataAssignments(). '
                screen: \'profile\',
                emptyProfile: true,
                label: \'default\',
                profileType: \'none\',
                siteLogos: [],
                activeLogo: null,
        ';
    }

    protected function renderComponentDismissalScript(): string
    {
        return parent::renderComponentDismissalScript() . '
                this.screen = \'profile\';
                this.label = \'default\';
                this.profileType = \'none\';
                this.activeLogo = null;
        ';
    }

    protected function renderComponentMethods() : string
    {
        return parent::renderComponentMethods() . '
            goToImageList: function() {
                ' . $this->activateRegisteredComponentByIdInModal($this->getImageManagerStaticId(), "edit", true, "this.entity", "[]", ["label" => "this.label", "media" => "this.siteLogos", "type" => "'logos'"], "this", true, "function() {
                }") . '
            },
            customModalWidth: function() {
                return '.$this->modalWidth.'
            },
            editLogo: function() {
                ' . $this->activateRegisteredComponentByIdInModal($this->getImageEditorStaticId(), "edit", true, "this.activeLogo", "[]", ["label" => "this.label", "media" => "this.siteLogos", "manageType" => "'logo'", "editExisting" => "true"]) . '
            },
            getLogoProfile() {
                if (this.siteLogos && this.siteLogos[this._props.label]) {
                    ezLog("Found it")
                    this.activeLogo = this.siteLogos[this._props.label]
                    return this.siteLogos[this._props.label]
                }
                
                this.activeLogo = null                
                return false
            },
            getActiveImageData() {
                if (this.entity.Logos && this.entity.Media[this._props.label]) {
                    return this.entity.Logos[this._props.label]
                }            
                return null
            },
            getLogoProfileUrl() {
                if (this.activeLogo === null) {
                    return "/_ez/images/no-image.jpg"
                }
                
                return this.activeLogo.url
            },
            loadProfile: function() {
                this.profileType = this.activeLogo.type                
                switch (this.profileType) {
                    case "logo":
                        break;
                }
            },
            loadLogos: function(entity) {
                this.siteLogos = []
                if (entity.Logos) {
                    for (let currLogoIndex in entity.Logos) {
                        const logoArray = entity.Logos[currLogoIndex].split("|");
                        if (logoArray[0] === "image") {
                            let imageUrl = logoArray[1]
                            if (logoArray[1].substr(0,5) === "/cdn/") {
                                imageUrl = imageServerUrl() + logoArray[1]
                            }
                            this.siteLogos[currLogoIndex] = {
                                url: imageUrl,
                                type: logoArray[0],
                                options: JSON.parse(logoArray[2])
                            }
                        }
                    }
                }
            },
            reloadSiteLogos: function() {
                this.loadLogos(this.entity)
                this.getLogoProfile()
                this.$forceUpdate()
            },
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return parent::renderComponentHydrationScript() . '
            this.screen = \'profile\'
            this.setModalWidth(this, 500)
            this.emptyProfile = false
            this.label = this._props.label
            this.loadLogos(this.entity)
            this.getLogoProfile()
            
            if (this.activeLogo === null) {
                this.emptyProfile = true
                this.goToImageList()
                return false; 
            }
        ';
    }

    protected function renderTemplate() : string
    {
        /** @var App $app */
        global $app;
        return '<div class="formwrapper-control logo-profile-main-wrapper">
            <div>
                <div v-if="!activeLogo">
                    <button class="btn btn-primary" v-on:click="goToImageList">Images List</button>
                </div>
                <div v-if="activeLogo">
                    <div class="width100">
                        <div style="border: 1px solid rgb(170, 170, 170); padding: 5px; border-radius: 3px;">
                            <div style="width:100%; padding-top:100%; position: relative;" v-bind:style="{backgroundSize: \'cover\', background: \'url(\' + getLogoProfileUrl() + \') no-repeat center center / contain\'}">
                                <div class="w-100" style="margin-bottom: 22px; position: absolute; bottom: 0px; padding-left: 15px; padding-right: 15px;">
                                    <div class="width50 pr-2"><button class="btn btn-secondary w-100" v-on:click="editLogo" style="color:white">Edit</button></div>
                                    <div class="width50 pl-2"><button class="btn btn-primary w-100" v-on:click="selectLogo">Change</button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ activeLogo.options }}
                </div>
            </div>
        </div>';
    }
}