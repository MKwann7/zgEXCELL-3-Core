<?php
?>
{
    name: "uploadMemberRecordComponent",
    mountType: "dynamic",
    parent: "<?php echo $mainComponentId; ?>",
    data() {
        return {
            entity: {},
            entities: [],
            directoryId: "",
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
<div>
    <div class="tabSelectionOuter divTable" style="">
        <div class="tabSelectionRow divRow">
            <div class="divCell tabSelectionLabel tabSelectionHtmlTab" style="width:33%;" v-on:click="downloadDirectoryColumnCsv()">
                <h2>Download CSV File</h2>
                <div class="tabSelectionActionButton">
                    <i class="fas fa-download"></i>
                </div>
            </div>
            <div class="divCell tabSelectionLabel tabSelectionSpecialTabs" style="width:33%;" v-on:click="uploadDirectoryColumnCsv()">
                <h2>Upload CSV File Into Membership</h2>
                <div class="tabSelectionActionButton">
                    <i class="fas fa-upload"></i>
                </div>
                <input class="form-control csv-file-input" type="file" ref="uploadFile" v-on:change="handleFileUpload()" style="display: none;" />
            </div>
            <div class="divCell tabSelectionLabel tabSelectionSpecialTabs" style="width:33%;" v-on:click="downloadMemberCsvFile()">
                <h2>Download Membership CSV</h2>
                <div class="tabSelectionActionButton">
                    <i class="fas fa-upload"></i>
                </div>
            </div>
        </div>
    </div>
</div>`,
    components: {

    },
    methods:
    {
        hydrateComponent: function(props, show, callback)
        {
            this.directoryId = (props !== null) ? props.directoryId : [];
            this.directoryMembers = (props !== null) ? props.directoryMembers : [];
            let self = this;
        },
        getModalTitle: function(action)
        {
            return 'Upload CSV File For Member Records';
        },
        getParentLinkActions()
        {
            return ["add", "edit", "delete", "read"];
        },
        uploadDirectoryColumnCsv: function()
        {
           this.$refs.uploadFile.click();
        },
        handleFileUpload: function()
        {
            this.csvFile = this.$refs.uploadFile.files[0];
            let formData = new FormData();
            formData.append("id", this.directoryId);
            formData.append("file", this.csvFile);

            const url = "/api/v1/directories/public-full-page/upload-template-csv-into-member-records?id=" + this.directoryId;
            modal.EngageFloatShield();

            let self = this;
            ajax.SendExternal(url, formData, "form", "json", true, function(result)
            {
                if (result.success === false || typeof result.data.newRecords === "undefined") { modal.CloseFloatShield(); return; }

                for(let currRecord of result.data.newRecords)
                {
                    self.directoryMembers.push(currRecord);
                }

                self.$forceUpdate();

                if (result.data.errors.length > 0)
                {
                    let alertMessage = "<ul>";

                    for(let currErrorIndex in result.data.errors)
                    {
                        alertMessage += "<li>Line: " + (currErrorIndex + 3) + " - Data {" + result.data.errors[currErrorIndex].first_name + " "  + result.data.errors[currErrorIndex].last_name + "}</li>";
                    }

                    alertMessage += "<ul>";

                    modal.EngagePopUpAlert({title:"Member Directory Errors", html: "The following records <b>failed</b> to load into the system:" + alertMessage}, function() {
                        modal.CloseFloatShield();
                        self.$parent.backToComponent();
                        self.$refs.uploadFile.files = null;
                    });
                    return;
                }
                else
                {
                    modal.CloseFloatShield();
                    self.$parent.backToComponent();
                    self.$refs.uploadFile.files = null;
                }
            });
        },
        downloadDirectoryColumnCsv: function()
        {
            const url = "/api/v1/directories/public-full-page/download-template-csv-for-member-records?id=" + this.directoryId;
            window.location = url;
        },
        downloadMemberCsvFile: function()
        {
            const url = "/api/v1/directories/public-full-page/download-member-record-csv?id=" + this.directoryId;
            window.location = url;
        },
        removeAjaxClass: function()
        {
            let bodyDialogBox = document.getElementsByClassName("entityColumnDetailsInner");
            bodyDialogBox[0].classList.remove("ajax-loading-anim");
        },
        showComponent: function()
        {
            this.$forceUpdate();
        },
    }
}
