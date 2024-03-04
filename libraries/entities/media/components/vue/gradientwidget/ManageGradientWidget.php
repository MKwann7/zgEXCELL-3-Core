<?php

namespace Entities\Media\Components\Vue\GradientWidget;

use App\Core\App;
use App\Core\AppModel;
use App\Website\Vue\Classes\Base\VueComponent;

class ManageGradientWidget extends VueComponent
{
    protected string $id = "583f49e2-4e32-4ee5-a14a-e0979ccd88cc";
    protected string $modalWidth = "850";
    protected string $title = "Gradient Editor";

    public function __construct(?AppModel $entity = null)
    {
        parent::__construct($entity);


        $this->modalTitleForAddEntity = "View Gradient Editor";
        $this->modalTitleForEditEntity = "View Gradient Editor";
        $this->modalTitleForDeleteEntity = "View Gradient Editor";
        $this->modalTitleForRowEntity = "View Gradient Editor";
        $this->setDefaultAction("view");
    }

    protected function renderComponentDataAssignments() : string
    {
        return parent::renderComponentDataAssignments(). '
                myGradientPicker: null,
                selectedGradient: "linear-gradient(to right, #085078 1%, #85D8CE 99%)",
                gradientType: "linear",
                gradientDirection: "center",
                gradientDataColors: [],
        ';
    }

    protected function renderComponentMethods() : string
    {
        return parent::renderComponentMethods() . '
            assignGradient: function(color) {
                dispatch.broadcast("assign_gradient_to_site", {gradient: this.selectedGradient, label: this.label, options: {
                    colors: this.gradientDataColors,
                    type: this.gradientType,
                    direction: this.gradientDirection,
                }})
            },
            setColor: function(color) {
                this.selectedGradient = "rgba(" + Math.floor(color.rgb.r * 255) + "," + Math.floor(color.rgb.g * 255) + "," + Math.floor(color.rgb.b * 255) + "," + (color.alpha) + ")"
            },
            renderBackground: function() {
                return {
                    backgroundImage: this.selectedGradient
                }
            },
            createGrapick: function() {
                const self = this
                this.myGradientPicker = new Grapick({
                    el: "#gradientPicker",
                    type: self.gradientType,
                    direction: self.gradientDirection,
                    min: 1,
                    max: 99,
                    height: "75px"
                });
                if ( self.gradientDataColors && self.gradientDataColors.length > 0) {
                    for (const currValue of self.gradientDataColors) {
                        const currValArray = currValue.trim().split(" ")
                        this.myGradientPicker.addHandler(currValArray[1].replace("%",""), currValArray[0], 1);
                    }
                } else {
                    this.myGradientPicker.addHandler(1, "#085078", 1);
                    this.myGradientPicker.addHandler(99, "#85D8CE", 1, { keepSelect: 1 });
                }

                this.myGradientPicker.on("change", function(complete) {
                    const gradientType = self.myGradientPicker.getType()
                    const direction = self.myGradientPicker.getDirection()
                    const colorValues = self.myGradientPicker.getColorValue().split(",")
                    self.gradientDataColors = [];
                    for (const currValue of colorValues) {
                        self.gradientDataColors.push(currValue.trim())
                    }
                    self.selectedGradient = self.myGradientPicker.getValue()
                })
                this.myGradientPicker.emit("change");
            },
            destroyGrapick: function() {
                this.myGradientPicker.destroy();
                this.myGradientPicker = null;
            },
            changeGradientType: function() {
                this.myGradientPicker && this.myGradientPicker.setType(this.gradientType || "linear");
            },
            changeGradientDirection: function() {
                this.myGradientPicker && this.myGradientPicker.setDirection(this.gradientDirection || "right");
            },
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return "
        const self = this
        if (props.label && props.media) {
            self.selectedGradient = props.source !== 'new' ? props.media[props.label].gradient : 'linear-gradient(to right, #085078 1%, #85D8CE 99%)'
            self.gradientType = props.source !== 'new' ? props.media[props.label].options.type : 'linear'
            self.gradientDirection = props.source !== 'new' ? props.media[props.label].options.direction : 'right'
            self.gradientDataColors = props.source !== 'new' ? props.media[props.label].options.colors : ['#085078 1%','#85D8CE 99%']
            
            if (this.myGradientPicker === null) {
               this.createGrapick()
            }
        }
    ";
    }

    protected function renderComponentDismissalScript() : string
    {
        return "
            this.destroyGrapick()
        ";
    }

    protected function renderTemplate() : string
    {
        /** @var App $app */
        global $app;
        return '<div class="formwrapper-control gradient-manager-wrapper">
            <v-style>
                .gradient-manager-wrapper .table td {
                    vertical-align: middle;
                }
            </v-style>
            <div id="gradientPickerWrapper">
                <div class="width50">
                    <div style="margin-top: 15px;padding: 21px 15px;background-image: linear-gradient(to bottom, #242424 1%, #aaa 50%, #242424 99%); border-radius:5px;">
                        <div id="gradientPicker" style="border:3px solid #000"></div>
                    </div>
                    <h2 class="pop-up-dialog-main-title-text mb-2" style="font-size: 23px;margin-top: 30px;">Preview</h2>
                    <div style="border:1px solid #aaa;padding:5px;border-radius: 3px;">
                        <div v-bind:style="renderBackground()" style="width:100%; height:150px;"></div>
                    </div>
                </div>
                <div class="width50 pl-4">
                    <h2 class="pop-up-dialog-main-title-text mb-2">Assign Gradient</h2>
                    <table class="table">
                        <tr>
                            <td>Type:</td>    
                            <td>
                                <select v-model="gradientType" class="form-control" v-on:change="changeGradientType">
                                    <option value="">- Select Type -</option>
                                    <option value="radial">Radial</option>
                                    <option value="linear">Linear</option>
                                    <option value="repeating-radial">Repeating Radial</option>
                                    <option value="repeating-linear">Repeating Linear</option>
                                </select>
                            </td>    
                        </tr>
                        <tr>
                            <td>Direction:</td>    
                            <td>
                                <select v-model="gradientDirection" class="form-control" v-on:change="changeGradientDirection">
                                    <option value="">- Select Direction -</option>
                                    <option value="top">Top</option>
                                    <option value="right">Right</option>
                                    <option value="center">Center</option>
                                    <option value="bottom">Bottom</option>
                                    <option value="left">Left</option>
                                </select>
                            </td>    
                        </tr>
                        <tr>
                            <td>CSS Code:</td>
                            <td><input class="form-control" v-model="selectedGradient"></td>
                        </tr>
                    </table>
                    <p>To use this gradient as your background profile, assign it below.</p>
                    <table class="table">
                        <tr>
                            <td><button class="btn btn-primary w-100" v-on:click="assignGradient">Assign Gradient</button></td>
                        </tr>
                   </table>
                </div>
            </div>
        </div>';
    }
}