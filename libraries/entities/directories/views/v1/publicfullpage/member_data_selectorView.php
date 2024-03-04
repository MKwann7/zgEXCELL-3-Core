<?php
?>
{
    name: "memberDataSelector",
    mountType: "dynamic",
    props: {
        memberColumn: { type: Object, required: true }
    },
    data() {
        return {
            entity: {},
            entities: [],
            imageFile: null,
            imageName: null,
            imageData: null,
            newImage: false,
            fileReader: null,
        }
    },
    mounted: function() {
        if (typeof this.memberColumn === "undefined") { return; }
        this.imageName = this.memberColumn.value;
        this.imageData = this.memberColumn.value;
        this.newImage = false;
    },
    template: `<div v-if="memberColumn">
    <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isText(memberColumn)">
        <tr>
            <td style="padding: 5px .75rem;"><textarea v-model="memberColumn.value" class="form-control" v-bind:placeholder="'Enter ' + memberColumn.type"></textarea></td>
        </tr>
    </table>
    <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isImage(memberColumn)">
        <tr>
            <td style="width: 50px;padding: 5px .75rem;">
                <img class="pointer" v-on:click="uploadNewImage(memberColumn)" v-show="imageData" v-bind:src="imageData" width="35" height="35" @error="imgError"/>
                <img class="pointer" v-on:click="uploadNewImage(memberColumn)" v-show="!imageData" src="/_ez/images/no-image.jpg" width="35" height="35" />
            </td>
            <td style="padding: 5px .75rem;">
                <input v-model="imageName" class="form-control" type="text" placeholder="Logo Url" readonly="readonly">
                <input type="file" ref="uploadImage" v-on:change="handleImageUpload()" style="display: none;" />
            </td>
            <td style="width: 50px;padding: 5px .75rem;">
                <span v-show="newImage === true" v-on:click="clearImage(memberColumn)" class="pointer deleteEntityButton" style="position: relative;top: 7px;"></span>
            </td>
        </tr>
    </table>
    <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isConnection(memberColumn)">
        <tr>
            <td style="width: 260px;padding: 5px .75rem;">
                <select v-model="memberColumn.typeInstance" class="form-control" >
                    <option value="">-- Select Connection Type --</option>
                    <option value="sms">SMS</option>
                    <option value="phone">Phone</option>
                    <option value="email">E-Mail</option>
                    <option value="url">Website</option>
                </select></td>
            <td style="padding: 5px .75rem;">
                <input v-model="memberColumn.value" class="form-control" type="text" placeholder="Enter Connection">
            </td>
        </tr>
    </table>
    <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isPhone(memberColumn)">
        <tr>
            <td style="padding: 5px .75rem;"><input v-model="memberColumn.value" class="form-control" type="text" placeholder="Enter phone number"></td>
        </tr>
    </table>
    <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isEmail(memberColumn)">
        <tr>
            <td style="padding: 5px .75rem;"><input v-model="memberColumn.value" class="form-control" type="text" placeholder="Enter e-mail"></td>
        </tr>
    </table>
    <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isState(memberColumn)">
        <tr>
            <td style="padding: 5px .75rem;"><input v-model="memberColumn.value" class="form-control" type="text" placeholder="Enter state"></td>
        </tr>
    </table>
    <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isZip(memberColumn)">
        <tr>
            <td style="padding: 5px .75rem;"><input v-model="memberColumn.value" class="form-control" type="text" placeholder="Enter zip"></td>
        </tr>
    </table>
</div>
    `,
    methods:
    {
        hydrateComponent: function(props, show, callback)
        {
            let self = this;
        },
        uploadNewImage: function()
        {
            this.$refs.uploadImage.click();
        },
        clearImage: function()
        {
            this.imageData = "";
            this.newImage = false;
            this.$refs.uploadImage.value = null;
            this.memberColumn.file = null;
            this.imageName = null;
        },
        handleImageUpload: function()
        {
            this.imageName = this.$refs.uploadImage.files[0].name;
            this.memberColumn.fileName = this.imageName;
            this.newImage = true;

            let self = this;
            let reader = new FileReader();
            const file = this.$refs.uploadImage.files[0];

            reader.addEventListener("load", function ()
            {
                self.imageData = reader.result;
                self.memberColumn.file = reader.result;
            }, false);

            if (file)
            {
                reader.readAsDataURL(file);
            }

            this.$forceUpdate();
        },
        isText: function(memberColumn)
        {
            if (!this.isDefined(memberColumn)) { return true; }
            if (memberColumn.type !== "text") { return false; }
            return true;
        },
        isImage: function(memberColumn)
        {
            if (!this.isDefined(memberColumn) || memberColumn.type !== "image") { return false; }
            return true;
        },
        isDate: function(memberColumn)
        {
            if (!this.isDefined(memberColumn) || memberColumn.type !== "date") { return false; }
            return true;
        },
        isEmail: function(memberColumn)
        {
            if (!this.isDefined(memberColumn) || memberColumn.type !== "email") { return false; }
            return true;
        },
        isPhone: function(memberColumn)
        {
            if (!this.isDefined(memberColumn) || memberColumn.type !== "phone" && memberColumn.type !== "sms") { return false; }
            return true;
        },
        isConnection: function(memberColumn)
        {
            if (!this.isDefined(memberColumn) || memberColumn.type !== "connection") { return false; }
            return true;
        },
        isState: function(memberColumn)
        {
            if (!this.isDefined(memberColumn) || memberColumn.type !== "state") { return false; }
            return true;
        },
        isZip: function(memberColumn)
        {
            if (!this.isDefined(memberColumn) || memberColumn.type !== "postal") { return false; }
            return true;
        },
        isDefined: function(val)
        {
            return typeof val !== "undefined";
        },
        imgError: function()
        {
            this.imageData = "";
            this.$forceUpdate();
        },
        getModalTitle: function(action)
        {
            return 'Upload CSV File For Member Records';
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
