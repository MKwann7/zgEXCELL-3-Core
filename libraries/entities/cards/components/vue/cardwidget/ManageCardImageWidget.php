<?php

namespace Entities\Cards\Components\Vue\CardWidget;

use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cards\Models\CardModel;

class ManageCardImageWidget extends VueComponent
{
    protected $id = "2ca66ebc-c50f-4c35-8794-8dd6e8eb2942";
    protected $modalWidth = 500;
    protected $cropperBannerId = "my-cropper-banner";

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

        $this->modalTitleForAddEntity = "Add Card ";
        $this->modalTitleForEditEntity = "Edit Card ";
        $this->modalTitleForDeleteEntity = "Delete Card ";
        $this->modalTitleForRowEntity = "View Card ";
    }

    protected function renderComponentDataAssignments() : string
    {
        return '
            profileImageUploadUrl: "",
            imageUrl: "",
            imageSize: "650,650",
            imageSizeRatio: "1:1",
            imageType: "banner",
            imageClass: "main-image",
            entityField: "banner",
            entityType: "card",
            displayImage: false,
        ';
    }

    protected function renderComponentMethods() : string
    {
        global $app;
        return '
            setProfileImageUploadUrl: function()
            {
                this.profileImageUploadUrl = "/process/slim/upload?entity_id=" + this.entity.card_id + "&user_id=" + this.userNum + "&entity_name=card&class=" + this.imageClass;
            },
            loadCropperData: function()
            {
                let self = this;
                let objMyCropper = document.getElementById("'.$this->cropperBannerId.'");
                Slim.destroy(objMyCropper);
                
                this.$forceUpdate();
                this.setProfileImageUploadUrl();
                
                let cropperChildren = objMyCropper.getElementsByTagName("img");
            
                if( this.entity.card_id !== null && typeof this.imageUrl === "string" && this.imageUrl !== "" && !this.imageUrl.includes("no-image.jpg"))
                {
                    cropperChildren[0].src = this.imageUrl;
                }
                else
                {
                    cropperChildren[0].removeAttribute("src");
                }
                
                Slim.create(
                    objMyCropper, 
                    Slim.getOptionsFromAttributes(objMyCropper, {browseButton: false, uploadButton: false, size: self.renderImageSize(self.imageSize), forceSize: self.renderImageSize(self.imageSize) }), 
                    {app: self, method: "updateEntityImage"},
                    {app: self, method: "removeEntityImage"}
                );
                
                this.displayImage = true;
                console.log("we loaded it!");
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
                
                if (cropperChildren[0]) 
                {
                    cropperChildren[0].src = url;
                    this.entity[this.entityField] = url;
                }
            },
            removeEntityImage: function()
            {
                this.entity[this.entityField]  = "'.$app->objCustomPlatform->getFullPublicDomain().'/_ez/images/no-image.jpg";
            },
            saveMainImage: function()
            {
                modal.EngageFloatShield();
                
                this.setProfileImageUploadUrl();
                Slim.setUploadUrl(document.getElementById("'.$this->cropperBannerId.'"), this.profileImageUploadUrl);
                
                Slim.save(document.getElementById("'.$this->cropperBannerId.'"), function()
                {
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
        return parent::renderComponentHydrationScript() . '
            this.displayImage = false;
            this.imageType = typeof props.imageSize !== "undefined" ? props.imageType : this.imageType;
            this.entityField = typeof props.imageSize !== "undefined" ? props.entityField : this.entityField;
            this.imageClass = typeof props.imageSize !== "undefined" ? props.imageClass : this.imageClass;
            this.imageSize = typeof props.imageSize !== "undefined" ? props.imageSize : this.imageSize ;
            this.imageUrl = this.entity[this.entityField];
            this.imageSizeRatio = this.setImageSizeRatio(this.imageSize);

            let self = this;
            
            setTimeout(function () { self.loadCropperData(); }, 100);
        ';
    }

    protected function renderTemplate() : string
    {
        return '
        <div>
            <div class="memberAvatarImage" v-show="displayImage === true">
                <div class="slim" v-bind:data-ratio="imageSizeRatio" v-bind:data-force-size="imageSize" v-bind:data-service="profileImageUploadUrl" id="'.$this->cropperBannerId.'" style="background-image: url(/_ez/images/no-image.jpg); background-size: auto 100%;">
                    <input type="file"/>
                    <img width="250" height="250" alt="">
                </div>
            </div>
            <button class="buttonID9234597e456 btn btn-primary w-100" style="margin-top:15px;" @click="saveMainImage()">Save {{ ucwords(imageType) }} Image</button>
        </div>';
    }
}