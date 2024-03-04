<?php

namespace Entities\Cards\Components\Vue\Cardwidget\Footer;

use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cards\Models\CardModel;

class ManagePageHighlightWidget extends VueComponent
{
    protected string $id = "3f42dea9-58bf-49c3-a85b-3579c287c6d8";
    protected string $modalWidth = "750";

    public function __construct (array $components = [])
    {
        $displayColumns = ["banner", "status"];

        if (userCan("manage-platforms")) {
            $displayColumns[] = "platform";
        }

        $displayColumns = array_merge($displayColumns, ["card_name", "card_num", "card_vanity_url", "card_owner_name", "card_contacts", "product", "created_on", "last_updated"]);

        $defaultEntity = (new CardModel())
            ->setDefaultSortColumn("card_num", "DESC")
            ->setDisplayColumns($displayColumns)
            ->setRenderColumns(["card_id", "owner_id", "card_owner_name", "card_name", "card_num", "card_vanity_url", "card_keyword", "product", "card_contacts", "status", "order_line_id", "platform", "company_id", "banner", "favicon", "created_on", "last_updated",]);

        parent::__construct($defaultEntity, $components);

        $this->modalTitleForAddEntity = "Edit Highlight Page";
        $this->modalTitleForEditEntity = $this->modalTitleForAddEntity;
        $this->modalTitleForDeleteEntity = $this->modalTitleForAddEntity;
        $this->modalTitleForRowEntity = $this->modalTitleForAddEntity;
    }

    protected function renderComponentDataAssignments (): string
    {
        return '
            entityClone: false,
        ';
    }

    protected function renderComponentHydrationScript () : string
    {
        return '
            if (this.entity) {
                this.entityClone = _.clone(this.entity);
            }
        '.parent::renderComponentHydrationScript();
    }

    protected function renderComponentDismissalScript() : string
    {
        return parent::renderComponentDismissalScript() . '
            this.entityClone = false
        ';
    }

    protected function renderComponentMethods (): string
    {
        return '
            updateShowcasePage: function() {
                const self = this
                const url = "api/v1/cards/update-theme-config";
                const postData = {card_id: this.entityClone.card_id}
                ajax.Post(url, postData, function(result) {
                    dispatch.broadcast("reload_site_settings");
                });
            },
        ';
    }

    protected function renderComponentComputedValues() : string
    {
        return 'cardPages: function()
            {
                if (typeof this.entityClone !== "undefined" && this.entityClone !== false && typeof this.entityClone.Tabs !== "undefined") { return this.entityClone.Tabs; }
                return [];
            },';
    }

    protected function renderTemplate() : string
    {
        return '
        <div class="editEntityProfile site-theme-settings">
            <v-style type="text/css">
            </v-style>
            <div v-if="cardPages.length > 0">
                <div class="augmented-form-items">
                    <table class="table no-top-border">
                        <tbody>
                            <tr>
                                <td style="width: 150px; vertical-align: middle;">Page Reference</td>
                                <td>
                                    <select class="form-control">
                                        <option v-for="cardPage in cardPages" v-if="cardPage.rel_sort_order > 1">{{ cardPage.title}}</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div v-if="cardPages.length === 0">
            </div>
            <button v-on:click="updateShowcasePage" class="buttonID9234594357456 btn btn-primary w-100">Update Highlight Page</button>
        </div>';
    }
}