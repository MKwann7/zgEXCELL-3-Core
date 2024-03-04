<?php

use App\Website\Vue\Classes\Base\VueCustomMethods;

?>
{
    id: "<?php echo $mainComponentId; ?>",
    name: "<?php echo $mainComponentName; ?>",
    modalWidth: 1200,
    mountType: "dynamic",
    data() {
        return {
            entity: {},
            entities: [],
            userId: "",
            userNum: "",
            users: null,
            directoryTitle: "",
            directoryId: "",
            directoryMembers: [],
            directoryPackages: [],
            mainEntityColumns: ["status", "avatar", "first_name", "last_name", "mobile_phone", "email"],
            searchMainQuery: "" ,
            orderKey: "first_name",
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
        this.directoryTitle = "";
        this.directoryId = '';
        this.directoryMembers = [];
        this.directoryPackages = [];
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

            let objSortedPeople = this.sortedEntity(this.searchMainQuery, this.directoryMembers, this.orderKey, this.sortByType, this.mainEntityPageIndex,  this.mainEntityPageDisplayCount, this.mainEntityPageTotal, function(data) {
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
                    <span class="fas fa-info-circle fas-large desktop-25px"></span> <span class="fas-large">Directory Profile</span>
                </h4>
            </div>
            <div class="entityDetailsTop">
                <div class="width75">
                    <input v-model="directoryTitle" class="form-control" placeholder="Widget Title" style="margin-bottom: 10px;float: left;" v-bind:style="renderTitleWidth()"/>
                    <select v-if="source === \'widgetEditor\'" v-model="ownerId" class="form-control" placeholder="Select User" style="float: left;width: 35%;margin-left: 15px;">
                        <option>-- Select a User --</option>
                        <option v-if="users" v-for="user in users" v-bind:value="user.user_id">
                            {{ user.user_id }} | {{ user.first_name }} {{ user.last_name }}
                        </option>
                    </select>
                </div>
                <div class="width25">
                    <div style="position: absolute;display: inline-block;">
                        <div v-on:click="updateDirectoryPageData()" class="pointer manageDataButton fas fa-save" style="background-color: #1e88e5; position: relative; right: -8px; top: 4px; width: 29px; height: 29px; font-size: 18px;"></div>
                    </div>
                    <div style="float:right; display: inline-block;">
                        <div v-on:click="uploadMemberRecordCsv()" style="position: relative;right: -4px;top: 4px;width: 29px;height: 29px;display: inline-block;background: url('/website/images/csv.black.png') 0% 0% / 100% no-repeat;cursor: pointer;"></div>
                        <div v-on:click="manageDirectory()" class="pointer manageDataButton fas fa-cog" style="background-color: #19a93e; position: relative; right: -4px; top: -3px; width: 29px; height: 29px; font-size: 19px;"></div>
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
                                    <span class="fas fa-list fas-large desktop-25px"></span> <span class="fas-large">Member List</span>
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
                        <tr v-for="member in orderedMainEntityList" v-on:dblclick="editMember(member)" class="pointer">
                            <td style="width:15px;">
                                <label class="switch-small">
                                    <input name="visibility" type="checkbox" v-model="member.status" true-value="active" false-value="disabled" v-on:click="updateRecordVisibility(member)" >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td>
                                <img v-show="member.profile_image_url" v-bind:src="member.profile_image_url" width="20" height="20" @error="imgError(member)" />
                                <img v-show="!member.profile_image_url" src="/_ez/images/users/defaultAvatar.jpg" width="20" height="20" />
                            </td>
                            <td>{{ member.first_name }}</td>
                            <td>{{ member.last_name }}</td>
                            <td>{{ member.mobile_phone }}</td>
                            <td>{{ member.email }}</td>

                            <td class="text-right">
                                <span v-on:click="emailMember(member)" class="pointer emailEntityButton"></span>
                                <span v-on:click="editMember(member)" class="pointer editEntityButton"></span>
                                <span v-on:click="deleteMember(member)" class="pointer deleteEntityButton"></span>
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

            let self = this

            ezLog(this.entity,"hydrateDirectories")

            self.directoryMembers = []
            self.directoryPackages = []
            self.addAjaxClass()
            self.ownerId = self.entity.user_id
            self.loadProps(props)
            self.directoryId = self.loadDirectoryId()
            self.engageModalLoadingSpinner()
            self.loadUsers()
            self.directoryTitle = self.entity.title
            self.$forceUpdate()

            ajax.GetExternal("/api/v1/directories/public-full-page/get-directory-data?id=" + self.directoryId, "", true, function(result) {
                if (!result || result.success === false || !result.data || typeof result.data === "undefined")
                {
                    if (typeof callback === "function") {
                        callback(self);
                        self.removeAjaxClass();
                        self.disableModalLoadingSpinner();
                    }
                    return;
                }

                for(let currMember of result.data.personas) {
                    self.directoryMembers.push(currMember)
                }

                for(let currMember of result.data.packages) {
                    self.directoryPackages.push(currMember)
                }

                self.reloadDirectoryList();

                if (typeof callback === "function") {
                    callback(self);
                    self.removeAjaxClass();
                    self.disableModalLoadingSpinner();
                }
            });
        },
        loadDirectoryId: function()
        {
            if (typeof this.entity.__app !== "undefined" && this.entity.__app.instance_uuid !== null)
            {
                return this.entity.__app.instance_uuid;
            }

            return this.directoryId;
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
            ajax.GetExternal("/api/v1/directories/public-full-page/get-ezdigital-users?id=" + this.directoryId, {}, true, function (objResult) {
                if (objResult.success !== true) { return; }
                self.users = objResult.data;
            });
        },
        reloadDirectoryList: function()
        {
            let self = this;
            this.directoryMembers = this.directoryMembers;
            this.$forceUpdate();
            setTimeout(function() {
                self.directoryMembers = self.directoryMembers;
                self.$forceUpdate();
            }, 2000);
        },
        addMember: function()
        {
            this.loadComponent("editMemberComponent","editMemberComponent", "main", "add", "Loading...", {}, this.directoryMembers, {directoryId: this.directoryId, directoryMembers: this.directoryMembers}, true);
            this.$forceUpdate();
        },
        emailMember: function(member)
        {
            this.loadComponent("emailMemberComponent","emailMemberComponent", "main", "edit", "Loading...", member, this.directoryMembers, {directoryId: this.directoryId, directoryMembers: this.directoryMembers}, true);
            this.$forceUpdate();
        },
        editMember: function(member)
        {
            this.loadComponent("editMemberComponent","editMemberComponent", "main", "edit", "Loading...", member, this.directoryMembers, {directoryId: this.directoryId, directoryMembers: this.directoryMembers}, true);
            this.$forceUpdate();
        },
        uploadMemberRecordCsv: function()
        {
            this.loadComponent("uploadMemberRecordComponent","uploadMemberRecordComponent", "main", "edit", "Loading...", {}, this.directoryMembers, {directoryId: this.directoryId, directoryMembers: this.directoryMembers}, true);
            this.$forceUpdate();
        },
        manageDirectory: function()
        {
            this.loadComponent("manageDirectory","manageDirectory", "main", "edit", "Loading...", {}, this.directoryMembers, {directoryId: this.directoryId}, true);
            this.$forceUpdate();
        },
        updateRecordVisibility: function(member)
        {
            window.setTimeout(function () {
                let recordId = member.member_directory_record_id;
                let status = member.status;
                ajax.PostExternal("/api/v1/directories/public-full-page/update-member-visibility?id=" + recordId + "&&status=" + status, "", "post", "json", true, function (objResult) {
                });
            },500);
        },
        updateDirectoryPageData: function()
        {
            const directory = {title: this.directoryTitle, ownerId: this.ownerId};
            let self = this;
            modal.EngageFloatShield();
            ajax.PostExternal("/api/v1/directories/public-full-page/update-directory-page-data?id=" + this.directoryId, directory, "post", "json", true, function (objResult) {
                self.entity.title = self.directoryTitle;
                self.entity.user_id = self.ownerId;
                modal.CloseFloatShield();
            });
        },
        deleteMember: function(member)
        {
            let self = this;
            modal.EngageFloatShield();
            let data = {title: "Remove Directory Member?", html: "Are you sure you want to proceed?<br>Please confirm."};

            modal.EngagePopUpConfirmation(data, function() {
                modal.EngageFloatShield();
                ajax.PostExternal("/api/v1/directories/public-full-page/delete-directory-record?member=" + member.member_directory_record_id, "", "post", "json", true, function(result)
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

                    self.directoryMembers = self.directoryMembers.filter(function (currEntity) {
                        return member.member_directory_record_id != currEntity.member_directory_record_id;
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
                case "add": return 'Add Member Directory';
                case "edit": return 'Edit Member Directory';
                case "delete": return 'Delete Member Directory';
                case "read": return 'View Member Directory';
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
            this.directoryMembers = this.directoryMembers;
            this.$forceUpdate();
        },
        prevMainEntityPage: function()
        {
            this.mainEntityPageIndex--;
            this.directoryMembers = this.directoryMembers;
            this.$forceUpdate();
        },

        nextMainEntityPage: function()
        {
            this.mainEntityPageIndex++;
            this.directoryMembers = this.directoryMembers;
            this.$forceUpdate();
        },
        imgError: function (member) {
            member.profile_image_url = "";
        },
        renderTitleWidth: function()
        {
            if (this.source === 'widgetEditor') { return "width: calc(65% - 15px);"; }
            return "width: calc(100% - 15px);";
        },
        <?php echo VueCustomMethods::renderSortMethods(); ?>
    }
}