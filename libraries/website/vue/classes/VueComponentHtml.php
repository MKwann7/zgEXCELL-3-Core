<?php

namespace App\website\vue\classes;

use App\Core\AppModel;
use App\Website\Vue\Classes\Base\VueComponent;

class VueComponentHtml extends VueComponent
{
    protected $name = "compHtml";
    protected $vueType = "compHtml";
    protected $froalaLicense;
    protected $froalaElementId;

    public function __construct(?AppModel $entity = null, $name = "Html Widget", $props = [])
    {
        $this->loadProps($props);
        $this->name = $name;;
        $this->froalaLicense = env("FROALA_LICENSE");
        $this->froalaElementId = "comp" . preg_replace("/[^A-Za-z0-9]/", '', getGuid());

        parent::__construct($entity);
    }

    public function getFroalaElementId() : string
    {
        return $this->froalaElementId;
    }

    protected function renderComponentDataAssignments() : string
    {
        return "
            froalaEditor: null,
            froalaId: '.". $this->froalaElementId ."',
            titleText: '',
            htmlData: '',
            action: '',
            htmlActionButton: '',
        ";
    }

    public function getComponentMethods() : string
    {
        return $this->renderComponentMethods();
    }

    protected function renderComponentMethods() : string
    {
        return '
            loadFroala: function(customId, callback) {
                if (customId) this.froalaId = customId;
                let self = this;
                $(function() { $(self.froalaId)
                    .froalaEditor({
                        key: \'' . $this->froalaLicense . '\',
                        heightMin: 150,
                        iconsTemplate: \'font_awesome_5\',
                        imageManagerPreloader: \'/website/images/LoadingIcon2.gif\',
                        imageEditButtons: [\'imageReplace\', \'imageAlign\', \'imageCaption\', \'imageLink\', \'linkOpen\', \'linkEdit\', \'linkRemove\',\'imageDisplay\', \'imageStyle\', \'imageAlt\', \'imageSize\'],
                        imageUploadURL: \'https://app.ezcardmedia.com/upload-image/users/\' + self.userId,
                        imagesLoadURL: \'https://app.ezcardmedia.com/upload-image/users/\' + self.userId,
                        imageManagerDeleteURL: \'https://app.ezcardmedia.com/delete-image/users/\' + self.userId,
                        imageManagerLoadURL: \'https://app.ezcardmedia.com/list-images/users/\' + self.userId,
                        imageUploadRemoteUrls: true,
                        imageUploadMethod: \'POST\',
                        imageManagerLoadMethod: "GET",
                        imageUploadParams: {
                            user_id: self.userId,
                            entity_id: self.userId,
                            image_class: \'editor\'
                        },
                        inlineStyles: {
                            \'Width100&\': \'width: 100% !important; height: auto !important;\'
                        }
                    })
                    .on(\'froalaEditor.image.uploaded\', function (e, editor, response) {
                        // Image was uploaded to the server.
                        console.log(JSON.stringify(response));
                    })
                    .on(\'froalaEditor.image.inserted\', function (e, editor, $img, response) {
                        // Image was inserted in the editor.
                        console.log(JSON.stringify(response));
                    })
                    .on(\'froalaEditor.image.replaced\', function (e, editor, $img, response) {
                        // Image was replaced in the editor.
                        console.log(JSON.stringify(response));
                    })
                    .on(\'froalaEditor.image.removed\', function (e, editor, $img) {
                        console.log(JSON.stringify($img));
                        $.ajax({
                                // Request method.
                            method: "POST",
    
                            // Request URL.
                            url: "https://app.ezcardmedia.com/delete-image/users/" + self.userId,
    
                            // Request params.
                            data: {
                                id: $img.data(\'id\')
                            }
                        })
                        .done (function (data) {
                            console.log (\'image was deleted\');
                        })
                        .fail (function () {
                            console.log (\'image delete problem\');
                        })
                    })
                    .on(\'froalaEditor.image.error\', function (e, editor, error, response) {
                        console.log(JSON.stringify(error));
                        console.log(JSON.stringify(response));
                    })
                    .on(\'froalaEditor.imageManager.error\', function (e, editor, error, response) {
                        // Bad link. One of the returned image links cannot be loaded.
                        console.log(JSON.stringify(error));
                        console.log(JSON.stringify(response));
                    })
                    
                    if (typeof callback === "function") callback();
                });
                
            },
        ';
    }
    protected function renderComponentDismissalScript() : string
    {
        return '
        $(this.froalaId).froalaEditor("html.set", "");
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return parent::renderComponentHydrationScript() . '        
        this.loadFroala();
        ';
    }

    protected function renderTemplate() : string
    {
        return '
            <div style="margin-top: 5px;">
                <div class="divTable">
                    <div class="divRow">
                        <div class="divCell">
                            <input placeholder="Enter a tab title..." style="margin-bottom:10px;"  class="form-control" id="tab_title" name="tab_title" v-model="titleText" />
                        </div>
                    </div>
                </div>
                <textarea class="' . $this->froalaElementId . '" name="tab_content"></textarea>
                <button style="margin-top:10px;" v-on:click="submitHtml" class="buttonID5576785443523 btn btn-primary w-100">{{ htmlActionButton }}</button>
            </div>
        ';
    }
}