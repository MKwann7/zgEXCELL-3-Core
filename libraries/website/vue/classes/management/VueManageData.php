<?php

namespace App\Website\Vue\Classes\Management;

use App\Core\AppModel;
use App\Website\Vue\Classes\Base\VueComponent;
use App\website\vue\classes\VueComponentHtml;

class VueManageData extends VueComponent
{
    protected string $id = "17f7d229-efe2-4392-8803-3a741d0bbf15";
    protected $froalaWidget = "";


    public function __construct(?AppModel $entity = null, $name = "Data Widget", $props = [])
    {
        $this->loadProps($props);
        $this->name = $name;
        
        parent::__construct($entity);

        $this->froalaWidget = new VueComponentHtml();

        $this->modalTitleForAddEntity = "Add " . $name;
        $this->modalTitleForEditEntity = "Edit " . $name;
        $this->modalTitleForDeleteEntity = "Delete " . $name;
        $this->modalTitleForRowEntity = "View " . $name;
    }

    protected function renderComponentMountedScript() : string
    {
        return '
            let self = this;
            this.uniqueId = "colorPicker" + this.getRndInteger(10000,99999);
            if (typeof this.dataRow === "undefined") { return; }

            setTimeout(function() 
            {
                if (self.isImage(self.dataRow))
                {
                    this.imageName = this.dataRow.value;
                    this.imageData = this.dataRow.value;
                    this.newImage = false;
                }
                
                if (self.isPage(self.dataRow))
                {
                    self.loadFroala("#" + self.uniqueId, function() {
                        //self.setFroalaEditorContents(self.dataRow);
                    });
                    self.dataRow.element = $("#" + self.uniqueId);
                }
                
                if (self.isColorPicker(self.dataRow)) 
                {          
                    self.dataColor = self.dataRow[self.dataField];
                                        
                    let picker = new Picker({
                        parent: elm(self.uniqueId),
                        color: self.dataRow[self.dataField],
                        alpha: false,
                        onChange: function(color) {
                          self.dataRow[self.dataField] = color.hex.substring(0, 7);
                          self.dataColor = color.hex.substring(0, 7);
                          self.$parent.$forceUpdate();
                      },
                    });
                }
            }, 500);
        ';
    }

    protected function renderComponentDismissalScript() : string
    {
        return '
        $("#" + this.uniqueId).froalaEditor("html.set", "");
        ';
    }
    
    protected function renderComponentDataAssignments() : string
    {
        return "
            uniqueId: null,
            dataColor: '#ff0000',
            froalaId: null,
            dataType: null,
            imageFile: null,
            imageName: null,
            imageData: null,
            newImage: false,
            fileReader: null,
            froalaEditor: null,
        ";
    }

    protected function renderComponentMethods() : string
    {
        return $this->froalaWidget->getComponentMethods() . '
            getRndInteger: function(min, max)
            {
                return Math.floor(Math.random() * (max - min) ) + min;
            },
            uploadNewImage: function()
            {
                this.$refs.uploadImage.click();
            },
            setFroalaEditorContents: function(dataRow)
            {
                $("#" + this.uniqueId).froalaEditor("html.set", atob(dataRow.content));
            },
            getHtmlCode: function(editor)
            {
                if (editor.froalaEditor("codeView.isActive"))
                {
                  return editor.froalaEditor("codeView.get").replace(/[\t\n]+/, "");
                }
                
                return editor.froalaEditor("html.get").replace(/[\t\n]+/, "");
            },
            clearImage: function()
            {
                this.imageData = "";
                this.newImage = false;
                this.$refs.uploadImage.value = null;
                this.dataRow.file = null;
                this.imageName = null;
            },
            handleImageUpload: function()
            {
                this.imageName = this.$refs.uploadImage.files[0].name;
                this.dataRow.fileName = this.imageName;
                this.newImage = true;
    
                let self = this;
                let reader = new FileReader();
                const file = this.$refs.uploadImage.files[0];
    
                reader.addEventListener("load", function ()
                {
                    self.imageData = reader.result;
                    self.dataRow.file = reader.result;
                }, false);
    
                if (file)
                {
                    reader.readAsDataURL(file);
                }
    
                this.$forceUpdate();
            },
            isPage: function(dataRow)
            {
                if (!this.isDefined(dataRow)) { return true; }
                if (typeof dataRow.card_tab_rel_id === "undefined") { return false; }
                return true;
            },
            isColorPicker: function(dataRow)
            {
                if (!this.isDefined(dataRow) || (dataRow.type !== "color-picker" && this.dataType !== "color-picker")) { return false; }
                return true;
            },
            isText: function(dataRow)
            {
                if (!this.isDefined(dataRow)) { return true; }
                if (dataRow.type !== "text") { return false; }
                return true;
            },
            isImage: function(dataRow)
            {
                if (!this.isDefined(dataRow) || (dataRow.type !== "image" && this.dataType !== "image")) { return false; }
                return true;
            },
            isDate: function(dataRow)
            {
                if (!this.isDefined(dataRow) || (dataRow.type !== "date" && this.dataType !== "date")) { return false; }
                return true;
            },
            isEmail: function(dataRow)
            {
                if (!this.isDefined(dataRow) || (dataRow.type !== "email" && this.dataType !== "email")) { return false; }
                return true;
            },
            isPhone: function(dataRow)
            {
                if (!this.isDefined(dataRow) || (dataRow.type !== "phone" && dataRow.type !== "sms" && this.dataType !== "phone")) { return false; }
                return true;
            },
            isConnection: function(dataRow)
            {
                if (!this.isDefined(dataRow) || (dataRow.type !== "connection" && this.dataType !== "connection")) { return false; }
                return true;
            },
            isState: function(dataRow)
            {
                if (!this.isDefined(dataRow) || (dataRow.type !== "state" && this.dataType !== "state")) { return false; }
                return true;
            },
            isZip: function(dataRow)
            {
                if (!this.isDefined(dataRow) || (dataRow.type !== "postal" && this.dataType !== "postal")) { return false; }
                return true;
            },
            isDefined: function(val)
            {
                return typeof val !== "undefined";
            },
            imgError: function()
            {
                this.imageData = "";
                this.$forceUpdate();
            },
        ';
    }

