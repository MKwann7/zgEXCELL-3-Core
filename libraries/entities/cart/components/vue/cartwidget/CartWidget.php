<?php

namespace Entities\Cart\Components\Vue\CartWidget;

use App\Core\App;
use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cards\Components\Vue\CardWidget\ListCardWidget;
use Entities\Cards\Components\Vue\CardWidget\ManageCardWidget;
use Entities\Cart\Models\CartModel;

class CartWidget extends VueComponent
{
    protected $id = "f878dc91-2ed7-4252-b5d8-ac25e92dabb8";

    public function __construct(array $components = [])
    {
        $defaultEntity = (new CartModel())
            ->setDefaultSortColumn("cart_id", "DESC")
            ->setDisplayColumns(["platform", "company_name", "portal_domain", "public_domain", "owner", "cards", "state", "country", "created_on", "last_updated"])
            ->setRenderColumns(["cart_id","platform", "company_name", "portal_domain", "public_domain", "owner", "cards", "state", "country", "created_on", "last_updated", "sys_row_id"]);

        parent::__construct($defaultEntity, $components);

        $this->modalTitleForAddEntity = "Shopping Cart";
        $this->modalTitleForEditEntity = "Shopping Cart";
        $this->modalTitleForDeleteEntity = "Shopping Cart";
        $this->modalTitleForRowEntity = "Shopping Cart";

        $this->setDefaultAction("view");
    }

    protected function renderComponentDataAssignments() : string
    {
        return '
            rootUrl: "",
            cartType: false,
            cartFuncType: 0,
            parentEntity: {id: 0, type: "unknown"},
            previousProductClass: "",
            cloningCart: false,
            cartItems: [],
            cartDisplayItemsForComplete: [],
            cartUser: null,
            cartUserId: null,
            userList: [],
            affiliateList: [],
            newUserForCreate: {affiliate_id:"", first_name: "", last_name: "", email: "", phone: "", username: "",  password: ""},
            newCardForCreate: {name:"", number: "", expMonth: "", expYear: "", cvc: "", line1: "",  line2: "",  state: "", zip: "", country: ""},
            userSearch: "",
            userSearchResult: "",
            userSearchHighlight: 0,
            searchBox: 0,
            searchBoxInner: 0,
            totalSearchDisplayCount: 0,
            dynamicSearch: false,
            displayComponent: "selectPackagesByClass",
            packageList: [],
            customerPaymentAccounts: [],
            checkoutUserId: null,
            cartPaymentAccount: null,
            toggleCreateNewCartPaymentAccount: false,
            creatingNewCustomer: false,
            creditCartType: "other",
            creditCardTest: {visa: "^4[0-9]{0,15}$", mc: "^5[0-9]{0,15}$", amex: "^3[47][0-9]{0,13}$", disc: "^6(?:011|5[0-9]{2})[0-9].*$", diners: "^3(?:0[0-5]|[68][0-9])[0-9].*$", jcb: "^(?:2131|1800|35[0-9]{3})[0-9].*$", other: "[0-9]{6,}$"},
            promoCodeList: [],
            cartPromoCodeSearch: null,
            activeCartPromoCode: null,
            completedDisplayCartPromoCode: null,
            applyCartPromoCode: false,
            applyCompletedCartPromoCode: false,
            termsAndAgreementAcceptence: false,
            cardList: [],
            errorText: {},
            listId: null,
            managerId: null,
            password: "",
            passwordStrengthText: "6 character minimum",
            passwordStrength: 0,
            ENUM_CardType: 1,
        ';
    }

