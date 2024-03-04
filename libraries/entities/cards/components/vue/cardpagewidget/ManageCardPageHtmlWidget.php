<?php

namespace Entities\Cards\Components\Vue\CardPageWidget;

use App\Core\AppModel;
use App\website\vue\classes\VueComponentHtml;

class ManageCardPageHtmlWidget extends VueComponentHtml
{
    protected string $id = "1db71dce-e26f-403d-b83f-3e79be9b0b98";
    protected string $name = "compHtml";
    protected string $vueType = "compHtml";
    protected string $modalWidth = "1200";

    public function __construct(?AppModel $entity = null, $name = "Html Widget", $props = [])
    {
        parent::__construct($entity,$name, $props);

        $this->modalTitleForAddEntity = "Add " . $name;
        $this->modalTitleForEditEntity = "Edit " . $name;
        $this->modalTitleForDeleteEntity = "Delete " . $name;
        $this->modalTitleForRowEntity = "View " . $name;
    }

    protected function renderComponentDataAssignments() : string
    {
        return parent::renderComponentDataAssignments() . '
            card_id: null,
        ';
    }

    protected function renderComponentMethods() : string
    {
        return parent::renderComponentMethods() . '
            submitHtml: function() {
                const url = "/cards/card-data/save-card-page-app-content?id=" + this.entity.card_tab_id;
                const self = this;
                const editor = $(".' . $this->froalaElementId . '");
                this.htmlData = this.getHtmlCode(editor);
                const htmlFroalaObject = {title: this.titleText, content: this.htmlData, card_id: this.entity.card_id, action: this.action};
                
                modal.EngageFloatShield();

                ajax.PostExternal(url, htmlFroalaObject, true, function(result) 
                {
                    if (result.success === false) 
                    {
                        let data = {title: "Widget Error", html: "Oh no! There was an error saving the data for this widget: " + objResult.message };
                        modal.EngagePopUpAlert(data, function() {
                            modal.CloseFloatShield();
                        }, 350, 115, true);
                        return;
                    }
                    
                    if (self.action === "create")
                    {
                        self.entities.push(result.response.data.card);
                    }
                    else
                    {
                        self.entities.forEach(function (currEntity, currIndex)
                        {
                            if (self.entity.card_tab_id === currEntity.card_tab_id)
                            {
                                self.entities[currIndex].title = self.titleText;
                            }
                        });
                    }
                     
                    modal.CloseFloatShield();
                });
            },
            getHtmlCode: function(editor)
            {
                if (editor.froalaEditor("codeView.isActive"))
                {
                  return editor.froalaEditor("codeView.get").replace(/[\t\n]+/, "");
                }
                
                return editor.froalaEditor("html.get").replace(/[\t\n]+/, "");
            },
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return parent::renderComponentHydrationScript() . '                
        if (this.entity && typeof this.entity.card_tab_id !== "undefined") 
        {
            this.titleText = this.entity.title;
            this.htmlActionButton = "Save '.$this->name.'";
            this.action = "update";
            
            const url = "/cards/card-data/get-card-page-app-content?id=" + this.entity.card_tab_id;
            const self = this;
            
            ajax.PostExternal(url, {}, true, function(result) 
            {
                if (result.success === false) 
                {
                    let data = {title: "Widget Error", html: "Oh no! There was an error getting the data for this widget: " + objResult.message };
                    modal.EngagePopUpAlert(data, function() {
                        modal.CloseFloatShield();
                    }, 350, 115, true);
                    return;
                }
                
                self.htmlData = atob(result.response.data);
                $(".' . $this->froalaElementId . '").froalaEditor("html.set", self.htmlData);
            });
        }
        else
        {
            this.titleText = "";
            this.htmlData = "";
            this.action = "create";
            $(".' . $this->froalaElementId . '").froalaEditor("html.set", "");
            this.htmlActionButton = "Create New '.$this->name.'";
        }
        ';
    }
}