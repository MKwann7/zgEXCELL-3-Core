<?php
?>
{
    name: "manageDirectory",
    mountType: "dynamic",
    parent: "<?php echo $mainComponentId; ?>",
    data() {
        return {
            entity: {},
            entities: [],
            directoryName: "",
            directorySortBy: "first_name",
            directorySortOrder: "asc",
            directoryTemplate: "1",
            directoryHeaderImage: "",
            directoryHeaderText: "",
            directoryFooterImage: "",
            directoryHexColor: "",
            activeDirectoryId: "",
            directoryMembers: [],
        }
    },
    created() {
        this.csvFile = null;
        this.entity = {};
        this.entities = [];
        this.directoryColumns = [];
        this.directoryMembers = [];
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
            <input v-model="directoryName" class="form-control" placeholder="Directory Name" style="margin-bottom:10px;" />
            <select v-model="directoryTemplate"  class="form-control" placeholder="Select Template" style="margin-bottom:10px;">
                <option value="1">Default Directory Template</option>
                <option value="3">Registration Template</option>
            </select>
            <select v-model="directorySortBy" class="form-control" placeholder="Sort By Column" style="margin-bottom:10px;">
                <option value="first_name">First Name</option>
                <option value="last_name">Last Name</option>
                <option value="company">Company</option>
                <option value="mobile_phone">Mobile Phone</option>
                <option value="email">Email</option>
            </select>
            <select v-model="directorySortOrder"  class="form-control" placeholder="Sort By Order" style="margin-bottom:10px;">
                <option value="asc">Ascending</option>
                <option value="desc">Descending</option>
            </select>
        </div>
        <div class="width50" style="padding-left:5px;">
            <input v-model="directoryHeaderImage" class="form-control" placeholder="Header Image" style="margin-bottom:10px;" />
            <input v-model="directoryHeaderText" class="form-control" placeholder="Header Text" style="margin-bottom:10px;" />
            <input v-model="directoryFooterImage"  class="form-control" placeholder="Footer Image" style="margin-bottom:10px;" />
            <input v-model="directoryHexColor"  class="form-control" placeholder="Hex Color" style="margin-bottom:10px;" />
        </div>
        <div style="clear:both;"></div>
        <button class="buttonID9234597e456 btn btn-primary w-100" @click="saveDirectory()">Update Directory</button>
    </div>
</div>`,
    components: {

    },
    methods:
    {
        hydrateComponent: function(props, show, callback)
        {
            let newDirectoryId = (props !== null) ? props.directoryId : [];

            if (newDirectoryId === this.activeDirectoryId)
            {
                this.removeAjaxClass();
                this.disableModalLoadingSpinner();

                if (typeof callback === "function")
                {
                    callback(this);
                }

                return;
            }

            this.activeDirectoryId = newDirectoryId;
            this.directoryName = "";
            this.directorySortBy = "first_name";
            this.directorySortOrder = "asc";
            this.directoryTemplate = "1";
            this.directoryHeaderImage = "";
            this.directoryHeaderText = "";
            this.directoryFooterImage = "";
            this.directoryHexColor = "";
            this.engageModalLoadingSpinner();
            let self = this;

            ajax.GetExternal("/api/v1/directories/public-full-page/get-directory-data?id=" + this.activeDirectoryId, "", "json", true, function(result)
            {
                if (!result || result.success === false || !result.data || typeof result.data === "undefined")
                {
                    if (typeof callback === "function")
                    {
                        callback(self);
                        self.removeAjaxClass();
                        self.disableModalLoadingSpinner();
                    }
                    return;
                }

                console.log(result.data);

                self.directoryName = result.data.directoryName;
                self.directorySortBy = result.data.directorySortBy;
                self.directorySortOrder = result.data.directorySortOrder;
                self.directoryTemplate = result.data.directoryTemplate;
                self.directoryHeaderImage = result.data.directoryHeaderImage;
                self.directoryHeaderText = result.data.directoryHeaderText;
                self.directoryFooterImage = result.data.directoryFooterImage;
                self.directoryHexColor = result.data.directoryHexColor;

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
            return 'Manage Directory';
        },
        getParentLinkActions()
        {
            return ["add", "edit", "delete", "read"];
        },
        saveDirectory()
        {
            const directoryData = {
                directoryName: this.directoryName,
                directorySortBy: this.directorySortBy,
                directorySortOrder: this.directorySortOrder,
                directoryTemplate: this.directoryTemplate,
                directoryHeaderImage: this.directoryHeaderImage,
                directoryHeaderText: this.directoryHeaderText,
                directoryFooterImage: this.directoryFooterImage,
                directoryHexColor: this.directoryHexColor,
            };

            const url = "/api/v1/directories/public-full-page/save-directory-data?id=" + this.activeDirectoryId;
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