    protected function renderComponentMethods() : string
    {
        /** @var App $app */
        global $app;

        return '
            selectPackagesByClass: function(name, public, parentEntity, userId)
            {
                const self = this
                const vc = this.findVc(this)
                this.setRootDomain();
                this.processDefaultTitle(vc, name)
                this.setCartDefaults(public)
                this.setCartSource(name, parentEntity, userId);
                
                return self;
            },
            setCartSource: function(name, parentEntity, userId)
            {
                if (typeof parentEntity === "undefined" || parentEntity.type !== "user")
                {
                    this.cartFuncType = "card_nouser"; 
                }
                else
                {
                    this.cardEntityBind(name, parentEntity, userId)
                }
                
                if (this.previousProductClass !== name)
                {
                    this.packageList = [];
                    this.previousProductClass = name;
                    this.loadPackagesByClass(name)
                }
            },
            cardEntityBind: function(name, parent, userId)
            {
                this.cartFuncType = name;                
                this.assignParentEntityToCartById(parent);
                this.cartUserId = userId;
                this.assignUserToCardById(userId);
            },
            cartIsCardProcess: function()
            {
                switch(this.cartFuncType)
                {
                    case "card":
                    case "card_nouser":
                        return true;
                    default:
                        return false; 
                }
            },
            cartIsNotCardProcess: function()
            {
                switch(this.cartFuncType)
                {
                    case "card":
                    case "card_nouser":
                        return false;
                    default:
                        return true; 
                }
            },
            cartIsMyCardProcess: function()
            {
                if (this.cartFuncType !== "card")
                {
                    return false;      
                }
                
                return true; 
            },
            cartIsAllCardProcess: function()
            {
                if (this.cartFuncType !== "card_nouser")
                {
                    return false;      
                }
                
                return true; 
            },
            cartIsCardAppProcess: function()
            {
                if (this.cartFuncType !== "card app")
                {
                    return false;      
                }
                
                return true; 
            },
            cartIsCardPageProcess: function()
            {
                if (this.cartFuncType !== "card page")
                {
                    return false;      
                }
                
                return true; 
            },
            processDefaultTitle: function(vc, name)
            {
                vc.showModal(this, function() {
                    vc.setTitle("Select a " + ucwords(name) + " Package");
                });
            },
            setCartDefaults: function(public)
            {
                if (typeof public !== "undefined")
                {
                    this.cartType = public;
                }
                
                this.displayComponent = "selectPackagesByClass";
                this.cartItems = [];
                this.setAppWrapperClass("products");
            },
            setRootDomain: function()
            {
                this.rootUrl = "'.getFullPortalUrl().'";
                
                if (this.cartType === true) 
                {
                    this.rootUrl = "'.getFullPublicUrl().'";
                }
            },
            registerEntityListAndManager: function(listId, managerId)
            {
                this.listId = listId;
                this.managerId = managerId;
            },
            loadPackagesByClass: function(name)
            {
                const self = this;
                
                console.log(this.rootUrl);
                
                let url = this.rootUrl + "/cart/get-packages-by-class-name?name=" + name;

                ajax.GetExternal(url, true, function(result) 
                {
                    if (result.success === false || (typeof result.response !== "undefined" && result.response.success === false))
                    {
                        self.packageList = [];
                        return;
                    }
                    
                    self.packageList = result.response.data.list;
                    
                    for (let currPackageIndex in self.packageList)
                    {
                        self.packageList[currPackageIndex].line = _.orderBy(self.packageList[currPackageIndex].line, "order", "asc");
                    }
                });
            },
            loadCartPackgesByExistingEntityId: function(name, id)
            {
                switch(name)
                {
                    case "card":
                        return this.loadCartPackagesByExistingCardId(id);
                    default:
                        return;
                }
            },
            loadCartPackagesByExistingCardId: function(id)
            {
                const self = this;
                self.cartItems = [];
                
                ajax.GetExternal("' . $app->objCustomPlatform->getFullPortalDomain() . '/cart/get-packages-from-existing-card?id=" + id, true, function(result) 
                {
                    if (result.success === false || (typeof result.response !== "undefined" && result.response.success === false))
                    {
                        return;
                    }
                    
                    for (let currCardPackage in result.response.data.list)
                    {
                        self.addPackageToCart(currCardPackage);
                    }
                });
            },
            getPackageFromCart: function(package)
            {
                for (let currPackage of this.cartItems)
                {
                    if (currPackage.package_id === package.package_id)
                    {
                        return currPackage;
                    }
                }
                
                return null;
            },
            addPackageToCart: function(package)
            {
                package.quantity = this.fixUndefined(package.quantity);
                package.max_quantity = this.fixUndefined(package.max_quantity);
                
                const existingPackage = this.getPackageFromCart(package);
                
                if (existingPackage !== null)
                {
                    if (package.max_quantity > 0 && existingPackage.quantity >= package.max_quantity)
                    {   
                        return;
                    }
                    
                    existingPackage.quantity = existingPackage.quantity + 1;
                    
                } else
                {
                    const newPackage = _.clone(package);
                    newPackage.quantity += 1;
                    this.cartItems.push(newPackage);
                }
                
                modal.EngageFloatShield();
                
                this.$forceUpdate();
                
                setTimeout(function() 
                {
                    modal.CloseFloatShield();
                    
                }, 250);
            },
            addPackageAndGoToCart: function(package)
            {
                if (!this.productCartContainsCard(this.cartItems) && ! this.packageContainsCard(package) && this.cartIsCardProcess()) 
                { 
                    return;
                }
                
                package.quantity = this.fixUndefined(package.quantity);
                package.max_quantity = this.fixUndefined(package.max_quantity);
                
                const existingPackage = this.getPackageFromCart(package);
                
                if (existingPackage !== null)
                {
                    if (package.max_quantity > 0 && existingPackage.quantity >= package.max_quantity)
                    {
                        return;
                    }
                    
                    existingPackage.quantity = existingPackage.quantity + 1;
                    
                } else
                {
                    const newPackage = _.clone(package);
                    newPackage.quantity += 1;
                    this.cartItems.push(newPackage);
                }
                
                modal.EngageFloatShield();
                let data = {title: "Adding To Cart", html: "We\'re adding this item to your cart.<br>Please wait a moment."};
                modal.EngagePopUpDialog(data, 350, 115, false);
                modal.SetLastModalClass(".zgpopup-dialog-box-inner", "dialog-right-loading-anim");
                const self = this;
                const vc = this.findVc(this);
                
                setTimeout(function() 
                {
                    modal.RemoveLastModalClass(".zgpopup-dialog-box-inner", "dialog-right-loading-anim");
                    modal.SetLastModalElementHtml("span.pop-up-dialog-main-title-text", "Success!");

                    let data = {html: "Success! We\'ve added your item to the cart..."};
                    modal.AddFloatDialogMessage(data, "checkbox");
                    
                    setTimeout(function() 
                    {
                        vc.showModal(self);
                        
                        if (self.cartIsCardProcess())
                        {
                            self.openAssignUser(false);
                        }
                        else
                        {
                            self.openCheckout(false);
                        }
                        
                    }, 750);
                    
                }, 750);
                
                return;
                
                ajax.PostExternal(this.rootUrl + "/cart/add-package-to-cart?id=" + package.package_id, {}, true, function(result) 
                {
                    if (result.success === false || (typeof result.response !== "undefined" && result.response.success === false))
                    {
                        self.packageList = [];
                        return;
                    }
                    
                    self.packageList = result.response.data.list;
                });

            },
            openCart: function(options)
            {
                if (!this.productCartContainsCard(this.cartItems) && ( this.cartIsCardProcess() && options.className !== false)) 
                { 
                    return;
                }
                
                if (options.className === false) { this.previousProductClass = \'\'; }
                
                const self = this;
                (this.findVc(this)).showModal(this);
                this.displayComponent = "shoppingCart";
                this.setAppWrapperClass("cart");
            },
            setAppWrapperClass: function(className)
            {
                const appCart = classFirstGlobal("appCartWrapper");
                appCart.classList.remove("products")
                appCart.classList.remove("cart")
                appCart.classList.remove("checkout")
                appCart.classList.remove("completed")
                appCart.classList.remove("user")
                appCart.classList.add(className);
            },
            backToProductSelection: function()
            {
                this.selectPackagesByClass(this.previousProductClass, this.cartType, this.parentEntity, this.cartUserId);
            },
            openAssignUser: function(useFloat)
            {
                const self = this;
                
                if (!this.productCartContainsCard(this.cartItems) && this.cartIsCardProcess())
                {
                    return;
                }
                
                if (typeof useFloat === "undefined" || useFloat === true)
                {
                    modal.EngageFloatShield()
                }
                
                ezLog("openAssignUser")
                ezLog(this.cartIsAllCardProcess())
                ezLog(this.cartType)
                ezLog(this.cartUser)
  
                if (this.cartIsAllCardProcess())
                {                
                    setTimeout(function() 
                    {
                        const vc = self.findVc(self)
                        
                        vc.showModal(self, function() {
                            vc.setTitle("Select a Customer")
                            self.loadUsers()
                        });
                        
                        if (self.displayComponent === "checkout" || self.cartUser === null || (self.cartType !== true && self.cartIsAllCardProcess() ) )
                        {
                            self.cartUser = null
                            self.setAppWrapperClass("user")
                            self.displayComponent = "assignUser"
                        }
                        else
                        {
                            
                            self.openCheckout()
                        }
                        
                        modal.CloseFloatShield()
                    }, 750);
                }
                else
                {
                    self.openCheckout(true)
                }
            },
            assignParentEntityToCartById: function(entity)
            {
                this.parentEntity = entity
            },
            assignUserToCart: function(user)
            {
                modal.EngageFloatShield()
                this.assignUserToCardById(user.user_id);
                const vc = this.findVc(this);
                vc.setTitle("Confirm Customer Selection");
            },
            assignUserToCardById: function(userId)
            { 
                const self = this;
                ajax.GetExternal(this.rootUrl + "/cart/get-user-information?id=" + userId, true, function(result) 
                {
                    if (result.success === false || (typeof result.response !== "undefined" && result.response.success === false))
                    {
                        self.clearCartUser();
                        return;
                    }
                    
                    self.cartUser = result.response.data.user;
                    self.dynamicSearch = false;
                    self.userSearch = "";
                    self.userSearchResult = "";
                    
                    modal.CloseFloatShield()                    
                });
            },
            assignAffiliateToUser: function(user)
            {
                this.userSearch = user.first_name + " " + user.last_name;
                this.newUserForCreate.affiliate_id = user.user_id;
                this.dynamicSearch = false;
            },
            clearCartUser: function()
            {
                this.cartUser = null;
                this.userSearch = "";
                this.userSearchResult = "";
                const vc = this.findVc(this);
                vc.setTitle("Select a Customer");
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
                }, 500);
            },
            loadUsers: function()
            {
                const self = this;
                this.userList = [];
                
                ajax.GetExternal(this.rootUrl + "/cart/get-all-users", true, function(result) 
                {
                    if (result.success === false || (typeof result.response !== "undefined" && result.response.success === false))
                    {
                        return;
                    }
                    
                    const users = Object.entries(result.response.data.list);
                    users.forEach(function([user_id, currUser])
                    {
                        self.userList.push(currUser);
                    });
                    
                    self.$forceUpdate();
                });
            },
            loadAffiliates: function(callback)
            {
                const self = this;
                this.affiliateList = [];
                
                ajax.GetExternal(this.rootUrl + "/cart/get-all-affiliates", true, function(result) 
                {
                    if (result.success === false || (typeof result.response !== "undefined" && result.response.success === false))
                    {
                        return;
                    }
                    
                    const users = Object.entries(result.response.data.list);
                    users.forEach(function([user_id, currUser])
                    {
                        self.affiliateList.push(currUser);
                    });
                    
                    self.$forceUpdate();
                });
            },
            toggleCreateNewCustomer: function()
            {
                this.loadAffiliates();
                this.creatingNewCustomer = true;
                this.dynamicSearch = false;
                this.userSearch = "";
            },
            loginCustomer: function()
            {
                let newUser = this.newUserForCreate;
                this.errorText.general = null;
                
                if (!this.dataValidation(newUser, {
                    username: "required",
                    password: "required",
                }))
                {
                    return;
                }
                
                modal.EngageFloatShield();
                const self = this;
                
                ajax.PostExternal(this.rootUrl + "/api/v1/users/validate-existing-user-credentials", newUser, true, function(result) 
                {
                    if (result.success === false || (typeof result.response !== "undefined" && result.response.success === false))
                    {
                        self.errorText.general = result.response.message; 
                        self.$forceUpdate();
                        modal.CloseFloatShield();
                        return;
                    }
                    
                    self.cartUser = result.response.data.user;
                    self.creatingNewCustomer = false;
                    self.openCheckout(true);
                });
            },
            createNewCustomer: function()
            {
                let newUser = this.newUserForCreate;
                this.errorText.general = null;
                
                if (!this.dataValidation(newUser, {
                    first_name: "required",
                    last_name: "required",
                    email: "required|email",
                    phone: "required|phone",
                    username: "required",
                    password: "required",
                }))
                {
                    return;
                }
                
                modal.EngageFloatShield();
                const self = this;

                ajax.PostExternal(this.rootUrl + "/api/v1/users/create-new-user", newUser, true, function(result) 
                {
                    if (result.success === false || (typeof result.response !== "undefined" && result.response.success === false))
                    {
                        switch(result.response.data.error)
                        {
                            case "duplicate_account":
                                self.errorText.general = "This account already exists. Try changing the username or email address.";
                                break;
                        }
                        
                        self.$forceUpdate();
                        modal.CloseFloatShield();
                        return;
                    }
                    
                    modal.CloseFloatShield();
                    
                    self.userList.push(result.response.data.user);
                    self.cartUser = result.response.data.user;
                    self.cartUser.user_email = self.cartUser.email;
                    self.cartUser.user_phone = self.cartUser.phone;
                    
                    if (self.cartType !== true)
                    {
                        const vc = self.findVc(self);
                        vc.setTitle("Confirm Customer Selection");
                        self.creatingNewCustomer = false;
                    }
                    else
                    {
                        self.creatingNewCustomer = false;
                        self.clearNewCustomer();
                        self.openCheckout();
                        self.creatingNewCustomer = false;
                    }
                });
            },
            dataValidation: function(entity, validation)
            {
                let validForm = true;

                for (let currValidationField in validation)
                {
                    if (typeof entity[currValidationField] !== "undefined")
                    {
                        let currRules = validation[currValidationField];
                        let currEntityField = entity[currValidationField];
                        let currValidationResult = this.validateRules(currEntityField, currValidationField, currRules, entity);

                        if (currValidationResult.success == false)
                        {
                            validForm = false;
                            this.errorText[currValidationField] = currValidationResult.error;
                        }
                    }
                }
                
                this.$forceUpdate();

                return validForm;
            },
            validateRules: function(entityValue, validationField, rules, entity)
            {
                let colRules = rules.split("|");

                for (let currRule of colRules)
                {
                    if (currRule.includes(":"))
                    {
                        let conditionalRule = currRule.split(":");

                        switch (conditionalRule[0])
                        {
                            case "sameAs":
                                if (entity[conditionalRule[1]] && entity[conditionalRule[1]] !== entityValue)
                                {
                                    return this.validationObject(false, validationField, entityValue, this.ucWords(validationField) + " needs to match " + ucWords(conditionalRule[1]) + ".");
                                }
                                break;
                        }
                    }
                    else
                    {
                        switch(currRule)
                        {
                            case "required":
                                if (typeof entityValue === "undefined" || entityValue === null|| entityValue === "")
                                {
                                    return this.validationObject(false, validationField, entityValue, this.ucWords(validationField) + " cannot be blank.");
                                }
                                break;
                            case "passwordComplex":
                                if (!this.isComplexPassword(entityValue))
                                {
                                    return this.validationObject(false, validationField, entityValue, this.ucWords(validationField) + " isn\'t complex enough.");
                                }
                                break;
                            case "email":
                                if (!this.isEmail(entityValue))
                                {
                                    return this.validationObject(false, validationField, entityValue, this.ucWords(validationField) + " isn\'t an email address.");
                                }
                                break;
                        }
                    }
                }

                return this.validationObject(true, validationField, entityValue, "Passes validation.");
            },
            ucWords: function(text)
            {
                return text.replace(/_/g," ").replace(/\w\S*/g, function (txt) {
                    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                });
            },
            validationObject: function(result, name, value, message)
            {
                return {success: result, name: name, value: value, error: message};
            },
            isEmail: function(email)
            {
                return /^([a-zA-Z0-9\.\+])*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email);
            },
            isComplexPassword: function(str)
            {
                let strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})");
                return strongRegex.test(str);
            },
            backToSelectCustomer: function()
            {
                this.creatingNewCustomer = false;
                this.clearNewCustomer();
                const vc = this.findVc(this);
                vc.setTitle("Select a Customer");
            },
            openCheckout: function(useFloat)
            {
                if (this.cartUser === null)
                {
                    ezLog("NO CART USER")
                    return;
                }
                
                const self = this;
                
                if (typeof useFloat === "undefined")
                {
                    modal.EngageFloatShield();
                }
                
                this.setAppWrapperClass("checkout");
                
                ajax.GetExternal(this.rootUrl + "/cart/get-checkout-data?id=" + this.cartUser.user_id, true, function(result) 
                {
                    if (result.response.success === true)
                    {
                        self.customerPaymentAccounts = [];
                       
                        const paymentAccounts = Object.entries(result.response.data.paymentAccounts);
                        paymentAccounts.forEach(function([index, currPaymentAccount])
                        {
                            self.customerPaymentAccounts.push(currPaymentAccount);
                        });
                        
                        if (typeof self.promoCodeList.length === "undefined" || self.promoCodeList.length === 0)
                        {
                            const promoCodes = Object.entries(result.response.data.promoCodes);
                            promoCodes.forEach(function([index, currPromoCode])
                            {
                                self.promoCodeList.push(currPromoCode);
                            });
                        }
                        
                        self.$forceUpdate();
                    }
                    
                    setTimeout(function() 
                    {
                        const vc = self.findVc(self);
                        vc.showModal(self, function() {
                            vc.setTitle("Checkout");
                        });
                        
                        self.loadUsers();
                        modal.CloseFloatShield();
                        
                        self.displayComponent = "checkout";
                    }, 750);
                });
            },
            openCompleted: function()
            {
                // check to make sure there is an assigned user for checkout
                const self = this;
                const vc = this.findVc(this);
                vc.showModal(this, function() {
                    vc.setTitle("Checkout Complete");
                });
                this.displayComponent = "completed";
                this.setAppWrapperClass("commpleted");
            },
            displayCurrency: function(currency)
            {
                return "$";
            },
            calculateTotalPackagePrice: function(package)
            {
                let quantity = 1;
                if (typeof package.quantity !== "undefined") quantity = package.quantity;
                
                return this.renderMoney(package.regular_price * quantity);
            },
            calculateTotalCartPrice: function(cartItems)
            {
                let totalCartPrice = 0;
                
                for (let currPackage of cartItems)
                {
                    totalCartPrice += parseFloat(this.calculateTotalPackagePrice(currPackage));
                }
                
                return this.renderMoney(totalCartPrice);
            },
            calculateTotalCheckoutPrice: function(cartPromoCode, cartItems, applyCartPromoCode)
            {
                let totalCartPrice = 0;
                
                for (let currPackage of cartItems)
                {
                    totalCartPrice += parseFloat(this.calculateTotalPackagePrice(currPackage));
                }
                
                if (cartPromoCode !== null && applyCartPromoCode === true)
                {
                    switch(cartPromoCode.discount_type)
                    {
                        case "%":
                            totalCartPrice = totalCartPrice * (parseFloat(cartPromoCode.discount_value) / 100);
                            break;
                        default:
                            totalCartPrice = totalCartPrice - parseFloat(cartPromoCode.discount_value);
                            break;
                    }
                }
                
                return this.renderMoney(totalCartPrice);
            },
            calculateTotalCheckoutPriceFee: function(money)
            {
                return this.renderMoney((money * 0.0298662) + 0.30);
            },
            calculateTotalCheckoutPriceWithFee: function(cartPromoCode, cartItems, applyCartPromoCode)
            {
                const fee = this.calculateTotalCheckoutPriceFee(this.calculateTotalCheckoutPrice(cartPromoCode, cartItems, applyCartPromoCode))
                const totalPrice = this.calculateTotalCheckoutPrice(cartPromoCode, cartItems, applyCartPromoCode);
                const newTotalPrice = (parseFloat(fee) + parseFloat(totalPrice));
                
                return this.renderMoney(newTotalPrice);
            },
            removePackageFromCart: function(package)
            {
                for (let currPackageIndex in this.cartItems)
                {
                    if (this.cartItems[currPackageIndex].package_id === package.package_id)
                    {
                        this.cartItems.splice(currPackageIndex, 1);
                        return;
                    }
                }
            },
            selectCartPaymentAccount: function(account)
            {
                this.cartPaymentAccount = account.payment_account_id;
                this.$forceUpdate();
            },
            createNewCartPaymentAccount: function()
            {
                this.toggleCreateNewCartPaymentAccount = true;
            },
            backToSelectPaymentAccount: function()
            {
                this.toggleCreateNewCartPaymentAccount = false;
            },
            updateProductQuantity: function(package)
            {
                package.quantity = parseFloat(package.quantity);
                
                if (package.quantity === 0)
                {
                    this.removePackageFromCart(package);
                }
                
                this.$forceUpdate();
            },
            cartQuantityCalculation: function(package)
            {
                let quantity = 10;
                
                if (typeof package.max_quantity !== "undefined" && package.max_quantity > 0)
                {
                    quantity = package.max_quantity
                }
                
                let customQuantity = [0];
                    
                for(let currIndex = 1; currIndex <= quantity; currIndex++)
                {
                    customQuantity.push(currIndex);
                }
                
                return customQuantity;
            },
            renderMoney: function(num) 
            {                
                return parseFloat(this.renderCartCurrency(num)).toFixed(2);
            },
            renderCartCurrency: function(num) 
            {
               return num;
            },
            keyMonitorUserList: function(event)
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
                        this.assignUserToCard(userByIndex, this.userSearchHighlight);
                        break;
                    default:
                        this.userSearchHighlight = 0;
                        break;
                }
                
                this.userList = this.userList;
                this.$forceUpdate();
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
                
                this.userList = this.userList;
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
            keyMonitorAffiliateList: function(event)
            {
                this.affiliateList = this.affiliateList;
                this.$forceUpdate();
            },
            renderCardType: function(account)
            {
                if (typeof account.type === "undefined" || account.type === null)
                {
                    account.type = "other";
                }
                
                return this.rootUrl + "/_ez/images/financials/cc_small_" + account.type + ".png";
            },
            isIterable: function(obj) 
            {
                if (obj == null) 
                {
                    return false;
                }
                
                return typeof obj[Symbol.iterator] === \'function\';
            },
            clearError: function(field)
            {
                this.errorText[field] = null;
                this.$forceUpdate();
            },
            checkForErrorTextDisplay: function(value)
            {
                if (typeof value === "undefined" || value === null || value === "")
                {
                    return false;
                }
                
                return true;
            },
            imgError: function(package)
            {
                package.image_url = "/_ez/images/no-image.jpg";
            },
            avatarError: function(user)
            {
                user.avatar_url = "/_ez/images/users/no-user.jpg";
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
            createNewCardForCustomer: function()
            {
                const card = this.newCardForCreate;
            
                if (!this.dataValidation(card, {
                    name: "required",
                    number: "required",
                    expMonth: "required",
                    expYear: "required",
                    cvc: "required",
                    line1: "required",
                    city: "required",
                    state: "required",
                    zip: "required",
                    country: "required",
                }))
                {
                    return;
                }

                const self = this;
                modal.EngageFloatShield();
                const url = this.rootUrl + "/cart/register-credit-card-with-user?id=" + this.cartUser.user_id;
                
                ajax.PostExternal(url, card, true, function(result) 
                {
                    if (result.success === false || (typeof result.response !== "undefined" && result.response.success === false))
                    {
                        let data = {title: "Credit Card Error", html: result.response.message};
                        modal.EngagePopUpAlert(data, function() {
                            modal.CloseFloatShield();
                        }, 350, 115);
                        return;
                    }
                    
                    self.cartPaymentAccount = result.response.data.payment_account_id;

                    self.customerPaymentAccounts.push(result.response.data);
                    self.toggleCreateNewCartPaymentAccount = false;
                    
                    self.clearNewCard();
                    self.$forceUpdate();
                    
                    setTimeout(function() {
                        self.creatingNewCustomer = false;
                        modal.CloseFloatShield();
                    }, 1000);
                });
            },
            clearNewCard: function()
            {
                this.newCardForCreate = {name:"", number: "", expMonth: "", expYear: "", cvc: "", line1: "",  line2: "",  state: "", zip: "", country: ""};
                this.errorText = [];
            },
            clearNewCustomer: function()
            {
                this.newUserForCreate = {affiliate_id:"", first_name: "", last_name: "", email: "", phone: "", username: "",  password: ""};
                this.errorText = [];
            },
            testCreditCard: function(number)
            {
                const types = Object.entries(this.creditCardTest);
                const self = this;
                self.creditCartType = "other";
                
                types.forEach(function([index, currType])
                {
                    var p = new RegExp(currType);
                    if (p.test(number)) {
                        self.creditCartType = index;
                        self.newCardForCreate.type = index;
                    }
                });
                
                this.$forceUpdate();
            },
            calculateProductCartDisplayCount: function()
            {
                switch(this.packageList.length)
                {
                    case 1:
                        return "100";
                    case 2:
                        return "50";
                    case 3:
                        return "33";
                    case 4:
                        return "50";
                    default:
                        return "33";
                }
            },
            displayColumnsCount: function(currCount, checkCount)
            {
                let checkForThree = currCount / checkCount; 
                let checkForThreeFloor = Math.floor(currCount / checkCount); 
                
                if (checkForThreeFloor === checkForThree)
                {
                    return true;
                }
                
                return false;
            },
            getMaxWidthColumnCount: function()
            {
            
                if (this.displayColumnsCount(this.packageList.length, 4))
                {
                    return "3";   
                }
                if (this.displayColumnsCount(this.packageList.length, 2))
                {
                    return "2";   
                }
                if (this.packageList.length === 1)
                {
                    return "1";
                }
                
                return "3";
            },
            setCreateNewAccount: function()
            {
                this.clearNewCustomer();
                this.creatingNewCustomer = true;
            },
            processCartOrder: function()
            {
                if (this.toggleCreateNewCartPaymentAccount === true || this.cartPaymentAccount === null || this.cartUser === null || this.termsAndAgreementAcceptence === false)
                {
                    return;
                }
                
                const self = this;
                modal.EngageFloatShield();
                
                let data = {title: "Processing Order", html: "We\'re submitting your order.<br>Please wait a moment and do not exit this screen."};
                modal.EngagePopUpDialog(data, 350, 115, false);
                modal.SetLastModalClass(".zgpopup-dialog-box-inner", "dialog-right-loading-anim");
                
                let promoCodeId = 0;
                if (this.activeCartPromoCode !== null)
                {
                    promoCodeId = this.activeCartPromoCode.promo_code_id;
                }

                let packageList = [];
                
                for (let currPackage of this.cartItems)
                {
                    packageList.push({id: currPackage.package_id, quantity: currPackage.quantity});
                }
                
                const cartData = {package_ids: packageList, user_id: this.cartUser.user_id, payment_id: this.cartPaymentAccount, promo_code: promoCodeId, cart_type: this.cartFuncType, parent_entity_id: this.parentEntity.id, parent_entity_type: this.parentEntity.type };
                
                ajax.PostExternal(this.rootUrl + "/cart/submit-order-checkout", cartData, true, function(result) 
                {
                    if (result.success === false || (typeof result.response !== "undefined" && result.response.success === false))
                    {
                        modal.RemoveLastModalClass(".zgpopup-dialog-box-inner", "dialog-right-loading-anim");
                        modal.SetLastModalElementHtml("span.pop-up-dialog-main-title-text", "Oops!");
                        modal.ShowCloseDialogOnLastModal();
                        
                        let data1 = {html: "We ran into a problem! Here\'s what we got back: "  + result.response.message};
                        let data2 = {html: \'<button style="margin-top: 20px;" class="btn btn-primary w-100" onclick="modal.CloseFloatShield(null);">Try Again?</button> \'};
                        modal.AddFloatDialogMessage(data1, "error");
                        modal.AddFloatDialogMessage(data2);
                        return;    
                    }
                    
                    setTimeout(function() 
                    {
                        modal.RemoveLastModalClass(".zgpopup-dialog-box-inner", "dialog-right-loading-anim");
                        modal.SetLastModalElementHtml("span.pop-up-dialog-main-title-text", "Success!");
                        
                        let data = {html: "Success! We\'ve processed your order and are registering your products.<br><br>Please wait a moment...."};
                        modal.AddFloatDialogMessage(data, "checkbox");
                        
                        let resultResponse = result.response;
                        if (typeof result.response === "undefined") resultResponse = result;
                        
                        setTimeout(function() 
                        {
                            modal.CloseFloatShield();
                            self.loadCardsIfApplicable(resultResponse);
                            
                            self.cartDisplayItemsForComplete = _.clone(self.cartItems);
                            self.completedDisplayCartPromoCode = _.clone(self.activeCartPromoCode);
                            self.applyCompletedCartPromoCode = _.clone(self.applyCartPromoCode);
                            
                            self.clearNewCustomer();
                            
                            self.activeCartPromoCode = null;
                            self.cartPromoCodeSearch = "";
                            self.applyCartPromoCode = false;
                          
                            self.openCompleted();
                            self.cartItems = [];
                            
                            if (self.cartIsCardPageProcess())
                            {
                                let vc = self.findAppVc(self);
                                const listCardPageWidget = vc.getComponentById(self.managerId);
                                listCardPageWidget.instance.refreshCard(function() 
                                {
                                    let objModal = self.findModal(self);                 
                                    objModal.close();   
                                });
                            }
                            
                        }, 2500);
                        
                    }, 1000);
                });                
            },
            loadCardsIfApplicable: function(response)
            {                
                if (this.productCartContainsProductType(this.cartItems, this.ENUM_CardType) !== true)
                {
                    return;
                }
                
                this.cardList = response.data.list;
            },
            renderCartItemsCount: function()
            {
                let totalCartItems = 0;
                
                for (let currPackage of this.cartItems)
                {
                    totalCartItems = totalCartItems + parseFloat(currPackage.quantity);
                }
                
                if (totalCartItems === 0)
                {
                    return "";
                }
                
                return "(" + totalCartItems + ") ";
            },
            removePromoCode: function()
            {
                const self = this;
                
                modal.EngageFloatShield();
                
                let data = {title: "Removing Promo Code", html: "We\'re removing your promo code.<br>Hold tight!"};
                modal.EngagePopUpDialog(data, 450, 115, false);
                modal.SetLastModalClass(".zgpopup-dialog-box-inner", "dialog-right-loading-anim");
                
                setTimeout(function() 
                {
                    self.activeCartPromoCode = null;
                    self.cartPromoCodeSearch = "";
                    self.applyCartPromoCode = false;
                    
                    modal.CloseFloatShield();
                }, 1500);

            },
            applyPromoCode: function()
            {
                const self = this;

                if (typeof self.cartPromoCodeSearch === "undefined" || self.cartPromoCodeSearch === "" || self.cartPromoCodeSearch === null)
                {
                    return;
                }
                
                modal.EngageFloatShield();
                
                let data = {title: "Applying Promo Code", html: "We\'re checking to see if your code is valid.<br>Please wait a moment."};
                modal.EngagePopUpDialog(data, 450, 115, false);
                modal.SetLastModalClass(".zgpopup-dialog-box-inner", "dialog-right-loading-anim");
                
                setTimeout(function() 
                {
                    for(currPromo of self.promoCodeList)
                    {
                        if (self.cartPromoCodeSearch.toLowerCase() === currPromo.promo_code.toLowerCase())
                        {
                            self.activeCartPromoCode = currPromo;
                            self.applyCartPromoCode = true;
                            self.$forceUpdate();
                            
                            modal.RemoveLastModalClass(".zgpopup-dialog-box-inner", "dialog-right-loading-anim");
                            modal.SetLastModalElementHtml("span.pop-up-dialog-main-title-text", "Success!");
                            
                            let data = {html: "Success! We\'ve added your promo code discount to the cart..."};
                            modal.AddFloatDialogMessage(data, "checkbox");
                            
                            setTimeout(function() {
                                modal.CloseFloatShield();
                            }, 1000);
                            
                            return;
                        } 
                    }
                    
                    modal.RemoveLastModalClass(".zgpopup-dialog-box-inner", "dialog-right-loading-anim");
                    modal.SetLastModalElementHtml("span.pop-up-dialog-main-title-text", "Oh Drat...");
                    
                    let data = {html: "Bummer. We were unable to identify your promo code.<br><b>Try another one?</b>"};
                    modal.AddFloatDialogMessage(data, "error");
                    
                    self.activeCartPromoCode = null;
                    self.applyCartPromoCode = false;
                    
                    setTimeout(function() {
                        modal.CloseFloatShield();
                    }, 2000);
                           
                }, 2500);
            },
            disableCartButton(package)
            {
                const existingPackage = this.getPackageFromCart(package);
                
                if (existingPackage === null)
                {
                    return false;
                }
               
                existingPackage.max_quantity = this.fixUndefined(existingPackage.max_quantity);
                existingPackage.quantity = this.fixUndefined(existingPackage.quantity);
           
                if (existingPackage.quantity >= existingPackage.max_quantity && existingPackage.max_quantity !== 0)
                {
                    return true;
                }
                
                return false;
            },
            disableGoToCartButton(package)
            {
                if (!this.productCartContainsCard(this.cartItems) && !this.packageContainsCard(package) && this.cartIsCardProcess()) 
                { 
                    return true;
                }

                const existingPackage = this.getPackageFromCart(package);
                
                if (existingPackage === null)
                {
                    return false;
                }
               
                existingPackage.max_quantity = this.fixUndefined(existingPackage.max_quantity);
                existingPackage.quantity = this.fixUndefined(existingPackage.quantity);
           
                if (existingPackage.quantity >= existingPackage.max_quantity && existingPackage.max_quantity !== 0)
                {
                    return true;
                }
                
                return false;
            },
            productCartContainsCard: function(items)
            {
                if (items.length === 0) { return false; }
                
                if (this.cartIsNotCardProcess()) return true;
                
                for (let currPackage of items)
                {
                    if (this.packageContainsCard(currPackage))
                    {
                        return true; 
                    }
                }
                
                return false;
            },
            packageContainsCard: function(package)
            {
                if (typeof package.line === "undefined")
                {
                    return false;
                }
                
                for (let currPackageLine of package.line)
                {
                    if (currPackageLine.product_entity === "product" && currPackageLine.product_type_id === 1)
                    {
                        return true;
                    }
                }
            },
            fixUndefined: function(value)
            {
                if (typeof value === \'undefined\')
                {
                    value = 0;
                }
                
                if (typeof value === \'string\')
                {
                    value = parseFloat(value);
                }
                
                if (isNaN(value))
                {
                    value = 0;
                }
                
                return value;
            },
            packageContainsProduct: function(package, productId)
            {
                if (typeof package.line === "undefined")
                {
                    return false;
                }
                
                for (let currPackageLine of package.line)
                {
                    if (currPackageLine.product_entity === "product" && currPackageLine.product_type_id === productId)
                    {
                        return true;
                    }
                }
            },
            productCartContainsProductType: function(items, productId)
            {
                if (items.length === 0)
                {
                    return false;
                }
                
                for (let currPackage of items)
                {
                    if (this.packageContainsProduct(currPackage, productId))
                    {
                        return true; 
                    }
                }
                
                return false;
            },
            getCountOfProduct: function(items, productId)
            {
                if (items.length === 0)
                {
                    return 0;
                }
                
                let productCount = 0;
                
                for (let currPackage of items)
                {
                    if (this.packageContainsProduct(currPackage, productId))
                    {
                        productCount++; 
                    }
                }
                
                return productCount;
            },
            renderProductImage: function(package)
            {
                return "url(" + package.image_url + ") no-repeat top center / contain";
            },
            goToCardDashboard: function(card)
            {                
                window.location.href = "/account/cards/card-dashboard/" + card.sys_row_id;
            },
            renderCardNameForLink: function(card)
            {
                const cardName = card.card_name.split(" - ");
                return cardName[0].replace("Card for ","");
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
        ';
    }

    protected function renderComponentComputedValues() : string
    {
        return '
            cartUserSearchList: function()
            {
                return this.parseUsersBySearch(this.userList);
            },
            cartAffiliateSearchList: function()
            {
                return this.parseUsersBySearch(this.affiliateList);
            },
            renderUserPrefix: function()
            {
                if (typeof this.cartUser !== "undefined" && this.cartUser !== null) return this.cartUser.name_prefx;
                return "";
            },
            renderUserSuffix: function()
            {
                if (typeof this.cartUser !== "undefined" && this.cartUser !== null) return this.cartUser.name_sufx;
                return "";
            },
        ';
    }

    protected function renderComponentDismissalScript() : string
    {
        return '
            this.cartUser = null;
            this.userSearch = "";
            this.dynamicSearch = false;
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return "if (typeof this.disableModalLoadingSpinner === 'function') { this.disableModalLoadingSpinner(); }";
    }

    protected function renderTemplate() : string
    {
        global $app;

        $privacyPolicyLink = $app->objCustomPlatform->getCompanySettings()->FindEntityByValue("label", "cart_privacy_policy_url");

        if ($privacyPolicyLink !== null)
        {
            $privacyPolicyLink = '<a href="'.$privacyPolicyLink->value.'" target="_blank">Privacy Policy</a>';
        }
        else
        {
            $privacyPolicyLink = "Privacy Policy";
        }

        return '
        <div class="appCartWrapper products" v-bind:class="{\'private-cart\': cartType === false, \'public-cart\': cartType === true}">
            <v-style type="text/css">
                .appCartWrapper .card-tile-100,
                .appCartWrapper .card-tile-50,
                .appCartWrapper .card-tile-33 {
                    position:relative;
                    height: 100%;
                }
                .appCartWrapper .card-main-image-wrapper {
                    position: absolute;
                    top: 20px;
                    left: -40px;
                    width: 159px;
                    height: 245px;
                    text-align: center;
                    overflow: hidden;
                }
                .appCartWrapper .product-icon-img-md {
                    position: absolute;
                    top: 0px;
                    left: -24px;
                    width: 100px;
                    height: 160px;
                    text-align: center;
                    overflow: hidden;
                }
                .appCartWrapper .product-icon-img-sm {
                    position: absolute;
                    top: 7px;
                    left: -24px;
                    width: 50px;
                    height: 50px;
                    text-align: center;
                    overflow: hidden;
                }
                .product-main-image-prime-md {
                    float: left;
                    width:67px;
                    position: relative;
                }
                .product-main-image-prime-sm {
                    float: left;
                    width:50px;
                }
                .appCartWrapper .back-to-entity-list {
                    border: 0 !important;
                    transform: rotate(0deg) !important;
                    margin-left:0;
                }
                .appCartWrapper.cart .product-main-details {
                    padding-left:90px;
                }
                .appCartWrapper .product-main-details {
                    padding-left: 90px;
                }
                .appCartWrapper.checkout .product-icon-img-sm {
                    left: 26px;
                }
                .appCartWrapper .card-tile-inner {
                    margin-left: 102px;
                    padding-right: 15px;
                    padding-bottom: 45px;
                }
                .appCartWrapper .card-tile-inner .btn {
                    width: calc(49% - 5px) !important;
                    font-size: 12px;
                    float: left;
                    margin-right: 5px;
                }
                .appCartWrapper .card-tile-title {
                    font-size: 20px;
                    margin:15px 0;
                }
                .appCartWrapper .card-tile-price {
                    font-weight: bold;
                    color: #fb9c27;
                    font-size: 2.2em;
                }
                .appCartWrapper .card-price-currency {
                    color: #000;
                    font-size: 15px;
                    position: relative;
                    bottom: 4px;
                }
                .appCartWrapper .card-tile-description {
                    padding-top: 5px;
                    padding-bottom: 10px;
                }
                .appCartWrapper .card-tile-description ul {
                    margin-left:17px;
                    padding:10px 0;
                }
                .appCartWrapper .card-tile-description ul li {
                    font-size: 12px;
                    list-style-type: circle;
                }
                .appCartWrapper .cart-title-lines {
                    margin-left: 17px;
                    margin-bottom: 65px;
                }
                .appCartWrapper .cart-title-line-item {
                    font-size:12px;
                    list-style-type: circle;
                }
                .appCartWrapper .cart-display-outer .item-header {
                    padding-bottom: 0;
                    border-bottom: 0;
                }
                .appCartWrapper .cart-display-outer {
                    border-collapse: separate;
                    border-spacing: 0 20px;
                    margin-top: -20px;
                    margin-bottom: -20px;
                }
                .appCartWrapper .product-info-detail .cart-title-lines {
                    margin-top: 10px;
                    margin-bottom: 10px;
                }
                .appCartWrapper .product-price .value {
                    font-size: 18px;
                }
                .appCartWrapper .cart-display-preview {
                    border-collapse: collapse !important;
                    border-spacing: 0 !important;
                    margin-top: 0px !important;
                    margin-bottom: 0 !important;
                }
                .appCartWrapper .item-product,
                .appCartWrapper .cart-display-preview {
                    position: relative;
                    z-index: 5;
                    background: #fff;
                    box-shadow: rgba(0,0,0,.3) 2px 2px 10px;
                }
                .appCartWrapper .item-product td:first-child,
                .appCartWrapper .cart-display-preview td:first-child {
                    padding-left:15px;
                }
                .appCartWrapper .item-product td:last-child,
                .appCartWrapper .cart-display-preview td:last-child {
                    padding-right:15px;
                }
                .appCartWrapper .cart-total-price {
                    text-align: right;
                    margin-top: 15px;
                }
                .appCartWrapper .legal-stuff .cart-total-price {
                    border-top: 1px solid rgb(204, 204, 204);
                    border-bottom: 1px solid rgb(204, 204, 204);
                    padding: 15px 0;
                    margin-bottom: 15px;
                }
                .appCartWrapper .dynamic-search-list {
                    position: absolute;
                    width: calc(100% - 35px);
                    background: #fff;
                    margin-left: 5px;
                    z-index: 1000;
                    max-height:40vh;
                    overflow-y:auto;
                }
                .appCartWrapper .dynamic-search-list > table {
                    width: 100%;
                }
                .appCartWrapper .dynamic-search-list > table > thead {
                    box-shadow: rgba(0,0,0,0.2) 0px 2px 5px;
                    background-color: #007bff;
                    color: #fff !important;
                }
                .appCartWrapper .dynamic-search-list > table tr {
                    cursor:pointer;
                }
                .appCartWrapper .dynamic-search-list > table tr:hover {
                    background-color:#d5e9ff !important;
                }
                .appCartWrapper .dynamic-search .inputpicker-arrow {
                    position: absolute;
                    top: 22px;
                    right: 21px;
                    width: 20px;
                    height: 20px;
                    cursor: pointer;
                }
                .appCartWrapper .dynamic-search .inputpicker-arrow b {
                    border-color: #888 transparent transparent;
                    border-style: solid;
                    border-width: 5px 4px 0;
                    height: 0;
                    left: 50%;
                    top: 50%;
                    margin-left: -4px;
                    margin-top: -2px;
                    position: absolute;
                    width: 0;
                    font-weight: 700;
                }
                .appCartWrapper .selected-customer-wrapper {
                    padding-left: 15px;
                    padding-bottom: 8px;
                    width: 100%;
                    position: relative;
                    padding-top: 14px;
                }
                .appCartWrapper .selected-customer-outer {
                    background: #fff;
                    border-radius: 5px;
                    margin:10px 0 15px;
                    width:100%;
                }
                .appCartWrapper .selected-customer-outer td:first-child {
                    width: 129px;
                }
                .appCartWrapper .selected-customer-outer td > .user-avatar {
                    margin: 16px 0 16px 16px;
                }
                .appCartWrapper .selected-customer td {
                    padding:5px 15px;
                }
                .appCartWrapper .create-new-customer-wrapper {
                    background:#ececec;
                    padding: 11px 15px;
                    border-radius:5px;
                    box-shadow:rgba(0,0,0,.2) 0 0 10px inset;
                    margin-bottom:15px;
                }
                .appCartWrapper .create-new-customer-wrapper > div {
                    margin-bottom:10px;
                }
                .appCartWrapper .pointer {
                    cursor:pointer;
                }
                .appCartWrapper .field-validation-error {
                    color: red;
                    padding: 7px 28px 6px;
                    background: url('.$app->objCustomPlatform->getFullPortalDomain().'/_ez/images/icons/warning.svg) no-repeat left center / 24px;
                }
                .appCartWrapper .cart-display-preview .product-main-title-h2 {
                    font-size: 21px;
                }
                .appCartWrapper .cart-display-preview .cart-title-line-item {
                    float: left;
                    margin-right: 32px;
                }
                .appCartWrapper .width50 {
                    display:inline-block;
                    width:50%;
                }
                .appCartWrapper .selected-customer-checkout {
                    margin-bottom: 15px;
                    border-bottom: 1px solid #ddd;
                    padding-bottom: 15px;
                    margin-right: 15px;
                }
                .appCartWrapper .selected-payment-account-checkout {
                    margin-right: 15px;
                }
                .appCartWrapper .selected-payment-account-list li {
                    margin-bottom: 8px;
                }
                .appCartWrapper .selected-payment-account-list > li > label,
                .appCartWrapper .selected-payment-account-list > li > div {
                    padding: 9px 34px 6px;
                    box-shadow: rgba(0,0,0,.3) 0 0 5px;
                    background-color: #fff;
                    display:block;
                    width:100%;
                    height:100%;
                    cursor:pointer;
                }
                .appCartWrapper .selected-payment-account-list li img {
                    margin-top: -.3rem;
                }
                .appCartWrapper .selected-payment-account-list li > .add-new-account {
                    position: relative;
                    padding-left: 40px;
                }
                .appCartWrapper .selected-payment-account-list li > .add-new-account:before {
                    display: block;
                    width: 22px;
                    height: 22px;
                    background: url(\'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxdJREFUeNqcVL9vHEUYfTM7u3eXvcN3/nFxsM6XRAgJyyYyChISdBQgxWAEFDRuESkSqqQCKfwDNOmTIkqqSCQGCxASDVUEtowiEKGwZNnyjwCOfdi3vt2dGd7s3tkmFg0jfVrtvO+9efN9367ApXs4tiwmAt+7UAq8874nm0LAS1KzEsV6oZPob5jwANzMc232EP8Ssrb/RMH/tN5XvFgrFwoONl1ISQGrDZ60Y2xuRzfbneQzii0fF7I4PdRX/HJ0oDzuKQ8poXo5QKWgMni9tY92rKFoRGuN1b/21ij4FokLDpddJzWKzJ2pV8YFRbT0EFmBD19p4M4H41m8WPWxxz2HCc9Dc6jy7MlqadYZyIVoLSwG15qD5TEj+cokUAy+gnPmruRCMtyeYLgcwxo1BsKRShhcpxiZQowNV0sfSZKsoEBPSJEAcVg+Eq2n4A5zjiRxxThVPTElpHhdlQpquhYWgphVjUjNuCTt0WxyROjPSOP3zZh3YI5OEbANo6HCQBigXPTfU2GgXnadafaX8Orpflie6IzGFHm+FhwIXRirY/AkW+CqSqF4P8Gdn1ax5a7tiUkVKNnsGIuxehlXXmvgv9bM+RHMHN0wGt8urmL579gJDzl9D/9rifzi+WAKlWiz4ktxbmkrwu2fN7GTWETasrgSb5ztw8RgMaN9/dsf+GVHk8caGYO4k2Bb5wPItaU4ZPO+wNSjx3tYfNzGo21aNbnR8N3nDoRuPFjB3SViHolxAqQMYXtfxEPZ7qRzrSi2Rdd5N+6WpXfD7BkEvUSukpJ5x9wZDncjbnJ3FJqV1tof17ejW26jE6dAQr+peyZ50kFxbb7HjvEbOQxjfiD6lRtItNrJJ2tP2hsZ4JKdEEWDwzGCJ7pCPTzNBHcp9DHhWODy/bwHQrwUFv17u6ltZNPNA14YruDUM4UM/3WthY3dOP9t5G5ajPcJfZf3sCvU/QOchRSfw5PTbiihbbcpzpLMi+uuq833FLzKmO9R1VOjsUTwHaT6TQjzNt8n6WwoQ7TeouhD4nN8/8JV7Sj1HwEGAGXUUvW2OWvwAAAAAElFTkSuQmCC\') no-repeat center center / 100%;
                    position: absolute;
                    content: " ";
                    top: 7px;
                    left: 9px;
                }
                .appCartWrapper .create-new-payment-account-box > div {
                    margin-bottom:10px;
                }
                .appCartWrapper .create-new-payment-account-box > div.inlineBlock > select {
                    display:inline-block;
                    width:50%;
                }
                .appCartWrapper .create-new-payment-account-box .billing-account-title { 
                    font-size: 18px;
                    margin: 25px 0 10px;
                }
                .appCartWrapper .create-new-payment-account-box .csv-number { 
                    width:150px;
                }
                .appCartWrapper button.external { 
                    width:190px !important;
                }
                .appCartWrapper button.internal { 
                    width:calc(50% - 5px) !important;
                }
                .appCartWrapper button.internal:first-child { 
                    margin-right:5px !important;
                    color:#ff;
                }
                .ico {
                    position: relative;
                    width: 1.1em;
                    height: 1.1em;
                    display: inline-block;
                    margin-bottom: -0.20em;
                    margin-right: .3em;
                }
                .ico:before {
                    top: 0;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    width: 100%;
                    height: 100%;
                    position: absolute;
                    content: " ";
                }
                .ico-home:before {
                    background: url('.$app->objCustomPlatform->getFullPortalDomain().'/_ez/images/icons/home.svg) no-repeat center center / 100% 100%;
                }
                .ico-billing:before {
                    background: url('.$app->objCustomPlatform->getFullPortalDomain().'/_ez/images/icons/credit-card.svg) no-repeat center center / 100% 100%;
                }
                .appCartWrapper .small-cart-icon {
                    background: url('.$app->objCustomPlatform->getFullPortalDomain().'/_ez/images/financials/cart-icon-black.png) center center / auto 100% no-repeat;
                    display: inline-block;
                    width: 21px;
                    height: 19px;
                    position: relative;
                    top: 4px;
                    margin-right: 5px;
                }
                .appCartWrapper .btn-add-to-cart {
                    background: #fb9c27;
                    color: #000;
                    border-color: #fb9c27;
                }
                .appCartWrapper .btn-add-to-cart:hover {
                    background: #d09042;
                    color: #000;
                    border-color: #d09042;
                }
                .appCartWrapper .view-cart-button {
                    display: inline-block;
                    background: #ff8600;
                    border-radius: 5px;
                    padding: 2px 13px 5px 10px;
                    color: #000;
                }
                .appCartWrapper .view-cart-button.disabled, 
                .appCartWrapper .product-price-submit-wrapper .buttonID23542445.disabled {
                    cursor:default;
                    opacity: .65;
                    border-color: transparent;
                    background-color:#6c757d !important;
                    color: #fff;
                }
                .appCartWrapper .view-cart-button.disabled .small-cart-icon {
                    background: url('.$app->objCustomPlatform->getFullPortalDomain().'/_ez/images/financials/cart-icon-white.png) center center / auto 100% no-repeat;
                }
                .appCartWrapper .empty-cart-text {
                    background-image: none;
                    display: table;
                    vertical-align: middle;
                }
                .appCartWrapper .empty-cart-text > div {
                    position: relative;
                    left: -30px;
                }
                .appCartWrapper .empty-cart-text .cart-icon-large {
                    background: url('.$app->objCustomPlatform->getFullPortalDomain().'/_ez/images/financials/cart-icon-white.svg) no-repeat center center / auto 100%;
                    display: inline-block;
                    width: 80px;
                    height: 75px;
                    vertical-align: middle;
                }
                .appCartWrapper .button-back-to-selection {
                    display: inline-block;
                    width: auto !important;
                    font-family: AvenirLTStd;
                    font-size: 12px;
                    margin-left: 12px;
                }
                .checkout-ribon {
                    margin-bottom: 15px;
                    background: linear-gradient(to left, rgba(0,0,0,.0) 0%, rgba(0,0,0,0.1) 50%, rgba(0,0,0,.0) 100%);
                }
                .checkout-ribon ul {
                    display:flex;
                    flex-direction: row;
                    justify-content: center; 
                    padding: 8px 0;
                }
                .checkout-ribon ul li {
                    display: flex;
                    width: 25%;
                    align-items: center;
                }
                .checkout-ribon ul li a {
                    width: 100%;
                    display: block;
                    text-align: center;
                    margin: 0 5px;
                    padding: 5px 0;
                    border-radius: 4px;
                    cursor:pointer;
                }
                .checkout-ribon ul li a.selected {
                    background: #6c757d;
                    color: #fff;
                    font-weight: bold;
                    cursor:default;
                }
                .checkout-ribon ul li a.no-click {
                    cursor:default !important;
                }
                .checkout-ribon ul li a.selected:hover {
                    color: #fff !important;
                }
                .appCartWrapper .card-tile-action-box {
                    position:absolute;
                    bottom:20px;
                    width: calc(100% - 160px);
                }
                .appCartWrapper .checkout-bottom-wrapper {
                    margin-top:15px;
                    margin-bottom:15px;
                }
                .appCartWrapper .checkout-bottom-wrapper .width50:first-child {
                    border-right:1px solid #aaa;
                    min-height:250px;
                }
                .appCartWrapper .checkout-bottom-wrapper .width50:nth-child(2) {
                    padding: 5px 0 0px 15px;
                }
                
                .appCartWrapper .card-tile-action-box button.disabled {
                    cursor:default;
                    background-color:#6c757d !important;
                    opacity: .65;
                    color: #fff !important;
                    border-color: transparent;
                    box-shadow: rgba(0,0,0,0) 0 0 0;
                }
                .appCartWrapper .card-tile-action-box button.disabled:active {
                    border-color: transparent;
                }
                
                .appCartWrapper .width100,
                .appCartWrapper .width50,
                .appCartWrapper .width33,
                .appCartWrapper .width25 {
                    margin-bottom: 15px;
                }
                .appCartWrapper .width50:nth-of-type(odd) .card-tile-50,
                .appCartWrapper .width50:nth-of-type(even) .card-tile-50,
                .appCartWrapper .width33:nth-of-type(n+1) .card-tile-33,
                .appCartWrapper .width33:nth-of-type(n+1) .card-tile-33,
                .appCartWrapper .width33:nth-of-type(n+3) .card-tile-33 {
                    background: #fff;
                    box-shadow: #cccccc 0 0 5px;
                    padding: 15px 25px;
                }
                .appCartWrapper .width33:nth-of-type(n+1) .card-tile-33 {
                    width: calc( 100% - 7px );
                    margin-right: 8px;
                }
                .appCartWrapper .width33:nth-of-type(n+2) .card-tile-33 {
                    width:calc( 100% - 16px );
                    margin-right:8px;
                    margin-left:8px;
                }
                .appCartWrapper .width33:nth-of-type(n+3) .card-tile-33 {
                    width:calc( 100% - 8px );
                    margin-left:8px;
                }
                .appCartWrapper .width50:nth-of-type(even) .card-tile-50 {
                    width:calc( 100% - 8px );
                    margin-left:8px;
                }
                .appCartWrapper .width50:nth-of-type(odd) .card-tile-50 {
                    width:calc( 100% - 7px );
                    margin-right:7px;
                }
                .appCartWrapper .cart-display-box > ul > li > div {
                    min-height: 295px;
                }
                .appCartWrapper .product-icon-img-md,
                .appCartWrapper .product-icon-img-sm, {
                    box-shadow:transparent 0 0 0;
                }
                
                @media (max-width:1100px)
                {
                    .appCartWrapper .width50 {
                        width: 100%;
                        float: none;
                    }
                    .appCartWrapper .width33 {
                        width: 50%;
                        float: left;
                    }
                    .appCartWrapper .width25 {
                        width: 33%;
                        float: left;
                    }
                    .appCartWrapper .width33:nth-of-type(odd) .card-tile-33 {
                        width:calc( 100% - 7px );
                        margin-right:8px;
                        margin-left:0px;
                    }
                    .appCartWrapper .width33:nth-of-type(even) .card-tile-33 {
                        width:calc( 100% - 7px );
                        margin-right:0px;
                        margin-left:8px;
                    }
                    .appCartWrapper .checkout-bottom-wrapper .width50:first-child {
                        border-right:0;
                        min-height:250px;
                    }
                    .appCartWrapper .checkout-bottom-wrapper .width50:first-child > div {
                        margin-right:0 !important;
                    }
                    .appCartWrapper .checkout-bottom-wrapper .width50:last-child {
                        padding: 5px 0 0 0;
                    }
                    .appCartWrapper .checkout-bottom-wrapper .width50:nth-child(2) {
                        padding: 5px 0 0 0 !important;
                    }
                }
                
                @media (max-width:900px)
                {
                    .appCartWrapper .width33:nth-child(3n + 1).itemCount3 .card-tile-title {
                        font-size: 20px;
                    }
                    .appCartWrapper .width33:nth-child(3n + 1).itemCount3 .card-tile-price {
                        font-size: 2.2em;
                    }
                    .appCartWrapper .width33:nth-child(3n + 1).itemCount3 .card-tile-description,
                    .appCartWrapper .width33:nth-child(3n + 1).itemCount3 .card-tile-description ul li {
                        font-size: 12px;
                    }
                }
                
                @media (max-width:1100px) and (min-width:700px)
                {
                    .appCartWrapper .width33:nth-child(3n + 1).itemCount3 {
                        width: 100%;
                        float: none;
                        display:block;
                    }
                    .appCartWrapper .width33:nth-child(3n + 1).itemCount3  .card-tile-33 {
                        margin-right: 0px;
                        width: 100%;
                    }
                    .appCartWrapper .width33:nth-child(3n + 1).itemCount3 .card-tile-title {
                        font-size: 2.9vw;
                    }
                    .appCartWrapper .width33:nth-child(3n + 1).itemCount3 .card-tile-price {
                        font-size: 3.6vw;
                    }
                    .appCartWrapper .width33:nth-child(3n + 1).itemCount3 .card-tile-description,
                    .appCartWrapper .width33:nth-child(3n + 1).itemCount3 .card-tile-description ul li {
                        font-size: 1.4vw;
                    }
                    .appCartWrapper .width33:nth-child(3n + 2).itemCount3  .card-tile-33 {
                        width: calc( 100% - 7px );
                        margin-right: 7px;
                        margin-left: 0px;
                    }
                    .appCartWrapper .width33:nth-child(3n + 3).itemCount3  .card-tile-33 {
                        width: calc( 100% - 7px );
                        margin-right: 0px;
                        margin-left: 8px;
                    }
                }
                
                @media (max-width:700px)
                {
                    .appCartWrapper .width50 {
                        width: 100%;
                        float: none;
                    }
                    .appCartWrapper .width33 {
                        width: 100%;
                        float: left;
                    }
                    .appCartWrapper .width25 {
                        width: 50%;
                        float: left;
                    }
                    .appCartWrapper .width33:nth-of-type(odd) .card-tile-33 {
                        width:100%;
                        margin-right:0px;
                        margin-left:0px;
                    }
                    .appCartWrapper .width33:nth-of-type(even) .card-tile-33 {
                        width:100%;
                        margin-right:0px;
                        margin-left:0px;
                    }
                    .appCartWrapper .card-tile-action-box {
                        width: calc(100% - 30px);
                        margin-left: -120px;
                    }
                    .appCartWrapper .product-info-detail,
                    .appCartWrapper .product-price,
                    .appCartWrapper .product-quantity {
                        display: inline-flex;
                        width: 100%;
                    }
                    .appCartWrapper .cart-display-outer thead {
                        display:none;
                    }
                    .appCartWrapper .product-main-data-outer {
                        text-align:center;
                        width: 100%;
                    }
                    .appCartWrapper .product-quantity-input {
                        display: inline-block;
                        width: calc(100% - 30px);
                        margin: 15px;
                    }
                    .appCartWrapper .product-price-inner {
                        width:100%;
                        text-align:center;
                        display:block;
                    }
                    .appCartWrapper .product-price-inner span {
                        display:inline-block;
                    }
                    .appCartWrapper .external:first-child {
                         width: calc( 50% - 10px) !important;
                         margin-right:5px;
                         display:inline-block;
                    }
                    .appCartWrapper .external:last-child {
                         width: calc( 50% - 5px) !important;
                         margin-left:5px;
                         display:inline-block;
                    }
                    .appCartWrapper .create-new-customer-wrapper .external {
                         width: 100% !important;
                         margin-left: 0 !important;
                    }
                    .appCartWrapper .checkout-bottom-wrapper button:first-child {
                        width:100% !important;
                        margin-left: 0 !important;
                    }
                    .appCartWrapper .cart-display-box > ul > li > div {
                        min-height: 320px;
                    }
                    .appCartWrapper .product-main-details {
                        padding-left:20px;
                    }
                    .appCartWrapper.cart .product-main-details {
                        padding-left:20px;
                    }
                }
                @media(max-width:500px)
                {
                    .appCartWrapper .empty-cart-text {
                        width:100%;
                    }
                    .appCartWrapper .empty-cart-text > div {
                        font-size:20px;
                        max-width:175px;
                        left: 0;
                        margin:auto;
                    }
                    .appCartWrapper .empty-cart-text .cart-icon-large {
                        display:block;
                        background-position: center;
                        width:100%;
                        position:relative;
                        left:-7px;
                    }
                    .terms-and-agreement {
                        padding-left:20px;
                        text-align: left;
                    }
                }
                @media(min-width:1400px)
                {
                    .appCartWrapper:not(.private-cart) .card-tile-title {
                        font-size: 1.6vw;
                    }
                    .appCartWrapper:not(.private-cart) .card-tile-price {
                        font-size: 2.2vw;
                    }
                    .appCartWrapper:not(.private-cart) .card-tile-description,
                    .appCartWrapper:not(.private-cart) .card-tile-description ul li {
                        font-size: 0.9vw;
                    }
                }
            </v-style>
            <div class="checkout-ribon" v-if="displayComponent !== \'selectPackagesByClass\' && displayComponent !== \'shoppingCart\'">
                <ul>
                    <li v-if="displayComponent !== \'completed\'"><a v-on:click="openCart">Cart</a></li>
                    <li v-if="displayComponent === \'completed\'"><a class="no-click">Cart</a></li>
                    <li v-if="cartType !== true && this.cartIsAllCardProcess()"><a v-on:click="openAssignUser" v-bind:class="{selected: displayComponent === \'assignUser\'}">Assign Customer</a></li>
                    <li v-if="cartType === true && cartUser !== null && this.cartIsCardProcess()"><a class="no-click" v-bind:class="{selected: displayComponent === \'assignUser\'}">You\'re Signed In!</a></li>
                    <li v-if="cartType === true && cartUser === null && this.cartIsCardProcess()"><a v-on:click="if (displayComponent !== \'assignUser\' ) { openAssignUser }" v-bind:class="{selected: displayComponent === \'assignUser\'}">Sign In</a></li>
                    <li v-if="displayComponent !== \'checkout\' && displayComponent !== \'assignUser\' && displayComponent !== \'completed\'"><a v-on:click="" v-bind:class="{selected: displayComponent === \'checkout\'}">Checkout</a></li>
                    <li v-if="displayComponent === \'checkout\' || displayComponent === \'assignUser\' || displayComponent === \'completed\'"><a class="no-click" v-bind:class="{selected: displayComponent === \'checkout\'}">Checkout</a></li>
                    <li><a class="no-click" v-bind:class="{selected: displayComponent === \'completed\'}">Complete</a></li>
                </ul>
            </div>
            <div v-if="displayComponent === \'selectPackagesByClass\'" class="cart-display-box entityDashboard">
                <ul>
                    <li v-for="currPackage in packageList" v-bind:class="\'width\' + calculateProductCartDisplayCount() + \' itemCount\' + getMaxWidthColumnCount()">
                        <div v-bind:class="\'card-tile-\' + calculateProductCartDisplayCount()">
                            <div class="card-main-image-wrapper" v-bind:style="{background: renderProductImage(currPackage)}">
                            </div>
                            <div class="card-tile-inner">
                                <h4 class="card-tile-title">{{ currPackage.name }}</h4>
                                <div class="card-tile-price">
                                    <span class="card-price-currency">{{ displayCurrency(currPackage.currency) }}</span> {{ renderMoney(currPackage.regular_price) }}
                                </div>
                                <div class="card-tile-description" v-html="currPackage.description">{{ currPackage.description }}</div>
                                <div v-if="currPackage.line && currPackage.hide_line_items !== 1" class="cart-title-lines">
                                    <ul>
                                        <li v-for="currLine in currPackage.line" class="cart-title-line-item">{{ currLine.name }}<span v-if="currLine.quantity > 1">s</span></li>
                                    </ul>
                                </div>
                                <div class="card-tile-action-box">
                                    <button v-bind:class="{disabled: disableCartButton(currPackage)}" v-on:click="addPackageToCart(currPackage)" class="buttonID23542445 btn btn-danger btn-add-to-cart w-100">Add To Cart</button> 
                                    <button v-bind:class="{disabled: disableGoToCartButton(currPackage)}" v-on:click="addPackageAndGoToCart(currPackage)" class="buttonID23542445 btn btn-warning w-100">Buy Now</button>
                                <div>
                                <div style="clear:both;"></div>
                            </div>
                        </div>
                    </li>   
                </ul>
                <div style="clear:both;"></div>
                <div style="text-align:center;padding-top:5px;">
                    <div v-if="!productCartContainsCard(cartItems) && this.cartIsCardProcess()" style="color:#cc0000;margin-bottom:15px;">This checkout requires the purchase of a new card.</div>
                    <div v-bind:class="{disabled: !productCartContainsCard(cartItems)}" class="view-cart-button pointer" v-on:click="openCart">
                        <span class="small-cart-icon"></span><span>{{ renderCartItemsCount() }}</span> View Cart
                    </div>
                </div>
            </div>
            
            <div v-if="displayComponent === \'shoppingCart\'" class="cart-display-box">
                <div v-if="cartItems.length === 0" class="cart-display-outer">
                    <div style="display: flex;top: 0px;left: 0px;right: 0px;bottom: 0px;width: 100%;height: 100%;">
                        <div style="display: flex;align-items: center; width:100%;flex-direction: column;">
                            <div class="empty-cart-text">
                                <div>
                                    <span class="cart-icon-large"></span>
                                    Your Shopping Cart is empty! 
                                    <button style="display:inline-block;" v-if="cartType !== true && previousProductClass !== \'\'" v-on:click="backToProductSelection" class="button-back-to-selection btn btn-secondary w-100">Back To Selection</button>
                                </div>
                            </div>                                
                            <button style="margin-top: -35px;" v-if="cartType === true" v-on:click="backToProductSelection" class="external buttonID23542445 btn btn-secondary w-100">Back To Selection</button>
                        </div> 
                    </div> 
                </div>
                <h4 v-if="cartItems.length > 0 && cartType === true" class="account-page-title" style="margin-bottom:15px;">
                    Your Shopping Cart
                </h4>
                <table v-if="cartItems.length > 0" class="cart-display-outer">
                    <thead>
                        <tr>
                            <th class="item-title item-header">Package Name &amp; Details</th>
                            <th class="item-quality item-header">Quantity</th>
                            <th class="item-price item-header">Price</th>
                            <th class="item-operate item-header"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="currPackage in cartItems" class="item-product">
                            <td class="product-info-detail">
                                <div class="product-main-image-prime-md">
                                    <div class="product-icon-img-md" v-bind:style="{background: renderProductImage(currPackage)}" @error="imgError(currPackage)"></div>
                                </div>
                                <div class="product-main-details">
                                    <div class="product-main-title">
                                        <h2 class="product-main-title-h2">{{ currPackage.name }}</h2>
                                    </div>
                                    <div v-html="currPackage.description">
                                        {{ currPackage.description }}
                                    </div>
                                    <div v-if="currPackage.line && currPackage.hide_line_items !== 1" class="cart-title-lines">
                                        <ul>
                                            <li v-for="currLine in currPackage.line" class="cart-title-line-item">{{ currLine.name }}<span v-if="currLine.quantity > 1">s</span></li>
                                        </ul>
                                    </div>
                                    <div class="product-attr-box">
                                    <a class="pointer remove-single-product" v-on:click="removePackageFromCart(currPackage)">Remove This Item</a>
                                    </div>
                                </div>
                            </td>
                            <td class="product-quantity">
                                <div class="product-main-data-outer">
                                    <select v-model="currPackage.quantity" class="product-quantity-input" @change="updateProductQuantity(currPackage)">
                                        <option v-for="option in cartQuantityCalculation(currPackage)">{{ option }}</option>
                                    </select>
                                </div>
                            </td>
                            <td class="product-price">
                                <div class="product-price-inner">
                                    <span class="currency"></span>
                                    <span class="value">{{ displayCurrency(currPackage.currency) }}{{ renderMoney(currPackage.regular_price) }}</span>
                                    <span class="separator">/</span>
                                    <span class="unit">Year</span>
                                </div>
                            </td>
                            <td class="product-price" style="text-align:right">
                                <div class="product-price-inner">
                                    <span class="value">{{ displayCurrency(currPackage.currency) }}{{ calculateTotalPackagePrice(currPackage) }}</span>
                                </div>
                             </td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="cartItems.length > 0" class="cart-total-price">
                    <div class="product-price-info-wrapper">
                        <div class="product-price-info1">
                            <span class="product-price-title">Subtotal:</span>
                            <span class="value notranslate product-price-value">US $ {{ calculateTotalCartPrice(cartItems) }}</span>
                        </div>
                        <div class="product-price-info1">
                            <span class="product-price-title">Tax:</span>
                            <span class="value notranslate product-price-value">US $0.00</span>
                        </div>
                        <div class="product-price-info2">
                            <span class="product-price-title">Total:</span>
                            <span class="product-price-total ui-cost notranslate product-price-value"><b>US $ {{ calculateTotalCartPrice(cartItems) }}</b></span>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    <div class="product-price-submit-wrapper">
                        <div class="product-price-info3" style="margin-top: 15px;">
                            <button v-bind:class="{external: cartType === true, internal: cartType === false, }" v-on:click="backToProductSelection" class="buttonID23542445 btn btn-secondary w-100" style="color:#fff;">Back To Selection</button> 
                            <button v-bind:class="{external: cartType === true, internal: cartType === false, disabled: !productCartContainsCard(cartItems) }" v-on:click="openAssignUser(true)" class="buttonID23542445 btn btn-warning w-100"">Go To Checkout</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div v-if="displayComponent === \'assignUser\' && cartType === false"class="cart-display-box entityDashboard">
                <p v-if="cartUser === null">Please assign a customer for this purchase.</p>
                <div v-if="creatingNewCustomer === false" style="background:#ddd;padding: 0px 8px 0px;border-radius:5px;box-shadow:rgba(0,0,0,.2) 0 0 10px inset;margin-bottom:15px;">
                    <table v-if="cartUser === null" class="table" style="margin-bottom: 5px; margin-top:10px;">
                        <tbody>
                            <tr>
                                <td style="width:100px;vertical-align: middle;">Customer</td>
                                <td style="position:relative;">
                                    <div class="dynamic-search">
                                        <span class="inputpicker-arrow">
                                            <b></b>
                                        </span>
                                        <input v-on:focus="engageDynamicSearch" v-on:blur="hideDynamicSearch" v-model="userSearch" v-on:keyup="keyMonitorUserList" autocomplete="off" value="" placeholder="Start Typing..." class="form-control ui-autocomplete-input">
                                        <div class="dynamic-search-list" style="position:absolute;" v-if="dynamicSearch === true && userSearchResult === \'\'">
                                            <table>
                                                <thead>
                                                    <th>User Id</th>
                                                    <th>Name</th>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="currUser in cartUserSearchList">
                                                        <td @click="assignUserToCart(currUser)">{{currUser.user_id}}</td>
                                                        <td @click="assignUserToCart(currUser)">{{currUser.first_name}} {{currUser.last_name}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        <div>
                                    </div>
                                </td>
                                <td v-if="cartUser === null" style="white-space: nowrap;width:240px;">
                                    or <button v-on:click="toggleCreateNewCustomer" class="buttonID23542445 btn btn-primary w-100" style="width: 195px !important; margin-left: 6px;">Create New Customer</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="cartUser !== null" class="selected-customer-wrapper">
                        <div type="button" v-on:click="clearCartUser" class="general-dialog-close" style="right: 11px;top: 33px !important;display: block;"></div>
                        <table class="selected-customer-outer">
                            <tr>
                                <td>
                                    <img class="user-avatar" src="'.$app->objCustomPlatform->getFullPortalDomain().'/_ez/images/users/no-user.jpg" @error="avatarError(cartUser)" width="100"/>
                                </td>
                                <td>
                                    <table class="selected-customer">
                                        <tr>
                                            <td>Name:</td>
                                            <td><b>{{ renderUserPrefix }} {{ cartUser.first_name }} {{ cartUser.last_name }} {{ renderUserSuffix }}</b></td>
                                        </tr>
                                        <tr>
                                            <td>Phone:</td>
                                            <td><b>{{ cartUser.user_phone }}</b></td>
                                        </tr>
                                        <tr>
                                            <td>Email:</td>
                                            <td><b>{{ cartUser.user_email }}</b></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="create-new-customer-wrapper" v-if="creatingNewCustomer === true">
                    <h4 class="account-page-title" style="margin-bottom:15px;"><a v-on:click="backToSelectCustomer" id="back-to-entity-list" class="back-to-entity-list pointer" style=""></a> Create New Customer</h4>
                    <p>Create a new customer below.</p>
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
                                                    <tr v-for="currUser, index in cartAffiliateSearchList" v-bind:class="{userSearchHighlight: userSearchMatchesIndex(index)}">
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
                    </div>'.'
                    <table class="table no-top-border userProfileTable">
                        <tr>
                            <td style="width:125px;vertical-align: middle;">First Name</td>
                            <td><input v-model="newUserForCreate.first_name" class="form-control" type="text" placeholder="Enter First Name..."></td>
                            <td style="width:125px;vertical-align: middle;">Last Name</td>
                            <td><input v-model="newUserForCreate.last_name" class="form-control" type="text" placeholder="Enter Last Name..."></td>
                        </tr>
                        <tr>
                            <td style="width:125px;vertical-align: middle;">Phone</td>
                            <td><input v-on:blur="checkForDuplicateUserPhone(entityClone)" v-model="newUserForCreate.user_phone_value" v-bind:class="{ \'pass-validation\': newUserForCreate.user_phone_value }" id="phone_1603190947" class="form-control" type="text" placeholder="Enter Phone..."></td>
                            <td style="width:125px;vertical-align: middle;">E-mail</td>
                            <td><input v-on:blur="checkForDuplicateUserEmail(entityClone)" v-model="newUserForCreate.user_email_value" v-bind:class="{ \'pass-validation\': newUserForCreate.user_email_value }" id="email_1603190947" class="form-control" type="text" placeholder="Enter E-mail..."></td>
                        </tr>
                    </table>'.'
                    <div class="augmented-form-items">
                        <table class="table usernameTable" style="margin-bottom:2px;">
                            <tr>
                                <td>Username</td>
                                <td><input v-on:blur="checkForDuplicateUsername(entityClone)" v-model="newUserForCreate.username" v-bind:class="{ \'pass-validation\': newUserForCreate.username }"  id="username_1603190947" class="form-control" type="text" placeholder="Enter Username..."></td>
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
                    </div>'.'
                    <div style="padding-left: 21px;"><label class="pointer" for="send-welcome-email"><input id="send-welcome-email" v-model="newUserForCreate.send_welcome_email" class="form-check-input" name="send-welcome-email" type="checkbox"> Send welcome email?</label></div>
                    <div v-if="checkForErrorTextDisplay(errorText.general)" class="field-validation-error">{{ errorText.general }}</div>
                    <button v-on:click="createNewCustomer" class="buttonID23542445 btn btn-primary w-100">Create & Assign New Customer</button>
                </div>
                <button v-if="creatingNewCustomer === false" v-bind:class="{disabled: (cartUser === null)}" v-on:click="openCheckout" class="buttonID23542445 btn btn-primary w-100">Assign Customer</button>
            </div>
            
            <div v-if="displayComponent === \'assignUser\' && cartType === true" class="cart-display-box entityDashboard">
                <h4 class="account-page-title" style="margin-bottom:15px;" v-if="creatingNewCustomer === false">
                    Checkout - Sign In To Your Account
                </h4>
                <div class="create-new-customer-wrapper" v-if="creatingNewCustomer === false">
                    <p style="margin-bottom:-17px; margin-top:6px;">Login below &#151; or <span class="btn btn-add-to-cart" v-on:click="setCreateNewAccount" style="padding: 0px 5px 2px;font-size: inherit;top: -3px;position: relative;">create a new account</span>.</p>
                    <div>
                        <input v-on:blur="clearError(\'username\')" v-model="newUserForCreate.username" class="form-control username-new" style="margin-top:25px;" type="text" placeholder="Enter Your Username">
                        <div v-if="checkForErrorTextDisplay(errorText.username)" class="field-validation-error">{{ errorText.username }}</div>
                    </div>
                    <div>
                        <input v-on:blur="clearError(\'password\')" v-model="newUserForCreate.password" class="form-control password-new" type="password" placeholder="Enter Your Password">
                        <div v-if="checkForErrorTextDisplay(errorText.password)" class="field-validation-error">{{ errorText.password }}</div>
                    </div>
                    <div v-if="checkForErrorTextDisplay(errorText.general)" class="field-validation-error">{{ errorText.general }}</div>
                    <div v-bind:class="{\'text-right\': cartType === true}">
                        <button v-bind:class="{external: cartType === true}" v-on:click="loginCustomer" class="buttonID23542445 btn btn-warning w-100">Sign In</button>
                    </div>
                </div>
                <h4 class="account-page-title" style="margin-bottom:15px;" v-if="creatingNewCustomer === true"><a v-on:click="backToSelectCustomer" id="back-to-entity-list" class="back-to-entity-list pointer" style=""></a> Create a New Account</h4>
                <div class="create-new-customer-wrapper" v-if="creatingNewCustomer === true">
                    <p>Create a new account below.</p>
                    <div>
                        <input v-on:blur="clearError(\'first_name\')" v-model="newUserForCreate.first_name" class="form-control first-name" type="text" placeholder="First Name">
                        <div v-if="checkForErrorTextDisplay(errorText.first_name)" class="field-validation-error">{{ errorText.first_name }}</div>
                    </div>
                    <div>
                        <input v-on:blur="clearError(\'last_name\')" v-model="newUserForCreate.last_name" class="form-control last-name" type="text" placeholder="Last Name">
                        <div v-if="checkForErrorTextDisplay(errorText.last_name)" class="field-validation-error">{{ errorText.last_name }}</div>
                    </div>
                    <div>
                        <input v-on:blur="clearError(\'email\')" v-model="newUserForCreate.email" class="form-control email" type="email" placeholder="Email">
                        <div v-if="checkForErrorTextDisplay(errorText.email)" class="field-validation-error">{{ errorText.email }}</div>
                    </div>
                    <div>
                        <input v-on:blur="clearError(\'phone\')" v-model="newUserForCreate.phone" class="form-control phone" type="phone" placeholder="Mobile Phone">
                        <div v-if="checkForErrorTextDisplay(errorText.phone)" class="field-validation-error">{{ errorText.phone }}</div>
                    </div>
                    <div>
                        <input v-on:blur="clearError(\'username\')" v-model="newUserForCreate.username" class="form-control username-new" style="margin-top:25px;" type="text" placeholder="Username">
                        <div v-if="checkForErrorTextDisplay(errorText.username)" class="field-validation-error">{{ errorText.username }}</div>
                    </div>
                    <div>
                        <input v-on:blur="clearError(\'password\')" v-model="newUserForCreate.password" class="form-control password-new" type="password" placeholder="Temporary Password">
                        <div v-if="checkForErrorTextDisplay(errorText.password)"  class="field-validation-error">{{ errorText.password }}</div>
                    </div>
                    <div v-if="checkForErrorTextDisplay(errorText.general)" class="field-validation-error">{{ errorText.general }}</div>
                    <div v-bind:class="{\'text-right\': cartType === true}">
                        <button v-bind:class="{external: cartType === true}" v-on:click="createNewCustomer" class="buttonID23542445 btn btn-warning w-100">Create New Account!</button>
                    </div>           
                </div>           
            </div>
            
            <div v-if="displayComponent === \'checkout\'"class="cart-display-box entityDashboard">
                <h4 v-if="cartType === true"  class="account-page-title" style="margin-bottom:15px;">
                    Checkout - Select A Payment Account And Submit Your Order
                </h4>
                <table v-if="cartItems.length > 0" class="cart-display-outer cart-display-preview">
                    <tbody>
                        <tr v-for="currPackage in cartItems" class="item-product-preview">
                            <td class="product-info-detail">
                                <div class="product-main-image-prime-sm">
                                    <div class="product-icon-img-sm" v-bind:style="{background: renderProductImage(currPackage)}" @error="imgError(currPackage)"></div>
                                </div>
                                <div class="product-main-details">
                                    <div class="product-main-title">
                                        <h2 class="product-main-title-h2">{{ currPackage.name }}</h2>
                                    </div>
                                    <div v-if="currPackage.line && currPackage.hide_line_items !== 1" class="cart-title-lines">
                                        <ul>
                                            <li v-for="currLine in currPackage.line" class="cart-title-line-item">{{ currLine.name }}<span v-if="currLine.quantity > 1">s</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                            <td class="product-quantity">
                                <div class="product-main-data-outer">
                                    Quantity: {{ currPackage.quantity }}
                                </div>
                            </td>
                            <td class="product-price">
                                <div class="product-price-inner">
                                    <span class="currency"></span>
                                    <span class="value">{{ displayCurrency(currPackage.currency) }}{{ renderMoney(currPackage.regular_price) }}</span>
                                    <span class="separator">/</span>
                                    <span class="unit">Year</span>
                                </div>
                            </td>
                            <td class="product-price" style="text-align:right">
                                <div class="product-price-inner">
                                    <span class="value">{{ displayCurrency(currPackage.currency) }}{{ calculateTotalPackagePrice(currPackage) }}</span>
                                </div>
                             </td>
                        </tr>
                    </tbody>
                </table>
                <div class="checkout-bottom-wrapper">
                    <div class="width50">
                        <div class="selected-customer-checkout">
                            <h4 v-if="cartType !== true" style="margin-bottom: 7px;">Selected Customer</h4>
                            <h4 v-if="cartType === true" style="margin-bottom: 7px;">Your Account</h4>
                            <table class="selected-customer-details">
                                <tr>
                                    <td style="width: 70px;">Name:</td>
                                    <td><b>{{ renderUserPrefix }} {{ cartUser.first_name }} {{ cartUser.last_name }} {{ renderUserSuffix }}</b></td>
                                </tr>
                                <tr>
                                    <td>Phone:</td>
                                    <td><b>{{ cartUser.user_phone }}</b></td>
                                </tr>
                                <tr>
                                    <td>Email:</td>
                                    <td><b>{{ cartUser.user_email }}</b></td>
                                </tr>
                            </table>
                        </div>
                        <div v-if="toggleCreateNewCartPaymentAccount === false" class="selected-payment-account-checkout">
                            <h4 style="margin-bottom: 7px;">Select Payment Account</h4>
                            <ul class="selected-payment-account-list">
                                <li v-for="currCard in customerPaymentAccounts" class="pointer" v-on:click="selectCartPaymentAccount(currCard)">
                                    <label v-bind:for="\'customer-payment-account-\' + currCard.payment_account_id">
                                        <input v-bind:id="\'customer-payment-account-\' + currCard.payment_account_id" name="cart-customer-payment-account" v-bind="cartPaymentAccount" :value="currCard.payment_account_id" class="form-check-input" type="radio" /> <img v-bind:src="renderCardType(currCard)" width="40" height="25" /> {{ currCard.display_1 }} {{ currCard.display_2 }}
                                    </label
                                </li>
                                <li class="pointer" v-on:click="createNewCartPaymentAccount">
                                    <div class="add-new-account">
                                        Add New Payment Account
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div v-if="toggleCreateNewCartPaymentAccount === true" class="selected-payment-account-checkout">
                            <h4 style="margin-bottom: 7px;"><a v-on:click="backToSelectPaymentAccount" id="back-to-entity-list" class="back-to-entity-list pointer" style=""></a> Create New Payment Account</h4>
                            <div class="create-new-payment-account">
                                <div class="create-new-payment-account-box">
                                    <h5 class="billing-account-title"><span class="ico ico-billing"></span> Credit Card Details</h5>
                                    <div>
                                        <input v-on:blur="clearError(\'name\')" v-model="newCardForCreate.name" type="text" class="form-control" placeholder="Name on Card">
                                        <div v-if="checkForErrorTextDisplay(errorText.name)" class="field-validation-error">{{ errorText.name }}</div>
                                    </div>
                                    <div>
                                        <input v-on:keypress="testCreditCard(newCardForCreate.number)" v-on:blur="clearError(\'number\')" v-model="newCardForCreate.number" type="text" class="form-control" placeholder="Card Number" style="width: calc(100% - 50px); display: inline-block;">
                                        <img v-bind:src="\'' . $app->objCustomPlatform->getFullPortalDomain() . '/_ez/images/financials/cc_small_\' + creditCartType + \'.png\'" style="position: relative; top: -3px; right: -5px;"/>
                                        <div v-if="checkForErrorTextDisplay(errorText.number)" class="field-validation-error">{{ errorText.number }}</div>
                                    </div>
                                    <div class="inlineBlock">
                                        <select v-on:blur="clearError(\'expMonth\')" v-model="newCardForCreate.expMonth" class="form-control" style="width:100px;">
                                            <option value="">Month</option>
                                            <option value="01">01</option>
                                            <option value="02">02</option>
                                            <option value="03">03</option>
                                            <option value="04">04</option>
                                            <option value="05">05</option>
                                            <option value="06">06</option>
                                            <option value="07">07</option>
                                            <option value="08">08</option>
                                            <option value="09">09</option>
                                            <option value="10">10</option>
                                            <option value="11">11</option>
                                            <option value="12">12</option>
                                        </select>
                                        <select v-on:blur="clearError(\'expYear\')" v-model="newCardForCreate.expYear" class="form-control" style="width:100px;margin-left: 5px;">
                                            <option value="">Year</option>
                                            <option value="2020">2020</option>
                                            <option value="2021">2021</option>
                                            <option value="2022">2022</option>
                                            <option value="2023">2023</option>
                                            <option value="2024">2024</option>
                                            <option value="2025">2025</option>
                                            <option value="2026">2026</option>
                                            <option value="2027">2027</option>
                                            <option value="2028">2028</option>
                                        </select>
                                        <div v-if="checkForErrorTextDisplay(errorText.expMonth)" class="field-validation-error">{{ errorText.expMonth }}</div>
                                        <div v-if="checkForErrorTextDisplay(errorText.expYear)" class="field-validation-error">{{ errorText.expYear }}</div>
                                    </div>
                                    <div>
                                        <input v-on:blur="clearError(\'cvc\')" v-model="newCardForCreate.cvc" type="text" class="form-control csv-number" placeholder="CSV Number">
                                        <div v-if="checkForErrorTextDisplay(errorText.cvc)" class="field-validation-error">{{ errorText.cvc }}</div>
                                    </div>
                                </div>
                                
                                <div class="create-new-payment-account-box">
                                    <h5 class="billing-account-title"><span class="ico ico-home"></span> Billing Address</h5>
                                    <div>
                                        <input v-on:blur="clearError(\'line1\')" v-model="newCardForCreate.line1" type="text" class="form-control" placeholder="Address Line 1">
                                        <div v-if="checkForErrorTextDisplay(errorText.line1)" class="field-validation-error">{{ errorText.line1 }}</div>
                                    </div>
                                    <div>
                                        <input v-model="newCardForCreate.line2" type="text" class="form-control" placeholder="Address Line 2">
                                    </div>
                                    <div>
                                        <input v-on:blur="clearError(\'city\')" v-model="newCardForCreate.city" type="text" class="form-control" placeholder="City">
                                        <div v-if="checkForErrorTextDisplay(errorText.city)" class="field-validation-error">{{ errorText.city }}</div>
                                    </div>
                                    <div>
                                        <input v-on:blur="clearError(\'state\')" v-model="newCardForCreate.state" type="text" class="form-control" placeholder="State">
                                        <div v-if="checkForErrorTextDisplay(errorText.state)" class="field-validation-error">{{ errorText.state }}</div>
                                    </div>
                                    <div>
                                        <input v-on:blur="clearError(\'zip\')" v-model="newCardForCreate.zip" type="text" class="form-control" placeholder="Postal Code">
                                        <div v-if="checkForErrorTextDisplay(errorText.zip)" class="field-validation-error">{{ errorText.zip }}</div>
                                    </div>
                                    <div>
                                        <input v-on:blur="clearError(\'country\')" v-model="newCardForCreate.country" type="text" class="form-control" placeholder="Country">
                                        <div v-if="checkForErrorTextDisplay(errorText.country)" class="field-validation-error">{{ errorText.country }}</div>
                                    </div>
                                    <div>
                                        <button v-on:click="createNewCardForCustomer" class="buttonID23542445 btn btn-primary w-100"">Authorize Card</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="width50">
                        <div class="promo-code">
                            <input style="display: inline-block;width: calc(100% - 77px);margin-right: 5px;" type="text" class="form-control" v-model="cartPromoCodeSearch" placeholder="Have a promo code?" /> 
                            <button v-if="applyCartPromoCode === false" v-on:click="applyPromoCode" class="btn btn-primary" style="margin-top: -4px;">Apply</button>
                            <button v-if="applyCartPromoCode === true" v-on:click="removePromoCode" class="btn btn-danger" style="margin-top: -4px;width: 65px;padding: 5px;font-size: 12px;height: 38px;">Remove</button>
                        </div>
                        <div class="legal-stuff" style="margin-top:25px">
                            <p v-if="cartType === true" style="text-align: right;"><label class="pointer" for="terms-and-agreement" class="terms-and-agreement"><input id="terms-and-agreement" v-model="termsAndAgreementAcceptence" class="form-check-input" type="checkbox"> I acknowledge and understand the ' . $app->objCustomPlatform->getPortalName() . ' ' . $privacyPolicyLink . ' and Terms of Service.</label><p>
                            <p v-if="cartType === false" style="text-align: right;"><label class="pointer" for="terms-and-agreement" class="terms-and-agreement"><input id="terms-and-agreement" v-model="termsAndAgreementAcceptence" class="form-check-input" type="checkbox"> The customer acknowledges and understands the ' . $app->objCustomPlatform->getPortalName() . ' ' . $privacyPolicyLink . ' and Terms of Service.</label><p>
                            <div v-if="cartItems.length > 0" class="cart-total-price">
                                <div class="product-price-info-wrapper">
                                    <div class="product-price-info1">
                                        <span class="product-price-title">Subtotal:</span>
                                        <span class="value notranslate product-price-value">US $ {{ calculateTotalCartPrice(cartItems) }}</span>
                                    </div>
                                    <div v-if="applyCartPromoCode === true" class="product-price-info1" style="color:#ff0000">
                                        <span class="product-price-title">Promo Code:</span>
                                        <span class="value notranslate product-price-value">US -<span v-if="activeCartPromoCode.discount_type ===\'$\'">{{ activeCartPromoCode.discount_type }}</span> {{ renderMoney(activeCartPromoCode.discount_value) }}<span v-if="activeCartPromoCode.discount_type ===\'%\'">{{ activeCartPromoCode.discount_type }}</span> </span>
                                    </div>
                                    <div class="product-price-info1">
                                        <span class="product-price-title">Processing Fee:</span>
                                        <span class="value notranslate product-price-value">US $ {{ calculateTotalCheckoutPriceFee(calculateTotalCheckoutPrice(activeCartPromoCode, cartItems, applyCartPromoCode)) }}</span>
                                    </div>
                                    <div class="product-price-info1">
                                        <span class="product-price-title">Tax:</span>
                                        <span class="value notranslate product-price-value">US $ 0.00</span>
                                    </div>
                                    <div class="product-price-info2">
                                        <span class="product-price-title">Total:</span>
                                        <span class="product-price-total ui-cost notranslate product-price-value"><b>US $ {{ calculateTotalCheckoutPriceWithFee(activeCartPromoCode, cartItems, applyCartPromoCode) }}</b></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="clear:both;"></div>
                    <div v-bind:class="{\'text-right\': cartType === true}">
                        <button v-if="cartType === true" v-bind:class="{external: cartType === true, disabled: (toggleCreateNewCartPaymentAccount === true || cartPaymentAccount === null || termsAndAgreementAcceptence === false)}" v-on:click="processCartOrder" class="buttonID23542445 btn btn-primary w-100"">Submit Order</button>
                    </div>
                </div>
                <button v-if="cartType !== true" v-bind:class="{disabled: (toggleCreateNewCartPaymentAccount === true || cartPaymentAccount === null || termsAndAgreementAcceptence === false)}" v-on:click="processCartOrder" class="buttonID23542445 btn btn-primary w-100"">Submit Order</button>
            </div>
            
            <div v-if="displayComponent === \'completed\'"class="cart-display-box entityDashboard">
                <h4 v-if="cartType === true"  class="account-page-title" style="margin-bottom:15px;">
                    Thank You For Your Purchase!
                </h4>
                <h4 v-if="cartType === false"  class="account-page-title" style="margin-bottom:15px;">
                    Purchase Complete
                </h4>
                <table v-if="cartDisplayItemsForComplete.length > 0" class="cart-display-outer cart-display-preview">
                    <tbody>
                        <tr v-for="currPackage in cartDisplayItemsForComplete" class="item-product-preview">
                            <td class="product-info-detail">
                                <div class="product-main-image-prime-sm">
                                    <div class="product-icon-img-sm" v-bind:style="{background: renderProductImage(currPackage)}" @error="imgError(currPackage)"></div>
                                </div>
                                <div class="product-main-details">
                                    <div class="product-main-title">
                                        <h2 class="product-main-title-h2">{{ currPackage.name }}</h2>
                                    </div>
                                    <div v-if="currPackage.line && currPackage.hide_line_items !== 1" class="cart-title-lines">
                                        <ul>
                                            <li v-for="currLine in currPackage.line" class="cart-title-line-item">{{ currLine.name }}<span v-if="currLine.quantity > 1">s</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                            <td class="product-quantity">
                                <div class="product-main-data-outer">
                                    Quantity: {{ currPackage.quantity }}
                                </div>
                            </td>
                            <td class="product-price">
                                <div class="product-price-inner">
                                    <span class="currency"></span>
                                    <span class="value">{{ displayCurrency(currPackage.currency) }}{{ renderMoney(currPackage.regular_price) }}</span>
                                    <span class="separator">/</span>
                                    <span class="unit">Year</span>
                                </div>
                            </td>
                            <td class="product-price" style="text-align:right">
                                <div class="product-price-inner">
                                    <span class="value">{{ displayCurrency(currPackage.currency) }}{{ calculateTotalPackagePrice(currPackage) }}</span>
                                </div>
                             </td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="cartDisplayItemsForComplete.length > 0" class="cart-total-price">
                    <div class="product-price-info-wrapper">
                        <div class="product-price-info1">
                            <span class="product-price-title">Subtotal:</span>
                            <span class="value notranslate product-price-value">US $ {{ calculateTotalCartPrice(cartDisplayItemsForComplete) }}</span>
                        </div>
                        <div v-if="applyCompletedCartPromoCode === true" class="product-price-info1" style="color:#ff0000">
                            <span class="product-price-title">Promo Code:</span>
                            <span class="value notranslate product-price-value">US -<span v-if="completedDisplayCartPromoCode.discount_type ===\'$\'">{{ completedDisplayCartPromoCode.discount_type }}</span> {{ renderMoney(completedDisplayCartPromoCode.discount_value) }}<span v-if="completedDisplayCartPromoCode.discount_type ===\'%\'">{{ completedDisplayCartPromoCode.discount_type }}</span> </span>
                        </div>
                        <div class="product-price-info1">
                            <span class="product-price-title">Processing Fee:</span>
                            <span class="value notranslate product-price-value">US $ {{ calculateTotalCheckoutPriceFee(calculateTotalCheckoutPrice(completedDisplayCartPromoCode, cartDisplayItemsForComplete, applyCompletedCartPromoCode)) }}</span>
                        </div>
                        <div class="product-price-info1">
                            <span class="product-price-title">Tax:</span>
                            <span class="value notranslate product-price-value">US $ 0.00</span>
                        </div>
                        <div class="product-price-info2">
                            <span class="product-price-title">Total:</span>
                            <span class="product-price-total ui-cost notranslate product-price-value"><b>US $ {{ calculateTotalCheckoutPriceWithFee(completedDisplayCartPromoCode, cartDisplayItemsForComplete, applyCompletedCartPromoCode) }}</b></span>
                        </div>
                    </div>
                </div>
                <div v-if="cartType === true" class="cart-display-box entityDashboard">
                    <div style="margin-top: 20px;"><b>Welcome to ' . $app->objCustomPlatform->getPortalName() . '!</b> We are thrilled that you have joined our community of positive, forward-thinking business owners and professionals!! These quick steps will help us get your card up and running right away.
                    </div>
                    <h4 style="font-size: 20px;margin-top: 20px;text-align: center;border-top: 1px #ccc solid;padding: 15px;border-bottom: 1px #ccc solid;margin-bottom: 15px;">Next Steps:</h4>
                    <p>It is now time to tell us what you want on your card!</p>
                    <ul>
                        <li v-for="currCard in cardList">
                            <a v-bind:href="\''.$app->objCustomPlatform->getFullPortalDomain().'/account/cards/card-dashboard/\' + currCard.sys_row_id" target="_blank" style="background: rgb(0, 133, 255);padding: 5px 15px;color: rgb(255, 255, 255);border-radius: 5px;cursor: pointer;display: inline-block;margin-bottom: 12px;margin-right: 11px;"><span class="fas fa-hammer" style="margin-right: 7px;"></span>Access your new card here!</a>
                        </li>
                    </ul>
                    <p>Once logged in you will find a <b>card build form</b> to submit to us. Please do so at your earliest convenience.</p>
                    <p>Additionally, please be aware that a member of our Customer Service team will be reaching out to you within <b>72 hours of your purchase</b> to answer any questions that you might have.</p>
                    <p>In the meantime <b>if you need anything</b>, please send an email to <a href="mailto:'.$app->objCustomPlatform->getCompany()->customer_support_email.'">'.$app->objCustomPlatform->getCompany()->customer_support_email.'</a> and someone will get back to you promptly.</p>
                </div>
                <div v-if="cartType === false && productCartContainsProductType(cartDisplayItemsForComplete, ENUM_CardType) === true" class="cart-display-box entityDashboard">
                    <h4 style="font-size: 20px;margin-top: 20px;border-top: 1px solid rgb(204, 204, 204);padding: 15px 0;">Access Products:</h4>
                    <p>For your convenience, you can access your cards below:</p>
                    <ul>
                        <li v-for="currCard in cardList" style="background: #0083ff;color: #fff;padding: 5px 13px;border-radius: 4px;">
                            <a style="color: #fff;font-size: 17px;" class="pointer underlined" v-on:click="goToCardDashboard(currCard)" >Card # {{ currCard.card_num }} - {{ renderCardNameForLink(currCard) }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>';
    }
}