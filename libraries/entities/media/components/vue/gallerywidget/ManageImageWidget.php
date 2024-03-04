<?php

namespace Entities\Media\Components\Vue\GalleryWidget;

use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\VueProps;
use Entities\Cards\Models\CardModel;

class ManageImageWidget extends VueComponent
{
    protected string $id = "cc9a65ea-e0c0-42b4-9aba-93ce56cb4583";
    protected string $title = "Image Dashboard";
    protected string $endpointUriAbstract = "image-dashboard/{id}";
    protected string $modalWidth = "850";
    protected string $cropperBannerId = "my-cropper-banner";

    public function __construct(array $components = [])
    {
        parent::__construct(new CardModel());

        $mainEntityList = new VueProps("mainEntityList", "array", "mainEntityList");
        $this->addProp($mainEntityList);

        $this->modalTitleForAddEntity = "Add Image";
        $this->modalTitleForEditEntity = "Edit Image";
        $this->modalTitleForDeleteEntity = "Delete Image";
        $this->modalTitleForRowEntity = "View Image";
    }

    protected function renderComponentDataAssignments(): string
    {
        return "
            label: 'default',
            entityClone: {},
            manageType: 'image',
            actionLabel: 'Assign',
            imageRepeat: 'no-repeat',
            imagePositionX: 'center',
            imagePositionY: 'center',
            imageBackgroundSize: 'cover',
            imageBackgroundBlend: 'default',
            imageSaveCallback: null,
            profileImageUploadUrl: '',
            imageUrl: '',
            imageSize: '650,650',
            imageSizeRatio: '1:1',
            imageType: 'banner',
            imageClass: 'main-image',
            entityField: 'banner',
            entityType: 'card',
            displayImage: false,
        ";
    }

