<?php

namespace Entities\Directories\Components\Vue\Directorywidget\Helper;

use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cards\Models\CardModel;

class AddPackageWidget extends VueComponent
{
    protected string $id = "afc61082-e412-4a97-916e-33af83888364";
    protected string $modalWidth = "750";
    protected string $mountType = "dynamic";

    public function __construct (array $components = [])
    {
        parent::__construct((new CardModel()), $components);

        $this->modalTitleForAddEntity = "Add Package";
        $this->modalTitleForEditEntity = "Edit Package";
        $this->modalTitleForDeleteEntity = "Delete Package";
        $this->modalTitleForRowEntity = "View Package";
    }

    protected function renderComponentDataAssignments() : string
    {
        return '
            disabled: true,
            testText: "",
            newEntity: {},
            widgetAction: "Create",
            directoryPackageList: [],
            serverError: false,
            errors: {}
        ';
    }

    protected function renderComponentMethods() : string
    {
        return '
            test: function()
            {
            },
            updateEntity: function()
            {
                this.$forceUpdate()
            },
            loadPackage: function(package)
            {
                this.newEntity.title = package.name;
                this.newEntity.description = package.description;
                this.newEntity.cycleType = package.__packageLine.cycle_type;
                this.newEntity.promoPrice = package.promo_price;
                this.newEntity.promoLength = package.__packageLine.promo_price_duration;
                this.newEntity.price = package.regular_price;
                this.newEntity.cycleLength = package.__packageLine.regular_price_duration;
                this.newEntity.directory_package_id = package.directory_package_id;
                this.newEntity.status = package.status;
                
                this.widgetAction = "Update"
            },
            loadFreeEntity: function(mainEntity)
            {
                this.newEntity = {}
                this.package = {}
                this.newEntity.title = "Free Registration";
                this.newEntity.description = "No charge to sign up!";
                this.newEntity.status = "active";
                
                if (mainEntity.settings) {
                    for (const key in mainEntity.settings) {
                        const currSetting = mainEntity.settings[key];
                        this.newEntity[currSetting.label.replace("free_package_", "")] = currSetting.value
                    }
                }
                
                this.widgetAction = "Update"
            },
            loadNewEntity: function()
            {
                this.newEntity = {}
                this.package = {}
                this.newEntity.cycleType = 8
            },
            upsertPackage: function()
            {
                switch(this.action) {
                    case "edit":
                        this.updatePackage();
                        break;
                    case "free":
                        this.updateFreePackage();
                        break;
                    default:
                        this.createPackage();
                        break;
                }
            },
            createPackage: function()
            {
                const self = this;
                const strAuthUrl = "/api/v1/directories/create-new-package?id=" + this.mainEntity.instance_uuid;
                modal.EngageFloatShield();
                ajax.Post(strAuthUrl, this.newEntity, function(result) {
                    modal.CloseFloatShield()
                    if (result.success == false || result.response.success == false) {
                        self.processValidationErrors(result)
                        return
                    }
                    if (result.response && result.response.data && result.response.data.package) {
                        self.parentWidget.directoryPackageList.push(result.response.data.package)
                        self.parentWidget.$forceUpdate()
                    }
                    modal.CloseFloatShield() 
                });
            },
            updatePackage: function()
            {
                const self = this;
                const strAuthUrl = "/api/v1/directories/update-package?id=" + this.mainEntity.instance_uuid;
                modal.EngageFloatShield();
                ajax.Post(strAuthUrl, this.newEntity, function(result) {
                    modal.CloseFloatShield()            
                    if (result.success == false || result.response.success == false) {
                        self.processValidationErrors(result)
                        return
                    }
                    for (let currPackageIndex in self.directoryPackageList) {
                        if (result.response && result.response.data && result.response.data.package && self.parentWidget.directoryPackageList[currPackageIndex].directory_package_id == result.response.data.package.directory_package_id) {
                            self.parentWidget.directoryPackageList[currPackageIndex] = result.response.data.package
                            self.parentWidget.$forceUpdate()
                        }
                    }
                    
                    modal.CloseFloatShield() 
                });
            },
            updateFreePackage: function()
            {
                const self = this;
                const strAuthUrl = "/api/v1/directories/update-free-package?id=" + this.mainEntity.instance_uuid;
                modal.EngageFloatShield();
                const freePackage = {
                    free_package_title: this.newEntity.title,
                    free_package_description: this.newEntity.description,
                    free_package_status: this.newEntity.status,
                }
                ajax.Post(strAuthUrl, freePackage, function(result) {
                    modal.CloseFloatShield()            
                    if (result.success == false || result.response.success == false) {
                        self.processValidationErrors(result)
                        return
                    }
                     
                    self.parentWidget.freePackage.free_package_title = self.newEntity.title
                    self.parentWidget.freePackage.free_package_description = self.newEntity.description
                    self.parentWidget.freePackage.free_package_status = self.newEntity.status
                    
                    for (const key in self.parentWidget.mainEntity.settings) {
                        const currSetting = self.parentWidget.mainEntity.settings[key];
                        self.parentWidget.mainEntity.settings[key].value = self.newEntity[currSetting.label.replace("free_package_", "")]
                    }
                    
                    self.parentWidget.$forceUpdate()
                    
                    modal.CloseFloatShield() 
                });
            },
            processValidationErrors: function(result)
            {
                const self = this;
                if (result.success == false) {
                    this.serverError = true;
                    setTimeout(function() { self.serverError = false }, 2000)
                    return;
                }
                if (result.response.message == "Validation errors.") {
                    errors = result.response.data
                    if (errors.cycleType) {
                        this.errors.cycleType = "You must select a billing cycle type."
                        this.$forceUpdate()
                    }
                    if (errors.price) {
                        this.errors.price = "You must have a default price set."
                        this.$forceUpdate()
                    }
                    if (errors.cycleLength) {
                        this.errors.cycleLength = errors.cycleLength[0]
                        this.$forceUpdate()
                    }
                    if (errors.promoLength) {
                        this.errors.promoLength = "A Promotional value must have a length of 1 or more cycles, but not infinite."
                        this.$forceUpdate()
                    }
                }
            },
            clearError: function(errorType)
            {
                this.errors[errorType] = null
                this.$forceUpdate()
            },
        ';
    }

