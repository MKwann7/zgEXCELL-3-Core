<?php
?>
{
    name: "manageMarketplace",
    parent: "<?php echo $mainComponentId; ?>",
    data() {
        return {
            entity: {},
            entities: [],
            marketplaceName: "",
            marketplaceSortBy: "first_name",
            marketplaceSortOrder: "asc",
            marketplaceTemplate: "1",
            marketplaceHeaderImage: "",
            marketplaceHeaderText: "",
            marketplaceFooterImage: "",
            marketplaceHexColor: "",
            activeMarketplaceId: "",
            marketplacePackages: [],
        }
    },
    created() {
        this.csvFile = null;
        this.entity = {};
        this.entities = [];
        this.marketplaceColumns = [];
        this.marketplacePackages = [];
    },
    template: `
<div class="entityColumnDirectoryDataInner">
    <div class="divTable">
        <h4 style="margin: 5px 0 10px;">
            <span class="fas fa-cogs fas-large desktop-35px"></span> <span class="fas-large">Manage Directory Profile</span>
        </h4>
    </div>
    <div class="entityDetailsTop">
        <div class="width50" style="padding-right:5px;">
            <input v-model="marketplaceName" class="form-control" placeholder="Directory Name" style="margin-bottom:10px;" />
            <select v-model="marketplaceTemplate"  class="form-control" placeholder="Select Template" style="margin-bottom:10px;">
                <option value="1">Default Directory Template</option>
            </select>
            <select v-model="marketplaceSortBy" class="form-control" placeholder="Sort By Column" style="margin-bottom:10px;">
                <option value="first_name">First Name</option>
                <option value="last_name">Last Name</option>
                <option value="company">Company</option>
                <option value="mobile_phone">Mobile Phone</option>
                <option value="email">Email</option>
            </select>
            <select v-model="marketplaceSortOrder"  class="form-control" placeholder="Sort By Order" style="margin-bottom:10px;">
                <option value="asc">Ascending</option>
                <option value="desc">Descending</option>
            </select>
        </div>
        <div class="width50" style="padding-left:5px;">
            <input v-model="marketplaceHeaderImage" class="form-control" placeholder="Header Image" style="margin-bottom:10px;" />
            <input v-model="marketplaceHeaderText" class="form-control" placeholder="Header Text" style="margin-bottom:10px;" />
            <input v-model="marketplaceFooterImage"  class="form-control" placeholder="Footer Image" style="margin-bottom:10px;" />
            <input v-model="marketplaceHexColor"  class="form-control" placeholder="Hex Color" style="margin-bottom:10px;" />
        </div>
        <div style="clear:both;"></div>
        <button class="buttonID9234597e456 btn btn-primary w-100" @click="saveMarketplace()">Update Marketplace</button>
    </div>
</div>`,
    components: {

    },
    methods:
    {
        hydrateComponent: function(props, show, callback)
        {
            let newMarketplaceId = (props !== null) ? props.marketplaceId : [];

            if (typeof newMarketplaceId === "undefined" || newMarketplaceId === null)
            {
                this.removeAjaxClass();
                this.disableModalLoadingSpinner();
                return;
            }

            if (newMarketplaceId === this.activeMarketplaceId)
            {
                this.removeAjaxClass();
                this.disableModalLoadingSpinner();

                if (typeof callback === "function")
                {
                    callback(this);
                }

                return;
            }

            this.activeMarketplaceId = newMarketplaceId;
            this.marketplaceName = "";
            this.marketplaceSortBy = "first_name";
            this.marketplaceSortOrder = "asc";
            this.marketplaceTemplate = "1";
            this.marketplaceHeaderImage = "";
            this.marketplaceHeaderText = "";
            this.marketplaceFooterImage = "";
            this.marketplaceHexColor = "";
            this.engageModalLoadingSpinner();
            let self = this;

            ezLog(this.activeMarketplaceId,"activeMarketplaceId")
            ezLog("/module-widget/ezcard/marketplace/v1/get-marketplace-data?id=" + this.activeMarketplaceId, "URL")

            ajax.SendExternal("/module-widget/ezcard/marketplace/v1/get-marketplace-data?id=" + this.activeMarketplaceId, "", "get", "json", true, function(result)
            {
                ezLog(result, "result")
                if (!result || result.success === false || !result.data || typeof result.data === "undefined")
                {
                    if (typeof callback === "function")
                    {
                        ezLog("HEREER 0.25");
                        callback(self);
                        self.removeAjaxClass();
                        self.disableModalLoadingSpinner();
                    }
                    return;
                }

                self.marketplaceName = result.data.marketplaceName;
                self.marketplaceSortBy = result.data.marketplaceSortBy;
                self.marketplaceSortOrder = result.data.marketplaceSortOrder;
                self.marketplaceTemplate = result.data.marketplaceTemplate;
                self.marketplaceHeaderImage = result.data.marketplaceHeaderImage;
                self.marketplaceHeaderText = result.data.marketplaceHeaderText;
                self.marketplaceFooterImage = result.data.marketplaceFooterImage;
                self.marketplaceHexColor = result.data.marketplaceHexColor;

                if (typeof callback === "function")
                {
                    callback(self);
                    self.removeAjaxClass();
                    self.disableModalLoadingSpinner();
                }
            });
        },
        getModalTitle: function(action)
        {
            return 'Manage Marketplace';
        },
        getParentLinkActions()
        {
            return ["add", "edit", "delete", "read"];
        },
        saveMarketplace()
        {
            const directoryData = {
                marketplaceName: this.marketplaceName,
                marketplaceSortBy: this.marketplaceSortBy,
                marketplaceSortOrder: this.marketplaceSortOrder,
                marketplaceTemplate: this.marketplaceTemplate,
                marketplaceHeaderImage: this.marketplaceHeaderImage,
                marketplaceHeaderText: this.marketplaceHeaderText,
                marketplaceFooterImage: this.marketplaceFooterImage,
                marketplacedirectoryHexColor: this.marketplaceHexColor,
            };

            const url = "/module-widget/ezcard/marketplace/v1/save-marketplace-data?id=" + this.activeMarketplaceId;
            modal.EngageFloatShield();

            ajax.SendExternal(url, directoryData, "POST", "json", true, function(result)
            {
                modal.CloseFloatShield();
            });
        },
        removeAjaxClass: function()
        {
            let bodyDialogBox = document.getElementsByClassName("entityColumnDirectoryDataInner");
            bodyDialogBox[0].classList.remove("ajax-loading-anim");
        },
    }
}