    protected function renderComponentMethods() : string
    {
        global $app;
        return '
            assignImageToSite: function() {
                if (typeof this.imageSaveCallback === "function") {
                    console.log(this.entityClone)
                    this.imageSaveCallback(this.renderImageUrl())
                    return;
                }
                
                let imageOptions = {
                    imageRepeat: this.imageRepeat,
                    imagePositionX: this.imagePositionX,
                    imagePositionY: this.imagePositionY,
                    imageBackgroundSize: this.imageBackgroundSize,
                    imageBackgroundBlend: this.imageBackgroundBlend,
                }
                
                let imageLabel = this.label;
                
                if (this.manageType === "logo") {
                    imageOptions = {
                        width: 150,
                        height: 75,
                        left: 100,
                        top: 100,
                    }
                }
                dispatch.broadcast("assign_" + this.manageType + "_to_site", {image: this.entityClone, label: imageLabel, options: Object.assign({
                    width: this.entityClone.width,
                    height: this.entityClone.height,
                    thumb: this.entityClone.thumb,
                    id: this.entityClone.image_id
                }, imageOptions)})
            },
            clearImageOnSite: function() {
                dispatch.broadcast("assign_" + this.manageType + "_to_site", {image: "__REMOVE__", label: this.label})
            },
            renderImageUrl: function() {
                let imageUrl = this.entityClone.url
                if (imageUrl.substr(0,5) === "/cdn/") {
                    imageUrl = imageServerUrl() + imageUrl
                }
                return imageUrl
            },
            setProfileImageUploadUrl: function()
            {
                this.profileImageUploadUrl = "/api/v1/media/upload-image?entity_id=" + this.entity.card_id + "&user_id=" + this.userNum + "&entity_name=card&class=" + this.imageClass;
            },
            loadCropperData: function()
            {
                let self = this;
                let objMyCropper = document.getElementById("'.$this->cropperBannerId.'");
                Slim.destroy(objMyCropper);
                
                this.$forceUpdate();
                this.setProfileImageUploadUrl();
                
                let cropperChildren = objMyCropper.getElementsByTagName("img");
            
                if( this.entity.card_id !== null && typeof this.imageUrl === "string" && this.imageUrl !== "" && !this.imageUrl.includes("no-image.jpg")) {
                    cropperChildren[0].src = this.imageUrl;
                } else {
                    cropperChildren[0].removeAttribute("src");
                }
                
                Slim.create(
                    objMyCropper, 
                    Slim.getOptionsFromAttributes(objMyCropper, {browseButton: false, uploadButton: false, removeButton: false, size: self.renderImageSize(self.imageSize), forceSize: self.renderImageSize(self.imageSize) }), 
                    {app: self, method: "updateEntityImage"},
                    {app: self, method: "removeEntityImage"}
                );
                
                this.displayImage = true;
            },
            renderImageSize: function(size)
            {
                const imageSize = size.split(",");
                return {width: imageSize[0], height: imageSize[1]};
            },
            updateEntityImage: function(url)
            {
                let objMyCropper = document.getElementById("'.$this->cropperBannerId.'");
                let cropperChildren = objMyCropper.getElementsByTagName("img");
                
                if (cropperChildren[0]) {
                    cropperChildren[0].src = url;
                    this.entity[this.entityField] = url;
                }
            },
            removeEntityImage: function()
            {
                this.entity[this.entityField]  = "'.$app->objCustomPlatform->getFullPublicDomainName().'/_ez/images/no-image.jpg";
            },
            saveMainImage: function()
            {
                modal.EngageFloatShield();
                
                this.setProfileImageUploadUrl();
                Slim.setUploadUrl(document.getElementById("'.$this->cropperBannerId.'"), this.profileImageUploadUrl);
                
                Slim.save(document.getElementById("'.$this->cropperBannerId.'"), function() {
                    modal.CloseFloatShield();
                    return;
                });
            },
            getModalTitle: function(action)
            {
                switch(action) {
                    case "add": return "Add Card " + this.ucwords(this.imageType);
                    case "edit": return "Edit Card " + this.ucwords(this.imageType);
                    case "delete": return "Delete Card " + this.ucwords(this.imageType);
                    default: return "View Card " + this.ucwords(this.imageType);
                }
            },
            ucwords: function(str)
            {
                if (typeof str === "undefined") return "";
                return str.replace(/_/g," ").replace(/\w\S*/g, function (txt) {
                    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                });
            },
            setImageSizeRatio: function(size)
            {
                return this.imageSize.replace(",",":");
            },
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return '
            this.label = props.label
            this.manageType = props.manageType
            this.imageSaveCallback = props.imageSaveCallback
            this.actionLabel = props.editExisting ? "Update" : "Assign"
            if (!this.entity) return;
            if (this.entity.type) {
                this.entityClone = {
                    image_id: this.entity.options.id,
                    width: this.entity.options.width,
                    height: this.entity.options.height,
                    thumb: this.entity.options.thumb,
                    url: this.entity.url,
                    options: this.entity.options,
                }
                this.displayImage = false;
                this.imageType = typeof props.imageSize !== "undefined" ? props.imageType : this.imageType;
                this.entityField = typeof props.imageSize !== "undefined" ? props.entityField : this.entityField;
                this.imageClass = typeof props.imageSize !== "undefined" ? props.imageClass : this.imageClass;
                this.imageSize = typeof props.imageSize !== "undefined" ? props.imageSize : this.imageSize ;
                this.imageUrl = this.renderImageUrl()
                this.imageSizeRatio = this.setImageSizeRatio(this.imageSize);
                
            } else {
                this.entityClone = this.entity
                this.imageRepeat = "no-repeat"
                this.imagePositionX = "center"
                this.imagePositionY = "center"
                this.imageBackgroundSize = "cover"
                this.imageBackgroundBlend = "default"
            }
            
            let self = this;
            setTimeout(function () { self.loadCropperData(); }, 100);

            let vc = this.findVc(this);
            vc.removeAjaxClass()
        ';
    }

