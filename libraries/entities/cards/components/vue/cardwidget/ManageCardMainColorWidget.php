<?php

namespace Entities\Cards\Components\Vue\CardWidget;

use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cards\Models\CardModel;

class ManageCardMainColorWidget extends VueComponent
{
    protected string $id = "096af019-2555-471c-afda-6a19a4d9d5a8";
    protected string $modalWidth = "520";

    public function __construct (array $components = [])
    {
        parent::__construct((new CardModel()), $components);

        $this->modalTitleForAddEntity = "Add Card Main Color";
        $this->modalTitleForEditEntity = "Edit Card Main Color";
        $this->modalTitleForDeleteEntity = "Delete Card Main Color";
        $this->modalTitleForRowEntity = "View Card Main Color";
    }

    protected function renderComponentDataAssignments() : string
    {
        return '
            mainCardColor: "ff0000",
        ';
    }

    protected function renderComponentMethods() : string
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
            },
            loadColorPickerModal: function()
            {
                let self = this;
                const mainCardColor = getJsonSetting(this.entity.card_data, "style.card.color.main");
                this.mainCardColor = (mainCardColor !== null) ? atob(mainCardColor) : this.mainCardColor;
                
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
                                // self.entity.card_data.style.card.color.main = strHex;
                                
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
            updateCardData: function(strStyleLabel, objValue, callback)
            {
                let intEntityId = this.entity.card_id;

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
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return parent::renderComponentHydrationScript() . '
            let self = this;
            setTimeout(function() {
                self.checkIfDomIsLoaded();
            }, 150);
            
        ';
    }

    protected function renderTemplate() : string
    {
        return '<div>
            <div v-if="entity" class="divTable">
                <div class="divRow">
                    <div class="divCell">
                        <img id="main-image-color-selector" v-bind:src="entity.banner" width="256" height="256" alt="" style="margin-right:15px;" />
                    </div>
                    <div class="divCell">
                        <p id="colorpickerHolder"></p>
                        <button id="updateCardParmaryColorSubmit" class="btn btn-primary w-100">Update Color</button>
                    </div>
                </div>
            </div>
        </div>';
    }
}