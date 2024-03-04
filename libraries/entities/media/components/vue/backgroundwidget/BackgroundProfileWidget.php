<?php

namespace Entities\Media\Components\Vue\BackgroundWidget;

use App\Core\App;
use App\Core\AppModel;
use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Media\Components\Vue\ColorWidget\ManageColorWidget;
use Entities\Media\Components\Vue\GalleryWidget\ListImageGalleryWidget;
use Entities\Media\Components\Vue\GalleryWidget\ManageImageWidget;
use Entities\Media\Components\Vue\GradientWidget\ManageGradientWidget;

class BackgroundProfileWidget extends VueComponent
{
    protected string $id = "dceb77eb-5a46-4d3c-bf73-c18617fd16a8";
    protected string $modalWidth = "500";
    protected string $title = "Background Profile";

    public function __construct(?AppModel $entity = null)
    {
        parent::__construct($entity);

        $editorComponent = $this->getImageManager();
        $editorComponent->addParentId($this->getInstanceId(), ["edit"]);

        $gradientComponent = $this->getGradientManager();
        $gradientComponent->addParentId($this->getInstanceId(), ["edit"]);

        $colorComponent = $this->getColorManager();
        $colorComponent->addParentId($this->getInstanceId(), ["edit"]);

        $imageComponent = $this->getImageEditor();
        $imageComponent->addParentId($this->getInstanceId(), ["edit"]);

        $this->addComponentsList($editorComponent->getDynamicComponentsForParent());
        $this->addComponentsList($imageComponent->getDynamicComponentsForParent());
        $this->addComponentsList($gradientComponent->getDynamicComponentsForParent());
        $this->addComponentsList($colorComponent->getDynamicComponentsForParent());

        $this->addComponent($editorComponent);
        $this->addComponent($imageComponent);
        $this->addComponent($gradientComponent);
        $this->addComponent($colorComponent);

        $this->modalTitleForAddEntity = "Background Profile";
        $this->modalTitleForEditEntity = "Background Profile";
        $this->modalTitleForDeleteEntity = "Background Profile";
        $this->modalTitleForRowEntity = "Background Profile";
        $this->setDefaultAction("view");
    }

    protected function getImageManager() : ?VueComponent
    {
        return new ListImageGalleryWidget();
    }

    protected function getImageManagerStaticId() : string
    {
        return ListImageGalleryWidget::getStaticId();
    }

    protected function getImageEditor() : ?VueComponent
    {
        return new ManageImageWidget();
    }

    protected function getImageEditorStaticId() : string
    {
        return ManageImageWidget::getStaticId();
    }

    protected function getGradientManager() : ?VueComponent
    {
        return new ManageGradientWidget();
    }

    protected function getGradientManagerStaticId() : string
    {
        return ManageGradientWidget::getStaticId();
    }

    protected function getColorManager() : ?VueComponent
    {
        return new ManageColorWidget();
    }

    protected function getColorManagerStaticId() : string
    {
        return ManageColorWidget::getStaticId();
    }

    protected function renderComponentMountedScript(): string
    {
        return '
            dispatch.register("reload_site_media", this, "reloadSiteMedia");
        ';
    }

    protected function renderComponentDataAssignments() : string
    {
        return parent::renderComponentDataAssignments(). '
                screen: \'profile\',
                emptyProfile: true,
                label: \'default\',
                profileType: \'none\',
                siteMedia: [],
                activeMedia: null,
        ';
    }

    protected function renderComponentDismissalScript(): string
    {
        return parent::renderComponentDismissalScript() . '
                this.screen = \'profile\';
                this.label = \'default\';
                this.profileType = \'none\';
                this.activeMedia = null;
        ';
    }