    protected function renderTemplate() : string
    {
        return '<div class="manage-image-widget-wrapper">
                <v-style type="text/css">
                    .manage-image-widget {
                        width:50%;
                    }
                    .manage-image-widget .dual-select {
                        width: 40%;
                        display: inline;
                    }
                    .manage-image-widget-wrapper .table td:not(.manage-image-widget) {
                        vertical-align: middle;
                    }
                    @media (max-width:1250px) {
                        .manage-image-widget {
                            width:100%;
                        }
                    }
                </v-style>
                <table class="table">
                    <tr>
                        <td class="manage-image-widget">
                            <div class="memberAvatarImage" v-show="displayImage === true">
                                <div class="slim" v-bind:data-ratio="imageSizeRatio" v-bind:data-force-size="imageSize" v-bind:data-service="profileImageUploadUrl" id="'.$this->cropperBannerId.'" style="background-image: url(/_ez/images/no-image.jpg); background-size: auto 100%;">
                                    <input type="file"/>
                                    <img width="250" height="250" alt="">
                                </div>
                            </div>
                            <div>Original Size: {{ entityClone.width }}px x {{ entityClone.height }}px</div>    
                        </td>
                        <td v-if="manageType === \'image\'" class="manage-image-widget">
                            <h2 class="pop-up-dialog-main-title-text mb-2">Selected Image</h2>
                            <table class="table">
                                <tr>
                                    <td>Repeat:</td>    
                                    <td>
                                        <select v-model="imageRepeat" class="form-control">
                                            <option value="no-repeat">No Repeat</option>
                                            <option value="repeat-x">Repeat X</option>
                                            <option value="repeat-y">Repeat Y</option>
                                            <option value="repeat">Repeat All</option>
                                        </select>
                                    </td>    
                                </tr>
                                <tr>
                                    <td>Position:</td>
                                    <td>
                                        <input list="imageWidgetBackgroundPositionX" v-model="imagePositionX" class="dual-select form-control mr-1" placeholder="X">
                                        <datalist id="imageWidgetBackgroundPositionX">
                                            <option value="Center">
                                            <option value="Left">
                                            <option value="Right">
                                            <option value="Top">
                                            <option value="Bottom">
                                        </datalist>
                                        <input list="imageWidgetBackgroundPositionY" v-model="imagePositionY" class="dual-select form-control" placeholder="Y">
                                        <datalist id="imageWidgetBackgroundPositionY">
                                            <option value="Center">
                                            <option value="Left">
                                            <option value="Right">
                                            <option value="Top">
                                            <option value="Bottom">
                                        </datalist>
                                    </td>    
                                </tr>
                                <tr>
                                    <td>Size:</td>    
                                    <td>
                                        <input v-model="imageBackgroundSize" list="imageWidgetBackgroundSize" class="form-control">
                                        <datalist id="imageWidgetBackgroundSize">
                                            <option value="Default">
                                            <option value="Cover">
                                            <option value="Contain">
                                            <option value="Length">
                                            <option value="Initial">
                                            <option value="Auto">
                                        </datalist>
                                    </td>    
                                </tr>
                                <tr>
                                    <td style="padding:0"></td>
                                    <td style="padding:0"><p style="font-size:14px;margin-bottom:-5px;" class="pt-2">Additional values, such as "100% auto" may be used, referencing x and y inputs.</p></td>
                                </tr>
                                <tr>
                                    <td>Blend Mode:</td>    
                                    <td>
                                        <select v-model="imageBackgroundBlend" class="form-control">
                                            <option value="normal">Normal (Default)</option>
                                            <option value="multiply">Multiply</option>
                                            <option value="screen">Screen</option>
                                            <option value="overlay">Overlay</option>
                                            <option value="darken">Darken</option>
                                            <option value="lighten">Lighten</option>
                                            <option value="color-dodge">Color Dodge</option>
                                            <option value="saturation">Saturation</option>
                                            <option value="color">Color</option>
                                            <option value="luminosity">Luminosity</option>
                                        </select>
                                    </td>    
                                </tr>
                            </table>
                            <p>To use this image as your background profile, assign it below.</p>
                            <table class="table">
                                <tr>
                                    <td><button class="btn btn-primary w-100" v-on:click="assignImageToSite()">{{ actionLabel }} Image</button></td>
                                </tr>
                            </table>
                        </td>
                        <td v-if="manageType === \'logo\'" class="manage-image-widget">
                            <h2 class="pop-up-dialog-main-title-text mb-2">Selected Logo</h2>
                            <table class="table">
                                <tr>
                                    <td>Default:</td>
                                    <td>
                                        <input class="form-control" placeholder="Height: e.g. 100px">
                                    </td>   
                                    <td>
                                        <select v-model="imageRepeat" class="form-control">
                                            <option value="">--Set Orientation--</option>
                                            <option value="top">Top</option>
                                            <option value="center">Center</option>
                                            <option value="bottom">Bottom</option>
                                        </select>
                                    </td>    
                                </tr>
                                <tr>
                                    <td>Tablet:</td>    
                                    <td>
                                        <select v-model="imageRepeat" class="form-control">
                                            <option value="no-repeat">No Repeat</option>
                                            <option value="repeat-x">Repeat X</option>
                                            <option value="repeat-y">Repeat Y</option>
                                            <option value="repeat">Repeat All</option>
                                        </select>
                                    </td>    
                                </tr>
                                <tr>
                                    <td>Mobile:</td>    
                                    <td>
                                        <select v-model="imageRepeat" class="form-control">
                                            <option value="no-repeat">No Repeat</option>
                                            <option value="repeat-x">Repeat X</option>
                                            <option value="repeat-y">Repeat Y</option>
                                            <option value="repeat">Repeat All</option>
                                        </select>
                                    </td>    
                                </tr>
                            </table>
                            <p>To use this image as your site logo, assign it below.</p>
                            <table class="table">
                                <tr>
                                    <td><button class="btn btn-primary w-100" v-on:click="assignImageToSite()">{{ actionLabel }} Logo</button></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                {{ entity }}
        </div>
        ';
    }
}