    protected function renderTemplate() : string
    {
        global $app;
        return '
            <div class="vueDataManagerRow">
                <v-style type="text/css">
                    .vueDataManagerRow {
                        width:100%;
                    }
                </v-style>
                <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isPage(dataRow)">
                    <tr>
                        <td>
                            <h6>Page Title:</h6>
                            <input type="text" v-model="dataRow.title" class="form-control"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h6>Page Header:</h6>
                            <input type="text" v-model="dataRow.header" class="form-control"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h6>Page Content:</h6>
                            <textarea v-bind:id="uniqueId" name="tab_content"></textarea>
                        </td>
                    </tr>
                </table>
                <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isColorPicker(dataRow)">
                    <tr>
                        <td style="padding: 5px .75rem; overflow: visible;"><div v-bind:id="uniqueId" v-bind:style="{\'background-color\': dataColor }" style="width:200px;height:85px;border-radius: 10px;"></div></td>
                    </tr>
                </table>
                <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isText(dataRow)">
                    <tr>
                        <td style="padding: 5px .75rem;"><input v-model="dataRow.value" class="form-control" type="text" v-bind:placeholder="\'Enter \' + dataRow.type"></td>
                    </tr>
                </table>
                <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isImage(dataRow)">
                    <tr>
                        <td style="width: 50px;padding: 5px .75rem;">
                            <img class="pointer" v-on:click="uploadNewImage(dataRow)" v-show="imageData" v-bind:src="imageData" width="35" height="35" @error="imgError"/>
                            <img class="pointer" v-on:click="uploadNewImage(dataRow)" v-show="!imageData" src="/_ez/images/no-image.jpg" width="35" height="35" />
                        </td>
                        <td style="padding: 5px .75rem;">
                            <input v-model="imageName" class="form-control" type="text" placeholder="Logo Url" readonly="readonly">
                            <input type="file" ref="uploadImage" v-on:change="handleImageUpload()" style="display: none;" />
                        </td>
                        <td style="width: 50px;padding: 5px .75rem;">
                            <span v-show="newImage === true" v-on:click="clearImage(dataRow)" class="pointer deleteEntityButton" style="position: relative;top: 7px;"></span>
                        </td>
                    </tr>
                </table>
                <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isConnection(dataRow)">
                    <tr>
                        <td style="width: 260px;padding: 5px .75rem;">
                            <select v-model="dataRow.typeInstance" class="form-control" >
                                <option value="">-- Select Connection Type --</option>
                                <option value="sms">SMS</option>
                                <option value="phone">Phone</option>
                                <option value="email">E-Mail</option>
                                <option value="url">Website</option>
                            </select></td>
                        <td style="padding: 5px .75rem;">
                            <input v-model="dataRow.value" class="form-control" type="text" placeholder="Enter Connection">
                        </td>
                    </tr>
                </table>
                <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isPhone(dataRow)">
                    <tr>
                        <td style="padding: 5px .75rem;"><input v-model="dataRow.value" class="form-control" type="text" placeholder="Enter phone number"></td>
                    </tr>
                </table>
                <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isEmail(dataRow)">
                    <tr>
                        <td style="padding: 5px .75rem;"><input v-model="dataRow.value" class="form-control" type="text" placeholder="Enter e-mail"></td>
                    </tr>
                </table>
                <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isState(dataRow)">
                    <tr>
                        <td style="padding: 5px .75rem;"><input v-model="dataRow.value" class="form-control" type="text" placeholder="Enter state"></td>
                    </tr>
                </table>
                <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isZip(dataRow)">
                    <tr>
                        <td style="padding: 5px .75rem;"><input v-model="dataRow.value" class="form-control" type="text" placeholder="Enter zip"></td>
                    </tr>
                </table>
            </div>
        ';
    }
}