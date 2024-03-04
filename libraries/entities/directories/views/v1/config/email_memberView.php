<?php
?>
{
    name: "emailMemberComponent",
    parent: "<?php echo $mainComponentId; ?>",
    mountType: "dynamic",
    data() {
        return {
            entity: {},
            entities: [],
        }
    },
    mounted: function() {

    },
    template: `<div>
    <div class="tabSelectionOuter divTable" style="">
        <div class="tabSelectionRow divRow">
            <div class="divCell tabSelectionLabel tabSelectionHtmlTab" style="width:50%;" v-on:click="emailMemberPublicManagementUrl(entity)">
                <h2>Send Member Public Management Link</h2>
                <div class="tabSelectionActionButton">
                    <i class="fas fa-cogs"></i>
                </div>
            </div>
            <div class="divCell tabSelectionLabel tabSelectionSpecialTabs" style="width:50%;" v-on:click="emailMemberCustomMessage(entity)">
                <h2>Email Member Custom Message</h2>
                <div class="tabSelectionActionButton">
                    <i class="fas fa-envelope"></i>
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
            let self = this;
        },
        emailMemberPublicManagementUrl: function(entity)
        {
            modal.EngageFloatShield();
            modal.EngagePopUpConfirmation({title:"Send Member An Email?", html:"This will send an email to this member with a link to their public management link."}, function() {
                ajax.SendExternal("/api/v1/directories/public-full-page/send-member-record-public-management-link?id=" + entity.member_directory_record_id, "", "post", "json", true, function(result)
                {
                    self.directoryId = result.data.id;
                    callback(self);
                });
                modal.CloseFloatShield();
            }, 400, 115);

        },
        emailMemberCustomMessage: function(entity)
        {
            console.log(entity);
        },
        getModalTitle: function(action)
        {
            return 'Email Directory Member';
        },
        getParentLinkActions()
        {
            return ["add", "edit", "delete", "read"];
        },
        removeAjaxClass: function()
        {
            let bodyDialogBox = document.getElementsByClassName("entityColumnDetailsInner");
            bodyDialogBox[0].classList.remove("ajax-loading-anim");
        },
    }
}
