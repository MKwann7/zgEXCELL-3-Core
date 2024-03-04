<?php

namespace Entities\Cards\Components\Vue\CardWidget;

use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cards\Models\CardModel;

class ManageSiteTemplateSettingsWidget extends VueComponent
{
    protected string $id = "276dc4cf-5d58-48c6-9b52-15b57a07dc4f";
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

        $this->modalTitleForAddEntity = "Add Theme Settings";
        $this->modalTitleForEditEntity = "Edit Theme Settings";
        $this->modalTitleForDeleteEntity = "Delete Theme Settings";
        $this->modalTitleForRowEntity = "View Theme Settings";
    }

    protected function renderComponentDataAssignments (): string
    {
        return '
            entityClone: false,
            defaultSettings: [],
            customSettings: [],
            themeSettings: [],
        ';
    }

    protected function renderComponentHydrationScript () : string
    {
        return '
            if (this.entity) {
                this.entityClone = _.clone(this.entity);
                this.mergeSettings(props.themeSettings, props.configSettings)
            }
        '.parent::renderComponentHydrationScript();
    }

    protected function renderComponentDismissalScript() : string
    {
        return parent::renderComponentDismissalScript() . '
            this.defaultSettings = []
            this.customSettings = []
            this.themeSettings = []
        ';
    }

    protected function renderComponentMethods (): string
    {
        return '
            updateThemeSettings: function() {
                const self = this
                const url = "api/v1/cards/update-theme-config";
                const postData = {card_id: this.entityClone.card_id, data: this.defaultSettings}
                ajax.Post(url, postData, function(result) {
                    self.entity.Settings.theme_config = self.themeSettings
                    dispatch.broadcast("reload_site_settings");
                });
            },
            mergeSettings: function(base, custom) {
                this.themeSettings = {}
                this.defaultSettings = _.clone(base)
                this.customSettings = _.clone(custom)
                
                for (currSettingIndex in this.defaultSettings) {
                    if (this.defaultSettings[currSettingIndex].type !== "theme") continue
                    this.themeSettings[currSettingIndex] = this.defaultSettings[currSettingIndex]
                    if (this.customSettings[currSettingIndex] && this.customSettings[currSettingIndex].elements) {
                        for (currSettingElementIndex in this.themeSettings[currSettingIndex].elements) {
                            const currElement = this.customSettings[currSettingIndex].elements[currSettingElementIndex]
                            if (currElement.data.default) {
                                this.themeSettings[currSettingIndex].elements[currSettingElementIndex].data.default = currElement.data.default
                            }
                            if (currElement.data.responsive.tablet) {
                                this.themeSettings[currSettingIndex].elements[currSettingElementIndex].data.responsive.tablet = currElement.data.responsive.tablet
                            }
                            if (currElement.data.responsive.mobile) {
                                this.themeSettings[currSettingIndex].elements[currSettingElementIndex].data.responsive.mobile = currElement.data.responsive.mobile
                            }
                        }
                    }
                }
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
        <div class="editEntityProfile site-theme-settings">
            <v-style type="text/css">
            </v-style>
            <div v-for="currSetting, settingIndex in themeSettings">
                <div v-if="currSetting.elements">
                    <span class="pop-up-dialog-main-title-text mb-2" style="font-size:20px;">{{ currSetting.title }}</span>
                    <div class="augmented-form-items">
                        <table class="table no-top-border">
                            <tbody>
                                <tr v-for="currSettingEl, elIndex in currSetting.elements">
                                    <td style="width: 150px; vertical-align: middle;">{{ currSettingEl.title }}</td>
                                    <td v-if="!currSettingEl.data.responsive">
                                        <input v-model="themeSettings[settingIndex].elements[elIndex].data.default" type="text" v-bind:placeholder="currSettingEl.data.default" class="form-control">
                                    </td>
                                    <td v-if="currSettingEl.data.responsive">
                                        <table class="table no-top-border mb-0">
                                            <tr>
                                                <td><input v-model="themeSettings[settingIndex].elements[elIndex].data.default" type="text" v-bind:placeholder="\'Desktop: \' + currSettingEl.data.default" class="form-control"></td>
                                                <td><input v-model="themeSettings[settingIndex].elements[elIndex].data.responsive.tablet" type="text" v-bind:placeholder="\'Tablet: \' + currSettingEl.data.responsive.tablet" class="form-control"></td>
                                                <td><input v-model="themeSettings[settingIndex].elements[elIndex].data.responsive.mobile" type="text" v-bind:placeholder="\'Mobile: \' + currSettingEl.data.responsive.mobile" class="form-control"></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <button v-on:click="updateThemeSettings" class="buttonID9234594357456 btn btn-primary w-100">Update Site Theme</button>
        </div>';
    }
}