<?php

namespace Entities\Directories\Components\Vue\Directorywidget\Helper;

use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\VueProps;
use Entities\Directories\Models\DirectoryModel;

class PackageManagementWidget extends VueComponent
{
    protected string $id = "9030ee29-8b0c-4b48-a2ab-8b8ea5157fef";
    protected string $modalWidth = "750";
    private VueComponent $addPackageWidget;

    public function __construct (array $components = [])
    {
        parent::__construct((new DirectoryModel()), $components);

        $this->addPackageWidget = $this->registerDynamicComponent(
            new AddPackageWidget(),
            "view");

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
            freePackage: {},
            directoryPackageList: [],
        ';
    }

    protected function renderComponentMethods() : string
    {
        return '
            addDirectoryPackage: function()
            {
                const self = this
                modal.EngageFloatShield(function(shield) {
                    let data = {};
                    data.title = "Add  Directory Package"
                    let editComponent = self.getComponent("'.$this->addPackageWidget->getId().'","'.$this->addPackageWidget->getId().'", "main", "add", "Loading...", {}, this.mainEntity, {mainEntity: self.mainEntity, directoryPackageList: self.directoryPackageList, parentWidget: self, action: \'add\'});
                    modal.EngagePopUpDialog(data, 850, 250, true, "default", true, editComponent, self);
                });
            },
            editRegistration: function(package)
            {
                const self = this
                modal.EngageFloatShield(function(shield) {
                    let data = {};
                    data.title = "Edit Directory Package"
                    let editComponent = self.getComponent("'.$this->addPackageWidget->getId().'","'.$this->addPackageWidget->getId().'", "main", "edit", "Loading...", {}, this.mainEntity, {mainEntity: self.mainEntity, package: package, directoryPackageList: self.directoryPackageList, parentWidget: self, action: \'edit\'});
                    modal.EngagePopUpDialog(data, 850, 250, true, "default", true, editComponent, self);
                });
            },
            deleteRegistration: function(package)
            {
                const self = this
                modal.EngageFloatShield(function(shield) {
                    const url = "/api/v1/directories/delete-directory-packages?id=" + package.dir_sys_row_id;
                    ajax.Post(url, {}, function(result) {
                        self.loadPackages(true)
                        modal.CloseFloatShield()
                    }, "GET");
                });
            },
            editFreeRegistration: function()
            {
                const self = this
                ezLog(this.mainEntity, "this.mainEntity")
                const package = {
                    name: (this.mainEntity && this.mainEntity.settings && this.mainEntity.settings.freePackageName) ? this.mainEntity.settings.freePackageName : "Free Registration",
                    description: (this.mainEntity && this.mainEntity.settings && this.mainEntity.settings.freePackageDesc) ? this.mainEntity.settings.freePackageDesc : "No charge to sign up!",
                    status: (this.mainEntity && this.mainEntity.settings && this.mainEntity.settings.freePackageStatus) ?  this.mainEntity.settings.freePackageStatus : "active"
                }
                modal.EngageFloatShield(function(shield) {
                    let data = {};
                    data.title = "Edit Free Directory Package"
                    let editComponent = self.getComponent("'.$this->addPackageWidget->getId().'","'.$this->addPackageWidget->getId().'", "main", "free", "Loading...", {}, this.mainEntity, {mainEntity: self.mainEntity, package: package, parentWidget: self, action: \'free\'});
                    modal.EngagePopUpDialog(data, 850, 250, true, "default", true, editComponent, self);
                });
            },
            loadPackages: function(force)
            {
                if (this.directoryPackageList.length > 0 && !force) return;
                let self = this;
                const url = "/api/v1/directories/get-directory-packages?id=" + this.mainEntity.instance_uuid;
                ajax.Get(url, null, function(result) {
                    self.directoryPackageList = result.response.data.list;
                    self.$forceUpdate();
                }, "GET");
            },
            loadFreePackage: function()
            {
                this.freePackage.free_package_title = "Free Registration";
                this.freePackage.free_package_description = "No charge to sign up!";
                this.freePackage.free_package_status = "active";
                
                if (this.mainEntity.settings) {
                    for (const key in this.mainEntity.settings) {
                        const currSetting = this.mainEntity.settings[key];
                        this.freePackage[currSetting.label] = currSetting.value
                    }
                }
            },
            loadProps: function(props)
            {
                for(let currPropLabel in props)
                {
                    this[currPropLabel] = props[currPropLabel];
                }
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
            this.loadProps(this._props);        
            this.loadPackages();        
            this.loadFreePackage();
        ';
    }

    protected function renderTemplate() : string
    {
        return '<div class="packageManagement">
            <v-style type="text/css">
                
            </v-style>
            <div id="assignConnection" class="swapConnectionItem" v-if="!disabled">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="card pointer">
                            <div class="card-body">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="card-title">{{ freePackage.free_package_title }}</h5>
                                    <small>{{ ucwords(freePackage.free_package_status) }}</small>
                                </div>
                                <p class="card-text">{{ freePackage.free_package_description }}</p>
                                <a href="#" class="btn btn-primary" v-on:click="editFreeRegistration">Edit</a>
                            </div>
                        </div>
                    </div>
                    <div v-for="currPackage in directoryPackageList" class="col-sm-3">
                        <div class="card pointer">
                            <div class="card-body">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="card-title">{{ currPackage.name }}</h5>
                                    <small>{{ ucwords(currPackage.status) }}</small>
                                </div>
                                <p class="card-text">{{ currPackage.description }}</p>
                                <a href="#" class="btn btn-primary" v-on:click="editRegistration(currPackage)">Edit</a>
                                <a href="#" class="btn btn-warning" v-on:click="deleteRegistration(currPackage)">Delete</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card pointer" style="height:100%;background:#ccc;" v-on:click="addDirectoryPackage">
                            <div class="card-body" style="text-align: center;">
                                <p class="card-text" style="height:100%;line-height: 110px;"><i class="fa fa-plus"></i> Add New Package</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }
}