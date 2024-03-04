<?php
?>
{
    name: "editPackageComponent",
    parent: "<?php echo $mainComponentId; ?>",
    dynamicComponents() {
        return {
            dynPackageDataSelector: { id: "packageDataSelector", instanceId: "packageDataSelector", title: "Title Test"}
        }
    },
    data() {
        return {
            switchComponent: false,
            public: false,
            action: "",
            entity: {},
            entities: [],
            userId: "",
            userNum: "",
            marketplaceId: "",
            packageId: "",
            marketplacePackages: [],
            marketplaceColumns: [],
            profileImageUploadUrl: "",
            submitButtonTitle: "",
            dynPackageDataSelector: null,
            dynPackageDataSelectorComponent: null,
        }
    },
    created() {
        this.public = false;
        this.action = "Add";
        this.submitButtonTitle = "";
        this.entity = {};
        this.entities = [];
        this.userId = 0;
        this.marketplaceId = '';
        this.marketplacePackages = [];
        this.packageId = '';
        this.marketplaceColumns = [];
    },
    template: `
<div class="entityMemberDetailsInner">
    <h4 style="margin-top: 4px;"><span class="fas fa-user-circle fas-large desktop-25px"></span> 
        <span class="fas-large" v-if="action === 'edit'" >{{ entity.name }}'s Profile</span>
        <span class="fas-large" v-if="action === 'add'" >Add New Profile</span>
    </h4>
    <div style="background:#ddd;padding: 15px 17px 2px;border-radius:5px;box-shadow:rgba(0,0,0,.2) 0 0 10px inset;margin-top:10px; ">
        <div class="width250px">
            <div class="memberAvatarImage">
                <div class="slim" data-ratio="1:1" data-force-size="650,650" v-bind:data-service="profileImageUploadUrl" id="my-cropper" style="background-image: url(/_ez/images/no-image.jpg); background-size:cover;">
                    <input type="file"/>
                    <img width="250" height="250" alt="">
                </div>
            </div>
        </div>
        <div class="widthAutoTo250px">    
            <table class="table no-top-border">
                <tbody>
                    <tr>
                        <td style="width:100px;vertical-align: middle;">Package Name</td>
                        <td>
                            <input v-model="entity.name" class="form-control" type="text" placeholder="Enter a title..." value="">
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100px;vertical-align: middle;">Description</td>
                        <td>
                            <input v-model="entity.description"class="form-control" type="text" placeholder="Add a description.." value="">
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100px;vertical-align: middle;">Price:</td>
                        <td>
                            <input v-model="entity.regular_price" class="form-control" type="text" placeholder="0.00" value="">
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100px;vertical-align: middle;">Initial Price (1st Time):</td>
                        <td>
                            <input v-model="entity.promo_price" class="form-control" type="text" placeholder="(Optional)" value="">
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100px;vertical-align: middle;">Display Order:</td>
                        <td>
                            <input v-model="entity.order" class="form-control" type="text" placeholder="1, 2, 3, etc." value="">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="clear:both;"></div>
    </div>
    <h4 style="margin-top: 14px;"><span class="fas fa-puzzle-piece fas-large desktop-25px"></span> <span class="fas-large">Custom Fields</span></h4>
    <table id="editMemberComponentCustomFields" class="table no-top-border ajax-loading-anim" style="min-height: 50px;">
        <tbody>
            <tr v-for="currColumn in marketplaceColumns">
                <td style="width:109px;vertical-align: middle;">{{ currColumn.name }}</td>
                <td style="padding: 5px .75rem;">
                    <component :is="dynPackageDataSelectorComponent" :package-column="currColumn"></component>
                </td>
            </tr>
        </tbody>
    </table>
    <button class="buttonID9234597e456 btn btn-primary w-100" @click="savePackage()">{{ submitButtonTitle }}</button>
</div>`,
    methods: 
    {
        hydrateComponent: function(props, show, callback)
        {
            this.entity = _.clone(this.entity);
            this.marketplaceColumns = [];
            this.marketplacePackages = (props !== null) ? props.marketplacePackages : [];
            this.public = (props !== null) ? props.public : false;
            this.submitButtonTitle = this.buildSaveButtonText(this.action);
            this.dynPackageDataSelectorComponent = this.dynPackageDataSelector;

            let self = this;
            let objMyCropper = document.getElementById("my-cropper");
            Slim.destroy(objMyCropper);
            
            this.$forceUpdate();
            
            this.addAjaxClass("editMemberComponentCustomFields");
            this.packageId = (this.entity !== null) ? this.entity.marketplace_package_id : null;
            this.setProfileImageUploadUrl();
            
            this.getMarketplaceId(((props !== null && props.marketplaceId) ? props.marketplaceId : null), function() 
            {
                self.loadComponentData(self.action, self.entity, self.entities, objMyCropper, self.userId, callback);
            });
        },
        setProfileImageUploadUrl: function()
        {
            this.profileImageUploadUrl = '/api/v1/media/upload-image?entity_id=' + this.packageId + '&user_id=' + this.userId + '&entity_name=ezcardMarketplacePackage&class=package-image';
        },
        buildSaveButtonText: function(action)
        {
            switch(action)
            {
                case "add":
                    return "Create New Package";
                case "edit":
                    return "Save Package";
                default:
                    return "Unknown!";
            }
        },
        getParentLinkActions: function()
        {
            return ["add", "edit"];
        },
        getMarketplaceId: function(marketplaceUuid, callback)
        {
            if(!marketplaceUuid || marketplaceUuid === null)
            {
                return marketplaceUuid;
            }
            
            let self = this;
            
            ajax.SendExternal("/module-widget/ezcard/marketplace/v1/get-marketplace-id?id=" + marketplaceUuid, "", "get", "json", true, function(result)
            {
                self.marketplaceId = result.data.id;
                callback(self);
            });
        },
        loadComponentData: function(action, entity, entities, objMyCropper, userId, callback)
        {     
            let cropperChildren = objMyCropper.getElementsByTagName("img");
            
            if( this.packageId !== null && typeof entity.package_image_url === "string" && entity.package_image_url !== "")
            {
                cropperChildren[0].src = entity.package_image_url;
            }
            else
            {
                cropperChildren[0].removeAttribute("src");
            }
            
            const memberLoadUrl = "/module-widget/ezcard/marketplace/v1/get-marketplace-columns?id=" + this.marketplaceId + "&package=" + this.packageId;

            let self = this;
            
            ajax.SendExternal(memberLoadUrl, "", "get", "json", true, function(result)
            {
                self.marketplaceColumns = result.data ? result.data : [] ;
                
                let bodyDialogBox = document.getElementById("editMemberComponentCustomFields");
                bodyDialogBox.classList.remove("ajax-loading-anim");
                
                Slim.create(
                    objMyCropper, 
                    Slim.getOptionsFromAttributes(objMyCropper, {browseButton: false, uploadButton: false, }), 
                    {app: self, method: "updateEntityAvatar"},
                    {app: self, method: "removeEntityAvatar"}
                );

                if (typeof callback === "function")
                {
                    callback(self);
                }
            });
        },
        updateEntityAvatar: function(url)
        {
            let objMyCropper = document.getElementById("my-cropper");
            let cropperChildren = objMyCropper.getElementsByTagName("img");
            
            if (cropperChildren[0]) 
            {
                cropperChildren[0].src = url;
                this.entity.package_image_url = url;
                const postUrl = "/module-widget/ezcard/marketplace/v1/save-marketplace-package-image-url?package=" + this.entity.marketplace_package_id;
                const postData = {package_url: url};

                ezLog(postUrl);
                ezLog(postData);

                ajax.SendExternal(postUrl, postData, "POST", "json", true, function(result)
                {
                    console.log(result);
                });
            }
        },
        removeEntityAvatar: function()
        {
            this.entity.package_image_url  = "__remove__";
        },
        savePackage: function()
        {
            modal.EngageFloatShield();
            let self = this;
            let allMemberData = {package: this.entity, customFields: this.marketplaceColumns};

            let marketplaceId = "new";

            if (self.action === "edit")
            {
                marketplaceId = self.entity.marketplace_package_id;
            }

            const url = "/module-widget/ezcard/marketplace/v1/upsert-marketplace-package-record?marketplaceId=" + self.marketplaceId + "&package=" + marketplaceId;

            ajax.SendExternal(url, JSON.stringify(allMemberData), "form", "json", true, function(result)
            {
                switch(self.action)
                {
                    case "add":
                        self.packageId = result.data.package.marketplace_package_id;
                        self.entity.marketplace_package_id = result.data.package.marketplace_package_id;
                        self.marketplacePackages.push(self.entity);
                        self.setProfileImageUploadUrl();
                        Slim.setUploadUrl(document.getElementById("my-cropper"), self.profileImageUploadUrl);
                        break;
                    default:
                        if (self.entity.package_image_url === "__remove__")
                        {
                            self.entity.package_image_url = "";
                        }

                        if (self.public === true) { break; }
                        break;
                }

                self.reloadMainList();

                Slim.save(document.getElementById("my-cropper"), function()
                {
                    if (self.public !== true)
                    {
                        modal.CloseFloatShield();
                        self.$parent.backToComponent("reloadDirectoryList");

                        self.marketplacePackages.forEach(function (currEntity, currIndex)
                        {
                            if (self.packageId === currEntity.marketplace_package_id)
                            {
                                self.marketplacePackages[currIndex] = self.entity;
                                //console.log(self.marketplacePackages[currIndex]);
                            }
                        });

                        return;
                    }

                    self.displaySuccessModal();
                });
            });
        },
        reloadMainList: function()
        {
            const mainVc = self.findVc(self);
            const mainComponent = mainVc.getComponentByInstanceId(self.parentId)
            const mainList = document.getElementById(mainComponent.instanceId);
            const innerTable = mainList.querySelector(".entityDetailsInnerTable");
            const innerTableHeader = innerTable.querySelector("thead th a.active");
            innerTableHeader.click(); innerTableHeader.click();
        },
        displaySuccessModal: function()
        {
            let data = {};
            data.title = "Success!";
            data.html = "<hr><div style=\"text-align:center;\"><b>Your Member Record Was Update!</b></div><hr>You can close out of this window, or close this dialog and make more edits.";
            modal.EngagePopUpAlert(data, function() {
            modal.CloseFloatShield();
            }, 350, 115, true);
        },
        getModalTitle: function(action)
        {
            switch(action) {
                case "add": return 'Add Marketplace Package';
                case "edit": return 'Edit Marketplace Package';
                case "delete": return 'Delete Marketplace Package';
                case "read": return 'View Marketplace Package';
            }
        },
        addAjaxClass: function(id)
        {
            let bodyDialogBox = document.getElementById(id);
            bodyDialogBox.classList.add("ajax-loading-anim");
        },
        removeAjaxClass: function(id)
        {
            let bodyDialogBox = document.getElementById(id);
            bodyDialogBox.classList.remove("ajax-loading-anim");
        },
        titleCase: function(str) 
        {
            let wordsArray = str.toLowerCase().split(/\s+/);
            
            let upperCased = wordsArray.map(function(word) 
            {
                return word.charAt(0).toUpperCase() + word.substr(1);
            });
            
            return upperCased.join(" ");
        },
    }
}
