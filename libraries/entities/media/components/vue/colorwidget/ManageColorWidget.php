<?php

namespace Entities\Media\Components\Vue\ColorWidget;

use App\Core\App;
use App\Core\AppModel;
use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\Management\VueManageData;
use App\Website\Vue\Classes\VueProps;

class ManageColorWidget extends VueComponent
{
    protected string $id = "2b9e4ae5-3e73-4c52-8a79-1ce8c1e0aac5";
    protected string $modalWidth = "850";
    protected string $title = "Color Editor";

    protected ?VueComponent $manageDataWidget = null;

    public function __construct(?AppModel $entity = null)
    {
        parent::__construct($entity);

        $this->manageDataWidget = $this->registerDynamicComponent(
            new VueManageData(),
            "view",
            [
                new VueProps("dataRow", "object", "currPage"),
                new VueProps("dataField", "string", "dataField"),
                new VueProps("dataType", "string", "dataType"),
                new VueProps("userId", "string", "userId")
            ]);


        $this->modalTitleForAddEntity = "View Color Editor";
        $this->modalTitleForEditEntity = "View Color Editor";
        $this->modalTitleForDeleteEntity = "View Color Editor";
        $this->modalTitleForRowEntity = "View Color Editor";
        $this->setDefaultAction("view");
    }

    protected function renderComponentDataAssignments() : string
    {
        return parent::renderComponentDataAssignments(). '
                myColorPicker: null,
                selectedColor: "rgba(255,0,0,1)",
                selectedDisplayColor: "ff0000",
                selectedAlpha: "1",
        ';
    }

    protected function renderComponentMethods() : string
    {
        return parent::renderComponentMethods() . '
            assignColor: function(color) {
                dispatch.broadcast("assign_color_to_site", {color: this.selectedColor, label: this.label})
            },
            setColor: function(color) {
                this.selectedColor = "rgba(" + Math.floor(color.rgb.r * 255) + "," + Math.floor(color.rgb.g * 255) + "," + Math.floor(color.rgb.b * 255) + "," + (color.alpha) + ")"
                this.selectedDisplayColor = color.HEX
                this.selectedAlpha = color.alpha
            },
            renderBackground: function() {
                return {
                    backgroundColor: this.selectedColor
                }
            },
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return "
        const self = this
        if (this.myColorPicker === null) {
            this.myColorPicker = new ColorPicker({
                color: (props && props.media && props.media[props.label]) ? props.media[props.label].color : '#ff0000',
                mode: 'hsv-h',
                fps: 60,
                delayOffset: 8,
                CSSPrefix: 'cp-',
                size: 3,
                allMixDetails: true,
                customBG: '#808080',
                memoryColors: [{r: 100, g: 200, b: 10, a: 0.8}],
                appendTo: document.getElementById('colorPicker'),
                noHexButton: false,
                renderCallback: function(colors, mode){ 
                    self.setColor(colors)
                },
                actionCallback: function(e, action){},
                convertCallback: function(colors, type){},
            });
        }
        if (props && props.media && props.media[props.label] && props.source !== 'new') {
            const fullColor = props.media[props.label].color
            let alphaColor = 1
            if (fullColor.substr(0,4) === 'rgba') {
                alphaColor = fullColor.split(',')[3].replace(')','')
            }
        
            this.myColorPicker.setColor(props.media[props.label].color, 'rgb', alphaColor, true)
        } else {
            this.myColorPicker.setColor('#ff0000', 'rgb', 1, true)
        }
    ";
    }
    protected function renderTemplate() : string
    {
        /** @var App $app */
        global $app;
        return '<div class="formwrapper-control color-manager-wrapper">
            <v-style type="text/css">
                .color-manager-wrapper .cp-app {
                    position:relative;
                }
                .color-manager-wrapper .cp-bsav,
                .color-manager-wrapper .cp-bres,
                .color-manager-wrapper .cp-cold,
                .color-manager-wrapper .cp-cont {
                    display:none;
                }
                .color-manager-wrapper .cp-app * {
                    color:white !important;
                }
            </v-style>
            <div id="colorPickerWrapper">
                <div id="colorPicker" class="width50"></div>
                <div class="width50 pl-4">
                    <h2 class="pop-up-dialog-main-title-text mb-2">Selected Color</h2>
                    <div style="border:1px solid #aaa;padding:5px;border-radius: 3px;">
                        <div v-bind:style="renderBackground()" style="width:100%; height:25px;"></div>
                    </div>
                    <table class="table">
                        <tr>
                            <td style="padding-bottom:3px;">HEX: #{{ selectedDisplayColor }}</td>
                            <td style="padding-bottom:3px;">Alpha: {{ selectedAlpha }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-top:0;">{{ selectedColor }}</td>
                        </tr>
                    </table>
                    <p>To use this color as your background profile, assign it below.</p>
                    <table class="table">
                        <tr>
                            <td><button class="btn btn-primary w-100" v-on:click="assignColor">Assign Color</button></td>
                        </tr>
                   </table>
                </div>
            </div>
        </div>';
    }
}