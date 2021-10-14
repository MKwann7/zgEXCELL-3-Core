<?php

namespace Entities\Cards\Components\Vue\CardWidget;

use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cards\Models\CardModel;

class ManageCardTemplateSettingsWidget extends VueComponent
{
    protected $id = "276dc4cf-5d58-48c6-9b52-15b57a07dc4f";
    protected $modalWidth = 750;

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

        $this->modalTitleForAddEntity = "Add Card Template Settings";
        $this->modalTitleForEditEntity = "Edit Card Template Settings";
        $this->modalTitleForDeleteEntity = "Delete Card Template Settings";
        $this->modalTitleForRowEntity = "View Card Template Settings";
    }

    protected function renderComponentDataAssignments (): string
    {
        return '
            entityClone: false,
            mainCardColor: "ff0000",
            bannerBase64Data: "",
            hideSplashTitle: false,
        ';
    }

    protected function renderComponentHydrationScript () : string
    {
        return '
            if (this.entity)
            {
                this.entityClone = _.clone(this.entity);
            }
            
            let self = this;
            setTimeout(function() {
                self.checkIfDomIsLoaded();
            }, 150);
        '.parent::renderComponentHydrationScript();
    }

    protected function renderComponentMethods (): string
    {
        return '
            checkIfDomIsLoaded: function()
            {
                const elPicker = document.getElementById("colorpickerHolder");
                
                if (elPicker === null) 
                {
                    let self = this;
                    setTimeout(function() {
                        self.checkIfDomIsLoaded();
                    }, 50);
                    
                    return;
                }
                
                this.loadColorPickerModal();
                this.loadImageIntoCanvas();
                this.loadImageColorPicker();
            },
            loadColorPickerModal: function()
            {
                let self = this;
                const mainCardColor = getJsonSetting(this.entityClone.card_data, "style.card.color.main");
                const hideSplashTitle = getJsonSetting(this.entityClone.card_data, "style.card.settings.hideSplashTitle");
                this.mainCardColor = (mainCardColor !== null) ? atob(mainCardColor) : this.mainCardColor;
                this.hideSplashTitle = (hideSplashTitle !== null) ? true : this.hideSplashTitle;
                
                const elPicker = document.getElementById("colorpickerHolder");                
                const elPickerClass = elPicker.getElementsByClassName("colpick_hex");
                    
                $( function() {
                    $("#colorpickerHolder").colpick({
                        color: self.mainCardColor,
                        flat:true,
                        layout:"hex",
                        onSubmit: function(objColPick, strHex)
                        {
                            modal.EngageFloatShield();
                            
                            self.updateCardData("style.card.color.main", strHex, function(result){
                                $(".card-main-color-block").css("backgroundColor","#" + strHex);
                                self.entity.card_data.style.card.color.main = strHex;
                                
                                setTimeout(function() {
                                    modal.CloseFloatShield(function() { modal.CloseFloatShield(); });
                                    let vue = self.findApp(self);
                                    vue.$forceUpdate();
                                                 
                                    let objModal = self.findModal(self);                 
                                    objModal.close();   
                                }, 500);
                            });
                        }
                    });
                    
                    $(document).on("click","#updateCardParmaryColorSubmit",function() {
                        $(".colpick_submit").click();
                    });
    
                    $(document).on("click","#main-image-color-selector",function() {
                        let img = document.getElementById("main-image-color-selector");
                        let canvas = document.createElement("canvas");
                        canvas.width = img.width;
                        canvas.height = img.height;
                        canvas.getContext("2d").drawImage(img, 0, 0, img.width, img.height);
                        
                        let pixelData = canvas.getContext("2d").getImageData(event.offsetX, event.offsetY, 1, 1).data;
                        
                        console.log(JSON.stringify(pixelData));
                    });
                });
            },
            loadImageIntoCanvas: function()
            {
                let self = this;
                function toDataURL(src, callback, outputFormat) {
                  var img = new Image();
                  img.crossOrigin = \'Anonymous\';
                  img.onload = function() {
                    var canvas = document.createElement(\'CANVAS\');
                    var ctx = canvas.getContext(\'2d\');
                    var dataURL;
                    canvas.height = this.naturalHeight;
                    canvas.width = this.naturalWidth;
                    ctx.drawImage(this, 0, 0);
                    dataURL = canvas.toDataURL(outputFormat);
                    callback(dataURL);
                  };
                  img.src = src;
                  if (img.complete || img.complete === undefined) {
                    img.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
                    img.src = src;
                  }
                }
                
                toDataURL(
                  this.entity.banner,
                  function(dataUrl) {
                      self.bannerBase64Data = dataUrl;
                  }
                )
            },
            loadImageColorPicker: function()
            {
                let img = _(\'#main-image-color-selector\'),
                    canvas = _(\'#cs\'),
                    preview = _(\'.preview\'),
                    result = _(\'.result\'),x = \'\',y = \'\';
                const colorPicker = $("#colorpickerHolder");
                
                img.addEventListener(\'click\', function(e){
                  // chrome
                  if(e.offsetX) {
                    x = e.offsetX;
                    y = e.offsetY; 
                  }
                  // firefox
                  else if(e.layerX) {
                    x = e.layerX;
                    y = e.layerY;
                  }
                  useCanvas(canvas,img,function(){
                    // get image data
                    var p = canvas.getContext(\'2d\')
                    .getImageData(x, y, 1, 1).data;
                    
                    // show info
                    colorPicker.colpickSetColor(rgbToHex(p[0],p[1],p[2]));
                  });
                },false);
                
                img.addEventListener(\'mousemove\', function(e){
                  // chrome
                  if(e.offsetX) {
                    x = e.offsetX;
                    y = e.offsetY; 
                  }
                  // firefox
                  else if(e.layerX) {
                    x = e.layerX;
                    y = e.layerY;
                  }
                  
                  useCanvas(canvas,img,function(){
                    
                    // get image data
                    var p = canvas.getContext(\'2d\')
                    .getImageData(x, y, 1, 1).data;
                    // show preview color
                    preview.style.background = rgbToHex(p[0],p[1],p[2]);
                  });
                },false);
                
                // canvas function
                function useCanvas(el,image,callback){
                  el.width = image.width; // img width
                  el.height = image.height; // img height
                  // draw image in canvas tag
                  el.getContext(\'2d\')
                  .drawImage(image, 0, 0, image.width, image.height);
                  return callback();
                }
                
                // short querySelector
                function _(el){
                  return document.querySelector(el);
                };
                
                function componentToHex(c) {
                  var hex = c.toString(16);
                  return hex.length == 1 ? "0" + hex : hex;
                }
                
                function rgbToHex(r, g, b) {
                  return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
                }
                
                function findPos(obj) {
                    var curleft = 0, curtop = 0;
                    if (obj.offsetParent) {
                        do {
                            curleft += obj.offsetLeft;
                            curtop += obj.offsetTop;
                        } while (obj = obj.offsetParent);
                        return { x: curleft, y: curtop };
                    }
                    return undefined;
                }
            },
            updateCardData: function(strStyleLabel, objValue, callback)
            {
                let intEntityId = this.entityClone.card_id;

                if (!intEntityId)
                {
                    return;
                }

                let strCardUpdateDataParameters = "fieldlabels=" + btoa(strStyleLabel) + "&value=" + btoa(objValue);
                
                ajax.Post("cards/card-data/update-card-data?id=" + intEntityId + "&type=card-data", strCardUpdateDataParameters, function(objCardResult)
                {
                    if(typeof callback === "function")
                    {
                        callback(objCardResult);
                    }
                });
            },
            updateSplashTitle: function()
            {
                modal.EngageFloatShield();
                    
                const self = this;        
                self.updateCardData("style.card.settings.hideSplashTitle", this.hideSplashTitle, function(result) {

                    self.entity.card_data.style.card.settings.hideSplashTitle = self.hideSplashTitle;
                    
                    setTimeout(function() {
                        modal.CloseFloatShield(function() { modal.CloseFloatShield(); });
                        let vue = self.findApp(self);
                        vue.$forceUpdate();
                                     
                        let objModal = self.findModal(self);                 
                        objModal.close();   
                    }, 500);
                });
            },
        ';
    }

    protected function renderComponentComputedValues() : string
    {
        return '
        ';
    }

    protected function renderTemplate() : string
    {
        return '
        <div class="editEntityProfile">
            <v-style type="text/css">
                .main-image-color-box {
                    padding-right:15px;
                }
                .main-image-color-box-inner {
                    width:480px;
                    position:relative;
                }
                .preview {
                    position:absolute;
                    top:0px;
                    right:0px;
                    width: 25px;
                    height:25px;
                    border-left:1px solid #fff;
                    border-bottom:1px solid #fff;
                }
            
            </v-style>
            <div v-if="entity" class="divTable">
                <div class="divRow">
                    <div class="divCell main-image-color-box">
                        <div class="main-image-color-box-inner">
                            <img id="main-image-color-selector" v-bind:src="bannerBase64Data" alt="" style="width:480px;height:auto;" />
                            <div class="preview"></div>
                            <canvas style="display:none;" id="cs"></canvas>
                        </div>
                    </div>
                    <div class="divCell">
                        <p id="colorpickerHolder"></p>
                        <button id="updateCardParmaryColorSubmit" class="btn btn-primary w-100">Update Color</button>
                    </div>
                </div>
                <div class="divRow">
                    <select v-model="hideSplashTitle" class="form-control" v-on:change="updateSplashTitle">
                        <option value="false">Show Splash Title</option>
                        <option value="true">Hide Splash Title</option>
                    </select>
                </div>
            </div>
        </div>';
    }
}