<?php

namespace Entities\Cards\Components\Vue\CardPageWidget;

use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\VueProps;
use Entities\Cards\Models\CardPageModel;

class ManageCardPageWidget extends VueComponent
{
    protected string $id = "f27392d3-b370-423c-865f-8018592a5984";
    protected $htmlComponent;

    public function __construct(array $components = [])
    {
        $defaultEntity = (new CardPageModel())
            ->setDefaultSortColumn("card_tab_id", "DESC")
            ->setDisplayColumns(["card_tab_id", "title", "type", "card_count", "created_on", "last_updated"])
            ->setRenderColumns(["card_tab_id", "title", "user_id", "card_count", "type", "created_on", "last_updated", "__app"]);

        parent::__construct($defaultEntity, $components);

        $mainEntityList = new VueProps("mainEntityList", "array", "mainEntityList");
        $this->addProp($mainEntityList);

        $editorComponent = new ManageCardPageHtmlWidget($defaultEntity);
        $editorComponent->addParentId($this->getInstanceId(), ["edit"]);
        $this->addComponent($editorComponent);

        $selectWidget = new SelectNewCardPageWidget($editorComponent->getEntity(), "Card Page Widget", $editorComponent->getProps());
        $selectWidget->setParentId($editorComponent->getInstanceId(), ["add"]);
        $this->addComponent($selectWidget);

        $this->modalTitleForAddEntity = "Add Card Page Widget";
        $this->modalTitleForEditEntity = "Edit Card Page Widget";
        $this->modalTitleForDeleteEntity = "Delete Card Page Widget";
        $this->modalTitleForRowEntity = "View Card Page Widget";
    }

    protected function renderComponentDataAssignments(): string
    {
        return "
        showNewSelection: true,
        ";
    }

    protected function renderComponentMethods() : string
    {
        return '
            newHtmlPage: function(result) {
                const originalCard = {card_id: this.entity.card_id };
                '. $this->activateRegisteredComponentByIdInModal(ManageCardPageHtmlWidget::getStaticId(), "add", true, "this.entity", "this.entities", ["card" => "originalCard"]).'
                
            },
            newPageAndWidget: function(result) {
                '. $this->activateRegisteredComponentByIdInModal(SelectNewCardPageWidget::getStaticId(), "add", true, "this.entity", "this.entities").'
            }
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return '
        if (this.entity && typeof this.entity.card_tab_id !== "undefined") 
        {
            this.showNewSelection = false;
            this.engageModalLoadingSpinner();
            let self = this;

            ajax.Get("cards/card-data/get-card-tab?card_tab_id=" + this.entity.card_tab_id, null, function(result)
            {
                if (result.success === false || typeof result.response.data === "undefined" || result.response.data.length === 0) 
                { 
                    // Throw Error?
                }
                
                const cardData = {
                    cardPageId: result.response.data[0].card_tab_id, 
                    cardPageType: result.response.data[0].card_tab_type_id, 
                    title: result.response.data[0].title, 
                    html: atob(result.response.data[0].content), 
                    url: result.response.data[0].url, 
                    visibility: result.response.data[0].visibility, 
                    library: result.response.data[0].library_tab 
                };
                '. $this->activateRegisteredComponentByIdInModal(ManageCardPageHtmlWidget::getStaticId(), "edit", false,  "self.entity", "self.entities",["card" => "cardData"],"self").';
                self.disableModalLoadingSpinner();  
            });
        }
        else
        {
            this.showNewSelection = true;
            this.disableModalLoadingSpinner();  
        }
        ';
    }

    protected function renderTemplate() : string
    {
        return '<div>
            <div v-show="showNewSelection" class="tabSelectionOuter divTable" style="">
                <div class="tabSelectionRow divRow">
                    <div class="divCell tabSelectionLabel tabSelectionHtmlTab" style="width:50%;" v-on:click="newHtmlPage();">
                        <h2>Create a New HTML Page</h2>
                        <div class="tabSelectionActionButton">
                            <i class="fas fa-file-code"></i>
                        </div>
                    </div>
                    <div class="divCell tabSelectionLabel tabSelectionSpecialTabs" style="width:50%;" v-on:click="newPageAndWidget();">
                        <h2>Create a New Widget</h2>
                        <div class="tabSelectionActionButton">
                            <i class="fas fa-clone"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ';
    }
}