<?php
    global $app;
    $cartId = \Entities\Cart\Components\Vue\CartWidget\CartWidget::getStaticId();
?>
{
    name: "registerForDirectoryComponent",
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
            screen: "",
            entity: {},
            entities: [],
            userId: "",
            userNum: "",
            directoryId: "",
            memberId: "",
            registerTitle: "Request Directory Membership",
            registerScreenTitle: "Request Directory Membership",
            directoryMembers: [],
            directoryPackages: [],
            directorySettings: [],
            directoryPackagesActive: false,
            directoryPackagesActiveCount: 0,
            directoryFreeRegistration: true,
            freeRegistration: {},
            directoryColumns: [],
            profileImageUploadUrl: "",
            submitButtonTitle: "",
            dynMemberDataSelector: null,
            dynMemberDataSelectorComponent: null,
            dynShoppingCart: null,
            dynShoppingCartComponent: null,
            registeringUser: {},
            personas: [],
            selectedPersonaId: null,
            selectedPersona: {},
            registeredPersona: {},
            personaUpdated: false,
            objMyCropper: null,
        }
    },
    created() {
        this.public = false;
        this.screen = "";
        this.submitButtonTitle = "";
        this.entity = {};
        this.entities = [];
        this.userId = 0;
        this.userUuid = '';
        this.directoryId = '';
        this.directoryMembers = [];
        this.directoryPackages = [];
        this.directorySettings = [];
        this.memberId = '';
        this.directoryColumns = [];
    },
    mounted: function()
    {
        dispatch.register("user_auth", this, "setUserAuth")
    },
    template: `
<div class="entityMemberDetailsInner">
    <v-style type="text/css">
        .entityMemberDetailsInner .persona-card {
            background:white;
            box-shadow: rgba(0,0,0,.3) 0 0 5px;
            padding: 15px;
        }
        .entityMemberDetailsInner .persona-card table {
            width: 100%;
        }
        .entityMemberDetailsInner .persona-card .persona-avatar {
            padding-right:15px;
            width: 115px;
        }
        .entityMemberDetailsInner .persona-card .persona-name {
            font-size:22px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .entityMemberDetailsInner .persona-card .persona-details {
            padding-top: 5px;
        }
        .entityMemberDetailsInner .persona-card.selectedPersona {
            border: 5px solid red;
        }
        .entityMemberDetailsInner .width250px {width: 250px;float:left;}
        .entityMemberDetailsInner .widthAutoTo250px {width: calc(100% - 250px);float:left; padding-left:15px;}
        .entityMemberDetailsInner .widthAutoTo250px table tr:first-child td {border-top:0}
        .entityMemberDetailsInner .card-full-height,
        .entityMemberDetailsInner .card-full-height > div,
        .entityMemberDetailsInner .card-full-height > div > div {
            min-height: 100%;
        }
    </v-style>
    <div v-if="screen === 'login'">
        <h4 style="text-align:center">{{ registerScreenTitle }}</h4>
        <div v-if="user.login === 'active'">
            <p style="text-align:center">Welcome, {{ registeringUser.first_name }}. Select the persona you'd like to use in registration for this Directory.</p>
            <div>
                <div class="persona-card" v-for="currPersona in personas" v-on:click="selectPersonaForDirectory(currPersona)" v-bind:class="{selectedPersona: selectedPersonaId === currPersona.card_id}">
                    <table>
                        <tbody>
                        <tr>
                            <td class="persona-avatar"><img v-bind:src="renderPersonaAvatar(currPersona)" width="100" height="100"></td>
                            <td>
                                <div class="persona-name">{{ currPersona.Settings.display_name }}</div>
                                <div class="persona-details">
                                    <div>{{ currPersona.Settings.contact_phone }}</div>
                                    <div>{{ currPersona.Settings.contact_email }}</div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <p style="background: #fff;padding: 10px; box-shadow: rgba(0,0,0,.2) 0 0 5px;" class="mt-4"><strong>NOTE:</strong> If you'd like to change the visible data, like the display name, phone or email, you can do so here. Please note, that if you've registered this persona with another directory, changes you make here will also impact other directories.</p>
                <button class="w-100 btn btn-primary mt-3 mb-3" v-on:click="reviewPersonaForDirectory" :disabled="selectedPersonaId === null">Review Persona Information</button>
            </div>
        </div>
        <p v-if="user.login !== 'active'" style="text-align:center">Login to your existing <?php echo $app->objCustomPlatform->getCompany()->company_name ?> Account</p>
        <div style="text-align:center"><b>-- OR --</b></div>
        <div style="text-align:center"><button class="btn btn-secondary mt-3" v-on:click="signupForPersona">Create a New Account Here</button></div>

        <div v-if="user.login !== 'active'">
            <input class="form-control mt-4" type="text" placeholder="Username or Email" v-model="entity.emailOnUsername">
            <input class="form-control mt-2" type="password" placeholder="Password" v-model="entity.password2">
            <button class="w-100 btn btn-primary mt-3" v-on:click="registerForDirectory">Register For The Directory!</button>
        </div>
    </div>
    <div v-if="screen === 'select-package'">
        <h4 style="text-align:center">Select a Directory Membership Package</h4>
        <div class="row">
            <div v-bind:class="directorPackageContainerClass" v-if="directoryFreeRegistration === true" class="card-full-height" v-on:click="loginForPersonaFree">
                <div class="card pointer">
                    <div class="card-img-top"></div>
                    <div class="card-body">
                        <h5 class="card-title">{{ freeRegistration.name }}</h5>
                        <div class="card-text">{{ freeRegistration.description }}</div>
                    </div>
                </div>
            </div>
            <div v-bind:class="directorPackageContainerClass" v-if="directoryPackages && currPackage.status === 'active'" v-for="currPackage in directoryPackages" class="card-full-height" v-on:click="purchasePackage(currPackage)">
                <div class="card pointer">
                    <div class="card-img-top"></div>
                    <div class="card-body">
                        <h5 class="card-title">{{ currPackage.name }}</h5>
                        <div class="card-text">{{ currPackage.description }}</div>
                        <div>{{ currPackage.price }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-show="screen === 'cart'">
        <h4 style="text-align:center">Purchase a Directory Membership Package</h4>
        <div class="pt-3">
            <component ref="dynShoppingCartComponentRef" :is="dynShoppingCartComponent"></component>
        </div>
    </div>
    <div v-if="screen === 'no-registration'">
        <h4 style="text-align:center">No Directory Membership At This Time</h4>
        <p style="text-align:center">Unfortunately, access to this directory has been disabled at this present time.</p>
        <button class="w-100 btn btn-primary mt-3" v-on:click="closeWindow">Close This Window</button>
    </div>
    <div v-if="screen === 'signup'">
        <h4 style="text-align:center">Create A New Account!</h4>
        <p style="text-align:center">Create a new account below! If you have previously registered for a <?php echo $app->objCustomPlatform->getCompany()->company_name ?> account with the email you supply, we can send a <a href="javascript:void(0)" v-on:click="passwordReset">password reset to your email</a>, allowing you to complete this registration!</p>
        <h3 class="configLabel"><span>Account Profile Details</span></h3>
        <div>
            <input class="form-control mt-4" type="text" placeholder="Email" v-model="entity.email">
            <input class="form-control mt-2" type="password" placeholder="Password" v-model="entity.password">
            <input class="form-control mt-4" type="text" placeholder="First Name" v-model="entity.firstName">
            <input class="form-control mt-2" type="text" placeholder="Last Name" v-model="entity.lastName">
            <input class="form-control mt-4 mb-4" type="text" placeholder="Mobile Phone" v-model="entity.phone">
            <p style="background: #fff;padding: 10px; box-shadow: rgba(0,0,0,.2) 0 0 5px;"><strong>NOTE:</strong> By creating a new account, you can register with this directory and receive a free <?php echo $app->objCustomPlatform->getCompany()->company_name ?> Persona! This will allow you to customize your public profile and keep the private stuff where it should be. Don't worry, we'll help you set it up right here!</p>
            <button class="w-100 btn btn-primary mt-3" v-on:click="createNewAccount">Create A New Account!</button>
            <p class="mt-4">After creating your free account and free persona, we're going to have you add some basic data to it. This will include a photo for the directory, so make sure you are ready! If you don't have one available, you can always add it later via your <?php echo $app->objCustomPlatform->getCompany()->company_name ?> account.</p>
        </div>
    </div>
    <div v-if="screen === 'password-reset'">
        <h4 style="text-align:center">Request A Password Reset</h4>
        <p>If you already have an account with us, never fear. We just need to get your login credentials up to date!</p>
        <div>
            <input class="form-control mt-4" type="text" placeholder="Email" value="">
            <div style="text-align:center" class="mt-2"><b>-- OR --</b></div>
            <input class="form-control mt-2" type="text" placeholder="Phone" value="">
            <p style="background: #fff;padding: 10px; box-shadow: rgba(0,0,0,.2) 0 0 5px;" class="mt-4"><strong>NOTE:</strong> We're going to send you an email / text message with a passcode. Bring it here and we'll reset your account password right away!</p>
            <button class="w-100 btn btn-primary mt-3" v-on:click="requestPasswordReset">Request Password Reset</button>
        </div>
    </div>
    <div v-if="screen === 'no-active-personas'">
        <h4 style="text-align:center">No Active Personas!</h4>
        <p style="text-align:center"><b>Well, this is unfortunate.</b> You do not have any <b>ACTIVE PERSONAS</b> attached to your account! We strongly recommend <a href="">logging in to your account</a> and finishing your persona setup.</p>
        <button class="w-100 btn btn-primary mt-3" v-on:click="closeWindow">Close This Window</button>
    </div>
    <div v-if="screen === 'select-persona'">
        <h4 style="text-align:center">Select The Persona To Register With</h4>
        <p style="text-align:center">Awesome! You're nearly done, {{ registeringUser.first_name }}. Just select the persona you'd like to use for the Directory.</p>
        <div>
            <div class="persona-card" v-for="currPersona in personas" v-on:click="selectPersonaForDirectory(currPersona)" v-bind:class="{selectedPersona: selectedPersonaId === currPersona.card_id}">
                <table>
                    <tbody>
                        <tr>
                            <td class="persona-avatar"><img v-bind:src="renderPersonaAvatar(currPersona)" width="100" height="100"></td>
                            <td>
                                <div class="persona-name">{{ currPersona.Settings.display_name }}</div>
                                <div class="persona-details">
                                    <div>{{ currPersona.Settings.contact_phone }}</div>
                                    <div>{{ currPersona.Settings.contact_email }}</div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p style="background: #fff;padding: 10px; box-shadow: rgba(0,0,0,.2) 0 0 5px;" class="mt-4"><strong>NOTE:</strong> If you'd like to change the visible data, like the display name, phone or email, you can do so here. Please note, that if you've registered this persona with another directory, changes you make here will also impact other directories.</p>
            <button class="w-100 btn btn-primary mt-3" v-on:click="reviewPersonaForDirectory" :disabled="selectedPersonaId === null">Review Persona Information</button>
        </div>
    </div>
    <div v-if="screen === 'review-persona'">
        <h4 style="text-align:center">Review Your Directory Persona</h4>
        <p style="text-align:center">Is this information acceptable, {{ registeringUser.first_name }}? Making a change here will impact other directories.</p>
        <div>
            <div class="persona-card-review">
                <div class="width250px">
                    <div class="memberAvatarImage">
                        <div class="slim" data-ratio="1:1" data-force-size="650,650" v-bind:data-service="profileImageUploadUrl" id="my-cropper" style="background-image: url(/_ez/images/users/defaultAvatar.jpg); background-size: cover;background-position: center;">
                            <input type="file"/>
                            <img width="250" height="250" alt="">
                        </div>
                    </div>
                </div>
                <div class="widthAutoTo250px">
                    <table class="table no-top-border">
                        <tbody>
                        <tr>
                            <td style="width:100px;vertical-align: middle;">Display Name</td>
                            <td>
                                <input v-model="selectedPersona.Settings.display_name"class="form-control" type="text" placeholder="">
                            </td>
                        </tr>
                        <tr>
                            <td style="width:100px;vertical-align: middle;">Phone</td>
                            <td>
                                <input v-model="selectedPersona.Settings.contact_phone" class="form-control" type="text" placeholder="">
                            </td>
                        </tr>
                        <tr>
                            <td style="width:100px;vertical-align: middle;">Email</td>
                            <td>
                                <input v-model="selectedPersona.Settings.contact_email" class="form-control" type="text" placeholder="">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button class="w-100 btn btn-secondary mt-3" v-on:click="updatePersonaInformation">Update Persona Information</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div style="clear:both;"></div>
            </div>
            <p style="background: #fff;padding: 10px; box-shadow: rgba(0,0,0,.2) 0 0 5px;" class="mt-4"><strong>NOTE:</strong> Just to reiterate, <b>making a change here will impact other directories</b> this persona might be registered with. If you would like to register with this directory, but have different information than your existing personas, you can always <a href="">purchase a new persona!</a></p>
            <button class="w-100 btn btn-primary mt-3" v-on:click="assignPersonaToDirectory">Assign Persona to Directory</button>
        </div>
    </div>
    <div v-if="screen === 'registration-confirmation'">
        <h4 style="text-align:center">Congratulations!</h4>
        <p style="text-align:center">Your persona has been registered with this directory, {{ registeringUser.first_name }}. The directory owner may need to approve it for it to become visible, but not to worry we've sent them a notification and they will respond soon.</p>
        <div>
            <div class="persona-card">
                <table>
                    <tbody>
                    <tr>
                        <td class="persona-avatar"><img v-bind:src="renderPersonaAvatar(selectedPersona)" width="100" height="100"></td>
                        <td>
                            <div class="persona-name">{{ selectedPersona.Settings.display_name }}</div>
                            <div class="persona-details">
                                <div>{{ selectedPersona.Settings.contact_phone }}</div>
                                <div>{{ selectedPersona.Settings.contact_email }}</div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <button class="w-100 btn btn-primary mt-3" v-on:click="closeWindow">Close This Window</button>
        </div>
    </div>
    <div v-if="screen === 'already-registered'">
        <h4 style="text-align:center">You Are Already Registered For This Directory!</h4>
        <p style="text-align:center">One of your personas has been previously registered with this directory, {{ registeringUser.first_name }}. To manage it, go to your persona's profile and review the Directories tab.</p>
        <div>
            <div class="persona-card">
                <table>
                    <tbody>
                    <tr>
                        <td class="persona-avatar"><img v-bind:src="renderPersonaAvatar(registeredPersona)" width="100" height="100"></td>
                        <td>
                            <div class="persona-name">{{ registeredPersona.Settings.display_name }}</div>
                            <div class="persona-details">
                                <div>{{ registeredPersona.Settings.contact_phone }}</div>
                                <div>{{ registeredPersona.Settings.contact_email }}</div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <button class="w-100 btn btn-primary mt-3" v-on:click="closeWindow">Close This Window</button>
        </div>
    </div>
</div>`,
    methods: 
    {
        hydrateComponent: function(props, show, callback)
        {
            let self = this
            self.directoryId = self.page.__app.instance_uuid
            if (self.user.login === "active") {
                this.loadActiveUserPersonas()
            }
            self.dynMemberDataSelectorComponent = self.dynMemberDataSelector
            this.$forceUpdate()
            this.loadDirectorySettings()
            this.setDefaultScreen()
            vueApp.vc.loadComponentByStaticId("<?php echo $cartId; ?>","", "view", {}, [], props, false, true, function(component) {
                self.dynShoppingCart = component.rawInstance
                self.dynShoppingCartComponent = self.dynShoppingCart;
            });
        },
        loginForPersona: function()
        {
            this.screen = "login"
            this.registerScreenTitle = this.registerTitle
            this.$forceUpdate()
        },
        loginForPersonaFree: function()
        {
            this.screen = "login"
            this.registerScreenTitle = this.freeRegistration.name
            this.$forceUpdate()
        },
        activeCart: function()
        {
            this.screen = "cart"
            this.$forceUpdate()
        },
        signupForPersona: function()
        {
            this.screen = "signup"
            this.$forceUpdate()
        },
        selectPersona: function()
        {
            this.screen = "select-persona"
            this.$forceUpdate()
        },
        noActivePersonas: function()
        {
            this.screen = "no-active-personas"
            this.$forceUpdate()
        },
        reviewPersona: function()
        {
            this.screen = "review-persona"
            this.$forceUpdate()
        },
        passwordReset: function()
        {
            this.screen = "password-reset"
            this.$forceUpdate()
        },
        personaConfirmation: function()
        {
            this.screen = "registration-confirmation"
            dispatch.broadcast("reload_directory_main_view", {});
            this.$forceUpdate()
        },
        alreadyRegistered: function()
        {
            this.screen = "already-registered"
            this.$forceUpdate()
        },
        requestPasswordReset: function()
        {
            modal.EngageFloatShield()
            ajax.PostExternal("/api/v1/directories/public-full-page/request-password-reset?id=" + this.directoryId, "", true, function(result) {
                modal.CloseFloatShield()
            });
        },
        registerForDirectory: function()
        {
            const self = this;
            modal.EngageFloatShield();
            // validate fields...
            const postData = {
                email: this.entity.emailOnUsername,
                password: this.entity.password2,
            }
            ajax.PostExternal("/api/v1/directories/public-full-page/register-for-directory?id=" + this.directoryId, postData, true, function(result) {
                modal.CloseFloatShield(function() {
                    if (result.success === false) {
                        self.throwErrors(result)
                        return
                    }
                    const loginUser = {browserId: Cookie.get("instance"), username: postData.email,  password: postData.password};
                    ajax.PostExternal("/api/v1/users/log-user-into-core", loginUser, true, function(loginResult) {
                        if (loginResult.success === false) {
                            self.throwErrors(loginResult)
                            return
                        }

                        const userData = {
                            userId: loginResult.response.data.user.id,
                            userNum: loginResult.response.data.user.user_id,
                            user: JSON.stringify(loginResult.response.data.user),
                            isLoggedIn: "active",
                            authUserId: loginResult.response.data.user.id
                        }

                        Cookie.set("user", JSON.stringify(loginResult.response.data.user))
                        dispatch.broadcast("user_auth", userData)

                        self.userId = loginResult.response.data.user.user_id
                        self.userUuid = loginResult.response.data.user.id

                        if (result.response.data.already_registered) {
                            self.registeredPersona = result.response.data.persona
                            self.registeringUser = result.response.data.user
                            self.alreadyRegistered()
                            return;
                        }

                        if (result.response.data.personas.length === 0) {
                            self.registeringUser = result.response.data.user
                            self.noActivePersonas()
                            return;
                        }

                        self.registeringUser = result.response.data.user
                        self.personas = result.response.data.personas
                        self.selectPersona()
                    });
                }, 1000)
            });
        },
        purchasePackage: function(package)
        {
            this.$refs.dynShoppingCartComponentRef.assignParentEntityToCartById("custom")
            this.$refs.dynShoppingCartComponentRef.previousProductClass = ""
            this.$refs.dynShoppingCartComponentRef.displayComponent = "shoppingCart"
            this.$refs.dynShoppingCartComponentRef.setAppWrapperClass("cart")
            this.$refs.dynShoppingCartComponentRef.setCartPrivacy(true)
            this.$refs.dynShoppingCartComponentRef.registerCustomerByUuid(this.user.uuid)
            this.$refs.dynShoppingCartComponentRef.inModal = true
            this.$refs.dynShoppingCartComponentRef.addPackageToCartById(package.package_id)
            this.activeCart()
        },
        selectPersonaForDirectory: function(persona)
        {
            this.selectedPersonaId = persona.card_id
            this.selectedPersona = persona
            this.setProfileImageUploadUrl()
        },
        reviewPersonaForDirectory: function()
        {
            const self = this;
            if (this.screen != "review-persona") {
                self.reviewPersona()
            }
            self.objMyCropper = document.getElementById("my-cropper")
            if (this.objMyCropper === null) {
                setTimeout(function() {
                    self.reviewPersonaForDirectory();
                },10);
                return;
            }

            Slim.destroy(this.objMyCropper)
            this.loadComponentData(this.selectedPersona, this.objMyCropper)
        },
        createNewAccount: function()
        {
            modal.EngageFloatShield()
            // validate fields...

            const postData = {
                firstName: this.entity.firstName,
                lastName: this.entity.lastName,
                email: this.entity.email,
                phone: this.entity.phone,
                password: this.entity.password,
            }
            ajax.PostExternal("/api/v1/directories/public-full-page/create-new-account?id=" + this.directoryId, postData, true, function(result){
                modal.CloseFloatShield()
            });
        },
        closeWindow: function()
        {
            modal.CloseFloatShield();
        },
        throwErrors: function()
        {

        },
        loadDirectorySettings: function()
        {
            this.directoryFreeRegistration = true
            for (const key in this.directorySettings) {
                if (this.directorySettings[key].label === "free_package_status" && this.directorySettings[key].value !== "active") {
                    this.directoryFreeRegistration = false
                }
            }
            if (this.directoryFreeRegistration === true) {
                this.freeRegistration.name = this.directorySettings["free_package_title"] ? this.directorySettings["free_package_title"].label : "Free Registration"
                this.freeRegistration.description = this.directorySettings["free_package_description"] ? this.directorySettings["free_package_description"].label : "No charge to sign up!"
            }
            ezLog(this.directoryFreeRegistration, "directoryFreeRegistration")
            ezLog(this.directorySettings, "directorySettings")
            ezLog(this.directoryPackages, "directoryPackages")
        },
        loadActiveUserPersonas: function()
        {
            const self = this
            self.userId = this.user.id
            self.userUuid = this.user.uuid
            ajax.GetExternal("/api/v1/users/get-user-personas?id=" + this.user.id, true, function(result) {
                if (result.success === false) {
                    return
                }
                self.personas = result.response.data.personas
                self.registeringUser = result.response.data.user
            });
        },
        setDefaultScreen: function()
        {
            this.directoryPackagesActive = false;
            this.directoryPackagesActiveCount = 0
            for (const key in this.directoryPackages) {
                if (this.directoryPackages[key].status === "active") {
                    this.directoryPackagesActive = true
                    this.directoryPackagesActiveCount++
                }
            }
            if (this.directoryFreeRegistration && !this.directoryPackagesActive) {
                this.screen = "login"
            } else if (!this.directoryFreeRegistration || this.directoryPackagesActive) {
                this.screen = "select-package"
            } else {
                this.screen = "no-registration"
            }
        },
        renderPersonaAvatar: function(persona)
        {
            if (persona.Settings.avatar) {
                return imageServerUrl() + persona.Settings.avatar;
            }
            return "/_ez/images/users/defaultAvatar.jpg";
        },
        setProfileImageUploadUrl: function()
        {
            this.profileImageUploadUrl = '/api/v1/media/upload-image?entity_id=' + this.selectedPersonaId + '&user_id=' + this.userId + '&uuid=' + this.userUuid + '&entity_name=persona&class=persona-avatar';
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
            
            ajax.PostExternal("/api/v1/directories/public-full-page/get-directory-id?id=" + directoryUuid, "", true, function(result){
                self.directoryId = result.data.id;
                callback(self);
            });
        },
        loadComponentData: function(entity, objMyCropper)
        {
            const self = this;
            let cropperChildren = objMyCropper.getElementsByTagName("img");
            
            if (typeof entity.Settings.avatar === "string" && entity.Settings.avatar !== "") {
                cropperChildren[0].src = imageServerUrl() + entity.Settings.avatar;
            } else {
                cropperChildren[0].removeAttribute("src");
            }

            Slim.create(
                objMyCropper,
                Slim.getOptionsFromAttributes(objMyCropper, {browseButton: false, uploadButton: false, }),
                {app: self, method: "updateEntityAvatar"},
                {app: self, method: "removeEntityAvatar"}
            );

            Slim.setUploadUrl(objMyCropper, self.profileImageUploadUrl);
        },
        updateEntityAvatar: function(imageData)
        {
            let cropperChildren = this.objMyCropper.getElementsByTagName("img");
            const url = imageData.path
            if (cropperChildren[0]) {
                cropperChildren[0].src = imageServerUrl() + url
                this.selectedPersona.Settings.avatar = url
                const postUrl = "/api/v1/cards/save-persona-avatar-url?persona=" + this.selectedPersonaId
                const postData = {avatar_url: url}
           
                ajax.PostExternal(postUrl, postData, true, function(result) {
                    ezLog(result, "updateEntityAvatar");
                });
            }
        },
        removeEntityAvatar: function()
        {
            this.selectedPersona.Settings.avatar  = "__remove__";
        },
        updatePersonaInformation: function(callback, floatShield)
        {
            const self = this;
            let cropperChildren = this.objMyCropper.getElementsByTagName("img");
            if (cropperChildren[0]) {
                if (cropperChildren[0].src === (imageServerUrl() + this.selectedPersona.Settings.avatar)) {
                    if (typeof callback === "function") {
                        callback()
                    }
                }
            }

            if (floatShield) modal.EngageFloatShield()
            // validate fields...
            const postData = this.selectedPersona.Settings
            ajax.PostExternal("/api/v1/cards/update-persona-data?id=" + this.selectedPersona.card_id, postData, true, function(result) {
                if (result.success === false) {
                    if (floatShield)  modal.CloseFloatShield()
                    self.throwErrors(result)
                    return
                }

                Slim.save(self.objMyCropper, function() {
                    if (floatShield) modal.CloseFloatShield()
                    if (typeof callback === "function") {
                        callback()
                    }
                });
            });
        },
        assignPersonaToDirectory: function()
        {
            if (this.selectedPersonaId === null) return;
            const self = this;
            modal.EngageFloatShield()
            self.updatePersonaInformation(function() {
                ajax.PostExternal("/api/v1/directories/public-full-page/assign-persona-to-directory?id=" + self.directoryId + "&user=" + self.registeringUser.user_id + "&persona=" + self.selectedPersonaId, {}, true, function(result) {
                    if (result.success === false) {
                        self.throwErrors(result)
                        modal.CloseFloatShield( function() {}, 1500)
                        return
                    }

                    modal.CloseFloatShield( function() { self.personaConfirmation() }, 1000)
                });
            }, false)
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
    },
    computed: {
        directorPackageContainerClass: function() {
            let numColumn = this.directoryFreeRegistration ? 1 : 0
            let numColSm = 1
            numColumn = numColumn + this.directoryPackagesActiveCount
            if ((numColumn/3) == 3 || (numColumn/3) == 2 || (numColumn/3) == 1) { numColumn = 4; numColSm = 6; }
            else if ((numColumn/4) == 3 || (numColumn/4) == 2 || numColumn >= 4) { numColumn = 3; numColSm = 6; }
            else { numColumn = 6; numColSm = 12; }
            const className = 'col-lg-' + numColumn
            const classNameSm = 'col-sm-' + numColSm
            return [className, classNameSm];
        }
    }
}