    protected function renderComponentComputedValues() : string
    {
        return '
            
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return parent::renderComponentHydrationScript() . '
            if (!this.mainEntity) return
            this.disabled = false
            if (this.action === "edit") {
                this.loadPackage(this.package)
            } else if (this.action === "free") {
                this.loadFreeEntity(this.mainEntity)
            } else {
                this.loadNewEntity()
            }
            this.testText = "here!"
        ';
    }

    protected function renderTemplate() : string
    {
        return '<div class="addDirectoryPackage">  
            <v-style type="text/css">
                .addDirectoryPackage .errorValidation {
                    border:2px solid #ff0000;
                    box-shadow: #ff0000 0 0 5px;
                }
                .addDirectoryPackage .box-note {
                    background: rgb(85, 85, 85);color: rgb(255, 255, 255) !important;padding: 12px 19px;margin-left: 12px;margin-right: 12px; border-radius:4px
                }
                .addDirectoryPackage .serverError {
                    background: #cc0000; padding: 10px 15px 15px 15px; color: #fff !important;
                    text-align:center;
                    border-radius:4px;
                }
                .addDirectoryPackage .validationError {
                    background: #cc0000;
                    padding: 5px 10px;
                    color: #fff !important;
                    border-radius: 4px;
                    margin-top: 10px;
                }
            </v-style>
            <div class="addDirectoryPackageInner" v-if="!disabled">
            <div>
                <div v-if="serverError" class="serverError mb-2"><b style="font-size:1.5rem">-- ERROR --</b><br><b>There was an error processing this request.</b> Please wait and try again in a few minutes.</div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">General Information</h5>
                                <div>
                                    <table class="table no-top-border">
                                        <tbody>
                                            <tr>
                                                <td style="width: 125px; vertical-align: middle;">Title</td> 
                                                <td style="width: calc(50% - 125px)">
                                                    <input v-model="newEntity.title" type="text" class="form-control" v-bind:class="{errorValidation: errors.title && errors.title != null}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 125px; vertical-align: middle;">Description</td> 
                                                <td style="width: calc(50% - 125px)">
                                                    <input v-model="newEntity.description" type="text" class="form-control" v-bind:class="{errorValidation: errors.description && errors.description != null}">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="action !== \'free\'" class="row mt-2">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Pricing</h5>
                                <div>
                                    <table class="table no-top-border">
                                        <tbody>
                                            <tr>
                                                <td style="width: 125px; vertical-align: middle;">Billing Cycle</td> 
                                                <td style="width: calc(50% - 125px)" colspan="3">
                                                    <select v-model="newEntity.cycleType" class="form-control" v-on:change="updateEntity && clearError(\'cycleType\')" v-bind:class="{errorValidation: errors.cycleType && errors.cycleType != null}">
                                                        <option value="8">One-Time Purchase</option>
                                                        <option value="3">Weekly</option>
                                                        <option value="4">Bi-Monthly</option>
                                                        <option value="1">Monthly</option>
                                                        <option value="7">Quarterly</option>
                                                        <option value="6">Bi-Annually</option>
                                                        <option value="5">Yearly</option>
                                                    </select>
                                                    <div class="validationError" v-if="errors.cycleType && errors.cycleType != null"> {{ errors.cycleType }}</div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table no-top-border">
                                        <tbody>
                                            <tr v-if="!newEntity.cycleType || newEntity.cycleType == 8">
                                                <td style="width: 125px; vertical-align: middle;">Price</td> 
                                                <td colspan="3">
                                                    <input v-model="newEntity.price" type="number" class="form-control">
                                                    <div class="validationError" v-if="errors.price && errors.price != null"> {{ errors.price }}</div>
                                                </td>
                                            </tr>
                                            <tr v-if="newEntity.cycleType && newEntity.cycleType != 8">
                                                <td style="width: 125px; vertical-align: middle;">Promo Price</td> 
                                                <td style="width: calc(50% - 125px)" v-bind:colspan="newEntity.promoPrice > 0 ? 1 : 3">
                                                    <input v-model="newEntity.promoPrice" type="number" class="form-control" placeholder="No Promo Price" v-on:change="updateEntity">
                                                </td>
                                                <td v-if="newEntity.cycleType && newEntity.cycleType != 8 && newEntity.promoPrice > 0" style="width: 125px; vertical-align: middle;">Promo Count</td> 
                                                <td v-if="newEntity.cycleType && newEntity.cycleType != 8 && newEntity.promoPrice > 0" style="width: calc(50% - 125px)">
                                                    <input type="number" class="form-control" v-model="newEntity.promoLength" placeholder="1 or greater" v-bind:class="{errorValidation: newEntity.promoLength == 0 }" v-on:change="updateEntity && clearError(\'promoLength\')">
                                                    <div class="validationError" v-if="errors.promoLength && errors.promoLength != null" >{{ errors.promoLength }}</div>
                                                </td>
                                            </tr>
                                            <tr v-if="newEntity.cycleType && newEntity.cycleType != 8" >
                                                <td style="width: 125px; vertical-align: middle;">Recurring Price</td> 
                                                <td style="width: calc(50% - 125px)">
                                                    <input v-model="newEntity.price" type="number" class="form-control" placeholder="No Recurring Price" v-on:change="updateEntity && clearError(\'price\')">
                                                    <div class="validationError" v-if="errors.price && errors.price != null">{{ errors.price }}</div>
                                                </td>
                                                <td v-if="(newEntity.price != \'\' && newEntity.price != null) != \'\'"  style="width: 125px; vertical-align: middle;">Recurring Length</td> 
                                                <td v-if="(newEntity.price != \'\' && newEntity.price != null) != \'\'"  style="width: calc(50% - 125px)" v-bind:colspan="(newEntity.cycleLength != \'\' && newEntity.cycleLength != null) ? 1 : 3">
                                                    <input v-model="newEntity.cycleLength" type="number" class="form-control" placeholder="0 is infinite" v-on:change="updateEntity && clearError(\'cycleLength\')">
                                                    <div class="validationError" v-if="errors.cycleLength && errors.cycleLength != null">{{ errors.cycleLength }}</div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <p class="box-note">NOTE: All monetary values are currently in USD.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Availability</h5>
                                <div>
                                    <table class="table no-top-border">
                                        <tbody>
                                            <tr>
                                                <td style="width: 125px; vertical-align: middle;">Status</td> 
                                                <td style="width: calc(50% - 125px)">
                                                    <select v-model="newEntity.status" type="text" class="form-control">
                                                        <option value="active">Active</option> 
                                                        <option value="inactive">Inactive</option> 
                                                    </select>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary w-100" v-on:click="upsertPackage">{{ widgetAction }} New Package</button>
            </div>
            </div>
        </div>';
    }
}