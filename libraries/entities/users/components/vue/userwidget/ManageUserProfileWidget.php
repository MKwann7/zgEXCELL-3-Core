<?php

namespace Entities\Users\Components\Vue\UserWidget;

use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Users\Models\UserModel;

class ManageUserProfileWidget extends VueComponent
{
    protected string $id = "d0b1599e-48cd-4d9a-83f9-dc0c2c4fcdf6";
    protected string $title              = "Create User";
    protected string $saveNewButtonTitle = "Save New User";
    protected string $updateButtonTitle = "Update User";
    protected bool $assignUserType = true;
    protected bool $assignUserRoles = true;
    protected bool $enableOriginator = true;
    protected bool $enableAccountEditing = true;

    public function __construct(array $components = [])
    {
        parent::__construct(new UserModel(), $components);

        $this->modalTitleForAddEntity = "Create User";
        $this->modalTitleForEditEntity = "Create User";
        $this->modalTitleForDeleteEntity = "Create User";
        $this->modalTitleForRowEntity = "Create User";
        $this->setDefaultAction("view");
    }

    protected function renderComponentDataAssignments (): string
    {
        return '
            entityClone: false,
            dynamicSearch: false,
            strengthType: "Weak",
            password: "",
            passwordStrengthText: "6 character minimum",
            passwordStrength: 0,
            customerList: [],
            userSearch: "",
            userSearchResult: "",
            userSearchHighlight: 0,
            searchBox: 0,
            searchBoxInner: 0,
            totalSearchDisplayCount: 0,
            submitButton: "",
            customPlatformDepartments: [],
            customPlatformDepartmentQueues: [],
            customPlatformDepartmentQueuesList: [],
        ';
    }

    protected function renderComponentHydrationScript () : string
    {
        return '
            this.clearErrors();
            this.loadCustomers();
            this.loadCompany();
            
            if (this.entity)
            {
                this.entityClone = _.clone(this.entity);
                '.($this->enableAccountEditing === true ? '                
                this.checkForDuplicateUsername(this.entityClone);
                ' : '').'
                this.checkForDuplicateUserEmail(this.entityClone);
                this.checkForDuplicateUserPhone(this.entityClone);
            }
            else
            {
                this.entityClone = {};
                this.entityClone.status = "Active";
                this.entityClone.userDepartment = "";
                this.entityClone.userDepartmentRole = "";
                this.entityClone.userDepartmentQueue = [];
            }
            
            this.userSearchResult = "";
            
            const editCardUserProfile = document.getElementsByClassName("editEntityProfile");
            const cardUserProfile = Array.from(editCardUserProfile)[0];
            '.($this->enableOriginator === true ? '
            this.searchBox = cardUserProfile.getElementsByClassName("dynamic-search-list")[0]; 
            this.searchBoxInner = this.searchBox.getElementsByClassName("table")[0];
            ' : '').'
            this.submitButton = this.setSubmitButton();
        '.parent::renderComponentHydrationScript();
    }

