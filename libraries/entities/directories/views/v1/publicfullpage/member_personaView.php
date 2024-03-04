<?php
    global $app;
?>
{
    name: "showcaseMemberPersonaComponent",
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
            screen: "persona",
            member: {},
            persona: {},
            directoryId: "",
            memberId: "",
            disabled: true,
        }
    },
    created() {
        this.screen = "persona";
        this.member = {};
        this.userId = 0;
        this.memberId = '';
    },
    template: `
<div class="entityMemberDetailsInner">
    <v-style type="text/css">
    </v-style>
    <div v-if="!disabled" >
        <div v-if="screen === 'persona'">
            {{ persona }}
            <div>Here is where the Persona Data goes for {{ persona.display_name }}</div>
            <img v-bind:src="renderMemberAvatar()" width="200" height="200"/>
        </div>
    </div>
</div>`,
    methods:
    {
        hydrateComponent: function(props, show, callback)
        {
            if (!this.member) return
            this.disabled = false
            this.persona = _.clone(this.member.Settings)
        },
        renderMemberAvatar: function()
        {
            const mediaArray = this.member.Settings.avatar.split("|")
            return imageServerUrl() + mediaArray[1]
        },
    }
}
