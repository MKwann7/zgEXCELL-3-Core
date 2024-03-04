<?php
?>
{
    name: "packageDataSelector",
    props: {
        packageColumn: { type: Object, required: true }
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
        if (typeof this.packageColumn === "undefined") { return; }
        this.imageName = this.packageColumn.value;
        this.imageData = this.packageColumn.value;
        this.newImage = false;
    },
    template: `<div v-if="packageColumn">
    <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isText(packageColumn)">
        <tr>
            <td style="padding: 5px .75rem;"><textarea v-model="packageColumn.value" class="form-control" v-bind:placeholder="'Enter ' + packageColumn.type"></textarea></td>
        </tr>
    </table>
    <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isImage(packageColumn)">
        <tr>
            <td style="width: 50px;padding: 5px .75rem;">
                <img class="pointer" v-on:click="uploadNewImage(packageColumn)" v-show="imageData" v-bind:src="imageData" width="35" height="35" @error="imgError"/>
                <img class="pointer" v-on:click="uploadNewImage(packageColumn)" v-show="!imageData" src="/_ez/images/no-image.jpg" width="35" height="35" />
            </td>
            <td style="padding: 5px .75rem;">
                <input v-model="imageName" class="form-control" type="text" placeholder="Logo Url" readonly="readonly">
                <input type="file" ref="uploadImage" v-on:change="handleImageUpload()" style="display: none;" />
            </td>
            <td style="width: 50px;padding: 5px .75rem;">
                <span v-show="newImage === true" v-on:click="clearImage(packageColumn)" class="pointer deleteEntityButton" style="position: relative;top: 7px;"></span>
            </td>
        </tr>
    </table>
    <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isConnection(packageColumn)">
        <tr>
            <td style="width: 260px;padding: 5px .75rem;">
                <select v-model="packageColumn.typeInstance" class="form-control" >
                    <option value="">-- Select Connection Type --</option>
                    <option value="sms">SMS</option>
                    <option value="phone">Phone</option>
                    <option value="email">E-Mail</option>
                    <option value="url">Website</option>
                </select></td>
            <td style="padding: 5px .75rem;">
                <input v-model="packageColumn.value" class="form-control" type="text" placeholder="Enter Connection">
            </td>
        </tr>
    </table>
    <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isPhone(packageColumn)">
        <tr>
            <td style="padding: 5px .75rem;"><input v-model="packageColumn.value" class="form-control" type="text" placeholder="Enter phone number"></td>
        </tr>
    </table>
    <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isEmail(packageColumn)">
        <tr>
            <td style="padding: 5px .75rem;"><input v-model="packageColumn.value" class="form-control" type="text" placeholder="Enter e-mail"></td>
        </tr>
    </table>
    <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isState(packageColumn)">
        <tr>
            <td style="padding: 5px .75rem;"><input v-model="packageColumn.value" class="form-control" type="text" placeholder="Enter state"></td>
        </tr>
    </table>
    <table class="table" style="margin-top:0px; margin-bottom: 0px;" v-if="isZip(packageColumn)">
        <tr>
            <td style="padding: 5px .75rem;"><input v-model="packageColumn.value" class="form-control" type="text" placeholder="Enter zip"></td>
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
            this.packageColumn.file = null;
            this.imageName = null;
        },
        handleImageUpload: function()
        {
            this.imageName = this.$refs.uploadImage.files[0].name;
            this.packageColumn.fileName = this.imageName;
            this.newImage = true;

            let self = this;
            let reader = new FileReader();
            const file = this.$refs.uploadImage.files[0];

            reader.addEventListener("load", function ()
            {
                self.imageData = reader.result;
                self.packageColumn.file = reader.result;
            }, false);

            if (file)
            {
                reader.readAsDataURL(file);
            }

            this.$forceUpdate();
        },
        isText: function(packageColumn)
        {
            if (!this.isDefined(packageColumn)) { return true; }
            if (packageColumn.type !== "text") { return false; }
            return true;
        },
        isImage: function(packageColumn)
        {
            if (!this.isDefined(packageColumn) || packageColumn.type !== "image") { return false; }
            return true;
        },
        isDate: function(packageColumn)
        {
            if (!this.isDefined(packageColumn) || packageColumn.type !== "date") { return false; }
            return true;
        },
        isEmail: function(packageColumn)
        {
            if (!this.isDefined(packageColumn) || packageColumn.type !== "email") { return false; }
            return true;
        },
        isPhone: function(packageColumn)
        {
            if (!this.isDefined(packageColumn) || packageColumn.type !== "phone" && packageColumn.type !== "sms") { return false; }
            return true;
        },
        isConnection: function(packageColumn)
        {
            if (!this.isDefined(packageColumn) || packageColumn.type !== "connection") { return false; }
            return true;
        },
        isState: function(packageColumn)
        {
            if (!this.isDefined(packageColumn) || packageColumn.type !== "state") { return false; }
            return true;
        },
        isZip: function(packageColumn)
        {
            if (!this.isDefined(packageColumn) || packageColumn.type !== "postal") { return false; }
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
            return 'Package Data Selector';
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
