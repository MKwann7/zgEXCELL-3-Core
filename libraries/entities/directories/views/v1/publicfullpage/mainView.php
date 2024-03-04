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
            page: null,
            app: null,
            settings: {},
            settingsOriginal: {},
            entity: {},
            entities: [],
            userId: "",
            userNum: "",
            user: null,
            directoryTitle: "",
            directoryId: "",
            directoryMembers:  [],
            directoryPackages: [],
            hideJoinButton: true,
            mainEntityColumns: ["status", "avatar", "first_name", "last_name", "mobile_phone", "email"],
            searchMainQuery: "" ,
            orderKey: "first_name",
            sortByType: true,
            source: "widgetEditor",
            mainEntityPageDisplayCount: 15,
            mainEntityPageTotal: 1,
            mainEntityPageIndex: 1,
            editorMenus: [{name: "Defaults", icon: "fa fa-check"}, {name:"Themes", icon: "fa fa-file"}, {name:"Packages", icon: "fa fa-shopping-basket"},  {name:"Options", icon: "fas fa-eye"}, {name:"Config", icon: "fa fa-cog"}],
            themes: [{name: "Default"}, {name:"Excell"}, {name:"Galaxybiz"}, {name:"ConsultPro"}],
            activeEditorMenu: "Themes"
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
        this.directorySettings = [];
        this.hideJoinButton = true;
        this.showSelfPending = false;
        this.isInEditor = false;
    },
    mounted: function()
    {
        dispatch.register("reload_directory_main_view", this, "reloadDirectoryMainView");
        dispatch.register("user_auth", this, "setUserAuth")
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
        generateThemeClass: function() {
            let classTheme = {};
            const dmrc = this.settings.desk_max_row_count;
            let activeThemeName = this.activeTheme ? this.activeTheme.name : this.themes[0].name;
            classTheme[activeThemeName + "Theme"] = true;
            classTheme["themeWidth" + (dmrc ? dmrc : 3)] = true;
            return classTheme;
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
            <v-style type="text/css">
                .theme_shade_light .app-widget-content-inner ul.nav-buttons  li {
                    background: #cccccc;
                    border-radius:5px 5px 0 0;
                    margin: 0 3px;
                }
                .theme_shade_light .app-widget-content-inner ul.nav-buttons  li span {
                    color: #000 !important;
                }
                .theme_shade_light .editorMenuWrapper .nav-buttons li:not(.active) {
                    background: #efefef !important;
                    color: #555 !important;
                }
                .editorMenuWrapper {
                    display: flex;
                    flex-direction: column;
                    text-align: center;
                    margin-bottom:15px;
                }
                .editorMenuBody {
                    background: #cccccc;
                    border-radius: 0 0 5px 5px;
                    padding:20px;
                }
                .mainWidgetTitle {
                    text-align:center;
                    width: 100%;
                }
                .configLabel {
                    font-size: 1.4rem;
                    text-align:left;
                    position:relative;
                }
                .lightgrey.configLabel span {
                    background: #cccccc;
                }
                .configLabel span {
                    background: #fff;
                    position: relative;
                    padding-right: 10px;
                }
                .configLabel:before {
                    content: " ";
                    border-bottom: 1px dashed #555;
                    position: absolute;
                    top: 13px;
                    left: 0;
                    right: 0;
                    width: 100%;
                    bottom: 0;
                    display: block;
                    height: 0;
                }
                .app-widget-content-inner .displayDirectoryInner {
                    justify-content: center;
                }
                .displayDirectoryItemBox {
                    width: 100%;
                    height: 100%;
                    position:relative;
                }
                .displayDirectoryItem {
                    max-width: 48%;
                    aspect-ratio: 1 / 1;
                }
                .themeWidth2 .displayDirectoryItem {
                    max-width: 48%;
                }
                .themeWidth3 .displayDirectoryItem {
                    max-width: 31%;
                }
                .displayDirectoryItem .displayDirectoryAvatar {
                    width: 100%;
                    height: 100%;
                    aspect-ratio: 1 / 1;
                }
                .displayDirectoryItem .displayDirectoryAvatarPending {
                    position:absolute;
                    top: 0;
                    left:0;
                    right:0;
                    bottom:0;
                    width: 100%;
                    height: 100%;
                    color: white;
                    font-size: 2rem;
                    font-weight: bold;
                    margin-top: 40%;
                    text-shadow: #000 2px 2px 15px;
                }
                .themeWidth4 .displayDirectoryItem {
                    max-width: 24%;
                }
                .app-widget-content-inner .directoryMemberInfo {
                    position:absolute;
                    bottom:0;
                    left: 0;
                    right: 0;
                    height: 0;
                    transition: all .3s ease-in-out;
                    overflow-y: hidden;
                }
                .app-widget-content-inner .displayDirectoryItem:hover .directoryMemberInfo {
                    height: 140px;
                }
                .app-widget-content-inner .member-desc {
                    width: 75%;
                    background: #fff;
                    margin: auto;
                    padding: 15px 0 20px;
                    min-height: 250px;
                }
                .app-widget-content-inner h3.team-name {
                    margin:0 !important;
                }
                .displayDirectoryItem .social-links {
                    margin:auto;
                }
                .zgpopup-dialog-box.dialog-theme-persona {
                    background: #ffffff;
                    border-radius: 0px;
                    box-shadow: 0 0 10px #000000;
                    max-width: calc(100vw - 50px);
                    min-width: 250px;
                }
                .zgpopup-dialog-header.dialog-theme-persona {
                    background: #ffffff;
                    border-radius: 0 15px 0 0;
                    border-radius: 0px 0px 0 0;
                    margin-bottom: -7px;
                    margin-top: -5px;
                    padding: 15px 20px 7px;
                }
            </v-style>
            <div class="app-widget-content-inner entityDetailsInnerTable">
                <div class="displayDirectory" v-bind:class="{inEditor: isInEditor}">
                    <div v-if="isInEditor" class="editorMenuWrapper">
                        <div class="app-main-comp-nav-inner">
                            <ul class="nav-buttons">
                                <li v-for="currMenuItem in editorMenus" v-on:click="openMenu(currMenuItem)" v-bind:class="{active: currMenuItem.name === activeEditorMenu}"><span class="app-main-comp-page-item"><span v-bind:class="currMenuItem.icon"></span> {{ currMenuItem.name }}</span></li>
                            </ul>
                        </div>
                        <div class="editorMenuBody"><div v-if="activeEditorMenu === editorMenus[0].name">
                                <h3 class="lightgrey configLabel"><span>Directory Defaults</span></h3>
                                <table class="table no-top-border">
                                    <tbody>
                                        <tr>
                                            <td style="width: 125px; vertical-align: middle;">Sort By</td>
                                            <td>
                                                <select v-model="settings.record_sort_by" class="form-control" type="text" v-on:change="saveSettings">
                                                    <option value="display_asc">Display Name | Asc</option>
                                                    <option value="display_desc">Display Name | Desc</option>
                                                    <option value="company_asc">Company Name | Asc</option>
                                                    <option value="company_desc">Company Name | Desc</option>
                                                    <option value="date_added_asc">Date Added | Asc</option>
                                                    <option value="date_added_desc">Date Added | Desc</option>
                                                </select>
                                            </td>
                                            <td style="width: 125px; vertical-align: middle;">Initial Display Count</td>
                                            <td>
                                                <select v-model="settings.initial_page_count" class="form-control" v-on:change="saveSettings">
                                                    <option value="5">5</option>
                                                    <option value="10">10</option>
                                                    <option value="15">15</option>
                                                    <option value="25">25</option>
                                                    <option value="30">30</option>
                                                    <option value="40">40</option>
                                                    <option value="50">50</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 125px; vertical-align: middle;">Filters</td>
                                            <td>
                                                <div style="text-align:left;"><label for="filter_display_name"><input id="filter_display_name" type="checkbox"> Display Name</label></div>
                                                <div style="text-align:left;"><label for="filter_company"><input id="filter_company" type="checkbox"> Company Name</label></div>
                                                <div style="text-align:left;"><label for="filter_date_added"><input id="filter_date_added" type="checkbox"> Date Added</label></div>
                                            </td>
                                            <td style="width: 125px; vertical-align: middle;">Initial Count</td>
                                            <td><input class="form-control" type="number"></td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                            <div v-if="activeEditorMenu === editorMenus[1].name">
                                <h3 class="lightgrey configLabel"><span>Directory Themes</span></h3>
                                <div class="displayDirectoryInner">
                                    <div v-for="currTheme in themes" class="displayDirectoryItem"><div v-on:click="activateTheme(currTheme)" style="width:200px;height:200px;background:#cc0000;"></div></div>
                                </div>
                            </div>
                            <div v-if="activeEditorMenu === editorMenus[2].name">
                                <h3 class="lightgrey configLabel"><span>Directory Package Options</span></h3>
                            </div>
                            <div v-if="activeEditorMenu === editorMenus[3].name">
                                <h3 class="lightgrey configLabel"><span>Directory Theme Options</span></h3>
                            </div>
                            <div v-if="activeEditorMenu === editorMenus[4].name">
                                <h3 class="lightgrey configLabel"><span>Directory Records</span></h3>
                                <table class="table no-top-border">
                                    <tbody>
                                    <tr>
                                        <td style="width: 125px; vertical-align: middle;">Desktop Max Row Count</td>
                                        <td>
                                            <select v-model="settings.desk_max_row_count" class="form-control" v-on:change="saveSettings">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                            </select>
                                        </td>
                                        <td style="width: 125px; vertical-align: middle;">Mobile Max Row Count</td>
                                        <td>
                                            <select v-model="settings.mobile_max_row_count" class="form-control" v-on:change="saveSettings">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                            </select>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <h2 v-if="isInEditor"><input class="app-page-editor-text-transparent mainWidgetTitle" v-model="settings.directory_title" v-on:change="saveSettings"></h2>
                    <p v-if="isInEditor"><textarea class="app-page-editor-text-transparent mainWidgetTitle" v-model="settings.directory_text" v-on:change="saveSettings">This is text where we talk about things.</textarea></p>
                    <h2 v-if="!isInEditor">{{ settings.directory_title }}</h2>
                    <p v-if="!isInEditor">{{ settings.directory_text }}</p>
                    <div v-if="!hideJoinButton" class="mb-4"><button v-on:click="joinDirectory" class="btn btn-primary">Join This Directory</button></div>
                    <div class="displayDirectoryInner" v-bind:class="generateThemeClass">
                        <div class="displayDirectoryItem" v-for="currMember in directoryMembers" v-on:click="openMemberPersona(currMember)">
                            <div class="displayDirectoryItemBox" v-bind:style="{opacity: renderMemberOpacity(currMember)}">
                                <div class="directoryAvatar">
                                    <div class="displayDirectoryAvatar" v-bind:style="{background: renderMemberAvatar(currMember)}"></div>
                                    <div v-if="currMember.member_status === 'pending'" class="displayDirectoryAvatarPending">
                                        Pending
                                    </div>
                                </div>
                                <div class="directoryMemberInfo">
                                    <div class="member-desc">
                                        <h3 class="team-name">{{ currMember.Settings.display_name }}</h3>
                                        <span class="team-title">{{ currMember.Settings.title }}</span>
                                        <span class="team-company">{{ currMember.Settings.company }}</span>
                                        <table class="social-links">
                                            <tbody>
                                                <tr>
                                                    <td><i class="fa fa-globe"></i></td>
                                                    <td><i class="fab fa-facebook"></i></td>
                                                    <td><i class="fab fa-twitter"></i></td>
                                                    <td><i class="fa fa-envelope"></i></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `,
    methods:
    {
        hydrateComponent: function(props, show, callback)
        {
            let self = this
            self.directoryId = self.page.__app.instance_uuid;
            self.setDefaultSettings();
            self.settingsOriginal = _.clone(self.settings);
            self.isInEditor = self.editor ? self.editor : false;
            self.$forceUpdate();

            if (self.isInEditor) {
                this.activeEditorMenu = sessionStorage.getItem('active_editor_directory_tab_' + this.page.card_tab_id);
                if (this.activeEditorMenu === null) this.activeEditorMenu = this.editorMenus[0].name;
            }
            self.reloadDirectoryMainView(callback)
        },
        reloadDirectoryMainView: function(callback) {
            let self = this
            let url = "/api/v1/directories/public-full-page/get-directory-data?id=" + self.directoryId
            if (self.user.login === "active") {
                url = url + "&active=" + self.user.id
            }
            ajax.GetExternal(url, true, function(result) {
                if (!result || result.success === false || !result.response.data || typeof result.response.data === "undefined") {
                    if (typeof callback === "function") {
                        callback(self);
                        self.removeAjaxClass();
                    }
                    return;
                }
                self.directoryMembers = []
                self.directoryPackages = []
                self.directorySettings = []

                if (typeof result.response.data.packages !== "undefined" && result.response.data.packages.length > 0) {
                    for (let currPackage of result.response.data.packages) {
                        self.directoryPackages.push(currPackage)
                    }
                }

                if (typeof result.response.data.settings !== "undefined" && result.response.data.settings.length > 0) {
                    for (let currSetting of result.response.data.settings) {
                        self.directorySettings.push(currSetting)
                    }
                }

                let packageCount = 0
                for (const key in self.directoryPackages) {
                    if (self.directoryPackages[key].status == "active") {
                        packageCount++
                    }
                }

                let hideJoinButton = false

                for (const key in self.directorySettings) {
                    if (self.directorySettings[key].label == "status" && self.directorySettings[key].value === "inactive" && packageCount === 0 ) {
                        hideJoinButton = true
                    }
                }

                if (typeof result.response.data.personas !== "undefined" && result.response.data.personas.length > 0) {
                    for (let currMember of result.response.data.personas) {
                        if (currMember.user_id == self.user.id && currMember.status === "pending") {
                            hideJoinButton = true
                            self.showSelfPending = true
                        }
                        let personaItem = currMember.persona;
                        personaItem.member_status = currMember.status
                        self.directoryMembers.push(personaItem)
                    }
                }

                self.hideJoinButton = hideJoinButton

                self.reloadDirectoryList();

                if (typeof callback === "function") {
                    callback(self);
                    self.removeAjaxClass();
                }
            });
        },
        setUserAuth: function(data) {
            if (data === null || typeof data.isLoggedIn === "undefined" || typeof data.user === "undefined" || data.isLoggedIn === "inactive" || data.user === "visitor") {
                this.isLoggedIn = "inactive";
                this.authUserId = null;
                this.userId = null;
                this.userId = null;
                this.userNum = null;
                this.user = null;
                return false;
            }

            this.isLoggedIn = data.isLoggedIn
            this.authUserId = data.authUserId
            this.userId = data.userId
            this.userNum = data.userNum

            try {
                this.user.data = JSON.parse(data.user)
                this.user.id = data.userNum
                this.user.uuid = data.userId
                this.user.login = this.isLoggedIn
                return true
            } catch(e) {
                console.log(data);
                return false
            }
            this.hydrateAuth(this.$parent);
        },
        hydrateAuth: function(parent)
        {
            if (typeof parent.authentication === "undefined" || parent.authentication === null) {
                if (typeof parent.$parent === "undefined") {
                    return;
                }
                return this.hydrateAuth(parent.$parent);
            }

            parent.authentication.authenticate();
        },
        openMenu: function(item) {
            this.activeEditorMenu = item.name;
            sessionStorage.setItem('active_editor_directory_tab_' + this.page.card_tab_id, item.name);
        },
        loadDirectoryId: function()
        {
            this.directoryId = this.page.__app.instance_uuid;
        },
        setVal: function(newVal, defVal) {
            return newVal ? newVal : defVal;
        },
        setDefaultSettings: function() {
            this.settingsOriginal = _.clone(this.settings)
            this.settings.record_sort_by = this.setVal(this.settings.record_sort_by, "display_asc")
            this.settings.initial_page_count = this.setVal(this.settings.initial_page_count, "15")
            this.settings.desk_max_row_count = this.setVal(this.settings.desk_max_row_count, "3")
            this.settings.mobile_max_row_count = this.setVal(this.settings.mobile_max_row_count, "2")
            this.settings.directory_title = this.setVal(this.settings.directory_title, "Directory Title")
            this.settings.directory_text = this.setVal(this.settings.directory_text, "The best directory on the page. Click here to modify this text.")
            this.settings.active_theme = this.setVal(this.settings.active_theme, this.themes[0].name)
            for (currTheme of this.themes) {
                if (currTheme.name === this.settings.active_theme) {
                    this.activeTheme = currTheme
                }
            }
            this.saveSettings();
        },
        saveSettings: function() {
            const self = this;
            let settingsBatch = {};

            for (let currSettingLabel in this.settings) {
                if (this.settings[currSettingLabel] != this.settingsOriginal[currSettingLabel]) {
                    settingsBatch[currSettingLabel] = self.settings[currSettingLabel];
                }
            }

            if (Object.keys(settingsBatch).length === 0) return;
            self.updateSettingRecord(settingsBatch, function() {

            });
        },
        updateSettingRecord: function(batch, callback) {
            ajax.PostExternal("/modules/widget/update-setting?id=" + this.app.app_instance_rel_id, batch, true);
        },
        activateTheme: function(theme) {
            this.settings.active_theme = theme.name;
            this.activeTheme = theme;
            this.saveSettings();
        },
        reloadDirectoryList: function()
        {
            let self = this;
            this.directoryMembers = this.directoryMembers;
            this.$forceUpdate();
            setTimeout(function() {
                self.directoryMembers = self.directoryMembers;
                self.$forceUpdate();
            }, 200);
        },
        joinDirectory: function()
        {
            let self = this;
            modal.EngageFloatShield(function(shield) {
                let data = {};
                data.title = "Join Our Directory";
                let editComponent = self.getComponent("registerForDirectoryComponent","registerForDirectoryComponent", "main", "add", "Loading...", {}, self.directoryMembers, {directoryId: self.directoryId, directoryMembers: self.directoryMembers, directoryPackages: self.directoryPackages, directorySettings: self.directorySettings});
                modal.EngagePopUpDialog(data, 850, 250, true, "default", true, editComponent, self, function(widget) {
                });
            });
        },
        renderMemberAvatar: function(member) {
            if (!member.Settings.avatar) {
                return "url(/_ez/images/users/defaultAvatar.jpg) no-repeat center center / contain";
            }
            const mediaArray = member.Settings.avatar.split("|");
            return "url(" + imageServerUrl() + mediaArray[1] + ") no-repeat center center / contain";
        },
        renderMemberOpacity: function(member) {
            if (member.member_status === "pending") {
                return ".5";
            }
            return "1";
        },
        openMemberPersona: function(member) {
            let self = this;
            modal.EngageFloatShield(function(shield) {
                let data = {};
                data.title = member.Settings.display_name;
                data.class = "persona";
                let editComponent = self.getComponent("showcaseMemberPersonaComponent","showcaseMemberPersonaComponent", "main", "view", "Loading...", {}, this.directoryMembers, {directoryId: this.directoryId, member: member});
                modal.EngagePopUpDialog(data, 650, 250, true, "default", true, editComponent, self);
            });
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
