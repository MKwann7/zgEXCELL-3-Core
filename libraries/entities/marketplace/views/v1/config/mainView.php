<?php

use App\Website\Vue\Classes\Base\VueCustomMethods;

?>
{
    id: "<?php echo $mainComponentId; ?>",
    name: "<?php echo $mainComponentName; ?>",
    modalWidth: 1200,
    data() {
        return {
            entity: {},
            entities: [],
            userId: "",
            userNum: "",
            users: null,
            marketplaceTitle: "",
            marketplaceId: "",
            marketplacePackages: [],
            mainEntityColumns: ["status", "order", "image", "name", "regular_price", "promo_price"],
            searchMainQuery: "" ,
            orderKey: "order",
            sortByType: true,
            source: "widgetEditor",
            mainEntityPageDisplayCount: 15,
            mainEntityPageTotal: 1,
            mainEntityPageIndex: 1,
        }
    },
    created() {
        this.entity = {};
        this.entities = [];
        this.users = null;
        this.userId = 0;
        this.ownerId = "";
        this.marketplaceTitle = "";
        this.marketplaceId = '';
        this.marketplacePackages = [];
    },
    computed:
    {
        totalMainEntityPages: function()
        {
            return this.mainEntityPageTotal;
        },
        orderedMainEntityList: function()
        {
            var self = this;

            let objSortedPeople = this.sortedEntity(this.searchMainQuery, this.marketplacePackages, this.orderKey, this.sortByType, this.mainEntityPageIndex,  this.mainEntityPageDisplayCount, this.mainEntityPageTotal, function(data) {
                self.mainEntityPageTotal = data.pageTotal;
                self.mainEntityPageIndex = data.pageIndex;
            });

            return objSortedPeople;
        },
    },
    filters: {
        ucWords: function(str) {
            return str.replace("_"," ").replace(/\w\S*/g, function (txt) {
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            });
        },
    },
    template: `
        <div class="entityDetailsInner entityListActionColumn">
            <div class="entityDetailsProfilTitle">
                <h4 style="margin: 5px 0 10px;">
                    <span class="fas fa-info-circle fas-large desktop-25px"></span> <span class="fas-large">Marketplace Profile</span>
                </h4>
            </div>
            <div class="entityDetailsTop">
                <div class="width75">
                    <input v-model="marketplaceTitle" class="form-control" placeholder="Widget Title" style="margin-bottom: 10px;float: left;" v-bind:style="renderTitleWidth()"/>
                    <select v-if="source === \'widgetEditor\'" v-model="ownerId" class="form-control" placeholder="Select User" style="float: left;width: 35%;margin-left: 15px;">
                        <option>-- Select a User --</option>
                        <option v-if="users" v-for="user in users" v-bind:value="user.user_id">
                            {{ user.user_id }} | {{ user.first_name }} {{ user.last_name }}
                        </option>
                    </select>
                </div>
                <div class="width25">
                    <div style="position: absolute;display: inline-block;">
                        <div v-on:click="updateMarketplacePageData()" class="pointer manageDataButton fas fa-save" style="background-color: #1e88e5; position: relative; right: -8px; top: 4px; width: 29px; height: 29px; font-size: 18px;"></div>
                    </div>
                    <div style="float:right; display: inline-block;">
                        <div v-on:click="manageMarketplace()" class="pointer manageDataButton fas fa-cog" style="background-color: #19a93e; position: relative; right: -4px; top: -3px; width: 29px; height: 29px; font-size: 19px;"></div>
                    </div>
                </div>
                <div style="clear:both;"></div>
            </div>
            <div class="entityDetailsListTitle">
                <table class="table header-table" style="margin-bottom:0px;">
                    <tbody>
                        <tr>
                            <td>
                                <h4 style="margin: 10px 0;">
                                    <span class="fas fa-list fas-large desktop-25px"></span> <span class="fas-large">Package List</span>
                                    <span v-on:click="addMember()" class="pointer addNewEntityButton entityButtonFixInTitle"></span>
                                    <div class="form-search-box">
                                        <input v-model="searchMainQuery" type="text" placeholder="Search..." class="form-control">
                                    </div>
                                </h4>
                            </td>
                            <td class="text-right page-count-display" style="vertical-align: middle;">
                                <span class="page-count-display-data" style="top: 4px;position: relative;">
                                    Current: <span>{{ mainEntityPageIndex }}</span>
                                    Pages: <span>{{ totalMainEntityPages }}</span>
                                </span>
                                <button v-on:click="prevMainEntityPage()" class="btn prev-btn" :disabled="mainEntityPageIndex == 1">Prev</button>
                                <button v-on:click="nextMainEntityPage()" class="btn" :disabled="mainEntityPageIndex == totalMainEntityPages">Next</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="entityDetailsList" style="overflow-y: scroll;max-height: calc(100vh - 300px);">
                <table class="entityDetailsInnerTable table table-striped no-top-border table-shadow ajax-loading-anim" v-cloak>
                    <thead>
                        <th v-for="mainEntityColumn in mainEntityColumns">
                            <a v-on:click="orderByColumn(mainEntityColumn)" v-bind:class="{ active : orderKey == mainEntityColumn, sortasc : sortByType == true, sortdesc : sortByType == false }">
                                {{ mainEntityColumn | ucWords }}
                            </a>
                        </th>
                        <th class="text-right">
                            Actions
                        </th>
                    </thead>
                    <tbody>
                        <tr v-for="package, index in orderedMainEntityList" v-on:dblclick="editPackage(package)" class="pointer">
                            <td style="width:15px;">
                                <label class="switch-small">
                                    <input name="visibility" type="checkbox" v-model="package.status" true-value="active" false-value="disabled" v-on:click="updateRecordVisibility(package)" >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td>{{ renderOrderValue(package, index) }}</td>
                            <td>
                                <img v-show="package.package_image_url" v-bind:src="package.package_image_url" width="20" height="20" @error="imgError(package)" />
                                <img v-show="!package.package_image_url" src="/_ez/images/users/defaultAvatar.jpg" width="20" height="20" />
                            </td>
                            <td>{{ package.name }}</td>
                            <td>{{ package.regular_price }}</td>
                            <td>{{ package.promo_price }}</td>

                            <td class="text-right">
                                <span v-on:click="editPackage(package)" class="pointer editEntityButton"></span>
                                <span v-on:click="deletePackage(package)" class="pointer deleteEntityButton"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        `,
    methods:
    {
        hydrateComponent: function(props, show, callback)
        {
            this.marketplacePackages = []
            this.addAjaxClass()
            this.ownerId = this.entity.user_id
            this.loadProps(props)
            this.marketplaceId = this.loadMarketplaceId()
            let self = this
            this.engageModalLoadingSpinner()
            this.loadUsers()

            this.marketplaceTitle = this.entity.title

            this.$forceUpdate()

            ajax.SendExternal("/module-widget/ezcard/marketplace/v1/get-marketplace-packages?id=" + this.marketplaceId, "", "get", "json", true, function(result)
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

                for(let currMember of result.data)
                {
                    self.marketplacePackages.push(currMember)
                }

                self.reloadDirectoryList();

                if (typeof callback === "function")
                {
                    callback(self);
                    self.removeAjaxClass();
                    self.disableModalLoadingSpinner();
                }
            });
        },
        renderOrderValue: function(package, index)
        {
            return package.order ? package.order : (index+1)
        },
        loadMarketplaceId: function()
        {
            if (typeof this.entity.__app !== "undefined" && this.entity.__app.instance_uuid !== null)
            {
                return this.entity.__app.instance_uuid;
            }

            return this.marketplaceId;
        },
        loadProps: function(props)
        {
            for(let currPropLabel in props)
            {
                this[currPropLabel] = props[currPropLabel];
            }
        },
        loadUsers: function()
        {
            if (this.users !== null) { return; }
            let self = this;
            ajax.SendExternal("/module-widget/ezcard/marketplace/v1/get-ezdigital-users?id=" + this.marketplaceId, {}, "get", "json", true, function (objResult) {
                if (objResult.success !== true) { return; }
                self.users = objResult.data;
            });
        },
        reloadDirectoryList: function()
        {
            let self = this;
            this.marketplacePackages = this.marketplacePackages;
            this.$forceUpdate();
            setTimeout(function() {
                self.marketplacePackages = self.marketplacePackages;
                self.$forceUpdate();
            }, 2000);
        },
        addMember: function()
        {
            this.loadComponent("editPackageComponent","editPackageComponent", "main", "add", "Loading...", {}, this.marketplacePackages, {marketplaceId: this.marketplaceId, marketplacePackages: this.marketplacePackages}, true);
            this.$forceUpdate();
        },
        editPackage: function(member)
        {
            this.loadComponent("editPackageComponent","editPackageComponent", "main", "edit", "Loading...", member, this.marketplacePackages, {marketplaceId: this.marketplaceId, marketplacePackages: this.marketplacePackages}, true);
            this.$forceUpdate();
        },
        manageMarketplace: function()
        {
            this.loadComponent("manageMarketplace","manageMarketplace", "main", "edit", "Loading...", {}, this.marketplacePackages, {marketplaceId: this.marketplaceId}, true);
            this.$forceUpdate();
        },
        updateRecordVisibility: function(member)
        {
            window.setTimeout(function () {
                let recordId = member.marketplace_package_id;
                let status = member.status;
                ajax.SendExternal("/module-widget/ezcard/marketplace/v1/update-package-visibility?id=" + recordId + "&status=" + status, "", "post", "json", true, function (objResult) {
                });
            },500);
        },
        updateMarketplacePageData: function()
        {
            const directory = {title: this.marketplaceTitle, ownerId: this.ownerId};
            let self = this;
            modal.EngageFloatShield();
            ajax.SendExternal("/module-widget/ezcard/marketplace/v1/update-marketplace-page-data?id=" + this.marketplaceId, directory, "post", "json", true, function (objResult) {
                self.entity.title = self.marketplaceTitle;
                self.entity.user_id = self.ownerId;
                modal.CloseFloatShield();
            });
        },
        deletePackage: function(member)
        {
            let self = this;
            modal.EngageFloatShield();
            let data = {title: "Remove Directory Member?", html: "Are you sure you want to proceed?<br>Please confirm."};

            modal.EngagePopUpConfirmation(data, function() {
                modal.EngageFloatShield();
                ajax.SendExternal("/module-widget/ezcard/marketplace/v1/delete-marketplace-package?member=" + member.marketplace_package_id, "", "post", "json", true, function(result)
                {
                    if (result.success !== true)
                    {
                        modal.CloseFloatShield(function() {
                            modal.EngageFloatShield();
                            let alertData = {title: "Drat. Something Went Wrong!", html: "We've recorded it and our developers will look into it soon.<hr/><i>Please contact customer service to see if we can resolve your deletion request on our end.</i>"};
                            modal.EngagePopUpAlert(alertData, function() {
                                modal.CloseFloatShield(function() { modal.CloseFloatShield(); });
                            }, 500, 115, true);
                        },500);
                        return;
                    }

                    self.marketplacePackages = self.marketplacePackages.filter(function (currEntity) {
                        return member.marketplace_package_id != currEntity.marketplace_package_id;
                    });

                    self.$forceUpdate();
                    modal.CloseFloatShield(function() {
                        modal.CloseFloatShield();
                    },500);
                });
            }, 400, 115);
        },
        getModalTitle: function(action)
        {
            switch(action) {
                case "add": return 'Add Marketplace';
                case "edit": return 'Edit Marketplace';
                case "delete": return 'Delete Marketplace';
                case "read": return 'View Marketplace';
            }
        },
        showComponent: function(action)
        {
            this.$forceUpdate();
        },
        addAjaxClass: function()
        {
            let bodyDialogBox = document.getElementsByClassName("entityDetailsInnerTable");
            bodyDialogBox[0].classList.add("ajax-loading-anim");
        },
        removeAjaxClass: function()
        {
            let bodyDialogBox = document.getElementsByClassName("entityDetailsInnerTable");
            bodyDialogBox[0].classList.remove("ajax-loading-anim");
        },
        orderByColumn: function(column)
        {
            this.sortByType = ( this.orderKey == column ) ? ! this.sortByType : this.sortByType;
            this.orderKey = column;
            this.marketplacePackages = this.marketplacePackages;
            this.$forceUpdate();
        },
        prevMainEntityPage: function()
        {
            this.mainEntityPageIndex--;
            this.marketplacePackages = this.marketplacePackages;
            this.$forceUpdate();
        },

        nextMainEntityPage: function()
        {
            this.mainEntityPageIndex++;
            this.marketplacePackages = this.marketplacePackages;
            this.$forceUpdate();
        },
        imgError: function (member) {
            member.package_image_url = "";
        },
        renderTitleWidth: function()
        {
            if (this.source === 'widgetEditor') { return "width: calc(65% - 15px);"; }
            return "width: calc(100% - 15px);";
        },
        <?php echo VueCustomMethods::renderSortMethods(); ?>
    }
}