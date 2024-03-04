<?php
?>
{
    name: "editMemberComponent",
    parent: "<?php echo $mainComponentId; ?>",
    mountType: "dynamic",
    dynamicComponents() {
        return {
            dynMemberDataSelector: { id: "memberDataSelector", instanceId: "memberDataSelector", title: "Title Test"}
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
            directoryId: "",
            memberId: "",
            directoryMembers: [],
            directoryColumns: [],
            profileImageUploadUrl: "",
            submitButtonTitle: "",
            dynMemberDataSelector: null,
            dynMemberDataSelectorComponent: null,
        }
    },
    created() {
        this.public = false;
        this.action = "Add";
        this.submitButtonTitle = "";
        this.entity = {};
        this.entities = [];
        this.userId = 0;
        this.directoryId = '';
        this.directoryMembers = [];
        this.memberId = '';
        this.directoryColumns = [];
    },
    template: `
<div class="entityMemberDetailsInner">
    <h4 style="margin-top: 4px;"><span class="fas fa-user-circle fas-large desktop-25px"></span> 
        <span class="fas-large" v-if="action === 'edit'" >{{ entity.first_name }} {{ entity.last_name }}'s Profile</span>
        <span class="fas-large" v-if="action === 'add'" >Add New Profile</span>
    </h4>
    <div style="background:#ddd;padding: 15px 17px 2px;border-radius:5px;box-shadow:rgba(0,0,0,.2) 0 0 10px inset;margin-top:10px; ">
        <div class="width250px">
            <div class="memberAvatarImage">
                <div class="slim" data-ratio="1:1" data-force-size="650,650" v-bind:data-service="profileImageUploadUrl" id="my-cropper" style="background-image: url(/_ez/images/users/defaultAvatar.jpg); background-size:100%;">
                    <input type="file"/>
                    <img width="250" height="250" alt="">
                </div>
            </div>
        </div>
        <div class="widthAutoTo250px">    
            <table class="table no-top-border">
                <tbody>
                    <tr>
                        <td style="width:100px;vertical-align: middle;">First Name</td>
                        <td>
                            <input v-model="entity.first_name" class="form-control" type="text" placeholder="" value="">
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100px;vertical-align: middle;">Last Name</td>
                        <td>
                            <input name="card_vanity_url" v-model="entity.last_name"class="form-control" type="text" placeholder="" value="">
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100px;vertical-align: middle;">Phone</td>
                        <td>
                            <input name="card_vanity_url" v-model="entity.mobile_phone" class="form-control" type="text" placeholder="" value="">
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100px;vertical-align: middle;">Email</td>
                        <td>
                            <input name="card_vanity_url" v-model="entity.email" class="form-control" type="text" placeholder="" value="">
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
            <tr v-for="currColumn in directoryColumns">
                <td style="width:109px;vertical-align: middle;">{{ currColumn.name }}</td>
                <td style="padding: 5px .75rem;">
                    <component :is="dynMemberDataSelectorComponent" :member-column="currColumn"></component>
                </td>
            </tr>
        </tbody>
    </table>
    <button class="buttonID9234597e456 btn btn-primary w-100" @click="saveMember()">{{ submitButtonTitle }}</button>
</div>`,
    methods: 
    {
        hydrateComponent: function(props, show, callback)
        {
            this.entity = _.clone(this.entity);
            this.directoryColumns = [];
            this.directoryMembers = (props !== null) ? props.directoryMembers : [];
            this.public = (props !== null) ? props.public : false;
            this.submitButtonTitle = this.buildSaveButtonText(this.action);
            console.log(this);
            console.log(this.dynMemberDataSelector);
            this.dynMemberDataSelectorComponent = this.dynMemberDataSelector;

            let self = this;
            let objMyCropper = document.getElementById("my-cropper");
            Slim.destroy(objMyCropper);
            
            this.$forceUpdate();
            
            this.addAjaxClass("editMemberComponentCustomFields");
            this.memberId = (this.entity !== null) ? this.entity.member_directory_record_id : null;
            this.setProfileImageUploadUrl();
            
            this.getDirectoryId(((props !== null && props.directoryId) ? props.directoryId : null), function() 
            {
                self.loadComponentData(self.action, self.entity, self.entities, objMyCropper, self.userId, callback);
            });
        },
        setProfileImageUploadUrl: function()
        {
            this.profileImageUploadUrl = '/api/v1/media/upload-image?entity_id=' + this.memberId + '&user_id=' + this.userId + '&entity_name=ezcardDirectoryMemberRecord&class=member-avatar';
        },
        buildSaveButtonText: function(action)
        {
            switch(action)
            {
                case "add":
                    return "Create New Member";
                case "edit":
                    return "Save Member";
                default:
                    return "Unknown!";
            }
        },
        getParentLinkActions: function()
        {
            return ["add", "edit"];
        },
        getDirectoryId: function(directoryUuid, callback)
        {
            if(!directoryUuid || directoryUuid === null)
            {
                return directoryUuid;
            }
            
            let self = this;
            
            ajax.SendExternal("/api/v1/directories/public-full-page/get-directory-id?id=" + directoryUuid, "", "get", "json", true, function(result)
            {
                self.directoryId = result.data.id;
                callback(self);
            });
        },
        loadComponentData: function(action, entity, entities, objMyCropper, userId, callback)
        {     
            let cropperChildren = objMyCropper.getElementsByTagName("img");
            
            if( this.memberId !== null && typeof entity.profile_image_url === "string" && entity.profile_image_url !== "")
            {
                cropperChildren[0].src = entity.profile_image_url;
            }
            else
            {
                cropperChildren[0].removeAttribute("src");
            }
            
            const memberLoadUrl = "/api/v1/directories/public-full-page/get-directory-columns?id=" + this.directoryId + "&member=" + this.memberId;

            let self = this;
            
            ajax.SendExternal(memberLoadUrl, "", "get", "json", true, function(result)
            {
                self.directoryColumns = result.data ? result.data : [] ;
                
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
                this.entity.profile_image_url = url;
                const postUrl = "/api/v1/directories/public-full-page/save-directory-record-avatar-url?member=" + this.entity.member_directory_record_id;
                const postData = {avatar_url: url};
           
                ajax.SendExternal(postUrl, postData, "POST", "json", true, function(result)
                {
                    //console.log(result);
                });
            }
        },
        removeEntityAvatar: function()
        {
            this.entity.profile_image_url  = "__remove__";
        },
        saveMember: function()
        {
            modal.EngageFloatShield();
            let self = this;
            let allMemberData = {member: this.entity, customFields: this.directoryColumns};

            let memberDirectoryId = "new";

            if (self.action === "edit")
            {
                memberDirectoryId = self.entity.member_directory_record_id;
            }

            const url = "/api/v1/directories/public-full-page/upsert-directory-member-record?directoryId=" + self.directoryId + "&member=" + memberDirectoryId;

            ajax.SendExternal(url, JSON.stringify(allMemberData), "form", "json", true, function(result)
            {
                switch(self.action)
                {
                    case "add":
                        self.memberId = result.data.member.member_directory_record_id;
                        self.entity.member_directory_record_id = result.data.member.member_directory_record_id;
                        self.directoryMembers.push(self.entity);
                        self.setProfileImageUploadUrl();
                        Slim.setUploadUrl(document.getElementById("my-cropper"), self.profileImageUploadUrl);
                        break;
                    default:
                        if (self.entity.profile_image_url === "__remove__")
                        {
                            self.entity.profile_image_url = "";
                        }

                        if (self.public === true) { break; }
                        break;
                }

                Slim.save(document.getElementById("my-cropper"), function()
                {
                    if (self.public !== true)
                    {
                        modal.CloseFloatShield();
                        self.$parent.backToComponent("reloadDirectoryList");

                        self.directoryMembers.forEach(function (currEntity, currIndex)
                        {
                            if (self.memberId === currEntity.member_directory_record_id)
                            {
                                self.directoryMembers[currIndex] = self.entity;
                                //console.log(self.directoryMembers[currIndex]);
                            }
                        });

                        return;
                    }

                    self.displaySuccessModal();
                });
            });
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
                case "add": return 'Add Directory Member Record';
                case "edit": return 'Edit Directory Member Record';
                case "delete": return 'Delete Directory Member Record';
                case "read": return 'View Directory Member Record';
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
