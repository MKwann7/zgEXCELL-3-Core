<?php

namespace Entities\Cards\Components\Vue\CardWidget;

class ManageCardCustomizableImageWidget extends ManageCardImageWidget
{
    protected string $id = "0c8d4716-7d40-4f7c-8e15-564515e71324";
    protected $cropperBannerId = "my-cropper-banner-custom";

    protected function renderTemplate() : string
    {
        return '
        <div>
            <div class="memberAvatarImage">
                <div class="slim" v-bind:data-ratio="imageSizeRatio" v-bind:data-force-size="imageSize" v-bind:data-service="profileImageUploadUrl" id="'.$this->cropperBannerId.'" style="background-image: url(/_ez/images/no-image.jpg); background-size: auto 100%;">
                    <input type="file"/>
                    <img width="250" height="250" alt="">
                </div>
            </div>
            <div class="divTable">
                <div class="divRow">
                    <div class="divCell"></div>
                    <div class="divCell"></div>
                </div>
            </div>
            <button class="buttonID9234597e456 btn btn-primary w-100" style="margin-top:15px;" @click="saveMainImage()">Save {{ ucwords(imageType) }} Image</button>
        </div>';
    }
}