    protected function renderComponentMethods() : string
    {
        return parent::renderComponentMethods() . '
            goToImageList: function() {
                ' . $this->activateRegisteredComponentByIdInModal($this->getImageManagerStaticId(), "edit", true, "this.entity", "[]",["label" => "this.label", "media" => "this.siteMedia", "type" => "'images'"]) . '
            },
            gotoGradientEditor: function() {
                ' . $this->activateRegisteredComponentByIdInModal($this->getGradientManagerStaticId(), "edit", true, "this.entity", "[]",["label" => "this.label", "media" => "this.siteMedia", "source" => "'new'"]) . '
            },
            gotoColorEditor: function() {
                ' . $this->activateRegisteredComponentByIdInModal($this->getColorManagerStaticId(), "edit", true, "this.entity", "[]",["label" => "this.label", "media" => "this.siteMedia", "source" => "'new'"]) . '
            },
            customModalWidth: function() {
                if (this.screen === \'select\') {
                    return 850
                } 
                
                return '.$this->modalWidth.'
            },
            selectMedia: function() {
                this.screen = \'select\'
                this.setModalWidth(this, 850)
                this.$forceUpdate()
            },
            goToProfile: function() {
                this.screen = \'profile\'
                this.setModalWidth(this, 500)
                this.$forceUpdate()
            },
            editImage: function() {
                const self = this
                const myVc = self.findVc(self)
                const profileComponent = myVc.getComponentByInstanceId(self.instanceId)
                myVc.setNewParentIdForComponentById("'.$this->getImageEditorStaticId().'", profileComponent.id)
                ' . $this->activateRegisteredComponentByIdInModal($this->getImageEditorStaticId(), "edit", true, "this.activeMedia", "[]",["label" => "this.label", "media" => "this.siteMedia", "manageType" => "'image'", "editExisting" => "true"]) . '
            },
            editColor: function() {
                ' . $this->activateRegisteredComponentByIdInModal($this->getColorManagerStaticId(), "edit", true, "this.entity", "[]",["label" => "this.label", "media" => "this.siteMedia"]) . '
            },
            editGradient: function() {
                ' . $this->activateRegisteredComponentByIdInModal($this->getGradientManagerStaticId(), "edit", true, "this.entity", "[]",["label" => "this.label", "media" => "this.siteMedia"]) . '
            },
            getImageProfile() {
                if (this.siteMedia && this.siteMedia[this._props.label]) {
                    this.activeMedia = this.siteMedia[this._props.label]
                    return this.siteMedia[this._props.label]
                }
                
                this.activeMedia = null                
                return false
            },
            getActiveImageData() {
                if (this.entity.Media && this.entity.Media[this._props.label]) {
                    return this.entity.Media[this._props.label]
                }            
                return null
            },
            getImageProfileUrl() {
                if (this.activeMedia === null) {
                    return "/_ez/images/no-image.jpg"
                }
                
                return this.activeMedia.url
            },
            getColorValue() {
                if (this.activeMedia === null) {
                    return "#ff0000"
                }
                
                return this.activeMedia.color
            },
            getGradientValue() {
                if (this.activeMedia === null) {
                    return "linear-gradient(to right, #085078 1%, #85D8CE 99%)"
                }
                
                return this.activeMedia.gradient
            },
            loadProfile: function() {
                this.profileType = this.activeMedia.type
                ezLog(this.activeMedia, "this.activeMedia")
                
                switch (this.profileType) {
                    case "image":
                        break;
                    case "color":
                        break;
                    case "gradient":
                        break;
                }
            },
            loadImages: function(entity) {
                this.siteMedia = []
                if (entity.Media) {
                    for (let currMediaIndex in entity.Media) {
                        const mediaArray = entity.Media[currMediaIndex].split("|");
                        if (mediaArray[0] === "image") {
                            let imageUrl = mediaArray[1]
                            if (mediaArray[1].substr(0,5) === "/cdn/") {
                                imageUrl = imageServerUrl() + mediaArray[1]
                            }
                            this.siteMedia[currMediaIndex] = {
                                url: imageUrl,
                                type: mediaArray[0],
                                options: JSON.parse(mediaArray[2])
                            }
                        } else if (mediaArray[0] === "color") {
                            let color = mediaArray[1]                            
                            this.siteMedia[currMediaIndex] = {
                                color: color,
                                type: mediaArray[0],
                                options: JSON.parse(mediaArray[2])
                            }
                        } else if (mediaArray[0] === "gradient") {
                            let gradient = mediaArray[1]                            
                            this.siteMedia[currMediaIndex] = {
                                gradient: gradient,
                                type: mediaArray[0],
                                options: JSON.parse(mediaArray[2])
                            }
                        }
                    }
                }
            },
            reloadSiteMedia: function() {
                this.loadImages(this.entity)
                this.getImageProfile()
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
            this.loadImages(this.entity)
            this.getImageProfile()
            
            if (this.activeMedia === null || !this.activeMedia.type) {
                this.emptyProfile = true
                this.selectMedia()
                return;
            }
            
            this.profileType = this.activeMedia.type
        ';
    }

    protected function renderTemplate() : string
    {
        /** @var App $app */
        global $app;
        return '<div class="formwrapper-control background-profile-main-wrapper">
            <div>
                <div v-show="screen == \'select\'">
                    <div class="width100">
                        <div class="theme-outer">
                            <ul class="d-flex flex-column flex-wrap justify-content-between">
                                <li class="col-lg-4 col-md-3 col-sm-2">
                                    <div class="card">
                                        <img class="card-img-top" src="/website/images/sunset-beach-background.jpg" alt="Card image cap">
                                        <div class="card-body">
                                            <h5 class="card-title">Image</h5>
                                            <p class="card-text">Select an image to use</p>
                                            <a v-on:click="goToImageList" class="btn btn-primary w-100">Select Image</a>
                                        </div>
                                    </div>
                                </li> 
                                <li class="col-lg-4 col-md-3 col-sm-2">
                                    <div class="card">
                                        <img class="card-img-top" src="/website/images/gradient-background.jpg" alt="Card image cap">
                                        <div class="card-body">
                                            <h5 class="card-title">Gradient</h5>
                                            <p class="card-text">Create a gradient To use</p>
                                            <a v-on:click="gotoGradientEditor" class="btn btn-primary w-100">Select Gradient</a>
                                        </div>
                                    </div>
                                </li>  
                                <li class="col-lg-4 col-md-3 col-sm-2">
                                    <div class="card">
                                        <img class="card-img-top" src="/website/images/color-select-background.jpg" alt="Card image cap">
                                        <div class="card-body">
                                            <h5 class="card-title">Color</h5>
                                            <p class="card-text">Select a Color To use</p>
                                            <a v-on:click="gotoColorEditor" class="btn btn-primary w-100">Select Color</a>
                                        </div>
                                    </div>
                                </li>    
                            </ul>
                        </div>
                        <div v-if="!emptyProfile" style="text-align: center; margin-top: 23px;">
                            <button class="btn btn-danger w-100" v-on:click="goToProfile()" style="max-width: 250px; margin: auto; color:white !important;">Back To Profile</button>
                        </div>
                    </div>
                </div>
                <div v-show="screen == \'profile\'">
                    <div v-if="profileType == \'image\'" class="width100">
                        <div style="border: 1px solid rgb(170, 170, 170); padding: 5px; border-radius: 3px;">
                            <div style="width:100%; padding-top:100%; position: relative;" v-bind:style="{backgroundSize: \'cover\', background: \'url(\' + getImageProfileUrl() + \') no-repeat center center / cover\'}">
                                <div class="w-100" style="margin-bottom: 22px; position: absolute; bottom: 0px; padding-left: 15px; padding-right: 15px;">
                                    <div class="width50 pr-2"><button class="btn btn-secondary w-100" v-on:click="editImage" style="color:white">Edit</button></div>
                                    <div class="width50 pl-2"><button class="btn btn-primary w-100" v-on:click="selectMedia">Change</button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="profileType == \'color\'" class="width100">
                        <div style="border: 1px solid rgb(170, 170, 170); padding: 5px; border-radius: 3px;">
                            <div style="width:100%; padding-top:100%; position: relative;" v-bind:style="{backgroundColor: getColorValue()}">
                                <div class="w-100" style="margin-bottom: 22px; position: absolute; bottom: 0px; padding-left: 15px; padding-right: 15px;">
                                    <div class="width50 pr-2"><button class="btn btn-secondary w-100" v-on:click="editColor" style="color:white">Edit</button></div>
                                    <div class="width50 pl-2"><button class="btn btn-primary w-100" v-on:click="selectMedia">Change</button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="profileType == \'gradient\'" class="width100">
                        <div style="border: 1px solid rgb(170, 170, 170); padding: 5px; border-radius: 3px;">
                            <div style="width:100%; padding-top:100%; position: relative;" v-bind:style="{backgroundImage: getGradientValue()}">
                                <div class="w-100" style="margin-bottom: 22px; position: absolute; bottom: 0px; padding-left: 15px; padding-right: 15px;">
                                    <div class="width50 pr-2"><button class="btn btn-secondary w-100" v-on:click="editGradient" style="color:white">Edit</button></div>
                                    <div class="width50 pl-2"><button class="btn btn-primary w-100" v-on:click="selectMedia">Change</button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }
}