    protected function renderComponentMethods (): string
    {
        global $app;
        return '
            createNewCustomer: function()
            {
                let self = this;           
                let url = "/api/v1/users/create-new-user";
                
                if (this.action !== "add") { url = "/api/v1/users/update-user?user_id=" + this.entityClone.user_id; }
                 
                this.entityClone.phone = this.entityClone.user_phone_value;
                this.entityClone.email = this.entityClone.user_email_value;
                this.entityClone.affiliate_id = this.entityClone.sponsor_id;
                this.entityClone.affiliate_id = this.entityClone.sponsor_id;
                '.($this->enableAccountEditing === true ? '
                this.entityClone.password = this.password;
                ' : '').' 
                
                ajax.Post(url, this.entityClone, function(result) 
                {
                    if (result.success === false || result.response.success === false) 
                    {
                        self.showErrorFromServer(result);
                        return;
                    }
                    
                    updateAction = "update";
                    
                    if (!self.entity) 
                    {
                        let today = new Date();
                        let date = today.getFullYear()+\'-\'+(today.getMonth()+1)+\'-\'+today.getDate();
                        
                        updateAction = "new";
                        self.entity = {};
                        self.entity.avatar = "/_ez/images/users/no-user.jpg";
                        self.entity.cards = 0;
                        self.entity.platform = "'.$app->objCustomPlatform->getCompany()->platform_name.'";
                        self.entity.company_id = '.$app->objCustomPlatform->getCompanyId().';
                        self.entity.created_on = date;
                        self.entity.last_updated = date;
                    }
                    
                    self.entity.user_id = result.response.data.user.user_id;
                    self.entity.sys_row_id = result.response.data.user.id;
                    self.entity.first_name = result.response.data.user.first_name;
                    self.entity.last_name = result.response.data.user.last_name;
                    '.($this->enableAccountEditing === true ? '
                    self.entity.username = result.response.data.user.username;
                    ' : '').' 
                    self.entity.user_email = result.response.data.user.user_email;
                    self.entity.user_email_value = result.response.data.user.email;
                    self.entity.user_phone = result.response.data.user.user_phone;
                    self.entity.user_phone_value = result.response.data.user.phone;
                    self.entity.template_id = result.response.data.user.last_name;
                    self.entity.status = self.entityClone.status;
                    
                    if (updateAction === "new")
                    {
                        self.entities.push(self.entity);
                    }
                    
                    let vue = self.findApp(self);
                    vue.$forceUpdate();
                                 
                    let objModal = self.findModal(self);                 
                    objModal.close(); 
                });
            },
            showErrorFromServer: function(result)
            {
                switch(result.response.data.error)
                {
                    case "primary_phone_exists":
                        const el = document.getElementById("phone_1603190947");
                        
                        el.classList.remove("pass-validation");
                        el.classList.add("error-validation");
                        
                        let errorNode = null;
                        errorNode = createNode("div", [".phone-error", ".error-text"], "This Primary Phone Is Already Registered");  
                        insertAfterNode(el, errorNode);
                        
                        break;
                    case "primary_email_exists":
                        break;
                    case "duplicate_account":
                        break;
                    case "creation_failed":
                        break;
                    default:
                        break;
                }
                
                modal.EngageFloatShield();
                let data = {title: "Resolve Errors", html: "Please correct the data errors to continue."};
                modal.EngagePopUpAlert(data, function () {
                    modal.CloseFloatShield(function () {
                        modal.CloseFloatShield();
                    });
                }, 400, 115);
 
            },
            clearErrors: function()
            {
                '.($this->enableAccountEditing === true ? '
                const usernameEl = elm("username_1603190947");
                removeNodeByClass("username-error");
                usernameEl.classList.remove("error-validation");
                usernameEl.classList.remove("pass-validation");
                ' : '').' 
                
                const phoneEl = elm("phone_1603190947"); 
                removeNodeByClass("phone-error");
                phoneEl.classList.remove("error-validation");
                phoneEl.classList.remove("pass-validation"); 
                
                const emailEl = elm("email_1603190947"); 
                removeNodeByClass("email-error");
                emailEl.classList.remove("error-validation");
                emailEl.classList.remove("pass-validation"); 
            },
            setSubmitButton: function()
            {
                switch(this.action)
                {
                    case "add":
                        return "'.$this->saveNewButtonTitle.'";
                    default:
                        return "'.$this->updateButtonTitle.'";
                }
            },
            generateRandomPassword: function()
            {
                const specials = "!@#$%^&*+<>?|[],.~";
                const lowercase = "abcdefghijklmnopqrstuvwxyz";
                const uppercase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                const numbers = "0123456789";
                
                const all = specials + lowercase + uppercase + numbers;
                
                let password = "";
                password += specials.pick(1);
                password += lowercase.pick(1);
                password += uppercase.pick(1);
                password += all.pick(3, 10);
                password = password.shuffle();
                this.password = password;
                this.checkForStrongPassword();
            },
            checkForStrongPassword: function()
            {
                let strength = 0;
                this.passwordStrengthText = "weak";
                
                if (this.password.length < 4) 
                {
                    this.passwordStrength = 0;
                    this.passwordStrengthText = "Minimum number of characters is 6";
                    return;
                }
                else
                {
                    if (this.password.match(/[a-z]+/)) 
                    {
                        strength += 1;
                    }
                    if (this.password.match(/[A-Z]+/)) 
                    {
                        strength += 1;
                    }
                    if (this.password.match(/[0-9]+/))
                    {
                        strength += 1;
                    }
                    if (this.password.match(/[\[\]$@#&^!\-|~\,\.\+\<\>\?]+/)) 
                    {
                        strength += 1;
                    }
                    if (this.password.length >= 8) 
                    {
                        strength += 1;
                    }
                }
                
                
                switch (strength) 
                {
                    case 0:
                    case 1:
                        this.passwordStrength = 0;
                        this.passwordStrengthText = "weak";
                        break;

                    case 2:
                        this.passwordStrength = 25;
                        this.passwordStrengthText = "medium";
                        break;
                        
                    case 3:
                        this.passwordStrength = 50;
                        this.passwordStrengthText = "stronger";
                        break;
                    
                    case 4:
                        this.passwordStrength = 75;
                        this.passwordStrengthText = "strong!";
                        break;
                    
                    case 5:
                        this.passwordStrength = 100;
                        this.passwordStrengthText = "very strong!";
                        break;
                }
            },
            checkForDuplicateUsername: function(entity)
            {
                const usernameEl = elm("username_1603190947");
                removeNodeByClass("username-error");
                
                if ( typeof entity === "undefined" || typeof entity.username === "undefined" || entity.username === null || entity.username === "") 
                { 
                    usernameEl.classList.add("error-validation");
                    usernameEl.classList.remove("pass-validation"); 
                    
                    console.log(globalClassList("username-error").length);
                    
                    if (globalClassList("username-error").length === 0) 
                    {
                        let errorNode = createNode("div", [".username-error", ".error-text"], "A username is required.");                               
                        insertAfterNode(usernameEl, errorNode);
                    }
                    
                    return; 
                }
                
                ajax.Get("api/v1/users/check-user-username?username=" + entity.username + "&user_id=" + entity.user_id, null, function(objResult) 
                {
                    switch(objResult.match) 
                    {
                        case true:
                            usernameEl.classList.add("error-validation");
                            usernameEl.classList.remove("pass-validation");
                            
                            if (globalClassList("username-error").length === 0) 
                            {
                                let errorNode = createNode("div", [".username-error", ".error-text"], "This Username Already Exists");                               
                                insertAfterNode(usernameEl, errorNode);
                            }
                            
                            break;
                        default:
                            usernameEl.classList.remove("error-validation");
                            usernameEl.classList.add("pass-validation");
                            removeNodeByClass("username-error");
                            break;
                    }
                });
            },
            checkForDuplicateUserPhone: function(entity)
            {
                const el = document.getElementById("phone_1603190947");
                removeNodeByClass("phone-error");
                
                if (typeof entity === "undefined" || typeof entity.user_phone_value === "undefined" || entity.user_phone_value === null || entity.user_phone_value === "") 
                {    
                    el.classList.remove("pass-validation");
                    el.classList.add("error-validation");
                    
                    if (globalClassList("phone-error").length === 0) 
                    {
                        let errorNode = createNode("div", [".phone-error", ".error-text"], "A primary user phone is required.");                               
                        insertAfterNode(el, errorNode);
                    }
                    
                    return;
                }
                
                let userPhone = entity.user_phone_value;
                
                if (typeof userPhone === "string") 
                {
                    userPhone = userPhone.replace(/\D/g,"");
                }
                
                const url = "/api/v1/users/check-users-primary-phone?phone=" + userPhone + "&user_id=" + entity.user_id;
                
                ajax.Get(url, null, function(result) 
                {
                    if (result.success === false || result.response.data.match === true) 
                    {
                        el.classList.remove("pass-validation");
                        el.classList.add("error-validation");
                        
                        if (globalClassList("phone-error").length === 0) 
                        {
                            let errorNode = null;
                            
                            if (result.response.data.match === true)
                            {
                                errorNode = createNode("div", [".phone-error", ".error-text"], "This Primary Phone Is Already Registered");           
                            }
                            else if (result.message === "Validation errors.")
                            {
                                errorNode = createNode("div", [".phone-error", ".error-text"], "Please enter a valid phone number.");  
                            }
                                                 
                            insertAfterNode(el, errorNode);
                        }
                        
                        return;
                    }
                    
                    el.classList.add("pass-validation");
                    el.classList.remove("error-validation");
                });
            },
            checkForDuplicateUserEmail: function(entity)
            {
                const el = document.getElementById("email_1603190947");
                removeNodeByClass("email-error");
                
                if (typeof entity === "undefined" || typeof entity.user_email_value === "undefined" || entity.user_email_value === null || entity.user_email_value === "") 
                {   
                    el.classList.remove("pass-validation");
                    el.classList.add("error-validation");
                    
                    if (globalClassList("email-error").length === 0) 
                    {
                        let errorNode = createNode("div", [".email-error", ".error-text"], "A primary user email is required.");                               
                        insertAfterNode(el, errorNode);
                    }
                    
                    return
                }
                
                let userEmail = entity.user_email_value;
                
                if (!validateEmail(userEmail))
                {
                    el.classList.remove("pass-validation");
                    el.classList.add("error-validation");
                    let errorNode = createNode("div", [".email-error", ".error-text"], "Please enter a valid email address.");
                    insertAfterNode(el, errorNode);
                    return;
                }
                
                const url = "api/v1/users/check-users-primary-email?email=" + entity.user_email_value + "&user_id=" + entity.user_id;

                ajax.Get(url, null, function(result) 
                {
                    if (result.success === false || result.response.data.match === true) 
                    {                        
                        el.classList.remove("pass-validation");
                        el.classList.add("error-validation");
                        
                        if (globalClassList("email-error").length === 0) 
                        {
                            let errorNode = null;
                            
                            if (result.response.data.match === true)
                            {
                                errorNode = createNode("div", [".email-error", ".error-text"], "This Primary Email Is Already Registered");           
                            }
                            else if (result.message === "Validation errors.")
                            {
                                errorNode = createNode("div", [".email-error", ".error-text"], "Please enter a valid email address.");  
                            }
                                                 
                            insertAfterNode(el, errorNode);
                        }
                        
                        return;
                    }
                    
                    el.classList.add("pass-validation");
                    el.classList.remove("error-validation");
                });
            },
            engageDynamicSearch: function(user)
            {
                this.dynamicSearch = true;
            },
            toggleDynamicSearch: function(user)
            {
                this.dynamicSearch = !this.dynamicSearch;
            },
            hideDynamicSearch: function()
            {
                const self = this;
                setTimeout(function() {
                    if (self.userSearchResult === "") {
                        self.dynamicSearch = false;
                    }
                }, 100);
            },
            keyMonitorCustomerList: function(event)
            {
                switch(event.keyCode)
                {
                    case 38:
                        this.decreaseUserSearchHighlight();
                        break;
                    case 40:
                        this.increaseUserSearchHighlight();
                        break;
                    case 13:
                        let userByIndex = this.getUserByIndex(this.userSearchHighlight);
                        this.assignCustomerToCard(userByIndex, this.userSearchHighlight);
                        break;
                    default:
                        this.userSearchHighlight = 0;
                        break;
                }
                
                this.customerList = this.customerList;
                this.$forceUpdate();
            },
            getMiddleOffset: function()
            {
                const boxHeight = (this.searchBoxInner.offsetHeight / (this.totalSearchDisplayCount + 1));
                const boxContains = Math.ceil(this.searchBox.offsetHeight / boxHeight);
                return [boxHeight, (boxContains / 2) - 2];
            },
            increaseUserSearchHighlight: function()
            {
                this.userSearchHighlight++;
                const [boxHeight, middleOffset] = this.getMiddleOffset();                
                this.searchBox.scroll(0, ((this.userSearchHighlight - middleOffset) * boxHeight));
            },
            decreaseUserSearchHighlight: function()
            {
                if (this.userSearchHighlight === 0) { return; }
                this.userSearchHighlight--;
                const [boxHeight, middleOffset] = this.getMiddleOffset();             
                this.searchBox.scroll(0, ((this.userSearchHighlight - middleOffset) * boxHeight));
            },
            userConnectionSearchHighlight: function()
            {
                if (this.userSearchHighlight === 0) { return; }
                this.userSearchHighlight--;
                const [boxHeight, middleOffset] = this.getMiddleOffset();             
                this.searchBox.scroll(0, ((this.userSearchHighlight - middleOffset) * boxHeight));
            },
            userSearchMatchesIndex: function(index)
            {
                if (index !== this.userSearchHighlight)
                {
                    return false;
                }
                
                return true;
            },
            parseUsersBySearch(usersList)
            {
                const self = this;
                let newUserList = [];
                
                if (typeof usersList.length !== "number" || usersList.length === 0)
                {
                    return newUserList;
                }
                
                let intTotalCount = 0;
                
                for (let currUser of usersList)
                {
                    if (intTotalCount > 25) { break; }
                    if (
                        currUser.first_name.toLowerCase().includes(self.userSearch.toLowerCase()) || 
                        currUser.last_name.toLowerCase().includes(self.userSearch.toLowerCase()) ||
                        (currUser.first_name.toLowerCase() + " " + currUser.last_name.toLowerCase()).includes(self.userSearch.toLowerCase()) ||
                        currUser.user_id.toString().toLowerCase().includes(self.userSearch.toLowerCase())
                    )
                    {
                        newUserList.push(currUser);
                        intTotalCount++;
                    }
                }
                
                return newUserList;
            },
            
            getUserByIndex: function(index)
            {
                const users = this.cartCustomerSearchList;
                
                for(let currUserIndex in users)
                {
                    if (currUserIndex == index) 
                    { 
                        return users[currUserIndex]; 
                    }
                }
                
                return null;
            },
            assignCustomerToCard: function(user, index)
            {
                if (user === null) { return; }
            
                this.userSearch = ""
                this.userSearchResult = user.first_name + " " + user.last_name;
                this.entityClone.sponsor_id = user.user_id;
                this.dynamicSearch = false;
                this.userSearchHighlight = index;
            },
            loadCustomers: function(callback)
            {
                const self = this;
                this.customerList = [];
                const url = "'.$app->objCustomPlatform->getFullPortalDomainName().'/cart/get-all-users";
                
                ajax.GetExternal(url, {}, true, function(result) 
                {
                    if (result.success === false)
                    {
                        return;
                    }

                    const users = Object.entries(result.response.data.list);
                    users.forEach(function([user_id, currUser])
                    {
                        if (user_id == self.entityClone.sponsor_id) { self.userSearchResult = currUser.first_name + " " + currUser.last_name; }
                        self.customerList.push(currUser);
                    });
                    
                    self.$forceUpdate();
                });
            },
            loadCompany: function(callback)
            {
                const self = this;
                this.customerList = [];
                const url = "companies/get-company-data-for-user-management";
                
                ajax.Get(url, function(result) 
                {
                    if (result.success === false)
                    {
                        return;
                    }
                    
                    self.customPlatformDepartments = result.response.data.departments;
                    self.customPlatformDepartmentQueues = result.response.data.departmentQueues;
                    self.filterDepartmentData();

                    self.$forceUpdate();
                });
            },
            clearSelectedValue: function()
            {
                this.userSearchResult = "";
                this.userSearch = "";
            },
            filterDepartmentData: function()
            {
                let queueList = [];

                for (let currQueue of this.customPlatformDepartmentQueues)
                {
                    if (currQueue.company_department_id === this.entityClone.userDepartment)
                    {
                        queueList.push(currQueue);
                    }
                }
                
                this.customPlatformDepartmentQueuesList = queueList;
            },
        ';
    }

    protected function renderComponentComputedValues() : string
    {
        return '
            cartCustomerSearchList: function()
            {
                return this.parseUsersBySearch(this.customerList);
            },
            customPlatformDepartmentQueuesList: function()
            {
                let queueList = [];
                
                for (let currQueue of this.customPlatformDepartmentQueues)
                {
                    if (currQueue.company_department_id === this.entityClone.userDepartment)
                    {
                        queueList.push(currQueue);
                    }
                }
                
                return queueList;
            },
        ';
    }

    protected function renderTemplate() : string
    {
        return '
        <div class="editEntityProfile">
            <v-style type="text/css">
            
                .editEntityProfile .dynamic-search-list {
                    position: absolute;
                    width: calc(100% - 35px);
                    background: #fff;
                    margin-left: 5px;
                    z-index: 1000;
                    max-height:40vh;
                    overflow-y:auto;
                }
                .editEntityProfile .dynamic-search-list > table {
                    width: 100%;
                }
                .editEntityProfile .dynamic-search-list > table > thead {
                    box-shadow: rgba(0,0,0,0.2) 0px 2px 5px;
                    background-color: #007bff;
                    color: #fff !important;
                }
                .editEntityProfile .dynamic-search-list > table tr {
                    cursor:pointer;
                }
                .editEntityProfile .dynamic-search-list > table tr:hover {
                    background-color:#d5e9ff !important;
                }
                .editEntityProfile .augmented-form-items {
                    background:#ddd;padding: 0px 8px 0px;border-radius:5px;box-shadow:rgba(0,0,0,.2) 0 0 10px inset
                }
                .editEntityProfile .dynamic-search-list > table tr.userSearchHighlight {
                    background-color:#afd2f7 !important;
                }
                .editEntityProfile .selected-user {
                    position: absolute; 
                    top: calc(50% - 15px);
                    left: 20px;
                    padding: 3px 30px 3px 8px;
                    background: #eee;
                    border: 1px solid #ccc;
                }
                .editEntityProfile .clearSelectedValue {
                    position: absolute;
                    top: 5px;
                    right: 5px;
                }
                .editEntityProfile .password_wrapper {
                    position: relative;
                    top:3px;
                }
                .editEntityProfile .passwordStrength td:first-child {
                    width: 60px;
                    padding: 0 5px 0 0;
                    font-size: 12px;
                    text-align: right;
                }
                .editEntityProfile .passwordStrength td:last-child {
                    padding: 0; 
                }
                .editEntityProfile #password_strength_1603190947 {
                    width:100%;
                    height:16px;
                    display:block;
                    background:#000;
                    border-radius: 0 0 15px 0;
                }
                .editEntityProfile #password_strength_1603190947_bar {
                    height:16px;
                    background: #ff0000;
                    display:block;
                    padding-left:5px;
                    border-radius: 0 0 15px 0;
                }
                .editEntityProfile #password_strength_1603190947_bar span {
                    white-space: nowrap;
                    font-size: 11px;
                    position:absolute;
                    color:#fff;
                    text-transform:uppercase;
                }
                .userProfileTable td,
                .augmented-form-items td {
                    vertical-align:middle;
                    position:relative;
                }
                .augmented-form-items .passwordInput,
                .augmented-form-items .passwordStrength {
                    margin-bottom: 0;
                }
                .augmented-form-items .passwordInput td,
                .augmented-form-items .passwordStrength td {
                    padding:0;
                }
                .usernameTable td:nth-child(1) {
                    width:117px;
                    vertical-align: middle;
                }
                .usernameTable td:nth-child(2) {
                    width:455px;
                }
                .usernameTable td:nth-child(3) {
                    width:125px;
                    vertical-align: middle;
                }
                .passwordInput td:first-child input {
                    padding: 0 9px;
                    height: 28px;
                }
                .passwordInput td:last-child {
                    vertical-align: text-bottom;
                }
                .passwordInput > tr > td:last-child {
                    width: 40px;
                }
                .passwordInput td:last-child span.fas {
                    color: #fff;
                    background: #000;
                    padding: 10px;
                    border-radius: 45px;
                    margin-left: 5px;
                    cursor:pointer;
                }
                .username-error {
                    position: absolute;
                    right: 25px;
                    top: 26px;
                }
                
                .email-error,
                .phone-error {
                    position: absolute;
                    right: 25px;
                    top: 22px;
                }
                .queueWrapper {
                    padding: 5px 10px;
                    border: 1px rgba(0,0,0,.2) solid;
                    border-radius: 8px;
                }
            </v-style>'.($this->enableOriginator === true ? '
            <div class="augmented-form-items">
                <table class="table" style="margin-bottom:2px;">
                    <tr>
                        <td style="width:117px;vertical-align: middle;">Originator</td>
                        <td style="position:relative;">
                            <div class="dynamic-search">
                                <span class="inputpicker-arrow" @click="engageDynamicSearch" style="top: 20px;right: 21px;">
                                    <b></b>
                                </span>
                                <span v-if="userSearchResult !== \'\'" class="selected-user">{{ userSearchResult }} <span v-on:click="clearSelectedValue" class="clearSelectedValue general-dialog-close"></span></span>
                                <input v-on:focus="engageDynamicSearch" v-on:blur="hideDynamicSearch" v-model="userSearch" v-on:keyup="keyMonitorCustomerList" autocomplete="off" value="" placeholder="Start Typing..." class="form-control ui-autocomplete-input">
                                <div class="dynamic-search-list" style="position:absolute;" v-show="dynamicSearch === true && userSearchResult === \'\'">
                                    <table class="table">
                                        <thead>
                                            <th>User Id</th>
                                            <th>Name</th>
                                        </thead>
                                        <tbody>
                                            <tr v-for="currUser, index in cartCustomerSearchList" v-bind:class="{userSearchHighlight: userSearchMatchesIndex(index)}">
                                                <td @click="assignCustomerToCard(currUser, index)">{{currUser.user_id}}</td>
                                                <td @click="assignCustomerToCard(currUser, index)">{{currUser.first_name}} {{currUser.last_name}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                <div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>' : '').
            '<table class="table no-top-border userProfileTable">
                <tr>
                    <td style="width:125px;vertical-align: middle;">First Name</td>
                    <td><input v-model="entityClone.first_name" class="form-control" type="text" placeholder="Enter First Name..."></td>
                    <td style="width:125px;vertical-align: middle;">Last Name</td>
                    <td><input v-model="entityClone.last_name" class="form-control" type="text" placeholder="Enter Last Name..."></td>
                </tr>
                <tr>
                    <td style="width:125px;vertical-align: middle;">Phone</td>
                    <td><input v-on:blur="checkForDuplicateUserPhone(entityClone)" v-model="entityClone.user_phone_value" v-bind:class="{ \'pass-validation\': entityClone.user_phone_value }" id="phone_1603190947" class="form-control" type="text" placeholder="Enter Phone..."></td>
                    <td style="width:125px;vertical-align: middle;">E-mail</td>
                    <td><input v-on:blur="checkForDuplicateUserEmail(entityClone)" v-model="entityClone.user_email_value" v-bind:class="{ \'pass-validation\': entityClone.user_email_value }" id="email_1603190947" class="form-control" type="text" placeholder="Enter E-mail..."></td>
                </tr>
            </table>'.'
            '.($this->enableAccountEditing === true ? '
            <div class="augmented-form-items">
                <table class="table usernameTable" style="margin-bottom:2px;">
                    <tr>
                        <td>Username</td>
                        <td><input v-on:blur="checkForDuplicateUsername(entityClone)" v-model="entityClone.username" v-bind:class="{ \'pass-validation\': entityClone.username }"  id="username_1603190947" class="form-control" type="text" placeholder="Enter Username..."></td>
                        <td>Password</td>
                        <td>
                            <div class="password_wrapper">
                                <table class="table passwordInput">
                                    <tr>
                                        <td>
                                            <input v-on:keydown="checkForStrongPassword()" v-on:keyup="checkForStrongPassword()" v-model="password" id="password_1603190947" class="form-control" type="text" placeholder="Enter Password...">
                                            <table class="table passwordStrength">
                                                <tr>
                                                    <td>Strength:</td>
                                                    <td>
                                                        <span id="password_strength_1603190947">
                                                            <span id="password_strength_1603190947_bar" v-bind:style="{width: passwordStrength + \'%\'}">
                                                                <span>{{ passwordStrengthText }}</span>
                                                            </span>
                                                        </span>  
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td><span v-on:click="generateRandomPassword" class="fas fa-cog"></span></td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            '. $this->renderSetClassIfApplicable() . '
            '. $this->renderSetRolesIfApplicable() . '
            ' : '').
            '
            <table class="table no-top-border">
                <tr>
                    <td style="width:125px;vertical-align: middle;">Status</td>
                    <td>
                        <select v-model="entityClone.status" class="form-control">
                            <option value="Pending">Pending</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                            <option value="Cancelled">Cancelled</option>
                            <option value="Disabled">Disabled</option>
                        </select>
                    </td>
                </tr>
            </tbody></table>
            <button v-on:click="createNewCustomer" class="buttonID9234597e456 btn btn-primary w-100">{{ submitButton }}</button>
        </div>';
    }

    protected function renderSetClassIfApplicable() : string
    {
        if ($this->assignUserType === false) { return ""; }

        return '
            <table class="table no-top-border" style="margin-bottom: 0;">
                <tbody>
                    <tr>
                        <td style="width:125px;vertical-align: middle;">Class</td>
                        <td>
                            <div v-if="userSuperAdminRole === true" class="width33">
                                <ul>
                                    <li><label for="userEzAdminClass" class="pointer"><input v-model="entityClone.userClass" id="userEzAdminClass" value="1" type="radio" /> EZ Digital Admin</label></li>
                                    <li><label for="userEzTeamClass" class="pointer"><input v-model="entityClone.userClass" id="userEzTeamClass" value="2" type="radio" /> EZ Digital Team Member</label></li>
                                    <li><label for="userEzReadOnlyClass" class="pointer"><input v-model="entityClone.userClass" id="userEzReadOnlyClass" value="3" type="radio" /> EZ Digital Read-Only</label></li>
                                </ul>
                            </div>
                            <div v-if="userSuperAdminRole === true" class="width33">
                                <ul>
                                    <li><label for="userCpClientClass" class="pointer"><input v-model="entityClone.userClass" id="userCpClientClass" value="5" type="radio" /> Custom Platform Admin</label></li>
                                    <li><label for="userCpTeamMemberClass" class="pointer"><input v-model="entityClone.userClass" id="userCpTeamMemberClass" value="6" type="radio" /> Custom Platform Team Member</label></li>
                                    <li><label for="userCpReadOnlyClass" class="pointer"><input v-model="entityClone.userClass" id="userCpReadOnlyClass" value="7" type="radio"/> Custom Platform Read-Only</label></li>
                                </ul>
                            </div>
                            <div v-if="userAdminRole === true && userSuperAdminRole === false" class="width33">
                                <ul>
                                    <li><label for="userCpClientClass" class="pointer"><input v-model="entityClone.userClass" id="userCpClientClass" value="5" type="radio" /> Admin</label></li>
                                    <li><label for="userCpTeamMemberClass" class="pointer"><input v-model="entityClone.userClass" id="userCpTeamMemberClass" value="6" type="radio" /> Team Member</label></li>
                                    <li><label for="userCpReadOnlyClass" class="pointer"><input v-model="entityClone.userClass" id="userCpReadOnlyClass" value="7" type="radio"/> Read-Only</label></li>
                                </ul>
                            </div>
                            <div class="width33">
                                <ul>
                                    <li><label for="userThirdPartyClass" class="pointer"><input v-model="entityClone.userClass" id="userThirdPartyClass" value="8" type="radio"/> Third-Party Affiliate</label></li>
                                    <li><label for="userThirdPartyReadOnlyClass" class="pointer"><input v-model="entityClone.userClass" id="userThirdPartyReadOnlyClass" value="9" type="radio" /> Third-Party Read-Only</label></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        ';
    }

    protected function renderSetRolesIfApplicable() : string
    {
        if ($this->assignUserRoles === false) { return ""; }

        return '
            <div class="augmented-form-items">
                <table class="table usernameTable" style="margin-bottom:2px;">
                    <tr>
                        <td>Department</td>
                        <td colspan="3">
                            <select v-on:change="filterDepartmentData" v-model="entityClone.userDepartment" class="form-control">
                                <option disabled value="">-- Select a Department --</option>
                                <option v-for="currDepartment in customPlatformDepartments" v-bind:value="currDepartment.company_department_id">{{ currDepartment.label }}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Ticket Queues</td>
                        <td >
                            <label v-for="ticketQueue in customPlatformDepartmentQueuesList" class="pointer queueWrapper">
                                <input v-model="entityClone.userTicketQueues" type="checkbox" v-bind:value="ticketQueue.ticket_queue_id"> {{ ticketQueue.label }}
                            </label>
                            <span v-if="customPlatformDepartmentQueuesList.length === 0 && entityClone.userDepartment !== \'\'"> There are no queues for this department</span>
                            <span v-if="customPlatformDepartmentQueuesList.length === 0 && entityClone.userDepartment === \'\'"> Select a department to view its queue(s).</span>
                        </td>
                        <td><span v-if="customPlatformDepartmentQueuesList.length > 0">Role</span></td>
                        <td>
                            <select v-if="customPlatformDepartmentQueuesList.length > 0" v-model="entityClone.userDepartmentRole" class="form-control">
                                <option disabled value="">-- Select a Role --</option>
                                <option value="1">Team Member</option>
                                <option value="2">Manager</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
        ';
    }
}